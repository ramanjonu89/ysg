<?php

namespace App\Http\Resources\Mobile;

use Illuminate\Http\Resources\Json\JsonResource;

 
/**
 * @OA\Schema(
 *     schema="CartItemBySeller",
 *     type="object",
 *     title="Cart Item By Seller",
 *     description="Cart items grouped by a single seller",
 *     @OA\Property(
 *         property="info",
 *         type="object",
 *         description="Seller information",
 *         @OA\Property(property="name", type="string", example="Shop Name", description="Shop name"),
 *         @OA\Property(property="sellerId", type="integer", example=6166, description="Seller user ID"),
 *         @OA\Property(property="shopId", type="integer", example=100, description="Shop ID"),
 *         @OA\Property(property="shopSlug", type="string", example="shop-name", description="Shop slug"),
 *         @OA\Property(property="orderType", type="integer", example=1, description="Order type"),
 *         @OA\Property(property="address", type="string", example="", description="Address string"),
 *         @OA\Property(property="coupons", type="array", @OA\Items(ref="#/components/schemas/Coupon"), description="Available seller coupons"),
 *         @OA\Property(
 *             property="selectedVoucher",
 *             type="object",
 *             nullable=true,
 *             description="Selected voucher information for this seller",
 *             @OA\Property(property="discount", type="string", example="10.00", description="Discount amount"),
 *             @OA\Property(property="code", type="string", example="SHOP10", description="Voucher code"),
 *             @OA\Property(property="title", type="string", example="Off $10", description="Voucher title")
 *         ),
 *         @OA\Property(property="selectedSelfCollectionAddress", type="integer", nullable=true, example=null, description="Selected pickup point ID"),
 *         @OA\Property(property="shippingFee", type="number", format="float", example=5.00, description="Shipping fee"),
 *         @OA\Property(property="addMoreForFreeShipping", type="number", format="float", example=20.00, description="Amount to add for free shipping")
 *     ),
 *     @OA\Property(
 *         property="items",
 *         type="array",
 *         description="Cart items from this seller",
 *         @OA\Items(
 *             type="object",
 *             @OA\Property(property="productId", type="integer", example=123),
 *             @OA\Property(property="name", type="string", example="Rice Bowl"),
 *             @OA\Property(property="slug", type="string", example="rice-bowl"),
 *             @OA\Property(property="variantId", type="integer", example=456),
 *             @OA\Property(property="variation", type="string", example="Red-Small"),
 *             @OA\Property(property="quantity", type="integer", example=2),
 *             @OA\Property(property="price", type="number", format="float", example=50.00),
 *             @OA\Property(property="thumbnailImg", type="string", example="https://example.com/image.jpg"),
 *             @OA\Property(property="isSelected", type="boolean", example=false)
 *         )
 *     )
 * )
 */

