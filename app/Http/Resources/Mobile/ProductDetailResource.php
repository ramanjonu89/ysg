<?php

namespace App\Http\Resources\Mobile;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Schema(
 *     schema="ProductDetail",
 *     type="object",
 *     title="Product Detail",
 *     description="Product detail model for mobile application",
 *     @OA\Property(property="id", type="integer", format="int64", description="Unique identifier for the product", example=1),
 *     @OA\Property(property="name", type="string", description="Product name", example="iPhone 15 Pro"),
 *     @OA\Property(property="slug", type="string", description="Product slug for URL", example="iphone-15-pro"),
 *     @OA\Property(property="description", type="string", nullable=true, description="Product description", example="High-end smartphone with advanced features"),
 *     @OA\Property(property="thumbnail_img", type="string", nullable=true, description="Product thumbnail image URL", example="https://example.com/uploads/products/thumb-1.jpg"),
 *     @OA\Property(
 *         property="images",
 *         type="array",
 *         description="Gallery image URLs",
 *         @OA\Items(type="string", example="https://example.com/uploads/products/photo-1.jpg")
 *     ),
 *     @OA\Property(
 *         property="variants",
 *         type="array",
 *         description="Variant choices for this product",
 *         @OA\Items(
 *             type="object",
 *             @OA\Property(property="attribute_id", type="integer", example="1"),
 *             @OA\Property(property="attribute_name", type="string", example="Variation"),
 *             @OA\Property(
 *                 property="values",
 *                 type="array",
 *                 @OA\Items(type="string"),
 *                 example={"Cookies & Crème", "Milk Chocolate with Hazelnut"}
 *             )
 *         )
 *     ),
 *     @OA\Property(
 *         property="variant_products",
 *         type="array",
 *         description="List of variant product stock information",
 *         @OA\Items(
 *             type="object",
 *             @OA\Property(property="variant", type="string", description="Variant key/combination", example="Blue / 128GB"),
 *             @OA\Property(property="price", type="string", description="Original price (formatted)", example="$999.99"),
 *             @OA\Property(property="discount_price", type="string", description="Final price after discount (formatted)", example="$899.99"),
 *             @OA\Property(property="qty", type="integer", description="Available quantity", example=10),
 *             @OA\Property(property="discount", type="number", format="float", description="Discount value", example=10.0),
 *             @OA\Property(property="discount_type", type="string", description="Discount type (percent or amount)", example="percent"),
 *             @OA\Property(property="image", type="string", nullable=true, description="Variant image URL", example="https://example.com/uploads/products/variant-1.jpg")
 *         )
 *     ),
 *     @OA\Property(property="has_discount", type="boolean", description="Whether product has discount", example=true),
 *     @OA\Property(property="discount_badge", type="string", description="Product discount badge (percentage format)", example="-20%"),
 *     @OA\Property(property="old_product_price", type="string", description="Original product price before discount (formatted)", example="$999.99 - $1199.99"),
 *     @OA\Property(property="product_price", type="string", description="Current product price after discount (formatted)", example="$899.99 - $999.99"),
 *     @OA\Property(property="rating", type="number", format="float", description="Product rating", example=4.5),
 *     @OA\Property(
 *         property="reviews",
 *         type="array",
 *         description="Product reviews",
 *         @OA\Items(ref="#/components/schemas/ProductReview")
 *     ),
 *     @OA\Property(
 *         property="review_categories",
 *         type="array",
 *         description="Review categories available for this product's category",
 *         @OA\Items(ref="#/components/schemas/ReviewCategory")
 *     ),
 *     @OA\Property(property="total_review", type="integer", description="Total number of reviews", example=15),
 *     @OA\Property(property="current_stock", type="integer", description="Current stock quantity", example=25),
 *     @OA\Property(property="is_age_restricted", type="boolean", description="Whether category is age-restricted", example=false),
 *     @OA\Property(property="sku", type="string", description="Product SKU/unit", example="piece"),
 *     @OA\Property(property="sold_quantity", type="integer", description="Total quantity sold", example=150),
 *     @OA\Property(
 *         property="brand",
 *         type="object",
 *         description="Product brand information",
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="name", type="string", example="Apple")
 *     ),
 *     @OA\Property(
 *         property="category",
 *         type="object",
 *         description="Product category information",
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="name", type="string", example="Electronics")
 *     ),
 *     @OA\Property(
 *         property="seller",
 *         type="object",
 *         description="Product seller information",
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="logo", type="string", nullable=true, description="Shop logo URL", example="https://example.com/uploads/shop/logo.jpg"),
 *         @OA\Property(property="name", type="string", example="Tech Store"),
 *         @OA\Property(property="rating", type="number", format="float", description="Seller rating", example=4.7),
 *         @OA\Property(property="shop_name", type="string", nullable=true, example="TechWorld")
 *     ),
 *     @OA\Property(
 *         property="is_wishlist",
 *         type="boolean",
 *         description="Whether product is in user's wishlist",
 *         example=false
 *     )
 * )
 */
