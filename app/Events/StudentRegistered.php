<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StudentRegistered implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $totalStudents;
    public $totalCourses;
    public $totalInstructors;
    public $totalBootcamps;

    public function __construct($totalStudents = null, $totalCourses = null, $totalInstructors = null, $totalBootcamps = null)
    {
        $this->totalStudents = $totalStudents;
        $this->totalCourses = $totalCourses;
        $this->totalInstructors = $totalInstructors;
        $this->totalBootcamps = $totalBootcamps;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('stats'),
        ];
    }

    public function broadcastAs()
    {
        return 'stats.updated';
    }
}
