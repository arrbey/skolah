<?php

namespace App\Listeners;

use App\Mail\WelcomeMail;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendWelcomeEmail
{
    /**
     * Handle the event.
     *
     * Kirim email + notifikasi in-app setelah user berhasil registrasi.
     */
    public function handle(Registered $event): void
    {
        /** @var User $user */
        $user = $event->user;

        Mail::to($user->email)->send(new WelcomeMail($user));

        try {
            send_notification(
                user: $user,
                type: 'info',
                title: '🎉 Selamat Datang di ' . \App\Models\Setting::get('site_name', 'Skolah.com') . '!',
                message: "Hei {$user->name}, akun kamu sudah aktif. Mulai jelajahi ribuan kursus dan tingkatkan skill-mu sekarang!",
                url: route('courses.index'),
            );
        } catch (\Throwable $e) {
            Log::warning('Welcome notification failed', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);
        }
    }
}
