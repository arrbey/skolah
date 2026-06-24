<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderPaymentMail extends Mailable
{
    use SerializesModels;

    /**
     * Buat instance mailable baru.
     */
    public function __construct(
        public Order $order,
    ) {}

    /**
     * Envelope (subject, from, dll).
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pembayaran Berhasil — ' . $this->order->order_number . ' | ' . \App\Models\Setting::get('site_name', 'Skolah.com'),
        );
    }

    /**
     * Konten email.
     */
    public function content(): Content
    {
        $this->order->loadMissing(['user', 'items.itemable']);

        return new Content(
            view: 'emails.order-payment',
            with: [
                'order' => $this->order,
                'user'  => $this->order->user,
                'items' => $this->order->items,
            ],
        );
    }

    /**
     * Attachment (tidak ada untuk notifikasi ini).
     */
    public function attachments(): array
    {
        return [];
    }
}
