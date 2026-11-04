<?php
namespace App\Http\Controllers;

use App\BusinessSetting;
use App\Http\Controllers\AffiliateController;
use App\Http\Controllers\ClubPointController;
use App\Http\Controllers\OTPVerificationController;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\Shop;
use App\Models\SplitOrder;
use App\User;
use App\Services\CartService;
use App\Services\OrderService;
use App\Services\UserService;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\CheckoutService;

class OrderController extends Controller
{
    /**
     * @param OrderService
     */
    protected $orderService;

    public function __construct(OrderService $orderService) {
        $this->orderService = $orderService;
    }

    /**
     * Display a listing of the resource to seller.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $payment_status = null;
        $delivery_status = null;
        $sort_search = null;
        $orders = DB::table('orders')
            ->orderBy('code', 'desc')
            ->join('order_details', 'orders.id', '=', 'order_details.order_id')
            ->where('order_details.seller_id', Auth::user()->id)
            ->select('orders.id')
            ->distinct();
        if ($request->payment_status != null) {
            $orders = $orders->where('order_details.payment_status', $request->payment_status);
            $payment_status = $request->payment_status;
        }
        if ($request->delivery_status != null) {
            $orders = $orders->where('order_details.delivery_status', $request->delivery_status);
            $delivery_status = $request->delivery_status;
        }
        if ($request->has('search')) {
            $sort_search = $request->search;
            $orders = $orders->where('code', 'like', '%' . $sort_search . '%');
        }
        $orders = $orders->paginate(15);
        foreach ($orders as $key => $value) {
            $order = \App\Order::find($value->id);
            $order->viewed = 1;
            $order->save();
        }

        return view('frontend.seller.orders', compact('orders', 'payment_status', 'delivery_status', 'sort_search'));
    }

    /**
     * Display a listing of the resource to admin.
     *
     * @return \Illuminate\Http\Response
     */
    public function admin_orders(Request $request)
    {
        $payment_status = null;
        $delivery_status = null;
        $sort_search = null;
        $admin_user_id = User::where('user_type', 'admin')->first()->id;
        $orders = DB::table('orders')
            ->orderBy('code', 'desc')
            ->join('order_details', 'orders.id', '=', 'order_details.order_id')
            ->where('order_details.seller_id', $admin_user_id)
            ->select('orders.id')
            ->distinct();
        if ($request->payment_type != null) {
            $orders = $orders->where('order_details.payment_status', $request->payment_type);
            $payment_status = $request->payment_type;
        }
        if ($request->delivery_status != null) {
            $orders = $orders->where('order_details.delivery_status', $request->delivery_status);
            $delivery_status = $request->delivery_status;
        }
        if ($request->has('search')) {
            $sort_search = $request->search;
            $orders = $orders->where('code', 'like', '%' . $sort_search . '%');
        }
        $orders = $orders->paginate(15);

        return view('orders.index', compact('orders', 'payment_status', 'delivery_status', 'sort_search', 'admin_user_id'));
    }

    /**
     * Display a listing of the sales to admin.
     *
     * @return \Illuminate\Http\Response
     */
    public function sales(Request $request)
    {
        $from   = $request->start_date;
        $to     = $request->end_date;
        if ($from !== '' && $to !== '') {
            $from   = date("Y-m-d", strtotime($from));
            $to     = date('Y-m-d', strtotime("+1 day", strtotime($to)));
        }
        $orders = $this->orderService->getSaleData($from ?? '', $to ?? '');
        return view('sales.index', compact('orders'));
    }

    public function today_sales(Request $request)
    {
        $sort_search = null;
        $orders = Order::whereDate('created_at', Carbon::today())->orderBy('code', 'desc');
        if ($request->has('search')) {
            $sort_search = $request->search;
            $orders = $orders->where('code', 'like', '%' . $sort_search . '%');
        }
        $orders = $orders->paginate(15);

        return view('sales.sales_today', compact('orders', 'sort_search'));
    }