/**
 * @OA\Schema(
 *     schema="CartDataWithPricing",
 *     type="object",
 *     title="Cart Data With Pricing",
 *     description="Cart data grouped by seller with pricing information",
 *     @OA\Property(
 *         property="sellers",
 *         type="object",
 *         description="Cart items grouped by seller ID",
 *         additionalProperties=@OA\Property(
 *             type="object",
 *             @OA\Property(
 *                 property="items",
 *                 type="array",
 *                 description="Array of cart items from this seller",
 *                 @OA\Items(
 *                     type="object",
 *                     @OA\Property(property="key", type="string", example="Red-Small123", description="Unique key for cart item (variation + productId)"),
 *                     @OA\Property(property="productId", type="integer", example=123, description="Product ID"),
 *                     @OA\Property(property="name", type="string", example="Rice Bowl", description="Product name"),
 *                     @OA\Property(property="slug", type="string", example="rice-bowl", description="Product slug"),
 *                     @OA\Property(property="variantId", type="integer", example=456, description="Variant ID"),
 *                     @OA\Property(property="variation", type="string", example="Red-Small", description="Variation string"),
 *                     @OA\Property(property="quantity", type="integer", example=2, description="Quantity in cart"),
 *                     @OA\Property(property="price", type="number", format="float", example=50.00, description="Total price for this item (quantity * unit price)"),
 *                     @OA\Property(property="thumbnailImg", type="string", example="https://example.com/image.jpg", description="Product thumbnail URL"),
 *                     @OA\Property(property="isSelected", type="boolean", example=true, description="Whether this item is selected")
 *                 )
 *             ),
 *             @OA\Property(
 *                 property="info",
 *                 type="object",
 *                 description="Seller and shipping information",
 *                 @OA\Property(property="name", type="string", example="Shop Name", description="Shop name"),
 *                 @OA\Property(property="sellerId", type="integer", example=6166, description="Seller user ID"),
 *                 @OA\Property(property="shopId", type="integer", example=100, description="Shop ID"),
 *                 @OA\Property(property="shopSlug", type="string", example="shop-name", description="Shop slug"),
 *                 @OA\Property(property="orderType", type="integer", example=1, description="Order type (1=delivery, 2+=self-collection). When >= 2, address field will be populated with user addresses"),
 *                 @OA\Property(
 *                     property="address",
 *                     type="array",
 *                     description="User addresses for self-collection (populated when orderType >= 2)",
 *                     @OA\Items(
 *                         type="object",
 *                         @OA\Property(property="id", type="integer", example=1, description="Address ID"),
 *                         @OA\Property(property="user_id", type="integer", example=123, description="User ID"),
 *                         @OA\Property(property="title", type="string", example="Home", description="Address title"),
 *                         @OA\Property(property="address", type="string", example="123 Main Street", description="Street address"),
 *                         @OA\Property(property="country", type="string", nullable=true, example="Singapore", description="Country name"),
 *                         @OA\Property(property="city", type="string", nullable=true, example="Singapore", description="City name"),
 *                         @OA\Property(property="country_id", type="integer", nullable=true, example=1, description="Country ID"),
 *                         @OA\Property(property="city_id", type="integer", nullable=true, example=1, description="City ID"),
 *                         @OA\Property(property="postal_code", type="string", nullable=true, example="123456", description="Postal code"),
 *                         @OA\Property(property="phone", type="string", nullable=true, example="+6512345678", description="Phone number"),
 *                         @OA\Property(property="set_default", type="integer", example=1, description="Is default address (1=yes, 0=no)")
 *                     )
 *                 ),
 *                 @OA\Property(property="coupons", type="array", @OA\Items(ref="#/components/schemas/Coupon"), description="Available seller coupons"),
 *                 @OA\Property(
 *                     property="selectedVoucher",
 *                     type="object",
 *                     nullable=true,
 *                     description="Selected voucher information for this seller",
 *                     @OA\Property(property="discount", type="string", example="10.00", description="Discount amount"),
 *                     @OA\Property(property="code", type="string", example="SHOP10", description="Voucher code"),
 *                     @OA\Property(property="title", type="string", example="Off $10", description="Voucher title")
 *                 ),
 *                 @OA\Property(property="selectedSelfCollectionAddress", type="integer", nullable=true, example=10, description="Selected pickup point ID"),
 *                 @OA\Property(property="totalItems", type="integer", example=5, description="Total number of items in cart"),
 *                 @OA\Property(property="shippingFee", type="number", format="float", example=5.00, description="Shipping fee for this seller (0 if orderType is self-collection)"),
 *                 @OA\Property(property="addMoreForFreeShipping", type="number", format="float", example=20.00, description="Amount needed to add for free shipping (0 if orderType is self-collection)")
 *             )
 *         )
 *     ),
 *     @OA\Property(
 *         property="info",
 *         type="object",
 *         description="Overall cart summary information",
 *         @OA\Property(property="totalItems", type="integer", example=5, description="Total number of items in entire cart"),
 *         @OA\Property(property="subtotalPrice", type="number", format="float", example=150.50, description="Subtotal price before discounts and shipping"),
 *         @OA\Property(property="totalPrice", type="number", format="float", example=150.50, description="Grand total after normal discounts and shipping"),
 *         @OA\Property(property="grandTotalPrice", type="number", format="float", example=150.50, description="Grand total final price"),
 *         @OA\Property(property="totalDiscount", type="number", format="float", example=20.00, description="Total discount from applied coupons"),
 *         @OA\Property(property="totalShippingFee", type="number", format="float", example=5.00, description="Total shipping fee for all sellers"),
 *         @OA\Property(
 *             property="ysgCoupons",
 *             type="array",
 *             description="Available YSG admin coupons",
 *             @OA\Items(ref="#/components/schemas/Coupon")
 *         ),
 *         @OA\Property(
 *             property="selected_voucher",
 *             type="object",
 *             nullable=true,
 *             description="Selected YSG voucher information from available ysgCoupons",
 *             @OA\Property(property="discount", type="string", example="20.00", description="Discount amount"),
 *             @OA\Property(property="code", type="string", example="YSG2024", description="Voucher code"),
 *             @OA\Property(property="title", type="string", example="Off $20", description="Voucher title")
 *         )
 *     )
 * )
 */
class CartListResource extends JsonResource
{
    public function toArray($request)
    {
        return $this->resource;
    }
}


