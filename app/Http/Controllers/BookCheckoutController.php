<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookCheckoutRequest;
use App\Http\Requests\BookShippingRequest;
use App\Models\Book;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\BookOrderService;
use App\Services\MidtransService;
use App\Services\MinioStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BookCheckoutController extends Controller
{
    public function __construct(
        protected MidtransService  $midtrans,
        protected BookOrderService $bookOrderService,
    ) {}

    // ─────────────────────────────────────────────────────────────────────────
    // PROCESS — buat order + snap token (atau direct free)
    // POST /book/checkout/process
    // ─────────────────────────────────────────────────────────────────────────

    public function process(BookCheckoutRequest $request)
    {
        $data = $request->validate([
            'book_id'       => ['required', 'integer', 'exists:books,id'],
            'purchase_type' => ['required', 'in:digital,physical,both'],
            'quantity'      => ['sometimes', 'integer', 'min:1', 'max:10'],
        ]);

        /** @var \App\Models\User $user */
        $user     = $request->user();
        $book     = Book::published()->findOrFail($data['book_id']);
        $quantity = $data['quantity'] ?? 1;
        $purchaseType = $data['purchase_type'];

        // Guard: stok habis untuk fisik
        if (in_array($purchaseType, ['physical', 'both']) && $book->stock < $quantity) {
            return back()->with('error', 'Maaf, stok buku fisik tidak mencukupi.');
        }

        // Guard: sudah pernah beli (digital — hanya boleh 1x)
        if (in_array($purchaseType, ['digital', 'both'])) {
            $alreadyOwned = OrderItem::whereHas('order', function ($q) use ($user) {
                    $q->where('user_id', $user->id)->where('status', 'paid');
                })
                ->where('itemable_type', Book::class)
                ->where('itemable_id', $book->id)
                ->exists();

            if ($alreadyOwned) {
                return redirect()->route('books.show', $book->slug)
                    ->with('info', 'Kamu sudah memiliki buku ini.');
            }
        }

        $price = $book->effective_price * $quantity;

        // ── Gratis → langsung proses ─────────────────────────────────────
        if ($price === 0) {
            return $this->handleFreeBook($user, $book, $quantity, $purchaseType);
        }

        // ── Buku fisik → perlu form alamat dulu ──────────────────────────
        if (in_array($purchaseType, ['physical', 'both'])) {
            // Simpan data ke session, redirect ke form alamat
            session([
                'book_checkout' => [
                    'book_id'       => $book->id,
                    'purchase_type' => $purchaseType,
                    'quantity'      => $quantity,
                    'price'         => $price,
                ],
            ]);

            return redirect()->route('book.checkout.shipping', $book->slug);
        }

        // ── Digital → langsung ke payment ────────────────────────────────
        return $this->createOrderAndPay($user, $book, $quantity, $purchaseType, $price);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // SHIPPING FORM — form alamat untuk buku fisik
    // GET /book/checkout/{slug}/shipping
    // ─────────────────────────────────────────────────────────────────────────

    public function shipping(string $slug)
    {
        $checkoutData = session('book_checkout');
        if (! $checkoutData) {
            return redirect()->route('books.show', $slug)
                ->with('error', 'Session checkout tidak valid. Silakan ulangi.');
        }

        $book = Book::published()->where('slug', $slug)->firstOrFail();

        return view('pages.books.shipping', [
            'book'     => $book,
            'checkout' => $checkoutData,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // SHIPPING PROCESS — simpan alamat + buat order
    // POST /book/checkout/{slug}/shipping
    // ─────────────────────────────────────────────────────────────────────────

    public function shippingProcess(BookShippingRequest $request, string $slug)
    {
        // Validasi sudah ditangani oleh BookShippingRequest (form request)

        $checkoutData = session('book_checkout');
        if (! $checkoutData) {
            return redirect()->route('books.show', $slug)
                ->with('error', 'Session checkout tidak valid. Silakan ulangi.');
        }

        /** @var \App\Models\User $user */
        $user = $request->user();
        $book = Book::published()->where('slug', $slug)->findOrFail($checkoutData['book_id']);

        // Simpan alamat ke session untuk dipakai saat buat order
        $shippingAddress = [
            'name'        => $request->name,
            'phone'       => $request->phone,
            'address'     => $request->address,
            'city'        => $request->city,
            'province'    => $request->province,
            'postal_code' => $request->postal_code,
            'notes'       => $request->notes,
        ];

        $courier = $request->courier;

        session()->forget('book_checkout');

        return $this->createOrderAndPay(
            $user,
            $book,
            $checkoutData['quantity'],
            $checkoutData['purchase_type'],
            $checkoutData['price'],
            $shippingAddress,
            $courier,
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // WEBHOOK — Midtrans callback
    // POST /book/webhook
    // ─────────────────────────────────────────────────────────────────────────

    public function webhook(Request $request)
    {
        $payload = $request->all();

        if (! $this->midtrans->verifySignature($payload)) {
            Log::warning('Book webhook: invalid signature', ['order_id' => $payload['order_id'] ?? null]);
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $order = Order::where('order_number', $payload['order_id'] ?? '')->first();
        if (! $order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        // Pastikan order ini mengandung book items
        $hasBookItem = $order->items()->where('itemable_type', Book::class)->exists();
        if (! $hasBookItem) {
            return response()->json(['message' => 'Not a book order'], 200);
        }

        try {
            if ($this->midtrans->isPaymentSuccess($payload)) {
                $order->update([
                    'status'                  => 'paid',
                    'paid_at'                 => now(),
                    'midtrans_transaction_id' => $payload['transaction_id'] ?? null,
                ]);
                $this->bookOrderService->handlePaymentSuccess($order);

            } elseif ($this->midtrans->isPaymentFailed($payload)) {
                $this->bookOrderService->handlePaymentFailed($order);
            }

        } catch (\Throwable $e) {
            Log::error('Book webhook processing failed', [
                'order_id' => $order->id,
                'error'    => $e->getMessage(),
            ]);
            return response()->json(['message' => 'Processing error'], 500);
        }

        return response()->json(['message' => 'OK'], 200);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // SUCCESS + FAILED redirects
    // ─────────────────────────────────────────────────────────────────────────

    public function success(Request $request)
    {
        $order = Order::where('order_number', $request->order_id)->first();

        // Fallback processing jika webhook belum sampai
        if ($order && $order->status === 'pending') {
            $order->loadMissing('items');
            $hasBook = $order->items()->where('itemable_type', Book::class)->exists();
            if ($hasBook) {
                $order->update(['status' => 'paid', 'paid_at' => now()]);
                $this->bookOrderService->handlePaymentSuccess($order);
            }
        }

        return redirect()->route('dashboard.my-books')
            ->with('success', 'Pembayaran berhasil! Buku kamu sudah bisa diakses.');
    }

    public function failed(Request $request)
    {
        $order = Order::where('order_number', $request->order_id)->first();
        if ($order && $order->status === 'pending') {
            $this->bookOrderService->handlePaymentFailed($order);
        }

        return redirect()->route('books.index')
            ->with('error', 'Pembayaran dibatalkan atau gagal. Silakan coba lagi.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // DOWNLOAD — download e-book setelah payment
    // GET /books/{slug}/download
    // ─────────────────────────────────────────────────────────────────────────

    public function download(Request $request, string $slug)
    {
        $book = Book::published()->where('slug', $slug)->firstOrFail();

        if (! $book->is_digital) {
            abort(404, 'Buku ini bukan format digital.');
        }

        // Policy: admin, instructor pemilik, atau user yang sudah beli
        $this->authorize('download', $book);

        // Cek file exists di MinIO
        if (! $book->file_path) {
            return back()->with('error', 'File buku belum tersedia. Silakan hubungi admin.');
        }

        // Generate signed URL dari MinIO (berlaku MINIO_BOOK_EXPIRY menit)
        $signedUrl = app(MinioStorageService::class)->getBookDownloadUrl($book->file_path);

        return redirect()->away($signedUrl);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PRIVATE helpers
    // ─────────────────────────────────────────────────────────────────────────

    protected function handleFreeBook($user, Book $book, int $quantity, string $purchaseType)
    {
        try {
            DB::transaction(function () use ($user, $book, $quantity, $purchaseType) {
                $order = Order::create([
                    'user_id'         => $user->id,
                    'subtotal'        => 0,
                    'discount_amount' => 0,
                    'total'           => 0,
                    'status'          => 'paid',
                    'paid_at'         => now(),
                    'payment_method'  => 'free',
                ]);

                OrderItem::create([
                    'order_id'      => $order->id,
                    'itemable_type' => Book::class,
                    'itemable_id'   => $book->id,
                    'item_name'     => $book->title,
                    'price'         => 0,
                    'quantity'      => $quantity,
                ]);

                $this->bookOrderService->handlePaymentSuccess($order);
            });

            if ($book->is_digital) {
                return redirect()->route('books.download', $book->slug)
                    ->with('success', 'Buku gratis berhasil didapatkan!');
            }

            return redirect()->route('books.show', $book->slug)
                ->with('success', 'Buku gratis berhasil didapatkan!');

        } catch (\Throwable $e) {
            Log::error('Free book acquisition failed', [
                'book_id' => $book->id,
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);
            return back()->with('error', 'Gagal memproses. Silakan coba lagi.');
        }
    }

    protected function createOrderAndPay($user, Book $book, int $quantity, string $purchaseType, int $price, ?array $shippingAddress = null, ?string $courier = null)
    {
        $order = DB::transaction(function () use ($user, $book, $quantity, $purchaseType, $price, $shippingAddress, $courier) {
            $order = Order::create([
                'user_id'         => $user->id,
                'subtotal'        => $price,
                'discount_amount' => 0,
                'total'           => $price,
                'status'          => 'pending',
                'payment_method'  => 'midtrans',
            ]);

            OrderItem::create([
                'order_id'      => $order->id,
                'itemable_type' => Book::class,
                'itemable_id'   => $book->id,
                'item_name'     => $book->title,
                'price'         => $book->effective_price,
                'quantity'      => $quantity,
                'meta'          => [
                    'purchase_type'    => $purchaseType,
                    'shipping_address' => $shippingAddress,
                    'courier'          => $courier,
                ],
            ]);

            return $order;
        });

        try {
            $redirectUrl = $this->midtrans->createSnapToken($order);
            
            // REDIRECT MODE: Langsung arahkan ke halaman pembayaran aman Midtrans
            return redirect()->away($redirectUrl);
        } catch (\Exception $e) {
            Log::error('Midtrans redirect generation failed for book', [
                'order_id' => $order->id,
                'error'    => $e->getMessage(),
            ]);
            $order->update(['status' => 'failed']);
            return back()->with('error', 'Gagal memproses pembayaran. Silakan coba lagi.');
        }
    }
}
