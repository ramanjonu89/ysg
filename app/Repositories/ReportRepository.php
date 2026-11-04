<?php
namespace App\Repositories;

use App\Models\CouponUsage;
use App\Models\Order;
use App\Models\SplitOrder;
use App\User;
use Illuminate\Support\Facades\DB;

class ReportRepository extends AbstractRepo
{
    const LOW_STOCK = 5;

    protected $sellerRepo;

    public function __construct(
        SellerRepository $sellerRepository
    )
    {
        $this->sellerRepo = $sellerRepository;

        return parent::__construct();
    }

    public function getModel(): string
    {
        return SplitOrder::class;
    }

    public function getSummaryReport(User $user)
    {
        $pendingOrders = $this->getTotalOrdersBySellerAndDeliveryStatus($user->id, 'pending');
        $cancelOrders = $this->getTotalOrdersBySellerAndDeliveryStatus($user->id, 'cancel');
        $unpaidOrders = $this->getTotalOrdersBySellerAndPaymentStatus($user->id, 'unpaid');
        $lowStockProducts = $this->getLowStockProductsBySeller($user->id);
        return [
            'pending' => $pendingOrders??0,
            'unpaid' => $unpaidOrders??0,
            'cancel' => $cancelOrders??0,
            'lowStockProducts' => $lowStockProducts??0,
        ];
    }

    public function getBusinessReport(User $user)
    {

        $products = $this->getProductsBySeller($user->id);
        $totalSales = $this->getTotalSalesBySeller($user->id);
        $successfulOrders = $this->getSuccessfulOrdersBySeller($user->id);

//        $revenue = $this->getRevenueBySeller($user->id);
        $revenue = $this->sellerRepo->getRevenue($user->id);
        $buyers = $this->getBuyersBySeller($user->id);
        $items = $this->getTotalItemsBySeller($user->id);
        $orders = $this->getOrdersBySeller($user->id);


        return [
            'products' => $products??0,
            'totalSales' => $totalSales??0,
            'successfulOrders' => $successfulOrders??0,
            'revenue' => $revenue??0,
            'buyers' => $buyers??0,
            'items' => $items??0,
            'orders' => $orders??0,
        ];
    }

    public function getSuccessfulOrdersBySeller($sellerId)
    {
        return DB::table('order_details')
            ->select(DB::raw('count(order_details.id) as total'))
            ->where('order_details.seller_id', $sellerId)
            ->where('order_details.delivery_status', 'delivered')
            ->value('total');
    }

    public function getTotalSalesBySeller($sellerId)
    {
        return DB::table('order_details')
            ->select(DB::raw('count(order_details.id) as total'))
            ->where('order_details.seller_id', $sellerId)
            ->value('total');
    }

    public function getProductsBySeller($sellerId)
    {
        return DB::table('products')
            ->select(DB::raw('count(products.id) as total'))
            ->where('products.user_id', $sellerId)
            ->value('total');
    }

    public function getOrdersChartData(User $user, $fromDate, $toDate): \Illuminate\Support\Collection
    {
        return DB::table('split_orders')
            ->select(DB::raw('count(id) as total, DATE(created_at) as date'))
            ->where('seller_id', $user->id)
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->get();
    }

    public function getRevenueBySeller($sellerId)
    {
        return DB::table('split_orders')
                        ->leftJoin('orders', 'split_orders.order_id', 'orders.id')
                        ->select(DB::raw('sum(split_orders.grand_total) as total'))
                        ->where('split_orders.seller_id', $sellerId)
                        ->where('orders.payment_status', 'paid')
                        ->value('total');
    }

    public function getBuyersBySeller($sellerId)
    {
        return DB::table('split_orders')
                        ->leftJoin('orders', 'split_orders.order_id', 'orders.id')
                        ->select(DB::raw('count(distinct orders.user_id) as total'))
                        ->where('split_orders.seller_id', $sellerId)
                        ->where('orders.payment_status', 'paid')
                        ->value('total');
    }

