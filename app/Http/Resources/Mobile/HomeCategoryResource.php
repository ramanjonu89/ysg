<?php

namespace App\Http\Resources\Mobile;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="HomeCategoryResource",
 *     title="Home Category Resource",
 *     description="Home category resource for mobile API",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="Home category ID",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="category_id",
 *         type="integer",
 *         description="Category ID",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="category_name",
 *         type="string",
 *         description="Category name",
 *         example="Electronics"
 *     ),
 *     @OA\Property(
 *         property="category_slug",
 *         type="string",
 *         description="Category slug",
 *         example="electronics"
 *     ),
 *     @OA\Property(
 *         property="banner",
 *         type="string",
 *         nullable=true,
 *         description="Category banner image URL",
 *         example="https://example.com/banner.jpg"
 *     ),
 *     @OA\Property(
 *         property="icon",
 *         type="string",
 *         nullable=true,
 *         description="Category icon image URL",
 *         example="https://example.com/icon.png"
 *     ),
 *     @OA\Property(
 *         property="products",
 *         type="array",
 *         description="List of products in this category",
 *         @OA\Items(ref="#/components/schemas/Product")
 *     )
 * )
 */
class HomeCategoryResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'category_id' => $this->category_id,
            'category_name' => $this->category ? $this->category->name : null,
            'category_slug' => $this->category ? $this->category->slug : null,
            'banner' => $this->category && $this->category->banner ? url($this->category->banner) : null,
            'icon' => $this->category && $this->category->icon ? url($this->category->icon) : null,
            'products' => isset($this->products) ? ProductResource::collection($this->products) : [],
        ];
    }
}
