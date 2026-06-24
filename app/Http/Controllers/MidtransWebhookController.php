<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Bootcamp;
use App\Models\Course;
use App\Models\Bundle;
use App\Models\MembershipPlan;
use App\Models\Order;
use App\Mail\OrderPaymentMail;
use App\Services\BookOrderService;
use App\Services\BootcampRegistrationService;
use App\Services\CourseEnrollmentService;
use App\Services\BundleEnrollmentService;
use App\Services\MembershipService;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Unified Midtrans Webhook Controller.
 *
 * Menangani semua webhook Midtrans untuk berbagai tipe order
 * (Bootcamp, Book, Course, Membership, dll.)
 * dan meneruskan ke service yang sesuai.
 */
class MidtransWebhookController extends Controller
{
    public function __construct(
        protected MidtransService             $midtrans,
        protected BootcampRegistrationService $bootcampService,
        protected BookOrderService            $bookOrderService,
        protected MembershipService           $membershipService,
        protected CourseEnrollmentService     $courseService,
        protected BundleEnrollmentService     $bundleService,
    ) {}

    public function __invoke(Request $request)
    {
        if (! $request->isMethod('post')) {
            abort(404);
        }

        $payload = $request->all();

        // ── 1. Log payload minimal; jangan simpan raw body/signature ──────
        Log::channel('webhook')->info('Midtrans webhook received', [
            'ip'                 => $request->ip(),
            'order_id'           => $payload['order_id'] ?? null,
            'transaction_status' => $payload['transaction_status'] ?? null,
            'fraud_status'       => $payload['fraud_status'] ?? null,
            'payment_type'       => $payload['payment_type'] ?? null,
            'status_code'        => $payload['status_code'] ?? null,
        ]);

        // ── 2. Verifikasi signature (hash_equals = timing-attack safe) ───
        if (! $this->midtrans->verifySignature($payload)) {
            Log::channel('webhook')->warning('Midtrans webhook: INVALID SIGNATURE', [
                'ip'       => $request->ip(),
                'order_id' => $payload['order_id'] ?? null,
            ]);
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        // ── 3. Cari order ────────────────────────────────────────────────
        $midtransOrderId = $payload['order_id'] ?? '';

        $order = Order::where('midtrans_order_id', $midtransOrderId)->first()
            ?? Order::where('order_number', $midtransOrderId)->first();

        // Fallback: strip suffix timestamp (format: SKL-YYYYMMDD-XXXX-HHmmss)
        if (! $order && preg_match('/^(SKL-\d{8}-\d{4})-\d{6}$/', $midtransOrderId, $m)) {
            $order = Order::where('order_number', $m[1])->first();
        }

        if (! $order) {
            Log::channel('webhook')->warning('Midtrans webhook: ORDER NOT FOUND', [
                'order_id' => $midtransOrderId,
            ]);
            return response()->json(['message' => 'Order not found'], 404);
        }

        // ── 4. Cegah double processing — order sudah paid/refunded ───────
        if (in_array($order->status, ['paid', 'refunded'])) {
            Log::channel('webhook')->info('Midtrans webhook: already processed (skip)', [
                'order_id' => $order->id,
                'status'   => $order->status,
            ]);
            return response()->json(['message' => 'Already processed'], 200);
        }

        // ── 5. Verifikasi jumlah TIDAK dimanipulasi ─────────────────────
        $grossAmount = (int) ($payload['gross_amount'] ?? 0);
        if ($grossAmount !== (int) $order->total) {
            Log::channel('webhook')->error('Midtrans webhook: AMOUNT MISMATCH', [
                'order_id' => $order->id,
                'expected' => (int) $order->total,
                'received' => $grossAmount,
                'ip'       => $request->ip(),
            ]);
            return response()->json(['message' => 'Amount mismatch'], 400);
        }

        // ── 6. Tentukan status pembayaran ────────────────────────────────
        $transactionStatus = $payload['transaction_status'] ?? '';
        $isSuccess = $this->midtrans->isPaymentSuccess($payload);
        $isFailed  = $this->midtrans->isPaymentFailed($payload);
        $isRefund  = in_array($transactionStatus, ['refund', 'partial_refund']);

        if (! $isSuccess && ! $isFailed && ! $isRefund) {
            Log::channel('webhook')->info('Midtrans webhook: pending/other status', [
                'order_id'           => $order->id,
                'transaction_status' => $transactionStatus,
            ]);
            return response()->json(['message' => 'OK'], 200);
        }

        // ── 7. Proses dalam DB transaction untuk atomicity ───────────────
        try {
            DB::transaction(function () use ($order, $payload, $isSuccess, $isFailed, $isRefund) {
                if ($isSuccess) {
                    $this->handleSuccess($order, $payload);
                } elseif ($isRefund) {
                    $this->handleRefund($order, $payload);
                } else {
                    $this->handleFailed($order, $payload);
                }
            });
        } catch (\Throwable $e) {
            Log::channel('webhook')->error('Midtrans webhook PROCESSING FAILED', [
                'order_id' => $order->id,
                'error'    => $e->getMessage(),
                'trace'    => substr($e->getTraceAsString(), 0, 1000),
            ]);
            
            // Tetap kembalikan 200 agar Midtrans berhenti mencoba (karena kita sudah mencatat errornya)
            return response()->json(['message' => 'Processing error logged'], 200);
        }

        return response()->json(['message' => 'OK'], 200);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // SUCCESS handler — dispatch ke service yang sesuai
    // ─────────────────────────────────────────────────────────────────────────

    protected function handleSuccess(Order $order, array $payload): void
    {
        // Update order
        $order->update([
            'status'                  => 'paid',
            'paid_at'                 => now(),
            'midtrans_transaction_id' => $payload['transaction_id'] ?? null,
        ]);

        $order->loadMissing('items');

        // Increment Flash Sale sold quantity if applicable
        foreach ($order->items as $item) {
            if ($item->flash_sale_item_id) {
                $flashItem = \App\Models\FlashSaleItem::find($item->flash_sale_item_id);
                if ($flashItem) {
                    $flashItem->increment('sold_quantity', $item->quantity);
                }
            }
        }

        // Cek tipe item dalam order & dispatch ke service
        $itemTypes = $order->items->pluck('itemable_type')->unique();

        foreach ($itemTypes as $type) {
            match ($type) {
                Course::class         => $this->courseService->handlePaymentSuccess($order),
                Bundle::class         => $this->bundleService->handlePaymentSuccess($order),
                Bootcamp::class       => $this->bootcampService->handlePaymentSuccess($order),
                Book::class           => $this->bookOrderService->handlePaymentSuccess($order),
                MembershipPlan::class => $this->membershipService->handlePaymentSuccess($order),
                default => Log::channel('webhook')->info('Midtrans webhook: no handler for item type', ['type' => $type]),
            };
        }

        // ── Kirim email notifikasi pembayaran ─────────────────────────
        try {
            Mail::to($order->user->email)
                ->send(new OrderPaymentMail($order));
        } catch (\Throwable $e) {
            Log::channel('webhook')->warning('Failed to queue payment email', [
                'order_id' => $order->id,
                'error'    => $e->getMessage(),
            ]);
        }

        // ── Kirim notifikasi in-app ────────────────────────────────────
        try {
            $order->loadMissing('user');
            send_notification(
                user: $order->user,
                type: 'order',
                title: '✅ Pembayaran Berhasil!',
                message: "Pesanan #{$order->order_number} senilai " . rupiah((int) $order->total) . " telah dikonfirmasi. Selamat belajar!",
                url: route('dashboard.orders'),
            );
        } catch (\Throwable $e) {
            Log::channel('webhook')->warning('Failed to send in-app notification', [
                'order_id' => $order->id,
                'error'    => $e->getMessage(),
            ]);
        }

        Log::channel('webhook')->info('Midtrans webhook: payment SUCCESS processed', [
            'order_id'   => $order->id,
            'item_types' => $itemTypes->toArray(),
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // FAILED handler
    // ─────────────────────────────────────────────────────────────────────────

    protected function handleFailed(Order $order, array $payload): void
    {
        $order->loadMissing('items');
        $itemTypes = $order->items->pluck('itemable_type')->unique();

        foreach ($itemTypes as $type) {
            match ($type) {
                Course::class         => $this->courseService->handlePaymentFailed($order),
                Bundle::class         => null, // No specific cleanup for bundle
                Bootcamp::class       => $this->bootcampService->handlePaymentFailed($order),
                Book::class           => $this->bookOrderService->handlePaymentFailed($order),
                MembershipPlan::class => $this->membershipService->handlePaymentFailed($order),
                default => null,
            };
        }

        // Pastikan order sudah failed
        if ($order->status !== 'failed') {
            $order->update(['status' => 'failed']);
        }

        // ── Kirim notifikasi in-app pembayaran gagal ───────────────────
        try {
            $order->loadMissing('user');
            send_notification(
                user: $order->user,
                type: 'error',
                title: '❌ Pembayaran Gagal',
                message: "Pembayaran untuk pesanan #{$order->order_number} senilai " . rupiah((int) $order->total) . " gagal diproses. Silakan coba lagi.",
                url: route('dashboard.orders'),
            );
        } catch (\Throwable $e) {
            Log::channel('webhook')->warning('Failed to send payment-failed in-app notification', [
                'order_id' => $order->id,
                'error'    => $e->getMessage(),
            ]);
        }

        Log::channel('webhook')->info('Midtrans webhook: payment FAILED processed', [
            'order_id' => $order->id,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // REFUND handler — Midtrans mengirim refund/partial_refund
    // ─────────────────────────────────────────────────────────────────────────

    protected function handleRefund(Order $order, array $payload): void
    {
        $order->update([
            'status' => 'refunded',
        ]);

        try {
            $order->loadMissing('user');
            send_notification(
                user: $order->user,
                type: 'info',
                title: '↩️ Refund Diproses',
                message: "Pesanan #{$order->order_number} senilai " . rupiah((int) $order->total) . " telah di-refund.",
                url: route('dashboard.orders'),
            );
        } catch (\Throwable $e) {
            Log::channel('webhook')->warning('Failed to send refund notification', [
                'order_id' => $order->id,
                'error'    => $e->getMessage(),
            ]);
        }

        Log::channel('webhook')->info('Midtrans webhook: REFUND processed', [
            'order_id'           => $order->id,
            'transaction_status' => $payload['transaction_status'] ?? null,
        ]);
    }
}
