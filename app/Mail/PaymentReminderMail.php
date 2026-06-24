<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentReminderMail extends Mailable
{
    use SerializesModels;

    public function __construct(
        public Order $order,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '⏰ Segera Selesaikan Pembayaran — ' . $this->order->order_number . ' | ' . \App\Models\Setting::get('site_name', 'Skolah.com'),
        );
    }

    public function content(): Content
    {
        $this->order->loadMissing(['user', 'items']);

        return new Content(
            view: 'emails.payment-reminder',
            with: [
                'order' => $this->order,
                'user'  => $this->order->user,
                'items' => $this->order->items,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
