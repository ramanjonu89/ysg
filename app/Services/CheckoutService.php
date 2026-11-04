<?php
namespace App\Services;

use App\BusinessSetting;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Mail\InvoiceEmailManager;
use Illuminate\Support\Facades\Mail;
use App\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;
use PDF;
use Ixudra\Curl\Facades\Curl;



class CheckoutService
{
    protected $paymentGatewayService;
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function initGateway(string $gatewayName) {
        $this->paymentGatewayService = new PaymentGatewayService($gatewayName);
    }

    public function getFormData(User $user, Order $order): array
    {
        return $this->paymentGatewayService->getFormData($user, $order);
    }

    public function getRedirectLink(User $user, Order $order): string
    {
        return $this->paymentGatewayService->getRedirectLink($user, $order);
    }

    public function successCallback($data) {
        try {
            return $this->paymentGatewayService->successCallback($data);
        } catch (Throwable $e) {
            Log::debug($data);
            return false;
        }
    }

    public function extractOrderId($data) {
        return $this->paymentGatewayService->extractOrderId($data);
    }

    public function storesPdfInvoice($order) {
        try {
            $pdf = PDF::setOptions([
                'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true,
                'logOutputFile' => storage_path('logs/log.htm'),
                'tempDir' => storage_path('logs/')
            ])->loadView('invoices.customer_invoice', compact('order'));
            $output = $pdf->output();
            file_put_contents('invoices/' . 'Order#' . $order->code . '.pdf', $output);
        } catch (Throwable $e) {
            Log::error('storesPdfInvoice:'. $order->id);
            return false;
        }
    }

    public function sendNewOrderMail($order) {
        try {
            $hasHomeDelivery = $order->orderDetails->contains('shipping_type', OrderDetail::SHIPPING_TYPE_HOME_DELIVERY_KEY);

            $array = [
                'view'             => $hasHomeDelivery ? 'emails.delivery_invoice' : 'emails.self_collection_invoice',
                'subject'          => 'Thank You For Your Order!',
                'from'             => env('MAIL_FROM_ADDRESS'),
                'content'          => __('Hi. A new order has been placed. Please check the attached invoice.'),
                'grand_total'      => $order->grand_total,
                'order_id'         => $order->code,
                'purchase_history' => env('APP_URL') . '/purchase_history#' . $order->code,
                'file'             => 'invoices/Order#' . $order->code . '.pdf',
                'file_name'        => 'Order#' . $order->code . '.pdf',
                'name'             => json_decode($order->shipping_address)->name,
            ];

            if (env('MAIL_FROM_ADDRESS')) {
                Mail::to(json_decode($order->shipping_address)->email)->send(new InvoiceEmailManager($array));
                Mail::to('cs@ysg.com.sg')->send(new InvoiceEmailManager($array));
            }
            unlink($array['file']);
        } catch (Exception $e) {
            Log::error('sendNewOrderMail: ' . $order->id . ' — ' . $e->getMessage());
            return false;
        }
    }

    // current disable
    public function processAffiliatePoints($order) {
        if (\App\Addon::where('unique_identifier', 'affiliate_system')->first() != null && \App\Addon::where('unique_identifier', 'affiliate_system')->first()->activated) {
            $affiliateController = new AffiliateController;
            $affiliateController->processAffiliatePoints($order);
        }
    }

    // current disable
    public function processClubPoints($order) {
        if (\App\Addon::where('unique_identifier', 'club_point')->first() != null && \App\Addon::where('unique_identifier', 'club_point')->first()->activated) {
            $clubpointController = new ClubPointController;
            $clubpointController->processClubPoints($order);
        }
    }

