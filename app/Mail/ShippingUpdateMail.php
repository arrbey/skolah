<?php

namespace App\Mail;

use App\Models\BookOrder;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ShippingUpdateMail extends Mailable
{
    use SerializesModels;

    public function __construct(
        public BookOrder $bookOrder,
        public string $note = '',
    ) {}

    public function envelope(): Envelope
    {
        $statusLabel = $this->bookOrder->status_label;
        $title       = $this->bookOrder->book->title ?? 'Buku';

        return new Envelope(
            subject: "Update Pengiriman: {$statusLabel} — {$title} | ' . \App\Models\Setting::get('site_name', 'Skolah.com') . '",
        );
    }

    public function content(): Content
    {
        $this->bookOrder->loadMissing(['book', 'order', 'user', 'histories.actor']);

        return new Content(
            view: 'emails.shipping-update',
            with: [
                'bookOrder' => $this->bookOrder,
                'book'      => $this->bookOrder->book,
                'user'      => $this->bookOrder->user,
                'note'      => $this->note,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
