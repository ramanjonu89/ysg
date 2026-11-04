<?php

namespace App\Http\Resources\Mobile;

use App\Models\Review;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="OrderDetail",
 *     type="object",
 *     title="Order Detail",
 *     description="Order detail resource for split order items",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         format="int64",
 *         description="Order detail ID",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="product_id",
 *         type="integer",
 *         format="int64",
 *         description="Product ID",
 *         example=42
 *     ),
 *     @OA\Property(
 *         property="product_name",
 *         type="string",
 *         description="Product name",
 *         example="iPhone 15 Pro"
 *     ),
 *     @OA\Property(
 *         property="variation",
 *         type="string",
 *         nullable=true,
 *         description="Product variation details",
 *         example="Color: Black, Storage: 256GB"
 *     ),
 *     @OA\Property(
 *         property="quantity",
 *         type="integer",
 *         description="Order quantity",
 *         example=2
 *     ),
 *     @OA\Property(
 *         property="product_price",
 *         type="string",
 *         description="Formatted unit price after discount",
 *         example="$899.99"
 *     ),
 *     @OA\Property(
 *         property="old_product_price",
 *         type="string",
 *         description="Formatted original price before discount",
 *         example="$999.99"
 *     ),
 *     @OA\Property(
 *         property="thumbnail_img",
 *         type="string",
 *         nullable=true,
 *         description="Product thumbnail image URL",
 *         example="https://example.com/uploads/products/thumb.jpg"
 *     ),
 *     @OA\Property(
 *         property="review",
 *         nullable=true,
 *         description="User's review for this product",
 *         oneOf={@OA\Schema(ref="#/components/schemas/Review")}
 *     )
 * )
 */
class OrderDetailResource extends JsonResource
{
    public function toArray($request)
    {
        $review = null;
        if ($user = $request->user()) {
            $review = Review::where('product_id', $this->product_id)
                ->where('user_id', $user->id)
                ->first();
        }

        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'product_name' => $this->product_name,
            'variation' => $this->variation,
            'quantity' => (int) $this->quantity,
            'product_price' => format_price($this->price / $this->quantity),
            'old_product_price' => format_price($this->rsp_price),
            'thumbnail_img' => $this->product->thumbnail_img ? url($this->product->thumbnail_img) : null,
            'review' => $review ? new ReviewResource($review) : null,
        ];
    }
}