    // here
    public function syncOmsOrder($order): bool
    {
        try {
            foreach ($order->splitOrders as $splitOrder) {
                $seller = $splitOrder->seller;
                $seller_id = $seller->id;
                $company_id = $seller->company_id;
                if(!isset($company_id) || $company_id == ''){
                    continue;
                }
                $company_shipping_type = $seller->company_shipping;

                if ($company_shipping_type == User::COMPANY_SHIPPING_TYPE_EXTERNAL) {
                    $shipping_cost = $seller->shipping_cost;
                    $shipping_cost_above = $seller->shipping_cost_above;
                } else {
                    $shipping_cost = 2;
                    $shipping_cost_above = 80;
                }

                $accessToken = $this->getOMSAuthToken($company_id);
                Log::channel('oms')->info('OMS realtime response syncOmsOrder ', ['accessToken' => $accessToken]);
                if ($accessToken != '') {
                    $response = $this->callOMSApi($accessToken, $order, $seller_id, $shipping_cost, $shipping_cost_above);
                    Log::channel('oms')->info('OMS realtime response syncOmsOrder ', ['response' => $response]);
                    // dd($response)
                    if (is_null($response) || $response->statusCode != 0) {
                        Log::error('failed syncOmsOrder:' . $order->id . ' result: ' . print_r($response, true));
                    }
                }
                
            }
        } catch (Throwable $e) {
            Log::error('syncOmsOrder:'. $order->id);
            Log::error('message sync: ' . $e->getMessage());
            return false;
        }

        return true;
    }

