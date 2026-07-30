<?php

namespace App\Notifications;

use App\Models\LiveSession;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewLiveSessionNotification extends Notification
{
    use Queueable;

    protected $session;

    public function __construct(LiveSession $session)
    {
        $this->session = $session;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => "📡 Nouvelle session en direct : '{$this->session->title}' dans le cours '{$this->session->course->title}'",
            'session_id' => $this->session->id,
            'course_id' => $this->session->course_id,
            'scheduled_at' => $this->session->scheduled_at?->toIso8601String(),
        ];
    }
}