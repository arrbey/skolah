<?php

namespace App\Console\Commands;

use App\Mail\PaymentReminderMail;
use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendPaymentReminders extends Command
{
    protected $signature = 'orders:send-payment-reminders';

    protected $description = 'Kirim email pengingat pembayaran untuk order yang akan kedaluwarsa dalam 3 jam';

    public function handle(): int
    {
        $orders = Order::payableAndExpiringSoon(3)
            ->with('user')
            ->get();

        if ($orders->isEmpty()) {
            $this->info('Tidak ada order yang perlu diingatkan.');
            return self::SUCCESS;
        }

        $count = 0;
        foreach ($orders as $order) {
            Mail::to($order->user->email)->send(new PaymentReminderMail($order));

            $order->update(['payment_reminder_sent' => true]);
            $count++;

            Log::info('Payment reminder sent', [
                'order_id'     => $order->id,
                'order_number' => $order->order_number,
                'user_email'   => $order->user->email,
                'expires_at'   => $order->payment_expires_at,
            ]);
        }

        $this->info("Berhasil mengirim {$count} email pengingat pembayaran.");

        return self::SUCCESS;
    }
}
