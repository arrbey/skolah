<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * Override notifikasi reset password bawaan Laravel
 * agar menggunakan template email branded Skolah.com.
 */
class ResetPasswordNotification extends BaseResetPassword
{
    /**
     * Build the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('Reset Password — ' . \App\Models\Setting::get('site_name', 'Skolah.com'))
            ->view('emails.reset-password', [
                'user' => $notifiable,
                'url'  => $url,
            ]);
    }
}
