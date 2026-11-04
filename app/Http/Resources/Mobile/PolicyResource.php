<?php

namespace App\Http\Resources\Mobile;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="Policy",
 *     type="object",
 *     title="Policy",
 *     description="Policy resource for mobile app",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         format="int64",
 *         description="Policy ID",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Policy name/slug",
 *         example="return_policy"
 *     ),
 *     @OA\Property(
 *         property="content",
 *         type="string",
 *         description="Policy content in HTML format",
 *         example="<h1>Return Policy</h1><p>Our return policy allows...</p>"
 *     )
 * )
 */
class PolicyResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'content' => $this->content,
        ];
    }
}

