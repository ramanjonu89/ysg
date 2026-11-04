<?php

namespace App\Http\Resources\SellerCenter;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class OrderDetailCollection extends ResourceCollection
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
            'items' => $this->collection,
        ];
    }

    public function toResponse($request)
    {
        return JsonResource::toResponse($request);
    }
}