    public function order_index(Request $request)
    {
        if (Auth::user()->user_type == 'staff' && Auth::user()->staff->pick_up_point != null) {
            //$orders = Order::where('pickup_point_id', Auth::user()->staff->pick_up_point->id)->get();
            $orders = DB::table('orders')
                ->orderBy('code', 'desc')
                ->join('order_details', 'orders.id', '=', 'order_details.order_id')
                ->where('order_details.pickup_point_id', Auth::user()->staff->pick_up_point->id)
                ->select('orders.id')
                ->distinct()
                ->paginate(15);

            return view('pickup_point.orders.index', compact('orders'));
        } else {
            //$orders = Order::where('shipping_type', 'Pick-up Point')->get();
            $orders = DB::table('orders')
                ->orderBy('code', 'desc')
                ->join('order_details', 'orders.id', '=', 'order_details.order_id')
                ->where('order_details.shipping_type', 'pickup_point')
                ->select('orders.id')
                ->distinct()
                ->paginate(15);

            return view('pickup_point.orders.index', compact('orders'));
        }
    }

    public function pickup_point_order_sales_show($id)
    {
        if (Auth::user()->user_type == 'staff') {
            $order = Order::findOrFail(decrypt($id));

            return view('pickup_point.orders.show', compact('order'));
        } else {
            $order = Order::findOrFail(decrypt($id));

            return view('pickup_point.orders.show', compact('order'));
        }
    }

