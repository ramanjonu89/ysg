<?php
namespace App\Http\Controllers;

use App\Models\Address;
use App\BusinessSetting;
use App\Models\Category;
use App\Models\Coupon;
use App\ObjectModels\Coupon as CouponModel;
use App\Http\Controllers\AffiliateController;
use App\Http\Controllers\ClubPointController;
use App\Http\Controllers\PaytmController;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Shop;
use App\Services\Api\Outbound\OctifiService;
use App\Services\BusinessSettingService;
use App\Services\CartService;
use App\Services\CheckoutService;
use App\Services\CouponService;
use App\Services\OrderService;
use App\Services\ShopService;
use App\Services\UserService;
use Auth;
use CoreComponentRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cookie;
use Mail;
use PDF;
use Session;
use Throwable;
use App\Repositories\CartRepository;
use App\User;
class CheckoutController extends Controller
{
    protected $service;
    protected $orderService;
    protected $cartService;
    protected $couponService;
    protected $orderController;
    protected $businessSettingService;

    public function __construct(
        CheckoutService $service,
        OrderService $orderService,
        CartService $cartService,
        CouponService $couponService,
        OrderController $orderController,
        BusinessSettingService $businessSettingService
    )
    {
        $this->service = $service;
        $this->orderService = $orderService;
        $this->cartService = $cartService;
        $this->couponService = $couponService;
        $this->orderController = $orderController;
        $this->businessSettingService = $businessSettingService;
    }

    protected function checkoutOctifi(Request $request, $order) {
        $request->session()->put('payment_type', Order::PAYMENT_TYPE_OCTIFI);
        $user = $request->user();
        $this->service->initGateway(Order::PAYMENT_TYPE_OCTIFI);
        $redirectUrl = $this->service->getRedirectLink($user, $order);
        if ($redirectUrl != '') {
            return redirect($redirectUrl);
        } else {
            flash(__('These is an error with the LatitudePay payment. Please try again or use another payment method.'))->warning();
        }
    }

    protected function checkoutCybersource(Request $request, $order) {
        $request->session()->put('payment_type', Order::PAYMENT_TYPE_CYBERSOURCE);
        $user = $request->user();
        $this->service->initGateway(Order::PAYMENT_TYPE_CYBERSOURCE);
        $formData = $this->service->getFormData($user, $order);
        return view('frontend.payment.cybersource', ['formData' => $formData]);
    }

    protected function checkoutNets(Request $request, $order) {
        $request->session()->put('payment_type', Order::PAYMENT_TYPE_NETS);
        $user = $request->user();
        $this->service->initGateway(Order::PAYMENT_TYPE_NETS);
        $formData = $this->service->getFormData($user, $order);
        $tempCart = $this->cartService->getTempCart();

        return view('frontend.payment.nets', ['formData' => $formData, 'tempCart' => $tempCart]);
    }

    protected function checkoutNetsTest(Request $request) {
        $formData = [];
        return view('frontend.payment.nets_test', ['formData' => $formData]);
    }

    //check the selected payment gateway and redirect to that controller accordingly
    public function checkout(Request $request)
    {
        $tempCart = $this->cartService->getTempCart();

        if (!$tempCart->validate()) {
            flash(__("One or more of the products in your cart is either out or low in stock. Please check your cart to update your order."))->warning();
            return redirect()->route('cart');
        }

        // comment this feature cause don't understand the meaning of remove order here...
//        if ($orderId = $request->session()->get('order_id')) {
//            $this->orderService->remove($orderId);
//        }

        //Stupid old code:
        $order = $this->orderController->store($request);
        $request->session()->put('order_id', $order->id);

        if ($order) {
            try {
                if ($order->grand_total == 0) {
                    return $this->checkout_done($order->id, []);
                }

                if ($request->payment_option == Order::PAYMENT_TYPE_OCTIFI) {
                    return $this->checkoutOctifi($request, $order);
                } else if ($request->payment_option == Order::PAYMENT_TYPE_CYBERSOURCE) {
                    return $this->checkoutCybersource($request, $order);
                } else if ($request->payment_option == Order::PAYMENT_TYPE_NETS) {
                    return $this->checkoutNets($request, $order);
                } else if($request->payment_option == Order::PAYMENT_TYPE_CASH_ON_DELIVERY) {
                    return $this->checkout_done($order->id, []);
                }
                else if ($request->payment_option == Order::PAYMENT_TYPE_STRIPE) {
                    $request->session()->put('payment_type', 'cart_payment');
                    $stripe = new StripePaymentController;
                    return $stripe->stripe();
                }
                flash(__('Please select a Payment Option.'))->warning();
            } catch (Throwable $e) {
                flash(__('There is an error with your payment. Please try again. Should the error persists, please contact cs@ysg.com.sg or use our Live Chat Function for assistance.'))->error();
                Log::debug($e->getMessage());
            }
        } else {
            flash(__('Have error with checkout process, can not find order, please try again later.'))->error();
        }
        return back();
    }

