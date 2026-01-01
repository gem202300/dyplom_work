<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TestNotification extends Notification
{
    use Queueable;

    private string $title;
    private string $message;
    private ?string $url;
    private bool $canResubmit;
    public function __construct(string $title, string $message, ?string $url = null, bool $canResubmit = false)
    {
        $this->title = $title;
        $this->message = $message;
        $this->url = $url;
        $this->canResubmit = $canResubmit;
    }
    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'url' => $this->url,
            'can_resubmit' => $this->canResubmit,
        ];
    }
}
