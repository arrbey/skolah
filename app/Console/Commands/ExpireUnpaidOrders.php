<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ExpireUnpaidOrders extends Command
{
    protected $signature = 'orders:expire-unpaid';

    protected $description = 'Otomatis gagalkan order pending yang melewati batas waktu pembayaran (24 jam)';

    public function handle(): int
    {
        $expired = Order::expiredPayment()->get();

        if ($expired->isEmpty()) {
            $this->info('Tidak ada order kedaluwarsa.');
            return self::SUCCESS;
        }

        $count = 0;
        foreach ($expired as $order) {
            $order->update(['status' => 'failed']);
            $count++;

            Log::info('Order auto-expired', [
                'order_id'     => $order->id,
                'order_number' => $order->order_number,
                'expired_at'   => $order->payment_expires_at,
            ]);
        }

        $this->info("Berhasil menggagalkan {$count} order kedaluwarsa.");

        return self::SUCCESS;
    }
}
