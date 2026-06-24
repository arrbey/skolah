<?php

namespace App\Mail;

use App\Models\Course;
use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EnrollmentConfirmationMail extends Mailable
{
    use SerializesModels;

    /**
     * Buat instance mailable baru.
     */
    public function __construct(
        public User   $user,
        public Course $course,
    ) {}

    /**
     * Envelope (subject, from, dll).
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Enrollment Berhasil: ' . $this->course->title . ' — ' . \App\Models\Setting::get('site_name', 'Skolah.com'),
        );
    }

    /**
     * Konten email.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.enrollment-confirmation',
            with: [
                'user'   => $this->user,
                'course' => $this->course,
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
