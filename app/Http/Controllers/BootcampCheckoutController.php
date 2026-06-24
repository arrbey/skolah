<?php

namespace App\Http\Controllers;

use App\Http\Requests\BootcampCheckoutRequest;
use App\Models\Bootcamp;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\BootcampRegistrationService;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BootcampCheckoutController extends Controller
{
    public function __construct(
        protected MidtransService             $midtrans,
        protected BootcampRegistrationService $registrationService,
    ) {}

    // ─────────────────────────────────────────────────────────────────────────
    // PROCESS CHECKOUT — buat order + snap token
    // POST /bootcamp/checkout/process
    // ─────────────────────────────────────────────────────────────────────────

    public function process(BootcampCheckoutRequest $request)
    {
        $request->validate([
            'bootcamp_id' => ['required', 'integer', 'exists:bootcamps,id'],
        ]);

        /** @var \App\Models\User $user */
        $user     = $request->user();
        $bootcamp = Bootcamp::findOrFail($request->bootcamp_id);

        // Guard: Bootcamp bisa didaftar?
        if ($bootcamp->is_full) {
            return back()->with('error', 'Maaf, pendaftaran bootcamp ini sudah penuh.');
        }
        if ($bootcamp->status === 'completed') {
            return back()->with('error', 'Bootcamp ini sudah selesai dan tidak menerima pendaftaran baru.');
        }

        // Guard: Sudah terdaftar?
        $alreadyPaid = \App\Models\BootcampRegistration::where('user_id', $user->id)
            ->where('bootcamp_id', $bootcamp->id)
            ->where('payment_status', 'paid')
            ->exists();

        if ($alreadyPaid) {
            return redirect()->route('bootcamps.show', $bootcamp->slug)
                ->with('info', 'Kamu sudah terdaftar di bootcamp ini.');
        }

        // ── Handle GRATIS — langsung daftar tanpa payment ─────────────────
        if ($bootcamp->effective_price === 0) {
            return $this->handleFreeRegistration($user, $bootcamp);
        }

        // ── Buat Order ──────────────────────────────────────────────────────
        $order = DB::transaction(function () use ($user, $bootcamp) {

            $price = $bootcamp->effective_price;

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
                'itemable_type' => Bootcamp::class,
                'itemable_id'   => $bootcamp->id,
                'item_name'     => $bootcamp->title,
                'price'         => $price,
                'quantity'      => 1,
            ]);

            // Buat pending registration (untuk menjaga konsistensi)
            $this->registrationService->createPendingRegistration($bootcamp, $user->id);

            return $order;
        });

        // ── Ambil Snap Redirect URL dari Midtrans ─────────────────────────
        try {
            $redirectUrl = $this->midtrans->createSnapToken($order);
            
            // REDIRECT MODE: Langsung arahkan ke halaman pembayaran aman Midtrans
            return redirect()->away($redirectUrl);
        } catch (\Exception $e) {
            Log::error('Midtrans redirect generation failed for bootcamp', [
                'order_id' => $order->id,
                'error'    => $e->getMessage(),
            ]);

            $order->update(['status' => 'failed']);
            return back()->with('error', 'Gagal memproses pembayaran. Silakan coba lagi.');
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // MIDTRANS WEBHOOK — verifikasi & proses payment sukses
    // POST /bootcamp/webhook (dikecualikan dari CSRF)
    // ─────────────────────────────────────────────────────────────────────────

    public function webhook(Request $request)
    {
        $payload = $request->all();

        // Verifikasi signature
        if (! $this->midtrans->verifySignature($payload)) {
            Log::warning('Bootcamp webhook: invalid signature', ['order_id' => $payload['order_id'] ?? null]);
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        // Cari order berdasarkan order_number
        $order = Order::where('order_number', $payload['order_id'] ?? '')->first();

        if (! $order) {
            Log::warning('Bootcamp webhook: order not found', ['order_id' => $payload['order_id'] ?? null]);
            return response()->json(['message' => 'Order not found'], 404);
        }

        // Cek apakah ada item bootcamp di order ini
        $hasBootcampItem = $order->items()
            ->where('itemable_type', Bootcamp::class)
            ->exists();

        if (! $hasBootcampItem) {
            // Bukan order bootcamp, biarkan webhook lain yang handle
            return response()->json(['message' => 'Not a bootcamp order'], 200);
        }

        try {
            if ($this->midtrans->isPaymentSuccess($payload)) {
                $order->update([
                    'status'                   => 'paid',
                    'paid_at'                  => now(),
                    'midtrans_transaction_id'  => $payload['transaction_id'] ?? null,
                ]);
                $this->registrationService->handlePaymentSuccess($order);

            } elseif ($this->midtrans->isPaymentFailed($payload)) {
                $this->registrationService->handlePaymentFailed($order);
            }

        } catch (\Throwable $e) {
            Log::error('Bootcamp webhook processing failed', [
                'order_id' => $order->id,
                'error'    => $e->getMessage(),
                'trace'    => $e->getTraceAsString(),
            ]);
            return response()->json(['message' => 'Processing error'], 500);
        }

        return response()->json(['message' => 'OK'], 200);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // SUCCESS REDIRECT dari Midtrans
    // GET /bootcamp/checkout/success?order_id=SKL-...
    // ─────────────────────────────────────────────────────────────────────────

    public function success(Request $request)
    {
        $order = Order::where('order_number', $request->order_id)->first();

        // Fallback: proses jika webhook belum terpanggil
        if ($order && $order->status === 'pending') {
            $order->loadMissing('items');
            $hasBootcamp = $order->items()->where('itemable_type', Bootcamp::class)->exists();
            if ($hasBootcamp) {
                $order->update(['status' => 'paid', 'paid_at' => now()]);
                $this->registrationService->handlePaymentSuccess($order);
            }
        }

        return redirect()->route('dashboard.my-bootcamps')
            ->with('success', 'Pembayaran berhasil! Tiket bootcamp kamu sudah aktif.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // FAILED REDIRECT
    // GET /bootcamp/checkout/failed?order_id=SKL-...
    // ─────────────────────────────────────────────────────────────────────────

    public function failed(Request $request)
    {
        $order = Order::where('order_number', $request->order_id)->first();

        if ($order && $order->status === 'pending') {
            $this->registrationService->handlePaymentFailed($order);
        }

        return redirect()->route('bootcamps.index')
            ->with('error', 'Pembayaran dibatalkan atau gagal. Silakan coba mendaftar kembali.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PRIVATE: Handle free registration (no payment)
    // ─────────────────────────────────────────────────────────────────────────

    protected function handleFreeRegistration($user, Bootcamp $bootcamp)
    {
        try {
            DB::transaction(function () use ($user, $bootcamp) {
                $ticketCode = 'SKLT-'
                    . str_pad($bootcamp->id, 4, '0', STR_PAD_LEFT)
                    . '-'
                    . str_pad($user->id, 4, '0', STR_PAD_LEFT)
                    . '-'
                    . strtoupper(substr(md5(uniqid()), 0, 6));

                \App\Models\BootcampRegistration::create([
                    'user_id'        => $user->id,
                    'bootcamp_id'    => $bootcamp->id,
                    'ticket_code'    => $ticketCode,
                    'payment_status' => 'paid',
                    'registered_at'  => now(),
                ]);

                $bootcamp->increment('total_registered');
            });

            return redirect()->route('bootcamps.show', $bootcamp->slug)
                ->with('success', 'Berhasil mendaftar! Tiket gratis kamu sudah aktif.');

        } catch (\Throwable $e) {
            Log::error('Free bootcamp registration failed', [
                'bootcamp_id' => $bootcamp->id,
                'user_id'     => $user->id,
                'error'       => $e->getMessage(),
            ]);

            return back()->with('error', 'Gagal mendaftar. Silakan coba kembali.');
        }
    }
}
