<?php
namespace App\Services\Oms;

use App\Models\Order;
use App\Notifications\OrderStatusChanged;
use App\Repositories\UserRepository;
use App\Repositories\ProductRepository;
use Illuminate\Support\Facades\Log;
use Ixudra\Curl\Facades\Curl;
use DB;
use Mail;
use Carbon\Carbon;
use Throwable;

class OrderService extends AbstractService
{
    /**
     * @var UserRepository
     *
     */
    protected $userRepo;

    /**
     * @var ProductRepository
     *
     */
    protected $productRepo;

    /**
     * @var string
     */
    protected $accessToken;

    /**
     * @var string
     */
    protected $time;

    /**
     * @var \App\User
     */
    protected $currentUser;

    const LIMIT = 9999;

    /**
     * The date OMS stop support to get order
     */
    const MIGRATION_DATE = '2020-12-01';

    public function __construct(UserRepository $userRepo, ProductRepository $productRepo)
    {
        $this->userRepo = $userRepo;
        $this->productRepo = $productRepo;
    }


    public function setTime($time)
    {
        $this->time = $time;
    }

    public function setCurrentUser($currentUser)
    {
        $this->currentUser = $currentUser;
    }

    public function runSync()
    {
        $users = $this->userRepo->getCompanies();
        $this->setTime(date("Y-m-d", time()-5*86400) . 'T'. date("H:i:s", time()-86400) . '.000Z');
        $this->getLog()->info('Time is: ' . $this->time);
        foreach ($users as $key => $user) {
            $this->sync($user);
        }
    }

    public function sync($user) {
        $this->getLog()->info("syncing company $user->company_id");
        try {
            $accessToken = $this->getAccessToken($user->company_id);
            $this->setAccessToken($accessToken);
            $this->setCurrentUser($user);

            $this->syncProducts();
            $this->syncStocks();
            $this->syncOrders();

        } catch (\Exception $e) {
            $this->getLog()->error($e->getMessage());
            $this->getLog()->error($e->getTraceAsString());
        }
    }

    public function syncOrders()
    {
        $this->getLog()->info('Start syncing Orders');
        $orderDetails = $this->currentUser->syncOrderDetails()->with(['order' => function($query) {
            $query
                ->where('payment_status', 'paid')
                ->where('created_at', '>=', self::MIGRATION_DATE)
                ;
        }, 'order.user'])->groupBy('order_id')->get();

        $current_deliverystatus = '';
        $statusdelivery = '';
        $orderid = '';
        foreach ($orderDetails as $orderDetail) {
            if (empty($orderDetail->order)) {
                continue;
            }
            $orderid = $orderDetail->order_id;
            $ordernumber = $orderDetail->order->code;
            $current_deliverystatus = $orderDetail->delivery_status;
            $seller_id = $orderDetail->seller_id;

            $response = Curl::to(config('api.oms') . 'v1/order/query/ysg-sg/' . $ordernumber)
                            ->withBearer($this->accessToken)
                            ->asJson()
                            ->get();
            var_dump($ordernumber);

            if ($response != NULL && $response->messages == 'Success') {
                $shippingInfo = $response->shippingInfo;
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

                $this->getLog()->info("Order ID: $orderid");
                $this->getLog()->info("Current status: $current_deliverystatus");
                $this->getLog()->info("Changed status: $statusdelivery");
                //update status
                DB::table('order_details')->where('order_id', $orderid)->where('seller_id', $seller_id)->where('delivery_status', '!=', 'cancelled')
                    ->update([
                            'trackingNum' => $shippingInfo[0]->trackingNum,
                            'delivery_status' => $statusdelivery,
                            'updated_at' => Carbon::now()
                        ]
                    );
                if (($statusdelivery == 'on_delivery') && ($current_deliverystatus != $statusdelivery)) {
                    $order = $orderDetail->order;
                    $order->user->notify(new OrderStatusChanged($orderDetail, $statusdelivery, $orderDetail->shipping_type));
                    $this->getLog()->info('Send email Your Shipment is on its way! to ' . $orderDetail->order->user->email);
                }
                if (($statusdelivery == 'delivered') && (($current_deliverystatus == 'on_delivery') || ($current_deliverystatus == 'on_review') || ($current_deliverystatus == 'pending'))) {
                    $order = $orderDetail->order;
                    $order->user->notify(new OrderStatusChanged($orderDetail, $statusdelivery, $orderDetail->shipping_type));
                    $this->getLog()->info('Send email Your shipment has been successfully delivered! to ' . $orderDetail->order->user->email);
                }
            }
        }
    }

    public function getLog()
    {
        return Log::channel('oms');
    }

