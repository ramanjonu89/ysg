<?php

namespace App\Http\Resources\Mobile;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="OrderDetailFull",
 *     type="object",
 *     title="Order Detail Full",
 *     description="Complete order detail including all products and pricing breakdown",
 *     @OA\Property(
 *         property="order_code",
 *         type="string",
 *         description="Order code",
 *         example="20260108-153045"
 *     ),
 *     @OA\Property(
 *         property="order_date",
 *         type="string",
 *         description="Order date formatted",
 *         example="09-01-2026 15:30 PM"
 *     ),
 *     @OA\Property(
 *         property="order_status",
 *         type="string",
 *         description="Order delivery status",
 *         example="Pending"
 *     ),
 *     @OA\Property(
 *         property="payment_type",
 *         type="string",
 *         description="Payment method",
 *         example="Cash On Delivery"
 *     ),
 *     @OA\Property(
 *         property="payment_status",
 *         type="string",
 *         description="Payment status",
 *         example="Unpaid"
 *     ),
 *     @OA\Property(
 *         property="grand_total",
 *         type="number",
 *         format="float",
 *         description="Grand total amount",
 *         example=199.99
 *     ),
 *     @OA\Property(
 *         property="grand_total_formatted",
 *         type="string",
 *         description="Formatted grand total",
 *         example="$199.99"
 *     ),
 *     @OA\Property(
 *         property="shipping_address",
 *         type="object",
 *         description="Shipping address information",
 *         @OA\Property(property="name", type="string", example="John Doe"),
 *         @OA\Property(property="email", type="string", example="john@example.com"),
 *         @OA\Property(property="phone", type="string", example="+1234567890"),
 *         @OA\Property(property="address", type="string", example="123 Main Street"),
 *         @OA\Property(property="city", type="string", example="New York"),
 *         @OA\Property(property="country", type="string", example="United States"),
 *         @OA\Property(property="postal_code", type="string", example="10001")
 *     ),
 *     @OA\Property(
 *         property="is_self_collection",
 *         type="boolean",
 *         description="Whether order is self collection",
 *         example=false
 *     ),
 *     @OA\Property(
 *         property="products",
 *         type="array",
 *         description="List of products in the order",
 *         @OA\Items(
 *             type="object",
 *             @OA\Property(property="id", type="integer", example=1),
 *             @OA\Property(property="product_id", type="integer", example=42),
 *             @OA\Property(property="product_name", type="string", example="iPhone 15 Pro"),
 *             @OA\Property(property="product_slug", type="string", nullable=true, example="iphone-15-pro"),
 *             @OA\Property(property="variation", type="string", nullable=true, example="Color: Black, Storage: 256GB"),
 *             @OA\Property(property="quantity", type="integer", example=2),
 *             @OA\Property(property="price", type="number", format="float", example=899.99),
 *             @OA\Property(property="price_formatted", type="string", example="$899.99"),
 *             @OA\Property(property="tax", type="number", format="float", example=80.00),
 *             @OA\Property(property="tax_formatted", type="string", example="$80.00"),
 *             @OA\Property(property="shipping_type", type="string", nullable=true, example="home_delivery"),
 *             @OA\Property(property="shipping_type_label", type="string", example="Home Delivery"),
 *             @OA\Property(property="collection_address", type="string", nullable=true, example="Store Address 123"),
 *             @OA\Property(property="pickup_point_name", type="string", nullable=true, example="Central Pickup Point"),
 *             @OA\Property(property="thumbnail_img", type="string", nullable=true, example="https://example.com/uploads/products/thumb.jpg"),
 *             @OA\Property(property="is_available", type="boolean", example=true)
 *         )
 *     ),
 *     @OA\Property(
 *         property="summary",
 *         type="object",
 *         description="Order pricing summary",
 *         @OA\Property(property="subtotal", type="number", format="float", example=179.99),
 *         @OA\Property(property="subtotal_formatted", type="string", example="$179.99"),
 *         @OA\Property(property="shipping_cost", type="number", format="float", example=5.00),
 *         @OA\Property(property="shipping_cost_formatted", type="string", example="$5.00"),
 *         @OA\Property(property="tax", type="number", format="float", example=15.00),
 *         @OA\Property(property="tax_formatted", type="string", example="$15.00"),
 *         @OA\Property(property="coupon_discount", type="number", format="float", example=0.00),
 *         @OA\Property(property="coupon_discount_formatted", type="string", example="$0.00"),
 *         @OA\Property(property="total", type="number", format="float", example=199.99),
 *         @OA\Property(property="total_formatted", type="string", example="$199.99")
 *     )
 * )
 */
