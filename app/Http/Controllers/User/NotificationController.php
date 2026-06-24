<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /** Halaman daftar semua notifikasi */
    public function index(Request $request)
    {
        $notifications = $request->user()
            ->notifications()
            ->latest()
            ->paginate(20);

        // Tandai semua sebagai dibaca saat halaman dibuka
        $request->user()->unreadNotifications()->update(['read_at' => now()]);

        return view('dashboard.notifications', compact('notifications'));
    }

    /** Tandai satu notifikasi sebagai dibaca & redirect ke URL tujuan */
    public function markRead(Request $request, string $id)
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        $url = $notification->data['url'] ?? route('dashboard');
        return redirect($url);
    }

    /** Tandai semua sebagai dibaca (AJAX / redirect) */
    public function markAllRead(Request $request)
    {
        $request->user()->unreadNotifications()->update(['read_at' => now()]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Semua notifikasi telah ditandai dibaca.');
    }

    /** Hapus satu notifikasi */
    public function destroy(Request $request, string $id)
    {
        $request->user()->notifications()->findOrFail($id)->delete();
        return back()->with('success', 'Notifikasi dihapus.');
    }
}
