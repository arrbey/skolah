<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AppNotification extends Notification
{
    use Queueable;

    /**
     * @param string $type    course|bootcamp|order|cert|success|warning|error|info
     * @param string $title   Judul singkat notifikasi
     * @param string $message Deskripsi lengkap notifikasi
     * @param string|null $url URL tujuan saat notifikasi diklik (optional)
     */
    public function __construct(
        protected string $type,
        protected string $title,
        protected string $message,
        protected ?string $url = null,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'    => $this->type,
            'title'   => $this->title,
            'message' => $this->message,
            'url'     => $this->url,
        ];
    }

    public function toBroadcast(object $notifiable): \Illuminate\Notifications\Messages\BroadcastMessage
    {
        return new \Illuminate\Notifications\Messages\BroadcastMessage([
            'type'    => $this->type,
            'title'   => $this->title,
            'message' => $this->message,
            'url'     => $this->url,
        ]);
    }

    public function broadcastType(): string
    {
        return 'new-notification';
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
