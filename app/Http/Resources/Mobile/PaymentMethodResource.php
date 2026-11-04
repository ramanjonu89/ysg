<?php

namespace App\Http\Resources\Mobile;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="PaymentMethod",
 *     type="object",
 *     title="Payment Method",
 *     description="Payment method resource for mobile application",
 *     @OA\Property(
 *         property="code",
 *         type="string",
 *         description="Payment method code",
 *         example="cybersource"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Payment method name",
 *         example="Cybersource"
 *     ),
 *     @OA\Property(
 *         property="logo",
 *         type="string",
 *         description="Payment method logo URL",
 *         example="https://example.com/assets/frontend/images/icons/cards/visa_master.png"
 *     )
 * )
 */
class PaymentMethodResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'code' => $this->code ?? '',
            'name' => $this->name ?? '',
            'logo' => $this->logo ?? '',
        ];
    }
}

