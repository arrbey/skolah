<?php

namespace App\Livewire;

use Livewire\Component;

class NotificationBell extends Component
{
    public int $unreadCount = 0;

    // Poll setiap 30 detik untuk update real-time
    protected $polling = '30s';

    public function mount(): void
    {
        $this->refresh();
    }

    public function refresh(): void
    {
        if (auth()->check()) {
            $this->unreadCount = auth()->user()->unreadNotifications()->count();
        }
    }

    public function render()
    {
        return view('livewire.notification-bell');
    }
}
