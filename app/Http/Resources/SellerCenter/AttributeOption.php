<?php

namespace App\Http\Resources\SellerCenter;

use Illuminate\Http\Resources\Json\JsonResource;

class AttributeOption extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'value' => $this->id,
            'text' => $this->name
        ];
    }
}