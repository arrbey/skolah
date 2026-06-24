<?php

namespace App\Services;

use App\Models\Bootcamp;
use App\Models\BootcampRegistration;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BootcampRegistrationService
{
    /**
     * Proses registrasi bootcamp setelah pembayaran sukses.
     *
     * Dipanggil dari:
     * - MidtransWebhookController (Midtrans server callback)
     * - BootcampCheckoutController (redirect sukses, sebagai fallback)
     *
     * @throws \Throwable
     */
    public function handlePaymentSuccess(Order $order): void
    {
        $order->loadMissing(['user', 'items.itemable']);

        DB::transaction(function () use ($order) {

            // Mark order paid
            if ($order->status !== 'paid') {
                $order->update([
                    'status'  => 'paid',
                    'paid_at' => now(),
                ]);
            }

            // Proses setiap order item yang merupakan Bootcamp
            foreach ($order->items as $item) {
                if (! ($item->itemable instanceof Bootcamp)) {
                    continue;
                }

                /** @var Bootcamp $bootcamp */
                $bootcamp = $item->itemable;

                // Cegah duplikat registrasi
                $exists = BootcampRegistration::where('user_id', $order->user_id)
                    ->where('bootcamp_id', $bootcamp->id)
                    ->exists();

                if ($exists) {
                    // Pastikan status sudah paid jika ada
                    BootcampRegistration::where('user_id', $order->user_id)
                        ->where('bootcamp_id', $bootcamp->id)
                        ->update(['payment_status' => 'paid']);
                    continue;
                }

                // Buat tiket dengan kode unik
                $ticketCode = $this->generateTicketCode($bootcamp, $order->user_id);

                BootcampRegistration::create([
                    'user_id'        => $order->user_id,
                    'bootcamp_id'    => $bootcamp->id,
                    'ticket_code'    => $ticketCode,
                    'payment_status' => 'paid',
                    'registered_at'  => now(),
                ]);

                // Increment total_registered dengan DB atomic increment
                $bootcamp->increment('total_registered');

                // Kirim notifikasi in-app registrasi bootcamp
                try {
                    $startDate = \Carbon\Carbon::parse($bootcamp->start_date)
                        ->locale('id')->translatedFormat('d F Y');

                    send_notification(
                        user: $order->user,
                        type: 'bootcamp',
                        title: '🚀 Pendaftaran Bootcamp Berhasil!',
                        message: "Kamu berhasil terdaftar di bootcamp \"{$bootcamp->title}\" yang dimulai pada {$startDate}. Kode tiket: {$ticketCode}.",
                        url: route('dashboard.bootcamps'),
                    );
                } catch (\Throwable $e) {
                    Log::warning('Bootcamp registration in-app notification failed', [
                        'user_id'     => $order->user_id,
                        'bootcamp_id' => $bootcamp->id,
                        'error'       => $e->getMessage(),
                    ]);
                }

                Log::info('Bootcamp registration created', [
                    'user_id'     => $order->user_id,
                    'bootcamp_id' => $bootcamp->id,
                    'ticket_code' => $ticketCode,
                    'order'       => $order->order_number,
                ]);
            }
        });
    }

    /**
     * Proses order pending menjadi failed.
     */
    public function handlePaymentFailed(Order $order): void
    {
        if (in_array($order->status, ['pending', 'failed'])) {
            $order->update(['status' => 'failed']);
        }

        // Tandai pending registrations sebagai failed
        $order->loadMissing(['items.itemable']);

        foreach ($order->items as $item) {
            if (! ($item->itemable instanceof Bootcamp)) {
                continue;
            }

            BootcampRegistration::where('user_id', $order->user_id)
                ->where('bootcamp_id', $item->itemable->id)
                ->where('payment_status', 'pending')
                ->update(['payment_status' => 'failed']);
        }
    }

    /**
     * Buat pending registration saat checkout dimulai
     * (sebelum payment selesai — untuk menahan slot sementara).
     */
    public function createPendingRegistration(Bootcamp $bootcamp, int $userId): BootcampRegistration
    {
        // Hapus pending lama jika ada
        BootcampRegistration::where('user_id', $userId)
            ->where('bootcamp_id', $bootcamp->id)
            ->where('payment_status', 'pending')
            ->delete();

        return BootcampRegistration::create([
            'user_id'        => $userId,
            'bootcamp_id'    => $bootcamp->id,
            'ticket_code'    => $this->generateTicketCode($bootcamp, $userId),
            'payment_status' => 'pending',
            'registered_at'  => now(),
        ]);
    }

    /**
     * Generate ticket code unik:
     * Format: SKLT-{BOOTCAMP_ID padded 4}-{USER_ID padded 4}-{RANDOM 6 HEX}
     *
     * Contoh: SKLT-0001-0042-A3F9E1
     */
    protected function generateTicketCode(Bootcamp $bootcamp, int $userId): string
    {
        do {
            $code = 'SKLT-'
                . str_pad($bootcamp->id, 4, '0', STR_PAD_LEFT)
                . '-'
                . str_pad($userId, 4, '0', STR_PAD_LEFT)
                . '-'
                . strtoupper(substr(md5(uniqid((string) rand(), true)), 0, 6));

        } while (BootcampRegistration::where('ticket_code', $code)->exists());

        return $code;
    }
}
