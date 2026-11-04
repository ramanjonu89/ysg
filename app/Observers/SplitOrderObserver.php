<?php

namespace App\Observers;

use App\Models\SplitOrder;
use App\Notifications\OrderStatusChanged;

class SplitOrderObserver
{
    public $afterCommit = true;

    public function updated(SplitOrder $splitOrder)
    {
        $order = $splitOrder->order;
        $orderDetail = $splitOrder->orderDetails->first();

        if ($splitOrder->isDirty('delivery_status')) {
            $order->user->notify(new OrderStatusChanged($orderDetail, $splitOrder->delivery_status, $orderDetail->shipping_type));

            foreach ($splitOrder->orderDetails as $orderDetail) {
                $orderDetail->delivery_status = $splitOrder->delivery_status;
                $orderDetail->save();
            }
        }
    }
}