    public function getOrdersBySeller($sellerId)
    {
        return DB::table('split_orders')
                        ->leftJoin('orders', 'split_orders.order_id', 'orders.id')
                        ->select(DB::raw('count(split_orders.id) as total'))
                        ->where('split_orders.seller_id', $sellerId)
                        ->where('orders.payment_status', 'paid')
                        ->value('total');
    }

    public function getTotalItemsBySeller($sellerId)
    {
        return DB::table('order_details')
                        ->leftJoin('orders', 'order_details.order_id', 'orders.id')
                        ->select(DB::raw('sum(order_details.quantity) as total'))
                        ->where('order_details.seller_id', $sellerId)
                        ->where('orders.payment_status', 'paid')
                        ->value('total');
    }

    public function getTotalOrdersBySellerAndDeliveryStatus($sellerId, $deliveryStatus)
    {
        return DB::table('split_orders')
                        ->select(DB::raw('count(id) as total'))
                        ->where('seller_id', $sellerId)
                        ->where('delivery_status', $deliveryStatus)
                        ->value('total');
    }

    public function getTotalOrdersBySellerAndPaymentStatus($sellerId, $paymentStatus)
    {
        return DB::table('split_orders')
                        ->leftJoin('orders', 'split_orders.order_id', 'orders.id')
                        ->select(DB::raw('count(split_orders.id) as total'))
                        ->where('split_orders.seller_id', $sellerId)
                        ->where('orders.payment_status', $paymentStatus)
                        ->value('total');
    }

    public function getLowStockProductsBySeller($sellerId)
    {
        return DB::table('products')
                        ->select(DB::raw('count(id) as total'))
                        ->where('user_id', $sellerId)
                        ->where('current_stock', '<', self::LOW_STOCK)
                        ->value('total');
    }
    
    public function getListCouponsReport($from, $to, $sellerId)
    {
        if ($sellerId > 0 ) {
            return DB::table('coupon_usages')
                            ->join('coupons', 'coupon_usages.coupon_id', '=', 'coupons.id')
                            ->leftJoin('orders', function ($join) {
                                $join->on('coupon_usages.order_id', '=', 'orders.id');
                                $join->on('coupon_usages.user_id', '=', 'orders.user_id');
                            })
                            ->join('split_orders','coupon_usages.order_id','=','split_orders.order_id')
                            ->where('orders.payment_status', '=', 'paid')
                            ->where('coupons.seller_id', $sellerId)
                            ->where(function ($query) use ($from, $to) {
                                $query->where('coupon_usages.created_at', '>', $from)
                                    ->where('coupon_usages.created_at', '<', $to);
                            })
                            ->leftJoin('shops', 'coupons.seller_id', '=', 'shops.id')
                            ->select('coupon_usages.created_at as couponused', 'coupon_usages.*', 'coupons.*', 'orders.code as order_id', 'orders.date', 'shops.name as shop_name')
                            ->distinct()
                            ->get();
        }

        return DB::table('coupon_usages')
                        ->join('coupons', 'coupon_usages.coupon_id', '=', 'coupons.id')
                        ->leftJoin('orders', function ($join) {
                            $join->on('coupon_usages.order_id', '=', 'orders.id');
                            $join->on('coupon_usages.user_id', '=', 'orders.user_id');
                        })
                        ->join('split_orders','coupon_usages.order_id','=','split_orders.order_id')
                        ->where('orders.payment_status', '=', 'paid')
                        ->where(function ($query) use ($from, $to) {
                            $query->where('coupon_usages.created_at', '>', $from)
                                ->where('coupon_usages.created_at', '<', $to);
                        })
                        ->select('coupon_usages.created_at as couponused', 'coupon_usages.*', 'coupons.*', 'orders.code as order_id', 'orders.date',
                                DB::raw('coupons.*, 
                                    CASE WHEN coupons.owner = "seller" THEN shops.name ELSE NULL END AS shop_name'))
                        ->leftJoin('shops', 'coupons.seller_id', '=', 'shops.id')
                        ->distinct()
                        ->get();
    }
}