class ProductDetailResource extends JsonResource
{
    public function toArray($request)
    {
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


        $photos = $this->photos ? json_decode($this->photos, true) : [];
        if (empty($photos)) {
            $images = [url('frontend/images/placeholder.jpg')];
        } else {
            $images = array_map(static function ($photo) {
                return url($photo);
            }, $photos);
        }


        $variants = [];
        $variantProducts = [];
        if ($this->variant_product) {
            $choiceOptions = $this->choice_options ? json_decode($this->choice_options, true) : [];
            $variants = array_map(static function ($choiceOption) {
                $choiceOption['attribute_name'] = $choiceOption['attribute']['text'] ?? null;
                unset($choiceOption['attribute']);
                return $choiceOption;
            }, $choiceOptions);

            foreach ($this->stocks as $stock) {
                $variantProducts[] = [
                    'variant_id' => $stock->id,
                    'variant' => $stock->variant,
                    'price' => format_price($stock->price),
                    'discount_price' => format_price($stock->final_price),
                    'qty' => (int) $stock->qty,
                    'discount' => (float) $stock->discount,
                    'discount_type' => $stock->discount_type,
                    'image' => $stock->image ? url($stock->image) : url('frontend/images/placeholder.jpg')
                ];
            }
        }

        $reviewCategories = collect();
        if ($this->category) {
            $reviewCategories = $this->category->reviewCategories()
                ->where('enabled', true)
                ->get();
        }
        $reviewCategories = ReviewCategoryResource::collection($reviewCategories);
        $reviews = ProductReviewResource::collection($this->reviews()->orderBy('rating', 'desc')->take(3)->get());
        $sellerRating = (float) $this->user->product_reviews_count > 0 ? $this->user->sum_review_rating / $this->user->product_reviews_count : 0;
        $isWishlist = $this->checkIfInWishlist();

        return [
            'id' => $this->id,
            'name' => $this->translation_name ?? $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'thumbnail_img' => $this->thumbnail_img ? url($this->thumbnail_img) : null,
            'images' => $images,
            'variants' => $variants,
            'variant_products' => $variantProducts,
            'has_discount' => $hasDiscount,
            'discount_badge' => $this->discount_badge,
            'old_product_price' => $oldProductPrice,
            'product_price' => $productPrice,
            'rating' => (float) $this->rating,
            'reviews' => $reviews,
            'review_categories' => $reviewCategories,
            'total_review' => $this->reviews()->count(),
            'current_stock' => $this->stocks->count() > 0 ? (int) $this->stocks->sum('qty') : (int) $this->current_stock,
            'is_age_restricted' => $this->category->is_age_restricted,
            'sku' => $this->unit,
            'sold_quantity' => $this->soldQuantity(),
            'brand' => $this->brand ? [
                'id' => $this->brand->id,
                'name' => $this->brand->name,
            ] : null,
            'category' => $this->category ? [
                'id' => $this->category->id,
                'name' => $this->category->name,
            ] : null,
            'seller' => $this->user ? [
                'id' => $this->user->seller->id,
                'logo' => $this->user->shop ? url($this->user->shop->logo) : null,
                'name' => $this->user->name,
                'rating' => $sellerRating,
                'shop_name' => $this->user->shop ? $this->user->shop->name : null,
            ] : null,
            'is_wishlist' => $isWishlist,
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