<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ConfirmDeliveryRequest;
use App\Http\Requests\Admin\UpdateBookOrderStatusRequest;
use App\Mail\ShippingUpdateMail;
use App\Models\BookOrder;
use App\Models\BookOrderHistory;
use App\Services\MinioStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class BookOrderController extends Controller
{
    // ── Index ──────────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $query = BookOrder::with(['book:id,title,cover_image', 'user:id,name,email', 'order:id,order_number,paid_at'])
            ->where('purchase_type', 'physical')
            ->whereHas('order', fn ($q) => $q->where('status', 'paid'));

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"))
                  ->orWhereHas('book', fn ($b) => $b->where('title', 'like', "%{$search}%"))
                  ->orWhere('tracking_number', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('shipping_status', $status);
        }

        if ($courier = $request->input('courier')) {
            $query->where('courier', $courier);
        }

        $stats = [
            'all'        => BookOrder::where('purchase_type', 'physical')->whereHas('order', fn ($q) => $q->where('status', 'paid'))->count(),
            'pending'    => BookOrder::where('purchase_type', 'physical')->whereHas('order', fn ($q) => $q->where('status', 'paid'))->where('shipping_status', 'pending')->count(),
            'processing' => BookOrder::where('purchase_type', 'physical')->whereHas('order', fn ($q) => $q->where('status', 'paid'))->where('shipping_status', 'processing')->count(),
            'shipped'    => BookOrder::where('purchase_type', 'physical')->whereHas('order', fn ($q) => $q->where('status', 'paid'))->where('shipping_status', 'shipped')->count(),
            'delivered'  => BookOrder::where('purchase_type', 'physical')->whereHas('order', fn ($q) => $q->where('status', 'paid'))->where('shipping_status', 'delivered')->count(),
        ];

        $bookOrders = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        return view('admin.book-orders.index', compact('bookOrders', 'stats'));
    }

    // ── Show ───────────────────────────────────────────────────────────────────

    public function show(BookOrder $bookOrder)
    {
        $bookOrder->load([
            'book:id,title,slug,cover_image,author,pages,isbn,publisher',
            'user:id,name,email',
            'order:id,order_number,paid_at,total',
            'histories.actor:id,name',
        ]);

        return view('admin.book-orders.show', compact('bookOrder'));
    }

    // ── Update Status (Proses / Kirim — dengan resi & kurir) ──────────────────

    public function updateStatus(UpdateBookOrderStatusRequest $request, BookOrder $bookOrder)
    {
        $oldStatus = $bookOrder->shipping_status;
        $newStatus = $request->shipping_status;

        $updateData = ['shipping_status' => $newStatus];

        if ($request->filled('tracking_number')) {
            $updateData['tracking_number'] = $request->tracking_number;
        }
        if ($request->filled('courier')) {
            $updateData['courier'] = $request->courier;
        }
        if ($newStatus === 'shipped' && ! $bookOrder->shipped_at) {
            $updateData['shipped_at'] = now();
        }

        $bookOrder->update($updateData);

        // Catat history
        BookOrderHistory::create([
            'book_order_id'  => $bookOrder->id,
            'actor_id'       => auth()->id(),
            'actor_name'     => auth()->user()->name,
            'status'         => $newStatus,
            'tracking_number'=> $bookOrder->tracking_number,
            'courier'        => $bookOrder->courier,
            'note'           => $request->note,
        ]);

        // Kirim email notifikasi ke user jika status berubah
        if ($oldStatus !== $newStatus) {
            Mail::to($bookOrder->user->email)
                ->send(new ShippingUpdateMail($bookOrder->fresh(['book', 'user', 'order']), $request->note ?? ''));

            // Notifikasi in-app pengiriman buku
            try {
                $bookOrder->loadMissing(['user', 'book']);
                $statusLabel = match ($newStatus) {
                    'processing' => 'sedang diproses',
                    'shipped'    => 'sedang dalam pengiriman',
                    'delivered'  => 'telah sampai',
                    'cancelled'  => 'dibatalkan',
                    default      => $newStatus,
                };

                $icon = match ($newStatus) {
                    'processing' => '📦',
                    'shipped'    => '🚚',
                    'delivered'  => '✅',
                    'cancelled'  => '❌',
                    default      => '📬',
                };

                $trackingInfo = $newStatus === 'shipped' && $bookOrder->tracking_number
                    ? " No. resi: {$bookOrder->tracking_number}."
                    : '';

                send_notification(
                    user: $bookOrder->user,
                    type: $newStatus === 'cancelled' ? 'error' : 'info',
                    title: "{$icon} Buku {$statusLabel}",
                    message: "Pesanan buku \"{$bookOrder->book->title}\" {$statusLabel}.{$trackingInfo}",
                    url: route('dashboard.books'),
                );
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Book shipping in-app notification failed', [
                    'book_order_id' => $bookOrder->id,
                    'error'         => $e->getMessage(),
                ]);
            }
        }

        return redirect()
            ->route('admin.book-orders.show', $bookOrder)
            ->with('success', 'Status pengiriman berhasil diperbarui dan email notifikasi telah dikirim.');
    }

    // ── Konfirmasi Terkirim + Upload Foto Bukti ────────────────────────────────

    public function confirmDelivery(ConfirmDeliveryRequest $request, BookOrder $bookOrder)
    {
        // Upload foto bukti pengiriman ke MinIO (PUBLIC URL)
        $photoUrl = app(MinioStorageService::class)
            ->uploadDeliveryPhoto($request->file('delivery_photo'), $bookOrder->id);

        $bookOrder->update([
            'shipping_status' => 'delivered',
            'delivery_photo'  => $photoUrl,
            'delivered_at'    => now(),
        ]);

        BookOrderHistory::create([
            'book_order_id'  => $bookOrder->id,
            'actor_id'       => auth()->id(),
            'actor_name'     => auth()->user()->name,
            'status'         => 'delivered',
            'tracking_number'=> $bookOrder->tracking_number,
            'courier'        => $bookOrder->courier,
            'delivery_photo' => $photoUrl,
            'note'           => $request->note ?? 'Paket telah diterima oleh pembeli.',
        ]);

        Mail::to($bookOrder->user->email)
            ->send(new ShippingUpdateMail($bookOrder->fresh(['book', 'user', 'order']), $request->note ?? 'Paket telah diterima!'));

        // Notifikasi in-app konfirmasi terkirim
        try {
            $bookOrder->loadMissing(['user', 'book']);
            send_notification(
                user: $bookOrder->user,
                type: 'success',
                title: '✅ Buku Telah Diterima!',
                message: "Buku \"{$bookOrder->book->title}\" telah berhasil dikirim dan diterima. Selamat membaca!",
                url: route('dashboard.books'),
            );
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Book delivered in-app notification failed', [
                'book_order_id' => $bookOrder->id,
                'error'         => $e->getMessage(),
            ]);
        }

        return redirect()
            ->route('admin.book-orders.show', $bookOrder)
            ->with('success', 'Status pengiriman diperbarui ke Terkirim dan email konfirmasi telah dikirim.');
    }
}