    //check the selected payment gateway and redirect to that controller accordingly
    public function successCallback($paymentMethod, Request $request)
    {
        try {
            $this->service->initGateway($paymentMethod);
            $orderId = $this->service->extractOrderId($request->all());
            $result = $this->service->successCallback($request->all());
            if ($result && $result['status'] == true) {
                return $this->checkout_done($orderId, $result['details']);
            } else {
                throw new \Exception('paymentGatewayService->successCallback failed');
            }
        } catch (Throwable $e) {
            Log::channel('payment')
                ->error('Error App\Http\Controllers\CheckoutController@successCallback',
                    [
                        'error' => $e->getMessage(),
                        'paymentMethod' => $paymentMethod,
                        'request' => $request->all()
                    ]);
            flash(__('There is an error with your payment. Please contact cs@ysg.com.sg or use our Live Chat Function for assistance.'))->warning();
            return redirect()->route('home');
        }
    }

    //check the selected payment gateway and redirect to that controller accordingly
    public function failedCallback($paymentMethod, Request $request): \Illuminate\Http\RedirectResponse
    {
        flash(__('Your payment has successfully been cancelled.'))->warning();
        return redirect()->route('home');
    }

    //redirects to this method after a successfull checkout
    public function checkout_done($order_id, $payment_details, $payment_status = Order::PAYMENT_STATUS_PAID)
    {
        $this->cartService->removeCartFromTempCart();
        Session::forget('order_id');
        Session::forget('payment_type');
        Session::forget('delivery_info');
        Session::forget('coupon_id');
        Session::forget('coupon_discount');

        $order = $this->orderService->get($order_id);
        if ($order->platform == 'mobile') {
            $result = $this->service->processAfterPayment($order, $payment_details, $payment_status);
            // remove cart item from cart db
            $user = User::find($order->user_id);
            $cart = $user->carts->first();
            $cartRepository = app()->make(CartRepository::class);
            foreach ($order->orderDetails as $item) {
                $cartRepository->deleteCartItem($cart->id, $item->product_id, $item->variation);
            }
            
            if ($result) {
                return redirect()->route('api.mobile.order.confirmed', $order->id);
            }
            return redirect()->route('api.mobile.order.failed', $order->id);
        }

        if ($order) {
            if (!$order->isPaid()) {
                $result = $this->service->processAfterPayment($order, $payment_details, $payment_status);
                if ($result) {
                    return view('frontend.order_confirmed', [
                        'order' => $order
                    ]);
                }
            } else {
                return view('frontend.order_confirmed', [
                    'order' => $order
                ]);
            }
        }

        flash(__('Payment process can not find order'))->error();
        return redirect()->route('home');
    }

//    public function get_shipping_info(Request $request)
//    {
//        $cart = $this->cartService->getCart();
//        if (!$cart->validate()) {
//            flash(__("One or more of the products in your cart is either out or low in stock. Please check your cart to update your order."))->warning();
//            return redirect()->route('cart');
//        }
//
//        if ($cart->getTotalItems() > 0) {
//            return view('frontend.shipping_info');
//        }
//        flash(__('Your cart is empty'))->success();
//
//        return back();
//    }

