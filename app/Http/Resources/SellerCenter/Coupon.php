<?php

namespace App\Http\Resources\SellerCenter;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class Coupon extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'code' => $this->code,
            'type' => $this->type,
            'discount' => $this->discount,
            'discount_type' => $this->discount_type,
            'usages' => $this->usages_count,
            'total' => (int)json_decode($this->details)->max_coupon,
            'details' => json_decode($this->details),
            'is_active' => boolval($this->is_active),
            'published' => boolval($this->published),
            'only_once' => boolval($this->only_once),
            'start_date' => date('Y-m-d', $this->start_date),
            'end_date' => date('Y-m-d', $this->end_date),
        ];
    }
}
