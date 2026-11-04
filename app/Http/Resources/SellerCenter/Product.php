<?php

namespace App\Http\Resources\SellerCenter;

use Illuminate\Http\Resources\Json\JsonResource;

class Product extends JsonResource
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
            'name' => $this->name,
            'sku' => $this->unit,
            'stock' => $this->current_stock,
            'thumbnail_img' => $this->thumbnail_img,
            'price' => $this->unit_price,
            'display_price' => $this->display_price(),
            'min_price' => $this->min_price,
            'max_price' => $this->max_price,
            'discount_price' => $this->discount_price,
            'display_discount_price' => $this->display_discount_price(),
            'published' => boolval($this->published),
            'stocks' => $this->stocks,
            'discount' => $this->discount,
            'discount_type' => $this->discount_type,
            'display_discount' => format_price($this->discount_amount)
        ];
    }

    private function display_price() {
        if (count($this->stocks)) {
            foreach ($this->stocks as $stock) { 
                $stock['display_price'] = format_price($stock->price);
                $stock['display_discount'] = format_price($stock->discount_amount);
                $stock['display_quantity'] = $stock->qty;
            }
        }
        return format_price($this->unit_price);
    }

    private function display_discount_price() {
        if (count($this->stocks)) {
            foreach ($this->stocks as $stock) { 
                $stock['display_discount_price'] = format_price($stock->discount_price);
            }
        }
        return format_price($this->discount_price);
    }
}
