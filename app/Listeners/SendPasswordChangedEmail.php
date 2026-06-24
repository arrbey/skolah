<?php

namespace App\Listeners;

use App\Mail\PasswordChangedMail;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendPasswordChangedEmail
{
    /**
     * Handle the event.
     *
     * Kirim email + notifikasi in-app setelah password berhasil direset.
     */
    public function handle(PasswordReset $event): void
    {
        /** @var User $user */
        $user = $event->user;

        Mail::to($user->email)->send(new PasswordChangedMail($user));

        try {
            send_notification(
                user: $user,
                type: 'warning',
                title: '🔐 Password Berhasil Diubah',
                message: 'Password akun kamu telah berhasil diubah. Jika ini bukan kamu, segera hubungi tim ' . \App\Models\Setting::get('site_name', 'Skolah.com') . '.',
                url: route('dashboard'),
            );
        } catch (\Throwable $e) {
            Log::warning('Password changed notification failed', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);
        }
    }
}
