<?php

namespace App\Notifications;

use App\Channels\FirebaseChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PushNotification extends Notification
{
    use Queueable;

    protected $title;
    protected $body;
    protected $data;

    public function __construct($title, $body, $data = [])
    {
        $this->title = $title;
        $this->body = $body;
        $this->data = $data;
    }

    public function via($notifiable)
    {
        return [FirebaseChannel::class];
    }

    public function toFirebase($notifiable)
    {
        return [
            'title' => $this->title,
            'body' => $this->body,
            'data' => $this->data
        ];
    }
}
