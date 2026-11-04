<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use App\Mail\InvoiceEmailManager;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OrderInvoiceNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $order;
    protected $array;

    public function __construct($order, $array)
    {
        $this->order = $order;
        $this->array = $array;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        try {
            Mail::to($notifiable->email)->send(new InvoiceEmailManager($this->array));
            Mail::to('cs@ysg.com.sg')->send(new InvoiceEmailManager($this->array));
        } catch (\Exception $e) {
            Log::error('OrderInvoiceNotification error: ' . $e->getMessage());
        }

        return null;
    }
}