    public function store_shipping_info(Request $request)
    {
        $cart = $this->cartService->getCart();

        if (!$cart->validate()) {
            flash(__("One or more of the products in your cart is either out or low in stock. Please check your cart to update your order."))->warning();
            return redirect()->route('cart');
        }

        if (Auth::check()) {
            if ($request->address_id == null) {
                flash(__("Please input your shipping address!"))->warning();
                return back();
            }
            $address = Address::findOrFail($request->address_id);
            $data['name'] = Auth::user()->name;
            $data['email'] = Auth::user()->email;
            $data['address'] = $address->address;
            $data['country'] = $address->country;
            $data['city'] = $address->city;
            $data['postal_code'] = $address->postal_code;
            $data['phone'] = $address->phone;
            $data['checkout_type'] = $request->checkout_type;
        } else {
            $data['name'] = $request->name;
            $data['email'] = $request->email;
            $data['address'] = $request->address;
            $data['country'] = $request->country;
            $data['city'] = $request->city;
            $data['postal_code'] = $request->postal_code;
            $data['phone'] = $request->phone;
            $data['checkout_type'] = $request->checkout_type;
        }
        $request->session()->put('shipping_info', $data);

        return redirect()->route('checkout.delivery_info');
    }

    public function deliveryInfo(ShopService $shopService, CouponService $couponService)
    {
        $cart = $this->cartService->getCart();

        if (!$cart->validate()) {
            flash(__("One or more of the products in your cart is either out or low in stock. Please check your cart to update your order."))->warning();
            return redirect()->route('cart');
        }

        $sellers = $cart->getItemsBySellerGroup();
        $coupons = [];
        foreach ($sellers as $seller) {
            $shopId = $seller['info']['shopId'];
            $shop = $shopService->get($shopId);
            $productIds = array_map(function ($item) {
                return $item->getId();
            }, $seller['items']);

            $coupons[$shopId] = $couponService->extractUsableCoupon($shop->sellerCoupons, $productIds);
        }

        return view('frontend.delivery_info', compact(['cart', 'coupons']));
    }

    public function store_delivery_info_old(Request $request)
    {
        if (Auth::check()) {
            if ($request->address_id == null) {
                flash(__("Please input your shipping address!"))->warning();

                return back();
            }
            $address = Address::findOrFail($request->address_id);
            $data['name'] = Auth::user()->name;
            $data['email'] = Auth::user()->email;
            $data['address'] = $address->address;
            $data['country'] = $address->country;
            $data['city'] = $address->city;
            $data['postal_code'] = $address->postal_code;
            $data['phone'] = $address->phone;
            $data['checkout_type'] = $request->checkout_type;
        } else {
            $data['name'] = $request->name;
            $data['email'] = $request->email;
            $data['address'] = $request->address;
            $data['country'] = $request->country;
            $data['city'] = $request->city;
            $data['postal_code'] = $request->postal_code;
            $data['phone'] = $request->phone;
            $data['checkout_type'] = $request->checkout_type;
        }
        $shipping_info = $data;
        $request->session()->put('shipping_info', $shipping_info);
        $subtotal = 0;
        $tax = 0;
        $shipping = 0;
        foreach (Session::get('cart') as $key => $cartItem) {
            $subtotal += $cartItem['price'] * $cartItem['quantity'];
            $tax += $cartItem['tax'] * $cartItem['quantity'];
            $shipping += $cartItem['shipping'] * $cartItem['quantity'];
        }
        $total = $subtotal + $tax + $shipping;
        if (Session::has('coupon_discount')) {
            $total -= Session::get('coupon_discount');
        }
        //return view('frontend.delivery_info');
        // return view('frontend.payment_select', compact('total'));
        if (Session::has('cart') && count(Session::get('cart')) > 0) {
            $cart = $request->session()->get('cart', collect([]));
            $cart = $cart->map(function($object, $key) use ($request) {
                if (\App\Product::find($object['id'])->added_by == 'admin') {
                    if ($request['shipping_type_admin'] == 'home_delivery') {
                        $object['shipping_type'] = 'home_delivery';
                    } else {
                        $object['shipping_type'] = 'pickup_point';
                        $object['pickup_point'] = $request->pickup_point_id_admin;
                    }
                } else {
                    if ($request['shipping_type_' . \App\Product::find($object['id'])->user_id] == 'home_delivery') {
                        $object['shipping_type'] = 'home_delivery';
                    } else {
                        $object['shipping_type'] = 'pickup_point';
                        $object['pickup_point'] = $request['pickup_point_id_' . \App\Product::find($object['id'])->user_id];
                    }
                }

                return $object;
            });
            $request->session()->put('cart', $cart);
            $cart = $cart->map(function($object, $key) use ($request) {
                $object['shipping'] = getShippingCost($key);

                return $object;
            });
            $request->session()->put('cart', $cart);
            $subtotal = 0;
            $tax = 0;
            $shipping = 0;
            foreach (Session::get('cart') as $key => $cartItem) {
                $subtotal += $cartItem['price'] * $cartItem['quantity'];
                $tax += $cartItem['tax'] * $cartItem['quantity'];
                $shipping += $cartItem['shipping'] * $cartItem['quantity'];
            }
            //hard code delivery cost as $2 less than $80 cart value
            $total = $subtotal + $shipping;
            if (Session::has('coupon_discount')) {
                $total -= Session::get('coupon_discount');
            }

            return view('frontend.delivery_info');
            //return view('frontend.payment_select', compact('total'));
        } else {
            flash(__('Your Cart was empty'))->warning();

            return redirect()->route('home');
        }
    }

