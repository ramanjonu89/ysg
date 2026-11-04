<?php

namespace App\Http\Resources\Mobile;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="BlockContent",
 *     type="object",
 *     title="Block Content",
 *     description="Block content resource representation",
 *     @OA\Property(property="id", type="integer", example=1, description="Block content ID"),
 *     @OA\Property(property="title", type="string", nullable=true, example="Content Title", description="Block content title"),
 *     @OA\Property(property="description", type="string", nullable=true, example="Content description", description="Block content description"),
 *     @OA\Property(property="content", type="string", nullable=true, example="Content text", description="Block content text"),
 *     @OA\Property(property="image", type="string", nullable=true, example="block_contents/image.jpg", description="Block content image path"),
 *     @OA\Property(property="options", type="object", nullable=true, description="Block content options as JSON object"),
 *     @OA\Property(property="other_text", type="string", nullable=true, example="Additional text", description="Additional text field"),
 * )
 */
class BlockContentResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'content' => $this->content,
            'image' => $this->image ? url($this->image) : null,
            'options' => analyzeLinkRoute($this->options),
            'other_text' => $this->other_text,
        ];
    }
}