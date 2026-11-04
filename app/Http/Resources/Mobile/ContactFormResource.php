<?php

namespace App\Http\Resources\Mobile;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="ContactForm",
 *     type="object",
 *     title="Contact Form",
 *     description="Contact form resource representation",
 *     @OA\Property(property="id", type="integer", example=1, description="Contact form ID"),
 *     @OA\Property(property="name", type="string", example="John Doe", description="Sender name"),
 *     @OA\Property(property="email", type="string", format="email", example="john@example.com", description="Sender email"),
 *     @OA\Property(property="message", type="string", example="Hello, I have a question about your products.", description="Contact message"),
 *     @OA\Property(property="is_read", type="boolean", example=false, description="Whether the message has been read by admin"),
 *     @OA\Property(property="submitted_at", type="string", format="date-time", example="2025-10-18 13:30:00", description="When the form was submitted"),
 * )
 */
class ContactFormResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'message' => $this->message,
            'is_read' => $this->is_read,
            'submitted_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}

