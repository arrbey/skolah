<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Bootcamp;
use App\Models\Cart;
use App\Models\Course;
use App\Models\Bundle;
use App\Models\MembershipPlan;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PromoCode;
use App\Mail\OrderCreatedMail;
use App\Mail\OrderPaymentMail;
use App\Services\BookOrderService;
use App\Services\BootcampRegistrationService;
use App\Services\CourseEnrollmentService;
use App\Services\BundleEnrollmentService;
use App\Services\MembershipService;
use App\Services\MidtransService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    public function __construct(
        protected MidtransService $midtransService,
    ) {}

    // ─────────────────────────────────────────────────────────────────────────
    // GET /checkout — Tampilkan summary order dari isi cart
    // ─────────────────────────────────────────────────────────────────────────

    public function index()
    {
        $cartItems = Cart::forUser(Auth::id())
            ->with(['cartable', 'courseVariant'])
            ->latest()
            ->get();

        // Redirect ke cart jika kosong
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart')
                ->with('warning', 'Keranjang belanja kosong. Tambahkan item terlebih dahulu.');
        }

        $subtotal = $cartItems->sum('subtotal');

        // ── Promo code dari session ──────────────────────────────────────
        $promoCode = null;
        $discount  = 0;
        $promoCodeText = session('promo_code');

        if ($promoCodeText) {
            $promoCode = PromoCode::where('code', $promoCodeText)->first();
            if ($promoCode && $promoCode->is_valid) {
                $discount = $promoCode->calculateDiscount($subtotal);
            } else {
                session()->forget('promo_code');
                $promoCode = null;
            }
        }

        $total = max(0, $subtotal - $discount);

        return view('checkout.index', compact(
            'cartItems', 'subtotal', 'discount', 'total', 'promoCode'
        ));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // POST /checkout/process — Buat order, generate snap token, return JSON
    // ─────────────────────────────────────────────────────────────────────────

    public function store(Request $request): JsonResponse
    {
        $user = Auth::user();

        $cartItems = Cart::forUser($user->id)
            ->with(['cartable', 'courseVariant'])
            ->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Keranjang belanja kosong.',
            ], 422);
        }

        $subtotal = $cartItems->sum('subtotal');

        // ── Promo code ───────────────────────────────────────────────────
        $promoCode = null;
        $discount  = 0;
        $promoCodeText = session('promo_code');

        if ($promoCodeText) {
            $promoCode = PromoCode::where('code', $promoCodeText)->first();
            if ($promoCode && $promoCode->is_valid) {
                $discount = $promoCode->calculateDiscount($subtotal);
            }
        }

        $total = max(0, $subtotal - $discount);

        try {
            $order = DB::transaction(function () use ($user, $cartItems, $subtotal, $discount, $total, $promoCode, $promoCodeText) {

                // ── 1. Buat Order ────────────────────────────────────────
                $order = Order::create([
                    'user_id'            => $user->id,
                    'subtotal'           => $subtotal,
                    'discount_amount'    => $discount,
                    'total'              => $total,
                    'status'             => 'pending',
                    'payment_method'     => 'midtrans',
                    'promo_code'         => $promoCodeText,
                    'payment_expires_at' => now()->addHours(24),
                ]);

                // ── 2. Buat Order Items ──────────────────────────────────
                foreach ($cartItems as $cartItem) {
                    OrderItem::create([
                        'order_id'          => $order->id,
                        'itemable_type'     => $cartItem->cartable_type,
                        'itemable_id'       => $cartItem->cartable_id,
                        'item_name'         => $this->getItemName($cartItem),
                        'price'             => $cartItem->price,
                        'quantity'          => $cartItem->quantity,
                        'meta'              => $this->getItemMeta($cartItem),
                        'course_variant_id' => $cartItem->course_variant_id,
                        'flash_sale_item_id'=> $cartItem->flash_sale_item_id,
                    ]);
                }

                // ── 3. Tandai promo sebagai terpakai ─────────────────────
                if ($promoCode) {
                    $promoCode->markAsUsed();
                }

                // ── 4. Kosongkan cart ─────────────────────────────────────
                Cart::forUser($order->user_id)->delete();

                return $order;
            });

            // ── 5. Generate Snap Redirect URL ──────────────────────────────
            $redirectUrl = $this->midtransService->createSnapToken($order);

            // Hapus promo code dari session
            session()->forget('promo_code');

            // ── 6. Kirim email "Segera Bayar" ───────────────────────────
            try {
                \Illuminate\Support\Facades\Mail::to($user->email)
                    ->send(new OrderCreatedMail($order));
            } catch (\Throwable $e) {
                Log::warning('Order created email failed', [
                    'order_id' => $order->id,
                    'error'    => $e->getMessage(),
                ]);
            }

            // ── 7. Kirim notifikasi in-app "Segera Bayar" ───────────────
            try {
                $itemSummary = $cartItems->map(fn($c) => $this->getItemName($c))->implode(', ');
                send_notification(
                    user: $user,
                    type: 'order',
                    title: '🛒 Pesanan Berhasil Dibuat',
                    message: "Pesanan #{$order->order_number} senilai " . rupiah((int) $order->total) . " menunggu pembayaran. Segera selesaikan sebelum 24 jam!",
                    url: route('dashboard.orders'),
                );
            } catch (\Throwable $e) {
                Log::warning('Order created in-app notification failed', [
                    'order_id' => $order->id,
                    'error'    => $e->getMessage(),
                ]);
            }

            Log::info('Checkout order created', [
                'order_id'     => $order->id,
                'order_number' => $order->order_number,
                'total'        => $order->total,
                'redirect_url' => substr($redirectUrl, 0, 30) . '...',
            ]);

            return response()->json([
                'success'      => true,
                'redirect_url' => $redirectUrl,
                'order'      => [
                    'id'           => $order->id,
                    'order_number' => $order->order_number,
                    'total'        => $order->total,
                ],
            ]);

        } catch (\Throwable $e) {
            Log::error('Checkout failed', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses pembayaran. Silakan coba lagi.',
            ], 500);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // GET /checkout/success — Redirect dari Midtrans setelah bayar sukses
    // ─────────────────────────────────────────────────────────────────────────

    public function success(Request $request)
    {
        $orderNumber = $request->query('order_id');

        $order = null;
        if ($orderNumber) {
            $order = Order::where('order_number', $orderNumber)
                ->forUser(Auth::id())
                ->with('items.itemable')
                ->first();
        }

        // Jika order tidak ditemukan, ambil order terakhir user
        if (! $order) {
            $order = Order::forUser(Auth::id())
                ->with('items.itemable')
                ->latest()
                ->first();
        }

        // ── Fallback: Jika order masih pending, cek status langsung ke Midtrans ──
        // Ini mengatasi kasus di mana webhook belum diterima
        // (sandbox, jaringan lambat, ngrok, dll.)
        if ($order && $order->status === 'pending') {
            try {
                $status = $this->midtransService->getTransactionStatus($order->order_number);

                if ($status && $this->midtransService->isPaymentSuccess($status)) {
                    // Update order ke paid
                    $order->update([
                        'status'                  => 'paid',
                        'paid_at'                 => now(),
                        'midtrans_transaction_id' => $status['transaction_id'] ?? null,
                    ]);

                    // Trigger fulfillment (enrollment, registrasi, dll.)
                    $this->fulfillOrder($order);

                    Log::info('Checkout success: order verified via API fallback', [
                        'order_id' => $order->id,
                    ]);
                }
            } catch (\Throwable $e) {
                Log::warning('Checkout success: failed to verify via API', [
                    'order_id' => $order?->id,
                    'error'    => $e->getMessage(),
                ]);
            }
        }

        return view('checkout.success', compact('order'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // GET /checkout/failed — Redirect dari Midtrans setelah bayar gagal
    // ─────────────────────────────────────────────────────────────────────────

    public function failed(Request $request)
    {
        $orderNumber = $request->query('order_id');

        $order = null;
        if ($orderNumber) {
            $order = Order::where('order_number', $orderNumber)
                ->forUser(Auth::id())
                ->with('items.itemable')
                ->first();
        }

        return view('checkout.failed', compact('order'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Private helpers
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Fulfill order setelah pembayaran berhasil.
     *
     * Dispatch ke service yang sesuai berdasarkan tipe item + kirim email.
     * Digunakan sebagai fallback saat webhook belum diterima.
     */
    private function fulfillOrder(Order $order): void
    {
        $order->loadMissing('items.itemable', 'user');

        // Increment Flash Sale sold quantity if applicable (Fallback sync)
        foreach ($order->items as $item) {
            if ($item->flash_sale_item_id) {
                $flashItem = \App\Models\FlashSaleItem::find($item->flash_sale_item_id);
                if ($flashItem) {
                    $flashItem->increment('sold_quantity', $item->quantity);
                }
            }
        }

        $itemTypes = $order->items->pluck('itemable_type')->unique();

        foreach ($itemTypes as $type) {
            try {
                match ($type) {
                    Course::class         => app(CourseEnrollmentService::class)->handlePaymentSuccess($order),
                    Bundle::class         => app(BundleEnrollmentService::class)->handlePaymentSuccess($order),
                    Bootcamp::class       => app(BootcampRegistrationService::class)->handlePaymentSuccess($order),
                    Book::class           => app(BookOrderService::class)->handlePaymentSuccess($order),
                    MembershipPlan::class => app(MembershipService::class)->handlePaymentSuccess($order),
                    default               => null,
                };
            } catch (\Throwable $e) {
                Log::warning("Fulfill order: failed for type $type", [
                    'order_id' => $order->id,
                    'error'    => $e->getMessage(),
                ]);
            }
        }

        // Kirim email notifikasi pembayaran
        try {
            \Illuminate\Support\Facades\Mail::to($order->user->email)
                ->send(new OrderPaymentMail($order));
        } catch (\Throwable $e) {
            Log::warning('Fulfill order: email failed', [
                'order_id' => $order->id,
                'error'    => $e->getMessage(),
            ]);
        }
    }

    /**
     * Ambil nama item dari cartable (course, bootcamp, book, membership plan).
     */
    private function getItemName(Cart $cartItem): string
    {
        $item = $cartItem->cartable;

        if (! $item) {
            return 'Item tidak diketahui';
        }

        return $item->title ?? $item->name ?? 'Item #' . $cartItem->cartable_id;
    }

    /**
     * Ambil metadata tambahan per item (misal: billing_cycle untuk membership).
     */
    private function getItemMeta(Cart $cartItem): ?array
    {
        $meta = [];

        // Untuk membership, simpan billing cycle
        if ($cartItem->cartable_type === \App\Models\MembershipPlan::class) {
            $meta['billing_cycle'] = session('membership_billing_cycle', 'monthly');
        }

        // Untuk buku fisik, simpan tipe pembelian
        if ($cartItem->cartable_type === \App\Models\Book::class) {
            $book = $cartItem->cartable;
            if ($book) {
                $meta['purchase_type'] = $book->type;
            }
        }

        // Untuk course dengan variant, simpan info variant
        if ($cartItem->cartable_type === \App\Models\Course::class && $cartItem->course_variant_id) {
            $variant = $cartItem->courseVariant;
            if ($variant) {
                $meta['variant_id']       = $variant->id;
                $meta['delivery_type']    = $variant->delivery_type;
                $meta['variant_label']    = $variant->display_label;
                $meta['schedule_start']   = $variant->schedule_start?->toDateTimeString();
                $meta['location']         = $variant->location;
                $meta['platform']         = $variant->platform;
            }
        }

        return ! empty($meta) ? $meta : null;
    }
}
