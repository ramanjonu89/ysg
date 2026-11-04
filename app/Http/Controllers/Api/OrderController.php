<?php
namespace App\Http\Controllers\Api;

use App\Models\BusinessSetting;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\SplitOrder;
use App\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        return $this->processOrder($request);
    }

    public function processOrder(Request $request)
    {
        $shippingAddress = json_decode($request->shipping_address);
        // create an order
        $order = Order::create([
            'user_id' => $request->user_id,
            'shipping_address' => json_encode($shippingAddress),
            'payment_type' => $request->payment_type,
            'payment_status' => $request->payment_status,
            'grand_total' => $request->grand_total - $request->coupon_discount,
            'coupon_discount' => $request->coupon_discount,
            'code' => date('Ymd-his'),
            'date' => strtotime('now')
        ]);
        $cartItems = Cart::where('user_id', $request->user_id)->get();
        // save order details
        $shipping = 0;
        $admin_products = array();
        $seller_products = array();
        //
        if (\App\BusinessSetting::where('type', 'shipping_type')->first()->value == 'flat_rate') {
            $shipping = \App\BusinessSetting::where('type', 'flat_rate_shipping_cost')->first()->value;
        } elseif (\App\BusinessSetting::where('type', 'shipping_type')->first()->value == 'seller_wise_shipping') {
            foreach ($cartItems as $cartItem) {
                $product = \App\Product::find($cartItem->product_id);
                if ($product->added_by == 'admin') {
                    array_push($admin_products, $cartItem->product_id);
                } else {
                    $product_ids = array();
                    if (array_key_exists($product->user_id, $seller_products)) {
                        $product_ids = $seller_products[$product->user_id];
                    }
                    array_push($product_ids, $cartItem->product_id);
                    $seller_products[$product->user_id] = $product_ids;
                }
            }
            if (!empty($admin_products)) {
                $shipping = \App\BusinessSetting::where('type', 'shipping_cost_admin')->first()->value;
            }
            if (!empty($seller_products)) {
                foreach ($seller_products as $key => $seller_product) {
                    $shipping += \App\Shop::where('user_id', $key)->first()->shipping_cost;
                }
            }
        }
        foreach ($cartItems as $cartItem) {
            $product = Product::findOrFail($cartItem->product_id);
            if ($cartItem->variation) {
                $cartItemVariation = $cartItem->variation;
                $product_stocks = $product->stocks->where('variant', $cartItem->variation)->first();
                $product_stocks->qty -= $cartItem->quantity;
                $product_stocks->save();
            } else {
                $product->update([
                    'current_stock' => DB::raw('current_stock - ' . $cartItem->quantity)
                ]);
            }
            $order_detail_shipping_cost = 0;
            if (\App\BusinessSetting::where('type', 'shipping_type')->first()->value == 'flat_rate') {
                $order_detail_shipping_cost = $shipping / count($cartItems);
            } elseif (\App\BusinessSetting::where('type', 'shipping_type')->first()->value == 'seller_wise_shipping') {
                if ($product->added_by == 'admin') {
                    $order_detail_shipping_cost = \App\BusinessSetting::where('type', 'shipping_cost_admin')->first()->value / count($admin_products);
                } else {
                    $order_detail_shipping_cost = \App\Shop::where('user_id', $product->user_id)->first()->shipping_cost / count($seller_products[$product->user_id]);
                }
            } else {
                $order_detail_shipping_cost = $product->shipping_cost;
            }
            OrderDetail::create([
                'order_id' => $order->id,
                'seller_id' => $product->user_id,
                'product_id' => $product->id,
                'variation' => $cartItem->variation,
                'price' => $cartItem->price * $cartItem->quantity,
                'tax' => $cartItem->tax * $cartItem->quantity,
                'shipping_cost' => $order_detail_shipping_cost,
                'quantity' => $cartItem->quantity,
                'payment_status' => $request->payment_status
            ]);
            $product->update([
                'num_of_sale' => DB::raw('num_of_sale + ' . $cartItem->quantity)
            ]);
        }
        // apply coupon usage
        if ($request->coupon_code != '') {
            CouponUsage::create([
                'user_id' => $request->user_id,
                'coupon_id' => Coupon::where('code', $request->coupon_code)->first()->id
            ]);
        }
        // calculate commission
        $commission_percentage = BusinessSetting::where('type', 'vendor_commission')->first()->value;
        foreach ($order->orderDetails as $orderDetail) {
            if ($orderDetail->product->user->user_type == 'seller') {
                $seller = $orderDetail->product->user->seller;
                $price = $orderDetail->price + $orderDetail->tax + $orderDetail->shipping_cost;
                $seller->admin_to_pay = ($request->payment_type == 'cash_on_delivery') ? $seller->admin_to_pay - ($price * $commission_percentage) / 100 : $seller->admin_to_pay + ($price * (100 - $commission_percentage)) / 100;
                $seller->save();
            }
        }
        // clear user's cart
        $user = User::findOrFail($request->user_id);
        $user->carts()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Your order has been placed successfully'
        ]);
    }

    /** API update split_orders, order detail va order */
    public function syncOMSUpdateDeliveryStatus(Request $request)
    {
        DB::beginTransaction();
        try{
            $inputs     = $request->all();
            $validator  = Validator::make($inputs, [
                'company_id'   => "required|exists:users,company_id",
                'tracking_num' => "required|exists:split_orders,tracking_number|exists:order_details,trackingnum",
                'status'       => "required|string|in:packaging,in-transit,delivered,cancelled,refunded"
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => "Data is not match", 'errors' => $validator->errors()], 400);
            }

            $user        = User::where(['company_id' => $request->company_id])->first();
            $spilitOrder = SplitOrder::where(['tracking_number' => $request->tracking_num])->first();
            $orderDetail = OrderDetail::where(['trackingnum' => $request->tracking_num])->first();


            if($spilitOrder->seller_id != $user->id || $orderDetail->seller_id != $user->id){
                return response()->json(['success' => false, 'message' => "Data is not match"], 400);
            }

            switch ($request->status) {
                case 'packaging';
                    $statusdelivery = "on_review";
                    break;
                case 'in-transit';
                    $statusdelivery = "on_delivery";
                    break;
                case 'delivered';
                    $statusdelivery = "delivered";
                    break;
                case 'cancelled';
                    $statusdelivery = "cancelled";
                    break;
                case 'refunded';
                    $statusdelivery = "refunded";
                    break;
                default:
                   $statusdelivery = "pending";
            }
            // update status spilit_order
            $spilitOrder->delivery_status   = $statusdelivery;
            $spilitOrder->save();

            // update status order detail
            $orderDetail->delivery_status = $statusdelivery;
            $orderDetail->save();

            DB::commit();
            return response()->json(['success' => true, 'message' => "Your order has been update status successfully!"], 200);
        }catch(Exception $e){
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

}