    public function store_delivery_info(Request $request)
    {
        return redirect()->route('checkout.payment_info');
    }

    public function get_payment_info(Request $request)
    {
        $cart = $this->cartService->getCart();
        $tempCart = $this->cartService->getTempCart();
        $paymentStep = Payment::PAYMENT_STEP;
        if ($cart->getTotalItems() > 0) {
            $hasPrivateSaleProducts = false;
            $sellers = $tempCart->getItemsBySellerGroup();
            $userService = app()->make(UserService::class);
            foreach ($sellers as $userId => $cartItem) {
                $sellerUser = $userService->get($userId);
                if ($sellerUser->isPrivateSale()) {
                    $hasPrivateSaleProducts = true;
                    break;
                }
            }
            $total = $cart->getGrandTotal();
            $user_type = Auth::user()->user_type;
            $enabledCashPayment = $this->businessSettingService->cashPaymentEnabled($user_type);
            $couponsYsg = $this->couponService->getExtractUsableCouponByAdmin($cart);
            
            return view('frontend.payment_select', compact('total', 'enabledCashPayment', 'paymentStep', 'couponsYsg', 'cart', 'tempCart', 'hasPrivateSaleProducts'));
        } else {
            flash(__('Your Cart was empty'))->warning();

            return redirect()->route('home');
        }
    }

