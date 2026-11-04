<?php

namespace App\Http\Resources\Mobile;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="Order",
 *     type="object",
 *     title="Order",
 *     description="Order resource for mobile checkout",
 *     @OA\Property(
 *         property="order_id",
 *         type="integer",
 *         format="int64",
 *         description="Order ID",
 *         example=12345
 *     ),
 *     @OA\Property(
 *         property="code",
 *         type="string",
 *         description="Order code",
 *         example="20260108-153045"
 *     ),
 *     @OA\Property(
 *         property="payment_type",
 *         type="string",
 *         description="Payment method type",
 *         example="cash_on_delivery"
 *     ),
 *     @OA\Property(
 *         property="payment_status",
 *         type="string",
 *         description="Payment status",
 *         example="unpaid"
 *     ),
 *     @OA\Property(
 *         property="grand_total",
 *         type="number",
 *         format="float",
 *         description="Grand total amount",
 *         example=199.99
 *     ),
 *     @OA\Property(
 *         property="coupon_discount",
 *         type="number",
 *         format="float",
 *         description="Coupon discount amount",
 *         example=20.00
 *     ),
 *     @OA\Property(
 *         property="total_shipping_cost",
 *         type="number",
 *         format="float",
 *         description="Total shipping cost",
 *         example=5.00
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         description="Order creation timestamp",
 *         example="2026-01-08T15:30:45.000000Z"
 *     )
 * )
 */
class OrderResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'order_id' => $this->id,
            'code' => $this->code,
            'payment_type' => $this->payment_type,
            'payment_status' => $this->payment_status,
            'grand_total' => (float) $this->grand_total,
            'coupon_discount' => (float) $this->coupon_discount,
            'total_shipping_cost' => (float) $this->total_shipping_cost,
            'created_at' => $this->created_at,
        ];
    }
}

