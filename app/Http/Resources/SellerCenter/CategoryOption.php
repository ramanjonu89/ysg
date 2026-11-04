<?php

namespace App\Http\Resources\SellerCenter;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryOption extends JsonResource
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
            'value' => $this->id,
            'text' => $this->name
        ];
    }
}