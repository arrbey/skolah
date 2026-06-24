<?php

namespace App\Mail;

use App\Models\UserMembership;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MembershipExpiryMail extends Mailable
{
    use SerializesModels;

    public function __construct(
        public UserMembership $membership,
    ) {}

    public function envelope(): Envelope
    {
        $days = $this->membership->days_remaining;

        return new Envelope(
            subject: "Membership Kamu Berakhir dalam {$days} Hari — Skolah.com",
        );
    }

    public function content(): Content
    {
        $this->membership->loadMissing(['user', 'plan']);

        return new Content(
            view: 'emails.membership-expiry',
            with: [
                'membership' => $this->membership,
                'user'       => $this->membership->user,
                'plan'       => $this->membership->plan,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
