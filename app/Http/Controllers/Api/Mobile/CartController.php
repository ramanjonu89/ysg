<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Api\Controller;
use App\Http\Resources\Mobile\CartListResource;
use App\Services\Api\Mobile\CartService;
use App\Services\CartService as WebCartService;
use Illuminate\Http\Request;
use App\Services\CouponService;
use App\Models\Coupon;
use App\ObjectModels\Coupon as CouponModel;
use App\Http\Resources\Mobile\CouponResource;
use App\Models\Shop;
use App\Services\UserService;

/**
 * @OA\Tag(
 *     name="Cart",
 *     description="Mobile API endpoints for cart"
 * )
 * 
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
 *         @OA\Property(property="selectedVoucher", type="string", nullable=true, example=null, description="Selected voucher code"),
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
class CartController extends Controller
{
    protected $cartService;
    protected $webCartService;
    protected $couponService;
    public function __construct(CartService $cartService, WebCartService $webCartService, CouponService $couponService)
    {
        $this->cartService = $cartService;
        $this->webCartService = $webCartService;
        $this->couponService = $couponService;
    }

    /**
     * @OA\Post(
     *     path="/api/mobile/v1/cart",
     *     operationId="mobileCartGetCart",
     *     tags={"Cart"},
     *     summary="Get cart data with pricing and vouchers (POST method)",
     *     description="Returns the current user's cart data via POST method with selected items, applied vouchers, self collection addresses, and calculated totals (price, discount, shipping)",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="selected_items",
     *                 type="array",
     *                 description="Array of selected cart item IDs or product variant combinations",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="product_id", type="integer", example=123),
     *                     @OA\Property(property="variation", type="string", nullable=true, example="4.5in Rice Bowl")
     *                 ),
     *                 example={{"product_id": 123, "variation": "4.5in Rice Bowl"}, {"product_id": 789, "variation": null}}
     *             ),
     *             @OA\Property(
     *                 property="selected_vouchers",
     *                 type="array",
     *                 description="Array of voucher codes to apply to the cart",
     *                 @OA\Items(type="string"),
     *                 example={"VOUCHER123", "VOUCHER456"}
     *             ),
     *             @OA\Property(
     *                 property="selected_self_collection_addresses",
     *                 type="object",
     *                 description="Object with seller_id as key and pickup point ID as value for self-collection orders",
     *                 example={"6166": 10, "1544": 20}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cart data retrieved successfully with grouped items by seller and calculated totals",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Cart Data"),
     *             @OA\Property(property="data", ref="#/components/schemas/CartDataWithPricing")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request - Invalid coupon, expired coupon, product out of stock, or validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="The coupon code VOUCHER123 is invalid!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - Invalid or missing token",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Can not get cart data === Error details")
     *         )
     *     )
     * )
     */
    public function getCart(Request $request)
    {
        try {
            $selectedItems = $request->input('selected_items', []);
            $selectedVouchers = $request->input('selected_vouchers', []);
            $selectedSelfCollectionAddresses = $request->input('selected_self_collection_addresses', []);

            $buildCartResult = $this->cartService->buildTempCartFromRequest($selectedItems, $selectedVouchers, $selectedSelfCollectionAddresses);
            if (!$buildCartResult['success']) {
                return $this->errorResponse($buildCartResult['message'], 400);
            }

            $cart = $this->webCartService->getCart();
            $tempCart = $this->webCartService->getTempCart();

            $data = $cart->toGroupedArray($selectedItems, $selectedVouchers, $selectedSelfCollectionAddresses);

            $couponsYsg = $this->couponService->getExtractUsableCouponByAdmin();
            $data['info']['subtotalPrice'] = $tempCart->getSubtotal() - $tempCart->getSellersDiscount() + $tempCart->getShippingFee();
            $data['info']['totalPrice'] = $tempCart->getGrandTotalAfterNormalCoupon();
            $data['info']['grandTotalPrice'] = $tempCart->getGrandTotal();
            $data['info']['totalDiscount'] = $tempCart->getCouponDiscount();
            $data['info']['totalShippingFee'] = $tempCart->getShippingFee();
            $data['info']['ysgCoupons'] = CouponResource::collection($couponsYsg);
            
            $data['info']['selected_voucher'] = $tempCart->getSelectedCoupon($couponsYsg, $selectedVouchers);
            $tempCartItems = $tempCart->getItemsBySellerGroup();

            foreach ($data['sellers'] as $sellerId => $seller) {
                $data['sellers'][$sellerId]['info']['shippingFee'] = 0;
                $data['sellers'][$sellerId]['info']['addMoreForFreeShipping'] = 0;

                if ($seller['info']['orderType'] != Shop::ORDER_TYPE_SELF_COLLECTION && isset($tempCartItems[$sellerId])) {
                    $data['sellers'][$sellerId]['info']['shippingFee'] = $tempCart->getShippingFeeByShopId($seller['info']['shopIdForShippingFeeCalculation']);
                    $data['sellers'][$sellerId]['info']['addMoreForFreeShipping'] = $tempCart->getSubTotalRemainsForFreeShippingByShopId($seller['info']['shopIdForShippingFeeCalculation']);
                }
            }
            
            return $this->successResponse('Cart Data', $data);
        } catch (\Exception $e) {
            return $this->errorResponse('Can not get cart data === ' . $e->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/mobile/v1/cart/add-item",
     *     operationId="mobileCartAddItem",
     *     tags={"Cart"},
     *     summary="Add product to cart",
     *     description="Adds a product to the current user's cart",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"product_id"},
     *             @OA\Property(property="product_id", type="integer", example=123),
     *             @OA\Property(property="variant_id", type="integer", nullable=true, example=456),
     *             @OA\Property(property="variation", type="string", nullable=true, example="Red, Small"),
     *             @OA\Property(property="quantity", type="integer", example=1, default=1, minimum=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Add Item Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Add Item Success"),
     *             @OA\Property(property="data", type="object", example={})
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="This product is out of stock")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - Invalid or missing token",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Can not add item")
     *         )
     *     )
     * )
     */
    public function addItem(Request $request)
    {
        try {
            $productId = $request->input('product_id');
            $variantId = $request->input('variant_id');
            $quantity = $request->input('quantity', 1);
            $result = $this->cartService->addProduct($productId, $quantity, $variantId);

            if ($result['status']) {
                return $this->successResponse('Add Item Success');
            } else {
                return $this->errorResponse($result['message'], 400);
            }
        } catch (\Exception $e) {
            return $this->errorResponse('Can not add item', 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/mobile/v1/cart/remove-item",
     *     operationId="mobileCartRemoveItem",
     *     tags={"Cart"},
     *     summary="Remove product from cart",
     *     description="Removes a product (or variant) from the current user's cart",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"product_id"},
     *             @OA\Property(property="product_id", type="integer", example=123),
     *             @OA\Property(property="variant_id", type="integer", nullable=true, example=456)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Remove Item Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Remove Item Success"),
     *             @OA\Property(property="data", type="object", example={})
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Product not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - Invalid or missing token",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Can not remove item")
     *         )
     *     )
     * )
     */
    public function removeItem(Request $request)
    {
        try {
            $productId = $request->input('product_id');
            $variantId = $request->input('variant_id');

            $result = $this->cartService->removeProduct($productId, $variantId);

            if ($result['status']) {
                return $this->successResponse('Remove Item Success');
            } else {
                return $this->errorResponse($result['message'], 400);
            }

        } catch (\Exception $e) {
            return $this->errorResponse('Can not remove item === ' . $e->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/mobile/v1/cart/update-quantity",
     *     operationId="mobileCartUpdateQuantity",
     *     tags={"Cart"},
     *     summary="Update product quantity in cart",
     *     description="Updates the quantity of a product (or variant) in the current user's cart",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"product_id", "quantity"},
     *             @OA\Property(property="product_id", type="integer", example=123, description="Product ID"),
     *             @OA\Property(property="variant_id", type="integer", nullable=true, example=456, description="Product variant ID (optional)"),
     *             @OA\Property(property="quantity", type="integer", example=2, minimum=0, description="New quantity for the product. Set to 0 to remove item from cart")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Update Quantity Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Update Quantity Success"),
     *             @OA\Property(property="data", type="object", example={})
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request - Invalid quantity, product not found, or out of stock",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Quantity must be greater than or equal to 0")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - Invalid or missing token",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Can not update quantity")
     *         )
     *     )
     * )
     */
    public function updateQuantity(Request $request)
    {
        try {
            $productId = $request->input('product_id');
            $variantId = $request->input('variant_id');
            $quantity = $request->input('quantity');

            if ($quantity < 0) {
                return $this->errorResponse('Quantity must be greater than or equal to 0', 400);
            }

            $result = $this->cartService->updateQuantity($productId, $quantity, $variantId);

            if ($result['status']) {
                return $this->successResponse('Update Quantity Success');
            } else {
                return $this->errorResponse($result['message'], 400);
            }
        } catch (\Exception $e) {
            return $this->errorResponse('Can not update quantity === ' . $e->getMessage(), 500);
        }
    }
}
