<?php

namespace App\Mail;

use App\Models\PromoCode;
use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PromoNotificationMail extends Mailable
{
    use SerializesModels;

    public function __construct(
        public User      $user,
        public PromoCode $promoCode,
        public ?string   $customMessage = '',
    ) {
        $this->customMessage = $this->customMessage ?? '';
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🎉 Promo Spesial: Diskon ' . $this->promoCode->discount_label . ' — ' . \App\Models\Setting::get('site_name', 'Skolah.com'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.promo-notification',
            with: [
                'user'          => $this->user,
                'promoCode'     => $this->promoCode,
                'customMessage' => $this->customMessage,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
