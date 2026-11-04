<?php

namespace App\Http\Resources\Mobile;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="Country",
 *     type="object",
 *     title="Country",
 *     description="Country item",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Singapore"),
 *     @OA\Property(property="country_code", type="string", example="+65")
 * )
 */
class CountryResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'country_code' => '+' . $this->country_code
        ];
    }
}
