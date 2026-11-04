<?php

namespace App\Http\Resources\SellerCenter;

use Illuminate\Http\Resources\Json\JsonResource;

class Order extends JsonResource
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
            'code' => $this->code,
            'customerName' => $this->user->name,
            'customerEmail' => $this->user->email,
            'total' => (float)$this->getGrandTotalBySeller(),
            'paymentStatus' => $this->payment_status,
            'deliveryStatus' => $this->deliveryStatus,
            'createdAt' => $this->created_at,
            'paymentMethod' => $this->payment_type,
            'shippingAddress' => json_decode($this->shipping_address),
            'shippingTotal' => (float)$this->total_shipping_cost,
            'taxTotal' => 0,
        ];
    }
}
