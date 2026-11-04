<?php

namespace App\Http\Resources\Mobile;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="Slider",
 *     type="object",
 *     title="Slider",
 *     description="Slider model for homepage banner",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="Unique identifier for the slider",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="photo",
 *         type="string",
 *         nullable=true,
 *         description="Full image URL for the slider",
 *         example="https://example.com/uploads/sliders/slider-1.jpg"
 *     ),
 *     @OA\Property(
 *         property="options",
 *         type="object",
 *         nullable=true,
 *         description="Slider content options as JSON object"),
 * )
 */
class SliderResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'photo' => $this->photo ? url($this->photo) : null,
            'options' => analyzeLinkRoute(((object)['link' => $this->link])),
        ];
    }
}