    /**
     * Display a single sale to admin.
     *
     * @return \Illuminate\Http\Response
     */
    public function sales_show($id)
    {
        $order = Order::findOrFail(decrypt($id));
        $status = [
            'pending' => __('Order placed'),
            'on_review' => __('Ready to Ship'),
            'on_delivery' => __('In Transit'),
            'delivered' => __('Delivered'),
            'cancelled' => __('Cancelled'),
        ];

        $selfCollectionStatus = [
            'pending' => __('Order placed'),
            'on_review' => __('Preparing Order'),
            'on_delivery' => __('Ready for Collection'),
            'delivered' => __('Collected'),
            'cancelled' => __('Cancelled'),
        ];
        $address = json_decode($order->shipping_address);
        if ($address->postal_code === '730339') { // default postal code number of self collection
            $address->address = null;
        }

        return view('sales.show', compact('order', 'status', 'selfCollectionStatus', 'address'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     * @TODO: Move to Order service 
     * @param \Illuminate\Http\Request $request
     */
    public function store(Request $request)
    {
        $cartService = app()->make(CartService::class);
        $orderService = app()->make(OrderService::class);
        $cart = $cartService->getCart();
        $tempCart = $cartService->getTempCart();

        $order = new Order;
        if (Auth::check()) {
            $order->user_id = Auth::user()->id;
        } else {
            $order->guest_id = mt_rand(100000, 999999);
        }
        $order->shipping_address = $this->getDeliveryAddress($request->session()->get('address'));
        $order->payment_type = $request->payment_option ?? Order::PAYMENT_TYPE_NONE;
        $order->delivery_viewed = '0';
        $order->payment_status_viewed = '0';
        $order->code = date('Ymd-His') . rand(10, 99);
        $order->coupon_code = $tempCart->getYsgCoupon();
        $order->date = strtotime('now');
        if ($order->save()) {
            //calculate shipping is to get shipping costs of different types
            //Order Details Storing
            foreach ($tempCart->getItems() as $key => $cartItem) {
                $product = Product::find($cartItem->getId());
                if ($product->getStock($cartItem->getVariation()) == 0) {
                    flash(__("The $product->name is out of stock and will be removed from your cart."));
                    Log::channel('order')->info($product->name . ' (id: ' . $product->id . ') is out of stock for order ' . $order->id);
                    $cart->removeItem($cartItem->getId());
                    $tempCart->removeItem($cartItem->getId());
                    continue;
                }
                $order_detail = new OrderDetail;
                $order_detail->rsp_price = $product->unit_price;

                if ('' !== $cartItem->getVariation()) {
                    $variant = $product->getVariant($cartItem->getVariation());
                    $variant->qty -= $cartItem->getQuantity();
                    $variant->save();
                    $order_detail->rsp_price = $variant->price;
                }  else {
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
                if ($tempCart->getShopOrderType($cartItem->getShopId()) == Shop::ORDER_TYPE_SELF_COLLECTION) {
                    $order_detail->shipping_type = OrderDetail::SHIPPING_TYPE_SELF_COLLECTION_KEY;    
                    $order_detail->collection_address = $tempCart->getShopAddress($cartItem->getShopId());
                } else {
                    $order_detail->shipping_type = OrderDetail::SHIPPING_TYPE_HOME_DELIVERY_KEY;
                }
                $order_detail->product_referral_code = null;
                $order_detail->shipping_cost = 0; // no calculate shipping cost for each item
                $order_detail->quantity = $cartItem->getQuantity();
                $order_detail->pickup_point_id = null;

                //Dividing Shipping Costs
                /*if ($cartItem['shipping_type'] == 'home_delivery') {
                    $order_detail->shipping_cost = getShippingCost($key);
                    $order_detail->shipping_cost = $Pshipping;
                    // $shipping += $order_detail->shipping_cost;
                } else {
                    $order_detail->shipping_cost = 0;
                }
                //End of storing shipping cost
                $order_detail->quantity = $cartItem['quantity'];
                if (\App\User::where('id', $product->user_id)->first()->company_shipping == 1) {
                    $shipmentcartotal += $cartItem['price'] * $cartItem['quantity'];
                    $order_detail->shipping_cost = 2;
                    $order_detail->shipping_type = "Combine";
                } else {
                    $order_detail->shipping_cost = \App\Shop::where('user_id', $product->user_id)->first()->shipping_cost;
                    $Pshipmentcartotal += $cartItem['price'] * $cartItem['quantity'];
                    $order_detail->shipping_type = "Private";
                }
                if (\App\Shop::where('user_id', $product->user_id)->first()->shipping_cost) {
                    $Pshipping = \App\Shop::where('user_id', $product->user_id)->first()->shipping_cost;
                    $order_detail->shipping_cost = $Pshipping;
                }
                $free_shipping = \App\Shop::where('user_id', $product->user_id)->first()->shipping_cost_above;
                if (($free_shipping < $Pshipmentcartotal) && (\App\User::where('id', $product->user_id)->first()->company_shipping == 2)) {
                    $Pshipping = 0;
                    $order_detail->shipping_cost = $Pshipping;
                }*/
                $order_detail->save();
            }
            $order->total_shipping_cost = $tempCart->getShippingFee();
            //end of checking
            $order->grand_total = $tempCart->getGrandTotal();
            if ($tempCart->hasCoupons()) {
                $order->coupon_discount = $tempCart->getCouponDiscount();
                $orderService->couponUsage($tempCart, $order);
            }
            $order->save();
            //stores the pdf for invoice
            // $pdf = PDF::setOptions([
            //     'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true,
            //     'logOutputFile' => storage_path('logs/log.htm'),
            //     'tempDir' => storage_path('logs/')
            // ])->loadView('invoices.customer_invoice', compact('order'));
            // $output = $pdf->output();
            // file_put_contents('invoices/' . 'Order#' . $order->code . '.pdf', $output);
            $request->session()->put('order_id', $order->id);

            $orderService->splitOrder($tempCart, $order);

            return $order;
        }
        return false;
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $order = Order::findOrFail(decrypt($id));
        $order->viewed = 1;
        $order->save();

        return view('orders.show', compact('order'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        if ($order != null) {
            foreach ($order->orderDetails as $key => $orderDetail) {
                $orderDetail->delete();
            }
            $order->delete();
            flash(__('Order has been deleted successfully'))->success();
        } else {
            flash(__('Something went wrong'))->error();
        }

        return back();
    }

    public function order_details(Request $request)
    {
        $order = Order::findOrFail($request->order_id);
        //$order->viewed = 1;
        $order->save();

        return view('frontend.partials.order_details_seller', compact('order'));
    }

    public function order_resync(Request $request)
    {
        $checkoutService = app()->make(CheckoutService::class);
        $order = Order::findOrFail($request->id);
        $status = $checkoutService->syncOmsOrder($order);

        if ($status) {
            flash(__('order: '.$order->code.' sync success'))->success();
        } else {
            flash(__('order: '.$order->code.' sync failed'))->error();
        }

        return back();
    }

    public function update_delivery_status(Request $request)
    {
        if (Auth::user()->user_type !== 'seller') {
            return response()->json([
                'error' => true,
                'message' => "Only seller",
            ], 403);
        }

        $order = Order::findOrFail($request->order_id);
        $order->delivery_viewed = '0';
        $order->save();

        $orderDetails = $order->orderDetails->where('seller_id', Auth::user()->id);
        foreach ($orderDetails as $key => $orderDetail) {
            $orderDetail->delivery_status = $request->status;
            $orderDetail->save();
        }

        $splitOrders = $order->splitOrders->where('seller_id', Auth::user()->id);

        foreach ($splitOrders as $splitOrder) {
            $splitOrder->delivery_status = $request->status;
            $splitOrder->save();
        }

        if (\App\Addon::where('unique_identifier', 'otp_system')->first() != null && \App\Addon::where('unique_identifier', 'otp_system')->first()->activated && \App\OtpConfiguration::where('type', 'otp_for_delivery_status')->first()->value) {
            try {
                $otpController = new OTPVerificationController;
                $otpController->send_delivery_status($order);
            } catch (\Exception $e) {
            }
        }

        return 1;
    }

    public function update_delivery_status_details(Request $request)
    {
        $orderDetail = OrderDetail::findOrFail($request->order_id);
        $orderDetail->delivery_status = $request->status;
        $orderDetail->save();

        $splitOrder = $orderDetail->splitOrder;
        $shouldUpdateSplitOrderDeliveryStatus = true;
        foreach ($splitOrder->orderDetails as $detail) {
            if ($detail->delivery_status !== $request->status) {
                $shouldUpdateSplitOrderDeliveryStatus = false;
                break;
            }
        }

        if ($shouldUpdateSplitOrderDeliveryStatus) {
            $splitOrder->delivery_status = $request->status;
            $splitOrder->save();
        }

        return 1;
    }

    public function update_payment_status(Request $request)
    {
        $order = Order::findOrFail($request->order_id);
        $order->payment_status_viewed = '0';
        $order->save();
        if (Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'seller') {
            foreach ($order->orderDetails->where('seller_id', Auth::user()->id) as $key => $orderDetail) {
                $orderDetail->payment_status = $request->status;
                $orderDetail->save();
            }
        } else {
            foreach ($order->orderDetails->where('seller_id', \App\User::where('user_type', 'admin')->first()->id) as $key => $orderDetail) {
                $orderDetail->payment_status = $request->status;
                $orderDetail->save();
            }
        }
        $status = 'paid';
        foreach ($order->orderDetails as $key => $orderDetail) {
            if ($orderDetail->payment_status != 'paid') {
                $status = 'unpaid';
            }
        }
        $order->payment_status = $status;
        $order->save();
        if ($order->payment_status == 'paid' && $order->commission_calculated == 0) {
            if (\App\Addon::where('unique_identifier', 'seller_subscription')->first() == null || !\App\Addon::where('unique_identifier', 'seller_subscription')->first()->activated) {
                if ($order->payment_type == 'cash_on_delivery') {
                    if (BusinessSetting::where('type', 'category_wise_commission')->first()->value != 1) {
                        $commission_percentage = BusinessSetting::where('type', 'vendor_commission')->first()->value;
                        foreach ($order->orderDetails as $key => $orderDetail) {
                            $orderDetail->payment_status = 'paid';
                            $orderDetail->save();
                            if ($orderDetail->product->user->user_type == 'seller') {
                                $seller = $orderDetail->product->user->seller;
                                $seller->admin_to_pay = $seller->admin_to_pay - ($orderDetail->price * $commission_percentage) / 100;
                                $seller->save();
                            }
                        }
                    } else {
                        foreach ($order->orderDetails as $key => $orderDetail) {
                            $orderDetail->payment_status = 'paid';
                            $orderDetail->save();
                            if ($orderDetail->product->user->user_type == 'seller') {
                                $commission_percentage = $orderDetail->product->category->commision_rate;
                                $seller = $orderDetail->product->user->seller;
                                $seller->admin_to_pay = $seller->admin_to_pay - ($orderDetail->price * $commission_percentage) / 100;
                                $seller->save();
                            }
                        }
                    }
                } elseif ($order->manual_payment) {
                    if (BusinessSetting::where('type', 'category_wise_commission')->first()->value != 1) {
                        $commission_percentage = BusinessSetting::where('type', 'vendor_commission')->first()->value;
                        foreach ($order->orderDetails as $key => $orderDetail) {
                            $orderDetail->payment_status = 'paid';
                            $orderDetail->save();
                            if ($orderDetail->product->user->user_type == 'seller') {
                                $seller = $orderDetail->product->user->seller;
                                $seller->admin_to_pay = $seller->admin_to_pay + ($orderDetail->price * (100 - $commission_percentage)) / 100;
                                $seller->save();
                            }
                        }
                    } else {
                        foreach ($order->orderDetails as $key => $orderDetail) {
                            $orderDetail->payment_status = 'paid';
                            $orderDetail->save();
                            if ($orderDetail->product->user->user_type == 'seller') {
                                $commission_percentage = $orderDetail->product->category->commision_rate;
                                $seller = $orderDetail->product->user->seller;
                                $seller->admin_to_pay = $seller->admin_to_pay + ($orderDetail->price * (100 - $commission_percentage)) / 100;
                                $seller->save();
                            }
                        }
                    }
                }
            }
            if (\App\Addon::where('unique_identifier', 'affiliate_system')->first() != null && \App\Addon::where('unique_identifier', 'affiliate_system')->first()->activated) {
                $affiliateController = new AffiliateController;
                $affiliateController->processAffiliatePoints($order);
            }
            if (\App\Addon::where('unique_identifier', 'club_point')->first() != null && \App\Addon::where('unique_identifier', 'club_point')->first()->activated) {
                $clubpointController = new ClubPointController;
                $clubpointController->processClubPoints($order);
            }
            $order->commission_calculated = 1;
            $order->save();
        }
        if (\App\Addon::where('unique_identifier', 'otp_system')->first() != null && \App\Addon::where('unique_identifier', 'otp_system')->first()->activated && \App\OtpConfiguration::where('type', 'otp_for_paid_status')->first()->value) {
            try {
                $otpController = new OTPVerificationController;
                $otpController->send_payment_status($order);
            } catch (\Exception $e) {
            }
        }

        return 1;
    }

    private function getDeliveryAddress($address)
    {
        $cartService = app(CartService::class);
        $tempCart = $cartService->getTempCart();
        $user = Auth::user();
        $data = [
            'name'          => $user->name,
            'email'         => $user->email,
            'checkout_type' => 'logged',
        ];

        if (!$tempCart->isShowSelectAddress()) {
            $defaultAddress = $user->getDefaultAddress();
            $data['address']     = 'Self collection';
            $data['country']     = $defaultAddress ? $defaultAddress->country : 'Singapore';
            $data['city']        = $defaultAddress ? $defaultAddress->city : 'Singapore';
            $data['postal_code'] = $defaultAddress ? $defaultAddress->postal_code : '648167';
            $data['phone']       = $user->fullPhone() ?? 'N/A';
        } else {
            $data['address']     = $address->address;
            $data['country']     = $address->country;
            $data['city']        = $address->city;
            $data['postal_code'] = $address->postal_code;
            $data['phone'] = $address->fullPhone();
            $data['title'] = $address->title;
        }

        return json_encode($data);
    }

    public function restock($id)
    {
        try {
            $order = Order::findOrFail($id);

            $result = $this->orderService->restockOrder($order, Auth::user()->id);

            return response()->json([
                'success' => $result['success'],
                'message' => $result['message']
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], $e->getMessage() === 'This order has already been restocked.' ||
                $e->getMessage() === 'Only unpaid orders can be restocked.' ? 400 : 500);
        }
    }

}