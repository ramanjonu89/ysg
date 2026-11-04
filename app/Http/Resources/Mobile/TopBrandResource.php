<?php

namespace App\Http\Resources\Mobile;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="TopBrand",
 *     type="object",
 *     title="Top Brand",
 *     description="Top brand model for mobile application",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="Unique identifier for the brand",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Brand name",
 *         example="Samsung"
 *     ),
 *     @OA\Property(
 *         property="logo",
 *         type="string",
 *         nullable=true,
 *         description="Brand logo URL",
 *         example="https://example.com/uploads/brands/samsung-logo.png"
 *     )
 * )
 */
class TopBrandResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'logo' => $this->logo ? url($this->logo) : null,
        ];
    }
}