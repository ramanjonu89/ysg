<?php
namespace App\Http\Controllers;

use App\CustomerPackage;
use App\Order;
use App\Repositories\OrderRepository;
use App\SellerPackage;
use App\Services\CheckoutService;
use App\Services\OrderService;
use App\Services\CartService;
use Illuminate\Http\Request;
use Session;
use Stripe;

class StripePaymentController extends Controller
{
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function stripe()
    {
        if (Session::has('payment_type')) {
            if (Session::get('payment_type') == 'cart_payment' || Session::get('payment_type') == 'wallet_payment') {
                return view('frontend.payment.stripe');
            } elseif (Session::get('payment_type') == 'customer_package_payment') {
                $customer_package = CustomerPackage::findOrFail(Session::get('payment_data')['customer_package_id']);

                return view('frontend.payment.stripe', compact('customer_package'));
            } elseif (Session::get('payment_type') == 'seller_package_payment') {
                $seller_package = SellerPackage::findOrFail(Session::get('payment_data')['seller_package_id']);

                return view('frontend.payment.stripe', compact('seller_package'));
            }
        }
    }

    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    function stripePost(Request $request)
    {
        if ($request->session()->has('payment_type')) {
            if ($request->session()->get('payment_type') == 'cart_payment') {
                $order = Order::findOrFail(Session::get('order_id'));
                Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
                $shipping_address = json_decode($order->shipping_address);
                // "billing_details" => $shipping_address->address,
                try {
                    $payment = json_encode(Stripe\Charge::create([
                        'description' => 'Payment for Order # ' . $order->code,
                        "amount" => round(($order->grand_total) * 100),
                        "currency" => "sgd",
                        "source" => $request->stripeToken
                    ]));
                } catch (\Exception $e) {
                    flash($e->getMessage())->error();

                    return redirect()->route('checkout.payment_info');
                }
                $checkoutController = new CheckoutController(new CheckoutService(), app()->make(OrderService::class), new CartService());

                return $checkoutController->checkout_done($request->session()->get('order_id'), $payment);
            } elseif ($request->session()->get('payment_type') == 'wallet_payment') {
                Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
                try {
                    $payment = json_encode(Stripe\Charge::create([
                        "amount" => round(($order->grand_total) * 100),
                        "currency" => "sgd",
                        "source" => $request->stripeToken
                    ]));
                } catch (\Exception $e) {
                    flash($e->getMessage())->error();

                    return redirect()->route('wallet.index');
                }
                $walletController = new WalletController;

                return $walletController->wallet_payment_done($request->session()->get('payment_data'), $payment);
            } elseif ($request->session()->get('payment_type') == 'customer_package_payment') {
                Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
                $customer_package = CustomerPackage::findOrFail(Session::get('payment_data')['customer_package_id']);
                try {
                    $payment = json_encode(Stripe\Charge::create([
                        "amount" => round(($order->grand_total) * 100),
                        "currency" => "usd",
                        "source" => $request->stripeToken
                    ]));
                } catch (\Exception $e) {
                    flash($e->getMessage())->error();

                    return redirect()->route('customer_packages_list_show');
                }
                $customer_package_controller = new CustomerPackageController;

                return $customer_package_controller->purchase_payment_done($request->session()->get('payment_data'), $payment);
            } elseif ($request->session()->get('payment_type') == 'seller_package_payment') {
                Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
                $seller_package = SellerPackage::findOrFail(Session::get('payment_data')['seller_package_id']);
                try {
                    $payment = json_encode(Stripe\Charge::create([
                        "amount" => round(($order->grand_total) * 100),
                        "currency" => "usd",
                        "source" => $request->stripeToken
                    ]));
                } catch (\Exception $e) {
                    flash($e->getMessage())->error();

                    return redirect()->route('seller_packages_list');
                }
                $seller_package_controller = new SellerPackageController;

                return $seller_package_controller->purchase_payment_done($request->session()->get('payment_data'), $payment);
            }
        }
    }
}

