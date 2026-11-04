<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Shop;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class OrderStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    protected $orderDetail;
    protected $status;
    protected $shippingType;

    public function __construct(OrderDetail $orderDetail, $status, $shippingType)
    {
        $this->orderDetail = $orderDetail;
        $this->status = $status;
        $this->shippingType = $shippingType;
    }

    public function via($notifiable)
    {
        if ($this->status == Order::SHIPPING_STATUS_ON_REVIEW) {
            Log::info('OrderStatusChanged::via skip mail', ['status' => $this->status, 'shippingType' => $this->shippingType]);
            return [];
        }

        if ($this->status == Order::SHIPPING_STATUS_PENDING || $this->status == Order::SHIPPING_STATUS_CANCELED) {
            Log::info('OrderStatusChanged::via skip mail', ['status' => $this->status, 'shippingType' => $this->shippingType]);
            return [];
        }

        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $orderDetail    = $this->orderDetail;
        $order          = $orderDetail->order;
        $orderCode      = $order->code;
        $status         = $this->status;
        $shippingType   = $orderDetail->shipping_type;
        $collectionAddress = $orderDetail->collection_address;

        switch ($status) {
            case Order::SHIPPING_STATUS_ON_DELIVERY:
                if ($shippingType == Shop::ORDER_TYPE_SELF_COLLECTION_KEY) {
                    $subject = "Your Order #{$orderCode} is ready for Self-Collection!";

                    $getUpcomingDeliveryRange = $this->getUpcomingDeliveryRange();

                    $data = [
                        'name'       => $order->user->name,
                        'order_code' => $orderCode,
                        'location'   => $collectionAddress,
                        'message'    => "Your Order #" . $orderCode . " is now ready for collection.",
                        'type'       => "Your Order #" . $orderCode . " is ready for Self-Collection!",
                        'footer'     => "Thank you for shopping with us and we know you're going to love your new buys!",
                        'subject'    => $subject,
                        'tracking_order_link' => env('APP_URL') . '/track_your_order?order_code=' . $orderCode,
                        'shipping_type' => $shippingType
                    ];

                    Log::info('OrderStatusChanged - toMail - status: ',['status' => $status, 'shippingType' => $shippingType]);

                    return (new MailMessage)
                        ->from(env('MAIL_FROM_ADDRESS'))
                        ->subject($subject)
                        ->view('emails.shipping_type_self_collection', [
                            'array' => $data
                        ]);
                    break;
                } else {
                    $subject = "Your Order #{$order->code} is on its Way!";
                    $data = [
                        'name'       => $order->user->name,
                        'order_code' => $orderCode,
                        'type'       => "Your Shipment is on its way!",
                        'footer'     => "Thank you for shopping with us and we know you're going to love your new buys!",
                        'subject'    => $subject,
                        'tracking_order_link' => env('APP_URL') . '/track_your_order?order_code=' . $orderCode
                    ];

                    Log::info('OrderStatusChanged - toMail - status: ',['status' => $status, 'shippingType' => $shippingType]);

                    return (new MailMessage)
                        ->from(env('MAIL_FROM_ADDRESS'))
                        ->subject($subject)
                        ->view('emails.shipping_type_home_delivery', [
                            'array' => $data
                        ]);
                    break;
                }

            case Order::SHIPPING_STATUS_DELIVERED:
                $subject = $shippingType == Shop::ORDER_TYPE_SELF_COLLECTION_KEY ?
                    "Your Order #{$orderCode} has been Collected" :
                    "Your Order #{$orderCode} has been Delivered";

                $note = "Your shipment has been successfully delivered to your indicated address.";

                $content = [
                    'type' => $subject,
                    'greeting' => "Thank you for your support.",
                    'note'  => $shippingType == Shop::ORDER_TYPE_SELF_COLLECTION_KEY ? "" : $note,
                ];

                $type = $shippingType === Shop::ORDER_TYPE_SELF_COLLECTION_KEY
                    ? "Your Order #{$orderCode} has been successfully collected!"
                    : "Your shipment #{$orderCode} has been successfully delivered!";

                $data = [
                    'name'       => $order->user->name,
                    'order_code' => $orderCode,
                    'type'       => $type,
                    'content'    => $content,
                    'footer'     => "Thank you in advance for your review and for being a preferred customer.",
                    'subject'    => $subject,
                    'tracking_order_link' => env('APP_URL') . '/track_your_order?order_code=' . $orderCode
                ];

                Log::info('OrderStatusChanged - toMail - status: ',['status' => $status, 'shippingType' => $shippingType]);

                return (new MailMessage)
                    ->from(env('MAIL_FROM_ADDRESS'))
                    ->subject($subject)
                    ->view('emails.shipped', [
                        'array' => $data
                    ]);

                break;
        }
    }

    private function getUpcomingDeliveryRange(): string
    {
        $start = Carbon::now()->addDay();
        $end = $start->copy()->addWeekdays(2);

        if ($start->month === $end->month && $start->year === $end->year) {
            return $start->format('d') . "–" . $end->format('d F Y');
        }

        return $start->format('d M Y') . " – " . $end->format('d M Y');
    }
}