class OrderDetailFullResource extends JsonResource
{
    public function toArray($request)
    {
        $shippingAddress = json_decode($this->shipping_address);
        $isSelfCollection = isset($shippingAddress->address) && $shippingAddress->address == 'Self collection';
        
        $status = $this->orderDetails->first() ? $this->orderDetails->first()->delivery_status : 'pending';

        return [
            'order_code' => $this->code,
            'order_date' => date('d-m-Y H:i A', $this->date),
            'order_status' => ucfirst(str_replace('_', ' ', $status)),
            'payment_type' => ucfirst(str_replace('_', ' ', $this->payment_type)),
            'payment_status' => ucfirst($this->payment_status),
            'grand_total' => (float) $this->grand_total,
            'grand_total_formatted' => single_price($this->grand_total),
            'shipping_address' => [
                'name' => $shippingAddress->name ?? '',
                'email' => $shippingAddress->email ?? '',
                'phone' => $shippingAddress->phone ?? '',
                'address' => $isSelfCollection ? 'Self Collection' : ($shippingAddress->address ?? ''),
                'city' => $shippingAddress->city ?? '',
                'country' => $shippingAddress->country ?? '',
                'postal_code' => $shippingAddress->postal_code ?? '',
            ],
            'is_self_collection' => $isSelfCollection,
            'products' => $this->orderDetails->map(function ($detail) {
                $shippingTypeLabel = '';
                if ($detail->shipping_type == 'home_delivery') {
                    $shippingTypeLabel = 'Home Delivery';
                } elseif ($detail->shipping_type == 'self_collection') {
                    $shippingTypeLabel = 'Self Collection';
                } elseif ($detail->shipping_type == 'pickup_point') {
                    $shippingTypeLabel = 'Pickup Point';
                }

                return [
                    'id' => $detail->id,
                    'product_id' => $detail->product_id,
                    'product_name' => $detail->product_name,
                    'product_slug' => $detail->product ? $detail->product->slug : null,
                    'variation' => $detail->variation,
                    'quantity' => (int) $detail->quantity,
                    'price' => (float) $detail->price,
                    'price_formatted' => single_price($detail->price),
                    'tax' => (float) $detail->tax,
                    'tax_formatted' => single_price($detail->tax),
                    'shipping_type' => $detail->shipping_type,
                    'shipping_type_label' => $shippingTypeLabel,
                    'collection_address' => $detail->shipping_type == 'self_collection' ? $detail->collection_address : null,
                    'pickup_point_name' => $detail->pickup_point ? $detail->pickup_point->name : null,
                    'thumbnail_img' => $detail->product && $detail->product->thumbnail_img 
                        ? url($detail->product->thumbnail_img) 
                        : null,
                    'is_available' => $detail->product != null,
                ];
            }),
            'summary' => [
                'subtotal' => (float) $this->orderDetails->sum('price'),
                'subtotal_formatted' => single_price($this->orderDetails->sum('price')),
                'shipping_cost' => (float) $this->total_shipping_cost,
                'shipping_cost_formatted' => single_price($this->total_shipping_cost),
                'tax' => (float) $this->orderDetails->sum('tax'),
                'tax_formatted' => single_price($this->orderDetails->sum('tax')),
                'coupon_discount' => (float) $this->coupon_discount,
                'coupon_discount_formatted' => single_price($this->coupon_discount),
                'total' => (float) $this->grand_total,
                'total_formatted' => single_price($this->grand_total),
            ],
        ];
    }
}

