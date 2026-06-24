<?php

namespace App\Mail;

use App\Models\InstructorApplication;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InstructorApplicationStatusMail extends Mailable
{
    use SerializesModels;

    /**
     * Buat instance mailable baru.
     */
    public function __construct(
        public InstructorApplication $application,
    ) {}

    /**
     * Envelope (subject, from, dll).
     */
    public function envelope(): Envelope
    {
        $status = $this->application->status === 'approved'
            ? 'Disetujui ✅'
            : 'Ditolak';

        return new Envelope(
            subject: 'Pengajuan Instruktur ' . $status . ' — ' . \App\Models\Setting::get('site_name', 'Skolah.com'),
        );
    }

    /**
     * Konten email.
     */
    public function content(): Content
    {
        $this->application->loadMissing('user');

        return new Content(
            view: 'emails.instructor-application-status',
            with: [
                'application' => $this->application,
                'user'        => $this->application->user,
                'isApproved'  => $this->application->status === 'approved',
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
