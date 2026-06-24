<?php

namespace App\Services;

use App\Models\Book;
use App\Models\BookOrder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BookOrderService
{
    /**
     * Handle pembayaran buku berhasil.
     * - Buku digital → aktifkan akses download
     * - Buku fisik  → buat BookOrder dengan shipping_status pending
     */
    public function handlePaymentSuccess(Order $order): void
    {
        $order->loadMissing('items');

        DB::transaction(function () use ($order) {
            // Update status order
            if ($order->status !== 'paid') {
                $order->update([
                    'status'  => 'paid',
                    'paid_at' => now(),
                ]);
            }

            foreach ($order->items as $item) {
                if ($item->itemable_type !== Book::class) {
                    continue;
                }

                $book = Book::find($item->itemable_id);
                if (! $book) continue;

                // Cek duplikasi
                $exists = BookOrder::where('user_id', $order->user_id)
                    ->where('book_id', $book->id)
                    ->whereHas('order', fn($q) => $q->where('status', 'paid'))
                    ->exists();

                if ($exists) {
                    Log::info('BookOrder already exists', [
                        'user_id' => $order->user_id,
                        'book_id' => $book->id,
                    ]);
                    continue;
                }

                // Tentukan purchase_type dari metadata order item atau default dari book
                $purchaseType = $item->meta['purchase_type'] ?? $book->type;

                // Buat BookOrder
                $bookOrder = BookOrder::create([
                    'user_id'          => $order->user_id,
                    'book_id'          => $book->id,
                    'order_id'         => $order->id,
                    'quantity'         => $item->quantity,
                    'price'            => $item->price,
                    'purchase_type'    => $purchaseType,
                    'shipping_address' => $item->meta['shipping_address'] ?? null,
                    'shipping_status'  => in_array($purchaseType, ['physical', 'both']) ? 'pending' : null,
                    'courier'          => $item->meta['courier'] ?? null,
                ]);

                // Kurangi stok untuk buku fisik
                if (in_array($purchaseType, ['physical', 'both']) && $book->stock > 0) {
                    $book->decrement('stock', $item->quantity);
                }

                Log::info('BookOrder created after payment success', [
                    'book_order_id' => $bookOrder->id,
                    'user_id'       => $order->user_id,
                    'book_id'       => $book->id,
                    'type'          => $purchaseType,
                ]);

                // Kirim notifikasi in-app buku berhasil dibeli
                try {
                    $user = $order->user ?? User::find($order->user_id);
                    if ($user) {
                        if (in_array($purchaseType, ['digital', 'both'])) {
                            send_notification(
                                user: $user,
                                type: 'info',
                                title: '📚 Buku Digital Siap Diunduh!',
                                message: "Buku \"{$book->title}\" sudah bisa kamu unduh. Nikmati bacaan digitalmu!",
                                url: route('dashboard.books'),
                            );
                        } else {
                            send_notification(
                                user: $user,
                                type: 'info',
                                title: '📦 Pesanan Buku Dikonfirmasi',
                                message: "Buku \"{$book->title}\" akan segera diproses untuk pengiriman. Kami akan menginformasikan nomor resi.",
                                url: route('dashboard.books'),
                            );
                        }
                    }
                } catch (\Throwable $e) {
                    Log::warning('Book order in-app notification failed', [
                        'user_id' => $order->user_id,
                        'book_id' => $book->id,
                        'error'   => $e->getMessage(),
                    ]);
                }
            }
        });
    }

    /**
     * Handle pembayaran gagal.
     */
    public function handlePaymentFailed(Order $order): void
    {
        $order->update(['status' => 'failed']);

        Log::info('Book order payment failed', [
            'order_id' => $order->id,
        ]);
    }

    /**
     * Cek apakah user sudah membeli buku digital ini → boleh download.
     */
    public function canDownload(int $userId, Book $book): bool
    {
        if (! $book->is_digital) {
            return false;
        }

        // Cek via order_items (polymorphic)
        $hasPaid = OrderItem::whereHas('order', function ($q) use ($userId) {
                $q->where('user_id', $userId)->where('status', 'paid');
            })
            ->where('itemable_type', Book::class)
            ->where('itemable_id', $book->id)
            ->exists();

        return $hasPaid;
    }

    /**
     * Generate path file untuk download buku digital.
     */
    public function getDownloadPath(Book $book): ?string
    {
        if (! $book->file_path) {
            return null;
        }

        // file_path disimpan relatif terhadap storage/app
        if (Storage::exists($book->file_path)) {
            return Storage::path($book->file_path);
        }

        return null;
    }
}