    public function getOMSAuthToken($companyId) {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => config('api.oms') . "v1/auth",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "{\n\t\"companyId\": \"$companyId\"\n}",
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "Authorization: Basic Og=="
            ),
        ));

        $response = json_decode(curl_exec($curl));
        curl_close($curl);
        if (is_object($response) && isset($response->accessToken)) {
            return $response->accessToken;
        }
        return '';
    }

    public function callOMSApi($accessToken, $order, $seller_id, $shipping_cost, $shipping_cost_above) {
        $subtotal = 0;
        foreach ($order->orderDetails as $key => $orderDetail) {
            if ($seller_id == $orderDetail->product->user->seller->user_id) {
                $subtotal += $orderDetail->price;
                $channelName = $orderDetail->product->user->seller->oms_channel_name;
            }
            // convert home_delivery to home-delivery | self_collection to self-collection
            $shippingMethod = str_replace('_', '-', $orderDetail->shipping_type);
        }

        if ($subtotal >= $shipping_cost_above) {
            $Pshipping = 0;
        } else {
            $Pshipping = $shipping_cost;
        }

        $data = array(
            'orderNumber' => $order->code,
            'orderDate' => date('c'),
            'marketplaceCode' => $channelName,
            'orderType' => "sale-order",
            // 'deliveryMethod' => $order->get_shipping_method(),
            'deliveryMethod' => $shippingMethod,
            'originalOrderNumber' => '',
            'referralCode' => '',
            'customerType' => 'guest',
            'customerInfo' =>
                array(
                    'address' => json_decode($order->shipping_address)->address,
                    'country' => 'SG',
                    'customerId' => $orderDetail->user_id,
                    'email' => json_decode($order->shipping_address)->email,
                    'name' => json_decode($order->shipping_address)->name,
                    'phoneNum' => json_decode($order->shipping_address)->phone,
                    'postCode' => json_decode($order->shipping_address)->postal_code,
                    'region' => 'SG',
                ),
            'currency' => 'SGD',
            'exchangeRate' => 0,
            'subTotal' => $subtotal,
            'shippingFee' => $Pshipping,
            'totalCartDiscount' => 0,
            'totalAmountDue' => $order->grand_total,
            'totalAmountPaid' => $order->grand_total,
            'balanceAmountDue' => 0,
            'totalTaxAmount' => 0,
            'base_TotalAmountDue' => 0,
            'base_TotalAmountPaid' => $order->grand_total,
            'base_BalanceAmountDue' => 0,
            'base_TotalTaxAmount' => 0,
            'paymentInfo' =>
                array(
                    array(
                        'amount' => $order->grand_total,
                        'mode' => "Credit Card",
                        'referenceNumber' => '', //not sure how to get it
                        'remarks' => '',
                    ),
                ),
            'shippingInfo' =>
                array(
                    array(
                        'deliveryAddress' => json_decode($order->shipping_address)->address,
                        'country' => 'SG',
                        'customerId' => $orderDetail->user_id,
                        'email' => json_decode($order->shipping_address)->email,
                        'recipientName' => json_decode($order->shipping_address)->name,
                        'recipientPhoneNumber' => json_decode($order->shipping_address)->phone,
                        'postCode' => json_decode($order->shipping_address)->postal_code,
                        'region' => 'SG',
                        'shippedDate' => date('c'),
                        'shippingLabel' => '',
                        'shippingMethod' => 'Dropshipping',
                        'status' => 'order-received',
                        'trackingNum' => '',
                    ),
                ),
            'status' => Order::PAYMENT_STATUS_PAID,
            'cancelledReason' => '',
            'createdOn' => date('c'),
            'lastUpdatedOn' => date('c'),
            'items' => array(),
            'shippingServiceLevel' => '3DA',
        );
        foreach ($order->orderDetails as $key => $orderDetail) {
            if ($seller_id == $orderDetail->product->user->seller->user_id) {
                //subtotal += $orderDetail->price;
                $data['items'][] = array(
                    'productCode' => $orderDetail->getSku(),
                    'productName' => $orderDetail->product->name,
                    'base_FinalLineAmount' => 0,
                    'finalLineAmount' => $orderDetail->price,
                    'lineDiscount' => 0,
                    'remarks' => '',
                    'returnQuantity' => 0,
                    'shipQuantity' => $orderDetail->quantity,
                    'soldQuantity' => $orderDetail->quantity,
                    'status' => 'paid',
                    'taxAmount' => 0,
                    'totalApportionedCartDiscount' => 0,
                    'totalLineDiscount' => 0,
                    'totalPriceAfterLineDiscount' => 0,
                    'unitPrice' => $orderDetail->product->unit_price,
                    'unitWeight' => 0,
                );
            }
        }
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => config('api.oms') . "v1/order/create",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "Authorization: Bearer $accessToken"
            ),
        ));
        $response_2 = json_decode(curl_exec($curl));
        curl_close($curl);

        return $response_2;
    }

    public function calculateCommission($order) {
        try {
            if (\App\Addon::where('unique_identifier', 'seller_subscription')->first() == null || !\App\Addon::where('unique_identifier', 'seller_subscription')->first()->activated) {
                if (BusinessSetting::where('type', 'category_wise_commission')->first()->value != 1) {
                    $commission_percentage = BusinessSetting::where('type', 'vendor_commission')->first()->value;
                    foreach ($order->orderDetails as $key => $orderDetail) {
                        $orderDetail->payment_status = 'paid';
                        $orderDetail->save();
                        if ($orderDetail->product->user->user_type == 'seller') {
                            $seller = $orderDetail->product->user->seller;
                            $seller->admin_to_pay = $seller->admin_to_pay + ($orderDetail->price * (100 - $commission_percentage)) / 100 + $orderDetail->tax + $orderDetail->shipping_cost;
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
                            $seller->admin_to_pay = $seller->admin_to_pay + ($orderDetail->price * (100 - $commission_percentage)) / 100 + $orderDetail->tax + $orderDetail->shipping_cost;
                            $seller->save();
                        }
                    }
                }
            } else {
                foreach ($order->orderDetails as $key => $orderDetail) {
                    $orderDetail->payment_status = 'paid';
                    $orderDetail->save();
                    if ($orderDetail->product->user->user_type == 'seller') {
                        $seller = $orderDetail->product->user->seller;
                        $seller->admin_to_pay = $seller->admin_to_pay + $orderDetail->price + $orderDetail->tax + $orderDetail->shipping_cost;
                        $seller->save();
                    }
                }
            }
        } catch (Throwable $e) {
            Log::error('calculateCommission:'. $order->id);
            return false;
        }
    }


    public function callOmsUpdateTrackingNum($order){
        try{
            foreach ($order->orderDetails as $key => $orderDetail) {
                if ($orderDetail->product->user->user_type == 'seller') {
                    Log::channel('oms')->info('OMS response callOmsUpdateTrackingNum', ['orderDetail' => $orderDetail]);
                    $seller = $orderDetail->product->user->seller;
                    $company_id = \App\User::where('id', $seller->user_id)->first()->company_id;
                    Log::channel('oms')->info('OMS response callOmsUpdateTrackingNum', ['company_id' => $company_id]);

                    if(!isset($company_id) || $company_id == ''){
                        continue;
                    }

                    $orderid                = $orderDetail->order_id;
                    $ordernumber            = $orderDetail->order->code;
                    $seller_id              = $orderDetail->seller_id;

                    $accessToken = $this->getOMSAuthToken($company_id);

                    $response = Curl::to(config('api.oms') . 'v1/order/query/ysg-sg/' . $ordernumber)
                    ->withBearer($accessToken)
                    ->asJson()
                    ->get();
                    Log::channel('oms')->info('OMS response callOmsUpdateTrackingNum', ['response' => $response]);

                    if ($response != NULL && $response->messages == 'Success') {
                        $shippingInfo = $response->shippingInfo;
                        Log::channel('oms')->info('OMS response callOmsUpdateTrackingNum', ['shippingInfo' => $shippingInfo]);

                        //on_review
                        if ($response->status == 'packaging') {
                            $statusdelivery = 'on_review';
                        } elseif ($response->status == 'in-transit') {
                            $statusdelivery = 'on_delivery';
                        } elseif ($response->status == 'delivered') {
                            $statusdelivery = 'delivered';
                        } elseif ($response->status == 'cancelled') {
                            $statusdelivery = 'cancelled';
                        } elseif ($response->status == 'refunded') {
                            $statusdelivery = 'refunded';
                        } else {
                            $statusdelivery = 'pending';
                        }

                        //update status
                        DB::table('order_details')->where('order_id', $orderid)->where('seller_id', $seller_id)->where('delivery_status', '!=', 'cancelled')
                            ->update(
                                [
                                    'trackingnum'       => $shippingInfo[0]->trackingNum,
                                    'delivery_status'   => $statusdelivery,
                                    'updated_at'        => Carbon::now()
                                ]
                            );
                        DB::table('split_orders')->where('order_id', $orderid)->where('seller_id', $seller_id)->where('delivery_status', '!=', 'cancelled')
                        ->update(
                            [
                                'tracking_number'   => $shippingInfo[0]->trackingNum,
                                'delivery_status'   => $statusdelivery,
                                'updated_at'        => Carbon::now()
                            ]
                        );
                    }
                }
            }
        }catch(Exception $e){
            Log::channel('oms')->error('Error CheckoutService@callOmsUpdateTrackingNum',['order' => $order, 'error' => $e->getMessage()]);
        }
    }

    public function processAfterPayment($order, $payment_details, $payment_status = Order::PAYMENT_STATUS_PAID): bool
    {
        try {
            $this->updatePaymentStatus($order, $payment_status, $payment_details);

            // stores the pdf for invoice
            $this->storesPdfInvoice($order);
            // send new order mail
            $this->sendNewOrderMail($order);

            $this->calculateCommission($order);

            //lets call OMS
            if ($payment_status == Order::PAYMENT_STATUS_PAID) {
                $this->syncOmsOrder($order);
                // call OMS update tracking number
                $this->callOmsUpdateTrackingNum($order);
            }
            // exit;

//        $this->service->processAffiliatePoints($order);
//        $this->service->processClubPoints($order);

            $data = [
                'commission_calculated' => 1,
            ];
            $this->orderService->update($order->id, $data);

            //flash(__('Payment completed'))->success();
            return true;
        } catch (Exception $e) {
            Log::channel('payment')
                ->error('Error CheckoutService@processAfterPayment',
                    [
                        'orderId' => $order->id,
                        'error' => $e->getMessage(),
                        'payment_details' => $payment_details
                    ]);
            return false;
        }
    }

    private function updatePaymentStatus($order, $payment_status, $payment_details) {
        $data = [
            'payment_status' => $payment_status,
            'payment_details' => $payment_details
        ];
        return $this->orderService->update($order->id, $data);
    }
}