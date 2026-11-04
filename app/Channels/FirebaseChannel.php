<?php

namespace App\Channels;

use App\Services\FirebaseNotificationService;
use Illuminate\Notifications\Notification;

class FirebaseChannel
{
    protected $firebaseService;

    public function __construct(FirebaseNotificationService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    public function send($notifiable, Notification $notification)
    {
        if (method_exists($notification, 'toFirebase')) {
            $data = $notification->toFirebase($notifiable);
            return $this->firebaseService->sendToUser(
                $notifiable,
                $data['title'],
                $data['body'],
                $data['data'] ?? []
            );
        }
    }
}
