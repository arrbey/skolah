<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index($receiverId = null)
    {
        $users = User::role(['admin', 'instructor', 'user'])
            ->where('id', '!=', Auth::id())
            ->get();

        $activeChat = null;
        $messages = [];

        if ($receiverId) {
            $activeChat = User::findOrFail($receiverId);
            $messages = Message::where(function($q) use ($receiverId) {
                $q->where('sender_id', Auth::id())->where('receiver_id', $receiverId);
            })->orWhere(function($q) use ($receiverId) {
                $q->where('sender_id', $receiverId)->where('receiver_id', Auth::id());
            })->orderBy('created_at', 'asc')->get();
        }

        return view('pages.chat.index', compact('users', 'activeChat', 'messages'));
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message'     => 'nullable|string|max:5000',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:20480', // Max 20MB, whitelist MIME
        ]);

        // Cegah self-messaging
        if ((int) $request->receiver_id === (int) Auth::id()) {
            return response()->json(['status' => 'error', 'message' => 'Tidak bisa mengirim pesan ke diri sendiri.'], 422);
        }

        if (!$request->message && !$request->hasFile('image')) {
            return response()->json(['status' => 'error', 'message' => 'Pesan atau gambar harus diisi.'], 422);
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('chat', 'public');
        }

        try {
            $message = Message::create([
                'sender_id' => Auth::id(),
                'receiver_id' => $request->receiver_id,
                'message' => $request->message,
                'image' => $imagePath
            ]);

            // Broadcast event realtime
            broadcast(new MessageSent($message));

            // Kirim notifikasi in-app ke penerima
            send_notification(
                $message->receiver, 
                'info', 
                'Pesan Baru', 
                Auth::user()->name . ' mengirimkan pesan baru kepada Anda.',
                route('dashboard.chat', Auth::id())
            );

            return response()->json([
                'status' => 'success',
                'message' => $message->load('sender')
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Chat Error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'trace'   => $e->getTraceAsString(),
            ]);
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal mengirim pesan. Silakan coba lagi.',
            ], 500);
        }
    }

    public function getApiUsers()
    {
        $users = User::role(['admin', 'instructor', 'user'])
            ->where('id', '!=', Auth::id())
            ->get()
            ->map(function($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'avatar' => avatarUrl($user),
                    'role' => $user->roles->first()->name ?? 'User'
                ];
            });

        return response()->json($users);
    }

    public function getApiMessages($receiverId)
    {
        $messages = Message::where(function($q) use ($receiverId) {
            $q->where('sender_id', Auth::id())->where('receiver_id', $receiverId);
        })->orWhere(function($q) use ($receiverId) {
            $q->where('sender_id', $receiverId)->where('receiver_id', Auth::id());
        })->orderBy('created_at', 'asc')->get();

        return response()->json($messages);
    }
}
