<?php

namespace App\Http\Resources\SellerCenter;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductWithVariant extends JsonResource
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
            'id' => $this->variant ? 'v-' . $this->variant_id : $this->id,
            'product_name' => $this->name,
            'variant_name' => $this->variant ? $this->variant : '',
            'name' => $this->name . ($this->variant ? " - $this->variant" : ''),
            'sku' => $this->variant ? $this->sku : $this->unit,
            'stock' => $this->current_stock,
            'thumbnail_img' => $this->variant ? ($this->image ?? $this->thumbnail_img) : $this->thumbnail_img,
            'price' => $this->variant ? $this->variant_price : $this->unit_price,
            'is_variant' => $this->variant ? true : false,
            'product_id' => $this->variant ? $this->product_id : $this->id,
        ];
    }
}
