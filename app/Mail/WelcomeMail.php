<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeMail extends Mailable
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
            subject: 'Selamat Datang di ' . \App\Models\Setting::get('site_name', 'Skolah.com') . ', ' . $this->user->name . '! 🎓',
        );
    }

    /**
     * Konten email.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.welcome',
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