    public function apply_coupon_code(Request $request)
    {
        $coupon = $this->couponService->getFirstWithConditions(['code' => $request->code, 'is_active' => Coupon::STATUS_ACTIVE]);

        if ($coupon === null ) {
            flash(__('The coupon code is invalid!'))->warning();
            return back();
        }

        if ($coupon->isExpired()) {
            flash(__('The coupon code has expired.'))->warning();
            return back();
        }

        if ($coupon->inValid()) {
            flash(__('The coupon code is not valid yet.'))->warning();
            return back();
        }

        $couponModel = new CouponModel($coupon);
        $cart = $this->cartService->getCart();
        $tempCart = $this->cartService->getTempCart();
        $validation = $tempCart->validateCoupon($couponModel);

        if ($validation['valid'] === false) {
            flash($validation['message'])->warning();
            return back();
        }

        $tempCart->addCoupon($couponModel);
        flash(__('The coupon code has been successfully applied.'))->success();

        /*if ($coupon != null) {
            if (strtotime(date('d-m-Y')) >= $coupon->start_date && strtotime(date('d-m-Y')) <= $coupon->end_date) {
                if (CouponUsage::where('user_id', Auth::user()->id)->where('coupon_id', $coupon->id)->first() == null) {
                    $coupon_details = json_decode($coupon->details);
                    if ($coupon->type == 'cart_base') {
                        //lets check for max coupon limit
                        if (($coupon_details->max_coupon >= 0) && (isset($coupon_details->max_coupon))) {
                            $counofuse = CouponUsage::where('coupon_id', $coupon->id)->get()->count();
                            if ($counofuse >= $coupon_details->max_coupon) {
                                flash(__('Coupon reached it max availablity.'))->warning();

                                return back();
                            }
                        }
                        if ($coupon_details->max_coupon == 0) {
                            //flash(__('Coupon reached it max availablity'))->warning();
                            //return back();
                        }
                        //end
                        $Psubtotal = 0;
                        $subtotal = 0;
                        $tax = 0;
                        $shipping = 0;
                        $seller_id = 0;
                        if ($coupon->seller_id > 0) {
                            $seller_id = \App\Shop::find($coupon->seller_id)->user_id;
                        }
                        foreach (Session::get('cart') as $key => $cartItem) {
                            $product_seller = \App\Product::find($cartItem['id'])->user_id;
                            if ($seller_id) {
                                if ($seller_id == $product_seller) {
                                    $subtotal += $cartItem['price'] * $cartItem['quantity'];
                                    $tax += $cartItem['tax'] * $cartItem['quantity'];
                                    $shipping += $cartItem['shipping'] * $cartItem['quantity'];
                                }
                            } else {
                                $subtotal += $cartItem['price'] * $cartItem['quantity'];
                                $tax += $cartItem['tax'] * $cartItem['quantity'];
                                $shipping += $cartItem['shipping'] * $cartItem['quantity'];
                            }
                        }
                        $sum = $subtotal;
                        if ($sum > $coupon_details->min_buy) {
                            if ($coupon->discount_type == 'percent') {
                                $coupon_discount = ($sum * $coupon->discount) / 100;
                                if ($coupon_discount > $coupon_details->max_discount) {
                                    $coupon_discount = $coupon_details->max_discount;
                                }
                            } elseif ($coupon->discount_type == 'amount') {
                                $coupon_discount = $coupon->discount;
                            }
                            $request->session()->put('coupon_id', $coupon->id);
                            $request->session()->put('coupon_discount', $coupon_discount);
                            flash(__('Coupon has been applied.'))->success();
                        } else {
                            flash(__('Cart did not reach minimum value'))->warning();
                        }
                    } elseif ($coupon->type == 'product_base') {
                        $coupon_discount = 0;
                        $i = 0;
                        foreach (Session::get('cart') as $key => $cartItem) {
                            foreach ($coupon_details as $key => $coupon_detail) {
                                if ($coupon_detail->product_id == $cartItem['id']) {
                                    if ($coupon->discount_type == 'percent') {
                                        $coupon_discount += ($cartItem['price'] * $coupon->discount / 100) * $cartItem['quantity'];
                                    } elseif ($coupon->discount_type == 'amount') {
                                        $coupon_discount += $coupon->discount;
                                    }
                                    $i = 1;
                                }
                            }
                        }
                        $request->session()->put('coupon_id', $coupon->id);
                        $request->session()->put('coupon_discount', $coupon_discount);
                        if ($i == 1) {
                            flash(__('Coupon has been applied'))->success();
                        } else {
                            flash(__('Coupon is not applicable'))->success();
                        }
                    }
                } else {
                    flash(__('You already used this coupon!'))->warning();
                }
            } else {
                flash(__('Coupon expired!'))->warning();
            }
        } else {
            flash(__('Invalid coupon!'))->warning();
        }*/

        return back();
    }

    public function remove_coupon_code(Request $request)
    {
        $request->session()->forget('coupon_id');
        $request->session()->forget('coupon_discount');

        return back();
    }

    public function order_confirmed()
    {
        $order = Order::findOrFail(Session::get('order_id'));

        return view('frontend.order_confirmed', compact('order'));
    }
}