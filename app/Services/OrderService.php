<?php

namespace App\Services;

use App\Models\Order;
use App\ObjectModels\Cart;
use App\ObjectModels\CartInterface;
use App\Repositories\OrderDetailRepository;
use App\Repositories\OrderRepository;
use App\Repositories\SplitOrderRepository;
use App\Services\CartService;
use App\User;
use Illuminate\Http\Request;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Services\AbstractService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderService extends AbstractService
{
    protected $user;
    protected $repo;
    protected $splitOrderRepo;
    protected $orderDetailRepo;

    const VOUCHER_USAGE_APPLIED_DATE = '2021-09-23';

    public function __construct(
        OrderRepository $repo,
        SplitOrderRepository $splitOrderRepo,
        OrderDetailRepository $orderDetailRepo
    )
    {
        parent::__construct($repo);
        $this->splitOrderRepo = $splitOrderRepo;
        $this->orderDetailRepo = $orderDetailRepo;
    }

    public function remove($orderId)
    {
        $order = $this->get($orderId);

        if ($order) {
            // reverting stock quantity
            foreach ($order->orderDetails as $item) {
                $product = Product::find($item->product->id);
                $product->current_stock += $item->quantity;
                $product->num_of_sale--;
                $product->save();
            }

            $order->orderDetails()->delete();
            $order->coupons()->detach();
            $order->delete();
        }
    }

    public function getByCode(string $code)
    {
        return $this->repo->getByCode($code);
    }

    public function buildParamsFromRequest(Request $request)
    {
        $params = [];

        $params['page'] = $request->input('page');
        $params['per_page'] = $request->input('per_page');

        if ($request->has('delivery_status')) {
            $params['delivery_status'] = $request->input('delivery_status');
        }

        if ($request->has('payment_status')) {
            $params['payment_status'] = $request->input('payment_status');
        }

        if ($request->has('code') && $request->input('code') != '') {
            $params['code'] = $request->input('code');
        }

        if ($request->has('start_date') && $request->input('start_date') != '') {
            $params['start_date'] = Carbon::createFromFormat('Y-m-d', $request->input('start_date'))->startOfDay()->timestamp;
        }

        if ($request->has('end_date') && $request->input('end_date') != '') {
            $params['end_date'] = Carbon::createFromFormat('Y-m-d', $request->input('end_date'))->endOfDay()->timestamp;
        }

        return $params;
    }

    /**
     * Get list of order by User
     * @param User $user
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getOrders(User $user, Request $request): \Illuminate\Database\Eloquent\Collection
    {
        return $this->repo->getOrders($user, $this->buildParamsFromRequest($request));
    }

    public function getSplitOrders(User $user, Request $request)
    {
        return $this->splitOrderRepo->getOrders($user, $this->buildParamsFromRequest($request));
    }

    /**
     * Get order info
     * @param User $user
     * @param Order $order
     * @return array
     */
    public function getOrderInfo(User $user, Order $order): array
    {
        return [];
    }

    public function getOrderItems($order)
    {
        return $this->orderDetailRepo->getItemsBySplitOrder($order);
    }

    /**
     * For command ysg:split-orders
     */
    public function runSplitOrders()
    {
        // get all orders from date that have record order_id in coupon_usage table
        $params = [
            // 'payment_status' => Order::PAYMENT_STATUS_PAID,
            'from_date' => self::VOUCHER_USAGE_APPLIED_DATE,
        ];
        $orders = $this->repo->getList($params);

        foreach ($orders as $order) {
            if (0 === count($order->splitOrders)) {
                try {
                    $cart = CartService::createCartFromOrder($order);
                    $this->splitOrder($cart, $order);
                } catch (\Exception $e) {
                    continue;
                }

            }
        }
    }

    public function splitOrder(CartInterface $cart, Order $order)
    {
        $sellers = $cart->getItemsBySellerGroup();
        foreach ($sellers as $userId => $seller) {
            $filter['shopId'] = $seller['info']['shopId'];

            $data = [
                'seller_id' => $userId,
                'order_id' => $order->id,
                'sub_total' => $cart->getSubTotal($filter),
                'grand_total' => $cart->getGrandTotalByShopId($seller['info']['shopId']),
                'shipping_cost' => $cart->getShippingFeeByShopId($seller['info']['shopId']),
                'discount_amount' => $cart->getCouponDiscountByShopId($seller['info']['shopId']),
                'ysg_discount_amount' => $cart->getYsgCouponDiscountByShopId($seller['info']['shopId']),
                'cash_discount_amount' => $cart->getCashVoucherDiscountByShopId($seller['info']['shopId']),
                'coupon_code' => $cart->getShopCoupon($seller['info']['shopId']),
                'created_at' => $order->created_at,
            ];

            $this->splitOrderRepo->create($data);
        }

        foreach ($order->orderDetails as $detail) {
            $this->syncSplitFromDetail($detail);
        }
    }

    # YSG-951: SQL to correct split_order delivery_status:
    # UPDATE split_orders s LEFT JOIN order_details od ON s.id = od.split_order_id SET s.delivery_status = od.delivery_status WHERE s.delivery_status != od.delivery_status
    public function syncSplitFromDetail($orderDetail) {
        $freshOrderDetail = $this->orderDetailRepo->find($orderDetail->id);

        if ($order = $freshOrderDetail->order) {
            $params = [
                [
                    'column' => 'order_id',
                    'value' => $order->id,
                ],
                [
                    'column' => 'seller_id',
                    'value' => $freshOrderDetail->seller_id,
                ]
            ];

            $splitOrder = $this->splitOrderRepo->filterBy($params);
            if ($splitOrder) {
                $freshOrderDetail->split_order_id = $splitOrder->id;
                $freshOrderDetail->save();

                $splitOrder->delivery_status = $freshOrderDetail->delivery_status;
                $splitOrder->tracking_number = $freshOrderDetail->tracking_number;
                $splitOrder->shipping_type = $freshOrderDetail->shipping_type;
                $splitOrder->save();
            }
        }
    }

    public function couponUsage($cart, $order)
    {
        return $this->repo->couponUsage($cart, $order);
    }

    public function getSaleData(string $from, string $to)
    {
        return $this->repo->getSaleData($from, $to);
    }

    public function restockOrder(Order $order, $userId = null)
    {
        if ($order->restock_status == 1) {
            throw new \Exception('This order has already been restocked.');
        }

        if ($order->payment_status !== 'unpaid') {
            throw new \Exception('Only unpaid orders can be restocked.');
        }

        DB::beginTransaction();

        try {
            $orderDetails = $order->orderDetails;

            foreach ($orderDetails as $orderDetail) {
                $product = Product::find($orderDetail->product_id);

                if (!$product) {
                    Log::warning("Product with ID {$orderDetail->product_id} not found for order {$order->code}");
                    continue;
                }

                $quantity = $orderDetail->quantity;

                if ($orderDetail->variation && $orderDetail->variation !== '') {
                    $variant = $product->getVariant($orderDetail->variation);
                    if ($variant) {
                        $variant->qty += $quantity;
                        $variant->save();
                    }
                } else {
                    $product->current_stock += $quantity;
                    $product->save();
                }

                if ($product->num_of_sale > 0) {
                    $product->num_of_sale = max(0, $product->num_of_sale - 1);
                    $product->save();
                }

                $orderDetail->delivery_status = 'cancelled';
                $orderDetail->save();
            }

            $splitOrders = $order->splitOrders;
            foreach ($splitOrders as $splitOrder) {
                $splitOrder->delivery_status = 'cancelled';
                $splitOrder->save();
            }

            $order->restock_status = 1;
            $order->save();

            DB::commit();

            $logData = [
                'order_id' => $order->id,
                'order_code' => $order->code,
                'items_count' => $orderDetails->count(),
                'order_details_cancelled' => $orderDetails->count(),
                'split_orders_cancelled' => $splitOrders->count(),
            ];

            if ($userId) {
                $logData['restocked_by'] = $userId;
                Log::info("Manual restock and cancellation completed for order", $logData);
            } else {
                $logData['created_at'] = $order->created_at;
                Log::info("Automatic restock and cancellation completed for order", $logData);
            }

            return [
                'success' => true,
                'message' => 'Order has been restocked and cancelled successfully.',
                'order_details_count' => $orderDetails->count(),
                'split_orders_count' => $splitOrders->count()
            ];

        } catch (\Exception $e) {
            DB::rollback();

            $errorLogData = [
                'order_id' => $order->id,
                'order_code' => $order->code,
                'error' => $e->getMessage()
            ];

            if ($userId) {
                $errorLogData['user_id'] = $userId;
            }

            Log::error("Failed to restock order", $errorLogData);
            throw $e;
        }
    }

    public function canRestockOrder(Order $order): bool
    {
        return $order->payment_status === 'unpaid' && $order->restock_status == 0;
    }

    public function getTotalOrdersByUser($userId)
    {
        return $this->repo->getTotalOrdersByUser($userId);
    }

    public function runUpdateYsgDiscountAmountInSplitOrders() {
        $allMismatchSplitOrders = [];
        $orders = $this->repo->getList([]);
        foreach ($orders as $order) {
            if (0 !== count($order->splitOrders)) {
                try {
                    $cart = CartService::createCartFromOrder($order);
                    $mismatches = $this->updateYsgDiscountAmount($cart, $order);

                    if (!empty($mismatches)) {
                        $allMismatchSplitOrders = array_merge($allMismatchSplitOrders, $mismatches);
                    }

                } catch (\Exception $e) {
                    Log::error('Run update ysg discount error', [
                        'order_id' => $order->id,
                        'error'    => $e->getMessage(),
                    ]);
                    continue;
                }
            }
        }

        if (!empty($allMismatchSplitOrders)) {
            $fileName = 'split_order_discount_mismatch_' . now()->format('Ymd_His') . '.txt';
            $filePath = storage_path('logs/' . $fileName);
            $lines = [];
            // header
            $lines[] = "order_id\tsplit_order_id\tseller_id";
            foreach ($allMismatchSplitOrders as $row) {
                $lines[] = implode("\t", [
                    $row['order_id'],
                    $row['split_order_id'],
                    $row['seller_id'],
                ]);
            }

            file_put_contents($filePath, implode(PHP_EOL, $lines));

            Log::info('Mismatch split order file generated', ['path' => $filePath]);
        } else {
            Log::info('No mismatch split order found');
        }
    }

    public function updateYsgDiscountAmount($cart, $order) {
        $mismatchSplitOrders = [];
        $sellers = $cart->getItemsBySellerGroup();

        foreach ($sellers as $userId => $seller) {
            try {
                $splitOrder = $order->splitOrders()->where('seller_id', $userId)->first();

                $discountAmount = $splitOrder->discount_amount;
                $sellerDiscountAmount = $cart->getSellerCouponDiscountByShopId($seller['info']['shopId']);
                $ysgDiscountAmount = $cart->getYsgCouponDiscountByShopId($seller['info']['shopId']);
                $cashDiscountAmount = $cart->getCashVoucherDiscountByShopId($seller['info']['shopId']);

                if ($discountAmount === 0) continue;

                if ($discountAmount !== $sellerDiscountAmount + $ysgDiscountAmount + $cashDiscountAmount) {
                    if (empty($splitOrder->coupon_code)) {
                        $ysgDiscountAmount = $discountAmount;
                        $cashDiscountAmount = 0;
                    } else {
                        $mismatchSplitOrders[] = [
                            'order_id'       => $order->id,
                            'split_order_id' => $splitOrder->id,
                            'seller_id'      => $userId,
                        ];
                        continue;
                    }
                };

                $data = [
                    'ysg_discount_amount' => $ysgDiscountAmount,
                    'cash_discount_amount' => $cashDiscountAmount
                ];

                $status = $splitOrder->update($data);

                Log::info('Update ysg discount amount in split orders', ['order_id' => $order->id, 'ysg_discount' => $ysgDiscountAmount, 'cash_discount' => $cashDiscountAmount, 'status' => $status]);
            } catch (\Exception $e) {
                Log::error('Update ysg discount amount in split orders', ['order_id' => $order->id, 'error' => $e->getMessage()]);
            }
        }

        return $mismatchSplitOrders;
    }
}