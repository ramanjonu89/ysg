<?php

namespace App\Http\Resources\Mobile;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="TopCategory",
 *     type="object",
 *     title="Top Category",
 *     description="Top category model for mobile application",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="Unique identifier for the category",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Category name",
 *         example="Electronics"
 *     ),
 *     @OA\Property(
 *         property="slug",
 *         type="string",
 *         description="Category slug for URL",
 *         example="electronics"
 *     ),
 *     @OA\Property(
 *         property="icon",
 *         type="string",
 *         nullable=true,
 *         description="Category icon URL",
 *         example="https://example.com/uploads/categories/electronics-icon.png"
 *     ),
 *     @OA\Property(
 *         property="banner",
 *         type="string",
 *         nullable=true,
 *         description="Category banner image URL",
 *         example="https://example.com/uploads/categories/electronics-banner.jpg"
 *     )
 * )
 */
class TopCategoryResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'icon' => $this->icon ? url($this->icon) : null,
            'banner' => $this->banner ? url($this->banner) : null,
        ];
    }
}