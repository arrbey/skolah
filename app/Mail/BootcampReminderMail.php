<?php

namespace App\Mail;

use App\Models\Bootcamp;
use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BootcampReminderMail extends Mailable
{
    use SerializesModels;

    public function __construct(
        public User     $user,
        public Bootcamp $bootcamp,
        public string   $reminderType = '1day', // '1day' atau '1hour'
    ) {}

    public function envelope(): Envelope
    {
        $prefix = $this->reminderType === '1hour'
            ? '🔴 Dimulai 1 Jam Lagi'
            : '📅 Besok Dimulai';

        return new Envelope(
            subject: "{$prefix}: {$this->bootcamp->title} — " . \App\Models\Setting::get('site_name', 'Skolah.com'),
        );
    }

    public function content(): Content
    {
        $this->bootcamp->loadMissing('instructor');

        return new Content(
            view: 'emails.bootcamp-reminder',
            with: [
                'user'         => $this->user,
                'bootcamp'     => $this->bootcamp,
                'reminderType' => $this->reminderType,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
