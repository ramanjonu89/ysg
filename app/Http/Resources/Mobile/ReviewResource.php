<?php

namespace App\Http\Resources\Mobile;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="Review",
 *     type="object",
 *     title="Review",
 *     description="Review resource for product reviews in mobile APIs",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         format="int64",
 *         description="Unique identifier for the review",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="product_id",
 *         type="integer",
 *         format="int64",
 *         description="ID of the product being reviewed",
 *         example=42
 *     ),
 *     @OA\Property(
 *         property="user",
 *         type="object",
 *         nullable=true,
 *         description="Reviewer information",
 *         @OA\Property(property="id", type="integer", format="int64", example=10),
 *         @OA\Property(property="name", type="string", example="John Doe"),
 *         @OA\Property(property="avatar", type="string", nullable=true, example="https://example.com/uploads/avatars/10.png")
 *     ),
 *     @OA\Property(
 *         property="rating",
 *         type="number",
 *         format="float",
 *         description="Rating given by the user",
 *         example=4.5
 *     ),
 *     @OA\Property(
 *         property="comment",
 *         type="string",
 *         description="Review comment text",
 *         example="Great product, fast delivery!"
 *     ),
 *     @OA\Property(
 *         property="is_recommended",
 *         type="boolean",
 *         description="Whether the reviewer recommends this product",
 *         example=true
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         nullable=true,
 *         description="When the review was created",
 *         example="2024-05-18 12:34:56"
 *     )
 * )
 */
class ReviewResource extends JsonResource
{
    public function toArray($request)
    {
        $user = $this->user;
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'user' => $this->user_id ? [
                'id' => $this->user_id,
                'name' => $user->name,
                'avatar' => $user->avatar_original ? url($user->avatar_original) : null,
            ] : null,
            'rating' => (float) $this->rating,
            'comment' => $this->comment,
            'is_recommended' => (bool) $this->is_recommended,
            'created_at' => $this->created_at ? $this->created_at->toDateTimeString() : null,
        ];
    }
}
