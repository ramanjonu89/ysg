<?php
namespace App\Repositories;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\CouponUsage;
use App\User;
use Illuminate\Support\Facades\Auth;

class OrderRepository extends AbstractRepo
{
    const PER_PAGE = 20;

    public function getModel(): string
    {
        return Order::class;
    }

    public function getByCode(string $code)
    {
        return $this->model::whereCode($code)->first();
    }

    /**
     * @param  User  $user
     * @param  array  $params
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getOrders(User $user, array $params = [])
    {
        $query = $this->model->with(['user', 'orderDetails', 'coupons']);
        $query->whereIn('id', function ($q) use ($user) {
            $q->select('order_id')->from('order_details')
                ->where('seller_id', $user->id)
                ->groupBy('order_id');
        });

        if (!empty($params['payment_status'])) {
            $query->where('payment_status', $params['payment_status']);
        }

        if (!empty($params['code'])) {
            $query->where('code', $params['code']);
        }

        $query->orderBy('id', 'desc');

        return $query->paginate(self::PER_PAGE);
    }

    public function getList(array $params)
    {
        $query = $this->model->with(['orderDetails', 'coupons']);
        if (!empty($params['payment_status'])) {
            $query->where('payment_status', $params['payment_status']);
        }

        if (!empty($params['code'])) {
            $query->where('code', $params['code']);
        }

        if (!empty($params['from_date'])) {
            $query->where('created_at', '>=', $params['from_date']);
        }

        return $query->get();
    }

    public function couponUsage($cart, $order)
    {
        foreach ($cart->getCoupons() as $key => $coupon) {
            $coupon_usage                 = new CouponUsage;
            $coupon_usage->user_id        = Auth::user()->id;
            $coupon_usage->coupon_id      = $coupon->getId();
            $coupon_usage->order_id       = $order->id;
            $coupon_usage->discount_value = $cart->getCouponDiscountValue($coupon);
            $coupon_usage->save();
        }
    }

    public function getSaleData($from, $to)
    {
        $orders = Order::orderByDesc('id')
                         ->whereBetween('created_at', [$from, $to])->get();

        return $orders;
    }

    public function getTotalOrdersByUser($userId)
    {
        return $this->model
            ->where('user_id', $userId)
            ->where('payment_status', Order::PAYMENT_STATUS_PAID)
            ->count();
    }
}
