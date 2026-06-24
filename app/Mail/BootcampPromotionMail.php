<?php

namespace App\Mail;

use App\Models\Bootcamp;
use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BootcampPromotionMail extends Mailable
{
    use SerializesModels;

    public function __construct(
        public User     $user,
        public Bootcamp $bootcamp,
        public ?string  $customMessage = '',
    ) {
        $this->customMessage = $this->customMessage ?? '';
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🚀 Bootcamp: ' . $this->bootcamp->title . ' — ' . \App\Models\Setting::get('site_name', 'Skolah.com'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.bootcamp-promotion',
            with: [
                'user'          => $this->user,
                'bootcamp'      => $this->bootcamp,
                'customMessage' => $this->customMessage,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
