<?php

namespace App\Http\Resources\SellerCenter;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderDetail extends JsonResource
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
            'image' => $this->product->getThumbnailImageOrder($this->variation),
            'name' => $this->product_name,
            'qty' => $this->quantity,
            'price' => (float)$this->rsp_price,
            'discountAmount' => $this->discount,
            'discountType' => $this->discount_type,
            'priceAfterDiscount' => $this->getPrice(),
            'total' => (float)$this->price,
            'deliveryStatus' => $this->delivery_status,
            'variantDetails' => $this->getVariantDetails(),
            'shippingType' => $this->shipping_type,
            'shopAddress' => $this->collection_address
        ];
    }
}
