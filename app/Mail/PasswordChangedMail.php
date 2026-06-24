<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordChangedMail extends Mailable
{
    use SerializesModels;

    /**
     * Buat instance mailable baru.
     */
    public function __construct(
        public User $user,
    ) {}

    /**
     * Envelope (subject, from, dll).
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Password Berhasil Diubah — ' . \App\Models\Setting::get('site_name', 'Skolah.com'),
        );
    }

    /**
     * Konten email.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.password-changed',
            with: [
                'user' => $this->user,
            ],
        );
    }

    /**
     * Attachment (tidak ada).
     */
    public function attachments(): array
    {
        return [];
    }
}
