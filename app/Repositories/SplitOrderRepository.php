<?php
namespace App\Repositories;

use App\Models\SplitOrder;
use App\User;
use Illuminate\Support\Facades\DB;

class SplitOrderRepository extends AbstractRepo
{
    const PER_PAGE = 20;

    public function getModel(): string
    {
        return SplitOrder::class;
    }

    public function getOrders(User $user, array $params = [])
    {
        $query = DB::table('split_orders as so')
            ->leftJoin('orders as o', 'o.id', '=', 'so.order_id')
            ->leftJoin('users as customer', 'o.user_id', '=', 'customer.id');

        $query->select('so.id', 'o.code', 'customer.name as customer_name', 'so.grand_total', 'o.payment_status', 'so.delivery_status', 'o.created_at', 'so.shipping_type');
        $query->where('so.seller_id', $user->id);
        if (!empty($params['payment_status'])) {
            $query->where('o.payment_status', $params['payment_status']);
        }

        if (!empty($params['delivery_status'])) {
            $query->where('so.delivery_status', $params['delivery_status']);
        }

        if (!empty($params['code'])) {
            $query->where('o.code', $params['code']);
        }

        if (!empty($params['start_date'])) {
            $query->where('o.date', '>=', $params['start_date']);
        }

        if (!empty($params['end_date'])) {
            $query->where('o.date', '<=', $params['end_date']);
        }

        $query->orderBy('so.id', 'desc');

        return $query->paginate($this->getLimit($params));
    }
}
