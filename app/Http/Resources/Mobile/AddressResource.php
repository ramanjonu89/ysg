<?php

namespace App\Http\Resources\Mobile;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="Address",
 *     type="object",
 *     title="User Address",
 *     description="Address resource for user",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="Unique identifier for the address",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="user_id",
 *         type="integer",
 *         description="Unique identifier of the user who owns this address",
 *         example=10
 *     ),
 *     @OA\Property(
 *         property="address",
 *         type="string",
 *         description="Detailed address line",
 *         example="10 Bayfront Avenue"
 *     ),
 *     @OA\Property(
 *         property="country",
 *         type="string",
 *         description="Country of the address",
 *         example="Singapore"
 *     ),
 *     @OA\Property(
 *         property="city",
 *         type="string",
 *         description="City of the address",
 *         example="Singapore"
 *     ),
 *     @OA\Property(
 *         property="postal_code",
 *         type="string",
 *         description="Postal code / ZIP code",
 *         example="018956"
 *     ),
 *     @OA\Property(
 *         property="country_code",
 *         type="string",
 *         nullable=true,
 *         description="Country code associated with this address",
 *         example="+65"
 *     ),
 *     @OA\Property(
 *         property="phone",
 *         type="string",
 *         nullable=true,
 *         description="Phone number associated with this address",
 *         example="+65 8123 4567"
 *     ),
 *     @OA\Property(
 *         property="title",
 *         type="string",
 *         nullable=true,
 *         description="Title or label for the address (e.g., Home, Office)",
 *         example="Home"
 *     ),
 *     @OA\Property(
 *         property="default",
 *         type="boolean",
 *         description="Indicates if this address is the default one",
 *         example=true
 *     )
 * )
 */
class AddressResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'          => $this->id,
            'user_id'     => $this->user_id,
            'address'     => $this->address,
            'country'     => $this->countryRelationship->name ?? 'N/A',
            'city'        => $this->cityRelationship->name ?? 'N/A',
            'postal_code' => $this->postal_code,
            'country_code' => $this->country_code ?? '+65',
            'phone'       => $this->phone,
            'title'       => $this->title,
            'default'     => (bool) $this->set_default,
        ];
    }
}
