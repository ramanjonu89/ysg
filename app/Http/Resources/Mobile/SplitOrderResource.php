<?php

namespace App\Http\Resources\Mobile;

use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="SplitOrder",
 *     type="object",
 *     title="Split Order",
 *     description="Split order resource for mobile application",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         format="int64",
 *         description="Split order ID",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="order_id",
 *         type="integer",
 *         format="int64",
 *         description="Parent order ID",
 *         example=100
 *     ),
 *     @OA\Property(
 *         property="order_code",
 *         type="string",
 *         description="Order code",
 *         example="20250923-18555730"
 *     ),
 *     @OA\Property(
 *         property="order_date",
 *         type="string",
 *         description="Order date in dd-mm-yyyy format",
 *         example="23-09-2025"
 *     ),
 *     @OA\Property(
 *         property="payment_status",
 *         type="string",
 *         description="Payment status from parent order",
 *         example="paid"
 *     ),
 *     @OA\Property(
 *         property="shipping_type",
 *         type="string",
 *         description="Shipping type (home_delivery or self_collection)",
 *         example="home_delivery"
 *     ),
 *     @OA\Property(
 *         property="shipping_address",
 *         type="string",
 *         description="Shipping address",
 *         example="99 Aljunied Crescent, Singapore 09-373, Singapore, 380099, Singapore"
 *     ),
 *     @OA\Property(
 *         property="delivery_status",
 *         type="string",
 *         description="Delivery status (translated based on shipping type)",
 *         example="Delivered"
 *     ),
 *     @OA\Property(
 *         property="subtotal",
 *         type="number",
 *         format="float",
 *         description="Subtotal amount",
 *         example=1999.99
 *     ),
 *     @OA\Property(
 *         property="shipping_fee",
 *         type="number",
 *         format="float",
 *         description="Shipping cost",
 *         example=10.00
 *     ),
 *     @OA\Property(
 *         property="coupon_discount",
 *         type="number",
 *         format="float",
 *         description="Coupon discount amount",
 *         example=100.00
 *     ),
 *     @OA\Property(
 *         property="total_price",
 *         type="number",
 *         format="float",
 *         description="Grand total amount",
 *         example=1909.99
 *     ),
 *     @OA\Property(
 *         property="tracking_number",
 *         type="string",
 *         nullable=true,
 *         description="Tracking number for delivery",
 *         example="TRACK123456"
 *     ),
 *     @OA\Property(
 *         property="tracking_link",
 *         type="string",
 *         nullable=true,
 *         description="Tracking link for delivery",
 *         example="https://etrack.ych.com/oms/v1/api/trackandtrace/inquiry?queryNo=TRACK123456&type=transactionNo"
 *     ),
 *     @OA\Property(
 *         property="coupon_code",
 *         type="string",
 *         nullable=true,
 *         description="Applied coupon code",
 *         example="SAVE100"
 *     ),
 *     @OA\Property(
 *         property="delivery_address",
 *         type="object",
 *         nullable=true,
 *         description="Delivery address object (parsed from JSON)",
 *         example={"address": "99 Aljunied Crescent", "city": "Singapore", "postal_code": "380099"}
 *     ),
 *     @OA\Property(
 *         property="seller",
 *         ref="#/components/schemas/Seller",
 *         description="Seller information"
 *     ),
 *     @OA\Property(
 *         property="order_details",
 *         type="array",
 *         description="List of order items",
 *         @OA\Items(ref="#/components/schemas/OrderDetail")
 *     )
 * )
 */
class SplitOrderResource extends JsonResource
{
    public function toArray($request)
    {
        $trackingLink = '';
        $item = $this->orderDetails->first();
        if ($item && $item->trackingnum) {
            $trackingLink = 'https://etrack.ych.com/oms/v1/api/trackandtrace/inquiry?queryNo=' . $item->trackingnum . '&type=transactionNo';
        }

        return [
            'id' => $this->id,
            'order_id' => $this->order_id,
            'order_code' => $this->order->code,
            'order_date' => date('d-m-Y', $this->order->date),
            'payment_status' => $this->order->payment_status,
            'shipping_type' => $this->shipping_type,
            'shipping_address' => $this->shipping_address,
            'delivery_status' => $this->delivery_status_text,
            'subtotal' => (float) $this->sub_total,
            'shipping_fee' => (float) $this->shipping_cost,
            'coupon_discount' => (float) $this->discount_amount,
            'total_price' => (float) $this->grand_total,
            'tracking_number' => $this->tracking_number,
            'tracking_link' => $trackingLink,
            'coupon_code' => $this->coupon_code,
            'delivery_address' => json_decode($this->order->shipping_address),
            'seller' => $this->seller && $this->seller->seller ? SellerResource::make($this->seller->seller) : null,
            'order_details' => OrderDetailResource::collection($this->orderDetails),
        ];
    }
}

