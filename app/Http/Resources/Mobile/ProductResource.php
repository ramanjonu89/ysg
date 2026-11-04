<?php

namespace App\Http\Resources\Mobile;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
/**
 * @OA\Schema(
 *     schema="Product",
 *     type="object",
 *     title="Product",
 *     description="Product model for mobile application",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="Unique identifier for the product",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Product name",
 *         example="iPhone 15 Pro"
 *     ),
 *     @OA\Property(
 *         property="slug",
 *         type="string",
 *         description="Product slug for URL",
 *         example="iphone-15-pro"
 *     ),
 *     @OA\Property(
 *         property="thumbnail_img",
 *         type="string",
 *         nullable=true,
 *         description="Product thumbnail image URL",
 *         example="https://example.com/uploads/products/thumb-1.jpg"
 *     ),
 *     @OA\Property(
 *         property="has_discount",
 *         type="boolean",
 *         description="Whether product has discount",
 *         example=true
 *     ),
 *     @OA\Property(
 *         property="discount_badge",
 *         type="string",
 *         description="Product discount badge (percentage format)",
 *         example="-20%"
 *     ),
 *     @OA\Property(
 *         property="old_product_price",
 *         type="string",
 *         description="Original product price before discount (formatted)",
 *         example="$999.99 - $1199.99"
 *     ),
 *     @OA\Property(
 *         property="product_price",
 *         type="string",
 *         description="Current product price after discount (formatted)",
 *         example="$899.99 - $999.99"
 *     ),
 *     @OA\Property(
 *         property="rating",
 *         type="number",
 *         format="float",
 *         description="Product rating",
 *         example=4.5
 *     ),

 *     @OA\Property(
 *         property="current_stock",
 *         type="integer",
 *         description="Current stock quantity",
 *         example=25
 *     ),
 *     @OA\Property(
 *         property="sku",
 *         type="string",
 *         nullable=true,
 *         description="Product SKU/unit",
 *         example="piece"
 *     ),
 *     @OA\Property(
 *         property="featured",
 *         type="boolean",
 *         description="Whether product is featured",
 *         example=true
 *     ),
 *     @OA\Property(
 *         property="brand",
 *         type="object",
 *         nullable=true,
 *         description="Product brand information",
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="name", type="string", example="Apple"),
 *         @OA\Property(property="logo", type="string", nullable=true, example="https://example.com/uploads/brands/apple-logo.png")
 *     ),
 *     @OA\Property(
 *         property="category",
 *         type="object",
 *         nullable=true,
 *         description="Product category information",
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="name", type="string", example="Electronics")
 *     ),
 *     @OA\Property(
 *         property="seller",
 *         type="object",
 *         nullable=true,
 *         description="Product seller information",
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="name", type="string", example="Tech Store"),
 *         @OA\Property(property="shop_name", type="string", example="TechWorld")
 *     ),
 *     @OA\Property(
 *         property="is_wishlist",
 *         type="boolean",
 *         description="Whether product is in user's wishlist",
 *         example=false
 *     ),
 *     @OA\Property(
 *         property="has_variant",
 *         type="boolean",
 *         description="Whether product has variant",
 *         example=false
 *     )
 * )
 */
class ProductResource extends JsonResource
{
    public function toArray($request)
    {
        $isWishlist = $this->checkIfInWishlist();
        $hasVariant = $this->stocks->count() > 0;
        $hasDiscount = $this->has_discount;
        $oldProductPrice = '';
        if ($hasDiscount) {
            $oldProductPrice = format_price($this->min_price);
            if ($this->min_price != $this->max_price) {
                $oldProductPrice .= ' - ' . format_price($this->max_price);
            }
        }

        $productPrice = format_price($this->min_final_price);
        if ($this->min_final_price != $this->max_final_price) {
            $productPrice .= ' - ' . format_price($this->max_final_price);
        }

        return [
            'id' => $this->id,
            'name' => $this->translation_name ?? $this->name,
            'slug' => $this->slug,
            'thumbnail_img' => $this->thumbnail_img ? url($this->thumbnail_img) : null,

            'has_discount' => $hasDiscount,
            'discount_badge' => $this->discount_badge,

            'old_product_price' => $oldProductPrice,
            'product_price' => $productPrice,

            'rating' => (float) $this->rating,
            'current_stock' => $this->stocks->count() > 0 ? (int) $this->stocks->sum('qty') : (int) $this->current_stock,
            'is_age_restricted' => $this->category->is_age_restricted,
            'sku' => $this->unit,
            'brand' => $this->brand ? [
                'id' => $this->brand->id,
                'name' => $this->brand->name,
            ] : null,
            'category' => $this->category ? [
                'id' => $this->category->id,
                'name' => $this->category->name,
            ] : null,
            'seller' => $this->user ? [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'shop_name' => $this->user->shop ? $this->user->shop->name : null,
            ] : null,
            'is_wishlist' => $isWishlist,
            'has_variant' => $hasVariant,
        ];
    }

    private function checkIfInWishlist()
    {
        $user = Auth::guard('api')->user();
        if (empty($user)) {
            return false;
        }

        $userId = $user->id;
        static $wishlistProductIds = [];    
        
        if (empty($wishlistProductIds)) {
            $wishlistProductIds = \App\Models\Wishlist::where('user_id', $userId)
                ->pluck('product_id')
                ->toArray();
        }

        return in_array($this->id, $wishlistProductIds);
    }
}
