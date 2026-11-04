<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Api\Controller;
use App\Http\Resources\Mobile\OrderResource;
use App\Models\Order;
use App\Services\Api\Mobile\CartService as MobileCartService;
use App\Services\CartService;
use App\Services\CheckoutService;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Checkout",
 *     description="Mobile API endpoints for checkout"
 * )
 */
class CheckoutController extends Controller
{
    protected $cartService;
    protected $orderService;
    protected $mobileCartService;
    protected $checkoutService;

    public function __construct(
        CartService $cartService,
        OrderService $orderService,
        MobileCartService $mobileCartService,
        CheckoutService $checkoutService
    ) {
        $this->cartService = $cartService;
        $this->orderService = $orderService;
        $this->mobileCartService = $mobileCartService;
        $this->checkoutService = $checkoutService;
    }

    /**
     * @OA\Post(
     *     path="/api/mobile/v1/checkout",
     *     operationId="mobileCheckout",
     *     tags={"Checkout"},
     *     summary="Checkout and create order",
     *     description="Create order from selected cart items with applied vouchers. Requires authentication.",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"payment_option", "selected_items"},
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
     *             ),
     *             @OA\Property(
     *                 property="address_id",
     *                 type="integer",
     *                 nullable=true,
     *                 description="User's address ID for shipping. Required only if cart contains items with home delivery. Not required if all items are self-collection.",
     *                 example=123
     *             ),
     *             @OA\Property(
     *                 property="payment_option",
     *                 type="string",
     *                 description="Payment method",
     *                 enum={"cash_on_delivery", "stripe", "octifi", "cybersource", "nets"},
     *                 example="cash_on_delivery"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Order created successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Order"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request - Cart validation failed or missing required fields",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="One or more of the products in your cart is either out or low in stock. Please check your cart to update your order.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="address_id",
     *                     type="array",
     *                     @OA\Items(type="string", example="The address id field is required.")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Failed to create order")
     *         )
     *     )
     * )
     */
    public function checkout(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'selected_items' => 'required|array|min:1',
                'selected_items.*.product_id' => 'required|integer',
                'selected_items.*.variation' => 'nullable|string',
                'selected_vouchers' => 'nullable|array',
                'selected_vouchers.*' => 'string',
                'selected_self_collection_addresses' => 'nullable|array',
                'address_id' => 'nullable|integer|exists:addresses,id',
                'payment_option' => 'required|string|in:' . implode(',', [
                    Order::PAYMENT_TYPE_CYBERSOURCE,
                ])
            ]);

            if ($validator->fails()) {
                return $this->errorResponse('Validation failed', $validator->errors(), 422);
            }

            $selectedItems = $request->input('selected_items', []);
            $selectedVouchers = $request->input('selected_vouchers', []);
            $selectedSelfCollectionAddresses = $request->input('selected_self_collection_addresses', []);

            $buildCartResult = $this->buildTempCartFromRequest($selectedItems, $selectedVouchers, $selectedSelfCollectionAddresses);
            if ($buildCartResult !== true) {
                return $buildCartResult;
            }

            $tempCart = $this->cartService->getTempCart();

            $requiresHomeDelivery = $this->checkIfRequiresHomeDelivery($tempCart);
            if ($requiresHomeDelivery && !$request->has('address_id')) {
                return $this->errorResponse('Address is required for home delivery', [], 422);
            }

            if ($requiresHomeDelivery && $request->address_id) {
                $user = Auth::user();
                $address = null;
                if ($user && method_exists($user, 'addresses')) {
                    $address = $user->addresses()->find($request->address_id);
                }
                if (!$address) {
                    return $this->errorResponse('Address not found or does not belong to you', [], 422);
                }
            }

            if (!method_exists($tempCart, 'validate') || !$tempCart->validate()) {
                return $this->errorResponse(
                    'One or more of the products in your cart is either out or low in stock. Please check your cart to update your order.',
                    [],
                    400
                );
            }

            if ($tempCart->getTotalItems() == 0) {
                return $this->errorResponse('Your cart is empty', [], 400);
            }

            $order = $this->createOrder($request);

            if (!$order) {
                return $this->errorResponse('Failed to create order', [], 500);
            }

            Log::channel('order')->info('Mobile checkout - Order created', [
                'order_id' => $order->id,
                'user_id' => Auth::id(),
                'payment_type' => $request->payment_option,
                'grand_total' => $order->grand_total
            ]);

            return $this->successResponse('Order created successfully', new OrderResource($order));

        } catch (\Exception $e) {
            Log::error('Mobile checkout error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->errorResponse('Failed to create order: ' . $e->getMessage(), [], 500);
        }
    }

    protected function buildTempCartFromRequest($selectedItems, $selectedVouchers, $selectedSelfCollectionAddresses)
    {
        $result = $this->mobileCartService->buildTempCartFromRequest($selectedItems, $selectedVouchers, $selectedSelfCollectionAddresses);
        
        if (!$result['success']) {
            return $this->errorResponse($result['message'], [], 400);
        }

        return true;
    }

    protected function checkIfRequiresHomeDelivery($tempCart): bool
    {
        if (!method_exists($tempCart, 'getItems')) {
            return true;
        }

        $items = $tempCart->getItems();
        
        if (empty($items)) {
            return false;
        }

        foreach ($items as $item) {
            if (!method_exists($item, 'getShopId')) {
                continue;
            }

            $shopId = $item->getShopId();
            
            if (method_exists($tempCart, 'getShopOrderType')) {
                $orderType = $tempCart->getShopOrderType($shopId);
                if ($orderType != \App\Models\Shop::ORDER_TYPE_SELF_COLLECTION) {
                    return true;
                }
            } else {
                return true;
            }
        }

        return false;
    }

    protected function createOrder(Request $request)
    {
        $cart = $this->cartService->getCart();
        $tempCart = $this->cartService->getTempCart();

        $order = new Order;
        $order->user_id = Auth::id();
        
        $user = Auth::user();
        $address = null;
        
        if ($request->has('address_id') && $request->address_id) {
            if ($user && method_exists($user, 'addresses')) {
                $address = $user->addresses()->find($request->address_id);
            }
            if (!$address) {
                throw new \Exception('Address not found');
            }
        }

        $order->shipping_address = $this->getDeliveryAddress($address);
        $order->payment_type = $request->payment_option ?? Order::PAYMENT_TYPE_NONE;
        $order->delivery_viewed = '0';
        $order->payment_status_viewed = '0';
        $order->code = date('Ymd-His') . rand(10, 99);
        $order->coupon_code = method_exists($tempCart, 'getYsgCoupon') ? $tempCart->getYsgCoupon() : '';
        $order->date = strtotime('now');
        $order->platform = 'mobile';
        
        if ($order->save()) {
            foreach ($tempCart->getItems() as $key => $cartItem) {
                $product = \App\Models\Product::find($cartItem->getId());
                if ($product->getStock($cartItem->getVariation()) == 0) {
                    Log::channel('order')->info($product->name . ' (id: ' . $product->id . ') is out of stock for order ' . $order->id);
                    $cart->removeItem($cartItem->getId());
                    $tempCart->removeItem($cartItem->getId());
                    continue;
                }
                
                $order_detail = new \App\Models\OrderDetail;
                $order_detail->rsp_price = $product->unit_price;

                if ('' !== $cartItem->getVariation()) {
                    $variant = $product->getVariant($cartItem->getVariation());
                    $variant->qty -= $cartItem->getQuantity();
                    $variant->save();
                    $order_detail->rsp_price = $variant->price;
                } else {
                    $product->current_stock -= $cartItem->getQuantity();
                }
                $product->num_of_sale++;
                $product->save();

                $order_detail->order_id = $order->id;
                $order_detail->seller_id = $cartItem->getSellerId();
                $order_detail->product_id = $cartItem->getId();
                $order_detail->product_name = $cartItem->getName();
                $order_detail->variation = $cartItem->getVariation();
                $order_detail->discount = $product->getDiscount($cartItem->getVariation());
                $order_detail->discount_type = $product->getDiscountType($cartItem->getVariation());
                $order_detail->price = $cartItem->getTotalPrice();
                $order_detail->tax = $cartItem->getTax() * $cartItem->getQuantity();
                
                if (method_exists($tempCart, 'getShopOrderType') && $tempCart->getShopOrderType($cartItem->getShopId()) == \App\Models\Shop::ORDER_TYPE_SELF_COLLECTION) {
                    $order_detail->shipping_type = 'self_collection';
                    if (method_exists($tempCart, 'getShopAddress')) {
                        $order_detail->collection_address = $tempCart->getShopAddress($cartItem->getShopId());
                    }
                } else {
                    $order_detail->shipping_type = 'home_delivery';
                }
                
                $order_detail->product_referral_code = null;
                $order_detail->shipping_cost = 0;
                $order_detail->quantity = $cartItem->getQuantity();
                $order_detail->pickup_point_id = null;
                $order_detail->save();
            }
            
            $order->total_shipping_cost = $tempCart->getShippingFee();
            $order->grand_total = $tempCart->getGrandTotal();
            
            if (method_exists($tempCart, 'hasCoupons') && $tempCart->hasCoupons()) {
                $order->coupon_discount = $tempCart->getCouponDiscount();
                $this->orderService->couponUsage($tempCart, $order);
            }
            
            $order->save();
            $this->orderService->splitOrder($tempCart, $order);

            return $order;
        }
        
        return false;
    }

    private function getDeliveryAddress($address)
    {
        $tempCart = $this->cartService->getTempCart();
        $user = Auth::user();

        $data = [
            'name'          => $user->name,
            'email'         => $user->email,
            'checkout_type' => 'logged',
        ];

        if (method_exists($tempCart, 'isShowSelectAddress') && !$tempCart->isShowSelectAddress()) {
            $defaultAddress = method_exists($user, 'getDefaultAddress') ? $user->getDefaultAddress() : null;
            $data['address']     = 'Self collection';
            $data['country']     = $defaultAddress ? $defaultAddress->country : 'Singapore';
            $data['city']        = $defaultAddress ? $defaultAddress->city : 'Singapore';
            $data['postal_code'] = $defaultAddress ? $defaultAddress->postal_code : '648167';
            $data['phone']       = $user->phone ?? 'N/A';
        } else {
            $data['address']     = $address->address;
            $data['country']     = $address->country;
            $data['city']        = $address->city;
            $data['postal_code'] = $address->postal_code;
            $data['phone']       = $address->phone;
            $data['title']       = $address->title;
        }

        return json_encode($data);
    }

    public function paymentGateway(Request $request, $paymentType, $orderId)
    {
        try {
            $order = Order::find($orderId);

            if (!$order) {
                return response()->json([
                    'error' => true,
                    'message' => 'Order not found'
                ], 404);
            }

            if ($order->payment_status === Order::PAYMENT_STATUS_PAID) {
                return response()->json([
                    'error' => true,
                    'message' => 'Order has already been paid'
                ], 400);
            }

            $user = Auth::user();
            if (!$user || $order->user_id !== $user->id) {
                return response()->json([
                    'error' => true,
                    'message' => 'Unauthorized access to this order'
                ], 403);
            }

            $this->checkoutService->initGateway($paymentType);
            $formData = $this->checkoutService->getFormData($user, $order);

            return view('frontend.payment.mobile_cybersource', ['formData' => $formData]);

        } catch (\Exception $e) {
            Log::error('Mobile payment gateway error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'order_id' => $orderId,
                'payment_type' => $paymentType,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => true,
                'message' => 'Failed to process payment: ' . $e->getMessage()
            ], 500);
        }
    }

    public function orderConfirmed($orderId)
    {
        return view('frontend.payment.mobile_order_confirmed', ['orderId' => $orderId]);
    }

    public function orderFailed($orderId)
    {
        return view('frontend.payment.mobile_order_failed', ['orderId' => $orderId]);
    }
}

