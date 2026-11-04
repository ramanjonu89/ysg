<?php

namespace App\Http\Resources\Mobile;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="NewsletterConfig",
 *     type="object",
 *     title="Newsletter Configuration",
 *     description="Newsletter configuration for popup image display",
 *     @OA\Property(
 *         property="photo",
 *         type="string",
 *         nullable=true,
 *         description="Full URL to the popup banner image",
 *         example="https://example.com/newsletter/photo/banner.jpg"
 *     ),
 *     @OA\Property(
 *         property="url",
 *         type="string",
 *         nullable=true,
 *         description="URL link when popup banner is clicked",
 *         example="https://example.com/promotion"
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         nullable=true,
 *         description="Description text for the popup",
 *         example="Subscribe to our newsletter for latest updates"
 *     )
 * )
 */
class NewsletterConfigResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'photo' => url($this->photo),
            'url' => $this->url,
            'description' => $this->description,
        ];
    }
}