<?php

namespace App\Http\Resources\Mobile;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="Seller",
 *     type="object",
 *     title="Seller",
 *     description="Seller model for mobile application",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="Unique identifier for the seller",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Seller name",
 *         example="John's Electronics Store"
 *     ),
 *     @OA\Property(
 *         property="image",
 *         type="string",
 *         nullable=true,
 *         description="Shop logo URL",
 *         example="https://example.com/uploads/shops/logo.jpg"
 *     ),
 *     @OA\Property(
 *         property="rating",
 *         type="number",
 *         format="float",
 *         description="Average rating based on product reviews",
 *         example=4.5,
 *         minimum=0,
 *         maximum=5
 *     ),
 *     @OA\Property(
 *         property="url",
 *         type="string",
 *         description="URL to visit the seller's shop",
 *         example="https://example.com/shop/visit/johns-electronics-store"
 *     ),
 *     @OA\Property(
 *         property="is_shop_adult",
 *         type="boolean",
 *         description="Indicates if the shop contains age-restricted (adult) products",
 *         example=true
 *    )
 * )
 */
class SellerResource extends JsonResource
{
    public function toArray($request)
    {
        $total = $this->user->product_reviews_count;
        $rating = $this->user->sum_review_rating;

        return [
            'id' => $this->id,
            'name' => $this->user->name,
            'image' => $this->user->shop->logo ? url($this->user->shop->logo) : null,
            'rating' => $total > 0 ? $rating/$total : 0,
            'url' => route('shop.visit', $this->user->shop->slug),
            'is_shop_adult' => $this->isShopAdult()
        ];
    }

    private function isShopAdult()
    {
        $categoryService = app()->make(\App\Services\CategoryService::class);
        $productService = app()->make(\App\Services\ProductService::class);
        $shopService = app()->make(\App\Services\ShopService::class);

        $validProducts = $productService->getValidProducts(['shop' => $this->user->shop]);
        $categories    = $categoryService->getCategoryFilter($validProducts);

        return $shopService->isShopAdult($categories);
    }
}