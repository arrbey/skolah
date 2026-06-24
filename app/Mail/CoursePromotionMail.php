<?php

namespace App\Mail;

use App\Models\Course;
use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CoursePromotionMail extends Mailable
{
    use SerializesModels;

    public function __construct(
        public User    $user,
        public Course  $course,
        public ?string $customMessage = '',
    ) {
        $this->customMessage = $this->customMessage ?? '';
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🎓 Kursus Baru: ' . $this->course->title . ' — ' . \App\Models\Setting::get('site_name', 'Skolah.com'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.course-promotion',
            with: [
                'user'          => $this->user,
                'course'        => $this->course,
                'customMessage' => $this->customMessage,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
