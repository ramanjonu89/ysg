<?php

namespace App\Http\Resources\Mobile;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="City",
 *     type="object",
 *     title="City",
 *     description="City item",
 *     @OA\Property(property="id", type="integer", example=101),
 *     @OA\Property(property="name", type="string", example="Singapore"),
 *     @OA\Property(property="country_id", type="integer", example=1)
 * )
 */
class CityResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'country_id'  => $this->country_id,
        ];
    }
}
