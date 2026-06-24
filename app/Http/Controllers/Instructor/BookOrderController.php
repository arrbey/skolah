<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Mail\ShippingUpdateMail;
use App\Models\Book;
use App\Models\BookOrder;
use App\Models\BookOrderHistory;
use App\Services\MinioStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class BookOrderController extends Controller
{
    // Hanya melihat order dari buku milik instructor yang sedang login
    private function instructorBookIds(): array
    {
        return Book::where('instructor_id', auth()->id())->pluck('id')->toArray();
    }

    // ── Index ──────────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $bookIds = $this->instructorBookIds();

        $query = BookOrder::with(['book:id,title,cover_image', 'user:id,name,email', 'order:id,order_number,paid_at'])
            ->whereIn('book_id', $bookIds)
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

        $stats = [
            'all'        => BookOrder::whereIn('book_id', $bookIds)->where('purchase_type', 'physical')->whereHas('order', fn ($q) => $q->where('status', 'paid'))->count(),
            'pending'    => BookOrder::whereIn('book_id', $bookIds)->where('purchase_type', 'physical')->whereHas('order', fn ($q) => $q->where('status', 'paid'))->where('shipping_status', 'pending')->count(),
            'processing' => BookOrder::whereIn('book_id', $bookIds)->where('purchase_type', 'physical')->whereHas('order', fn ($q) => $q->where('status', 'paid'))->where('shipping_status', 'processing')->count(),
            'shipped'    => BookOrder::whereIn('book_id', $bookIds)->where('purchase_type', 'physical')->whereHas('order', fn ($q) => $q->where('status', 'paid'))->where('shipping_status', 'shipped')->count(),
            'delivered'  => BookOrder::whereIn('book_id', $bookIds)->where('purchase_type', 'physical')->whereHas('order', fn ($q) => $q->where('status', 'paid'))->where('shipping_status', 'delivered')->count(),
        ];

        $bookOrders = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        return view('instructor.book-orders.index', compact('bookOrders', 'stats'));
    }

    // ── Show ───────────────────────────────────────────────────────────────────

    public function show(BookOrder $bookOrder)
    {
        // Pastikan buku milik instructor ini
        abort_unless(in_array($bookOrder->book_id, $this->instructorBookIds()), 403);

        $bookOrder->load([
            'book:id,title,slug,cover_image,author,pages,isbn,publisher',
            'user:id,name,email',
            'order:id,order_number,paid_at,total',
            'histories.actor:id,name',
        ]);

        return view('instructor.book-orders.show', compact('bookOrder'));
    }

    // ── Update Status ──────────────────────────────────────────────────────────

    public function updateStatus(Request $request, BookOrder $bookOrder)
    {
        abort_unless(in_array($bookOrder->book_id, $this->instructorBookIds()), 403);

        $request->validate([
            'shipping_status' => 'required|in:pending,processing,shipped,cancelled',
            'tracking_number' => 'nullable|string|max:100',
            'courier'         => 'nullable|in:jne,jnt',
            'note'            => 'nullable|string|max:500',
        ]);

        if ($request->shipping_status === 'shipped') {
            $request->validate([
                'tracking_number' => 'required|string|max:100',
                'courier'         => 'required|in:jne,jnt',
            ]);
        }

        $oldStatus  = $bookOrder->shipping_status;
        $newStatus  = $request->shipping_status;
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

        BookOrderHistory::create([
            'book_order_id'  => $bookOrder->id,
            'actor_id'       => auth()->id(),
            'actor_name'     => auth()->user()->name,
            'status'         => $newStatus,
            'tracking_number'=> $bookOrder->tracking_number,
            'courier'        => $bookOrder->courier,
            'note'           => $request->note,
        ]);

        if ($oldStatus !== $newStatus) {
            Mail::to($bookOrder->user->email)
                ->send(new ShippingUpdateMail($bookOrder->fresh(['book', 'user', 'order']), $request->note ?? ''));
        }

        return redirect()
            ->route('instructor.book-orders.show', $bookOrder)
            ->with('success', 'Status pengiriman berhasil diperbarui.');
    }

    // ── Konfirmasi Terkirim + Upload Foto ──────────────────────────────────────

    public function confirmDelivery(Request $request, BookOrder $bookOrder)
    {
        abort_unless(in_array($bookOrder->book_id, $this->instructorBookIds()), 403);

        $request->validate([
            'delivery_photo' => 'required|image|mimes:jpg,jpeg,png,webp|max:3072',
            'note'           => 'nullable|string|max:500',
        ]);

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

        return redirect()
            ->route('instructor.book-orders.show', $bookOrder)
            ->with('success', 'Status diperbarui ke Terkirim dan email notifikasi dikirim.');
    }
}