    public function syncOrderUpdate(Order $order): bool
    {

        try {
            //lets call OMS
            foreach ($order->orderDetails as $key => $orderDetail) {
                if ($orderDetail->product->user->user_type == 'seller') {
                    $seller = $orderDetail->product->user->seller;
                    $user = $seller->user;

                    $company_shipping_type = $user->company_shipping;
                    $seller_id = $seller->user_id;

                    if ($company_shipping_type == 2) {
                        $shipping_cost = $user->shipping_cost;
                        $shipping_cost_above = $user->shipping_cost_above;
                    } else {
                        $shipping_cost = 2;
                        $shipping_cost_above = 80;
                    }

                    $accessToken = $this->getAccessToken($user->company_id);
                    $this->setAccessToken($accessToken);

                    $data = $this->buildOrderData($order, $seller_id, $shipping_cost, $shipping_cost_above);

                    $response = Curl::to(config('api.oms') . '/v1/order/update')
                        ->withData($data)
                        ->withBearer($this->accessToken)
                        ->asJson()
                        ->patch();

                    if (is_null($response) || $response->statusCode != 0) {
                        Log::error('failed syncOrderUpdate:'. $order->id. ' result: '.print_r($response, true));
                        return false;
                    }

                }
            }
            // exit;
        } catch (Throwable $e) {
            Log::error('failed syncOrderUpdate:'. $order->id);
            Log::error('error message syncOrderUpdate: ' . $e->getMessage());
            return false;
        }

        return true;
    }

    public function buildOrderItemsData(Order $order, $seller_id): array
    {
        $result = [];
        foreach ($order->orderDetails as $key => $orderDetail) {
            if ($seller_id == $orderDetail->product->user->seller->user_id) {
                //subtotal += $orderDetail->price;
                $result[] = [
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
                ];
            }
        }
        return $result;
    }

    public function buildOrderData(Order $order, $seller_id, $shipping_cost, $shipping_cost_above): array
    {
        $subtotal = 0;
        foreach ($order->orderDetails as $key => $orderDetail) {
            if ($seller_id == $orderDetail->product->user->seller->user_id) {
                $subtotal += $orderDetail->price;
                $channelName = $orderDetail->product->user->seller->oms_channel_name;
            }
        }

        if ($subtotal >= $shipping_cost_above) {
            $Pshipping = 0;
        } else {
            $Pshipping = $shipping_cost;
        }

        $shippingAddress = json_decode($order->shipping_address);

        $data = [
            'orderNumber' => $order->code,
            'orderDate' => $order->created_at->toIso8601String(),
            'marketplaceCode' => $channelName,
            'orderType' => "sale-order",
            'deliveryMethod' => "home-delivery",
            'originalOrderNumber' => '',
            'referralCode' => '',
            'customerType' => 'guest',
            'customerInfo' =>
                array(
                    'address' => $shippingAddress->address,
                    'country' => 'SG',
                    'customerId' => $orderDetail->user_id,
                    'email' => $shippingAddress->email,
                    'name' => $shippingAddress->name,
                    'phoneNum' => $shippingAddress->phone,
                    'postCode' => $shippingAddress->postal_code,
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
                [
                    [
                        'amount' => $order->grand_total,
                        'mode' => "Credit Card",
                        'referenceNumber' => '', //not sure how to get it
                        'remarks' => '',
                    ],
                ],
            'shippingInfo' =>
                [
                    [
                        'country' => 'SG',
                        'deliveryAddress' => $shippingAddress->address,
                        'items' => [
                            'additionalProp1' => 0,
                            'additionalProp2' => 0,
                            'additionalProp3' => 0
                        ],
                        'postCode' => $shippingAddress->postal_code,
                        'recipientName' => $shippingAddress->name,
                        'recipientPhoneNumber' => $shippingAddress->phone,
                        'region' => 'SG',
                        'shippedDate' => '',
                        'shippingLabel' => '',
                        'shippingMethod' => 'Dropshipping',
                        'status' => 'order-received',
                        'trackingNum' => '',
                        'City' => '',
                        'State' => '',
                        'CountryCode' => '',
                    ],
                ],
            'Comments' => '',
            'Status' => Order::PAYMENT_STATUS_PAID,
            'CancelledReason' => '',
            'CreatedOn' => $order->created_at->toIso8601String(),
            'LastUpdatedOn' => date('c'),
            'items' => $this->buildOrderItemsData($order, $seller_id),
            'shippingServiceLevel' => '3DA',
            'OrderInstructions' => [
                [
                    'instruction_code' => '',
                    'instruction_type' => '',
                    'instruction_text' => ''
                ]
            ],
        ];

        return $data;
    }
}
