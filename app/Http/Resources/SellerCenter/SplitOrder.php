<?php

namespace App\Http\Resources\SellerCenter;

use Illuminate\Http\Resources\Json\JsonResource;

class SplitOrder extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->order->code,
            'customerName' => $this->seller->name,
            'customerEmail' => $this->seller->email,
            'subTotal' => (float)$this->sub_total,
            'grandTotal' => (float)$this->grand_total,
            'seller_discount' => (float)$this->discount_amount - (float)$this->ysg_discount_amount - (float)$this->cash_discount_amount,
            'ysg_discount' => (float)$this->ysg_discount_amount + (float)$this->cash_discount_amount,
            'shippingCost' => (float)$this->shipping_cost,
            'paymentStatus' => $this->order->payment_status,
            'deliveryStatus' => $this->delivery_status,
            'createdAt' => $this->created_at,
            'paymentMethod' => $this->order->payment_type,
            'shippingAddress' => json_decode($this->order->shipping_address),
            'shippingType' => $this->orderDetails->first()->shipping_type,
            'taxTotal' => 0,
            'trackingNumber' => $this->tracking_number,
        ];
    }
}
