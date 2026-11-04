<?php
namespace App\Repositories;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\SplitOrder;
use App\User;

class OrderDetailRepository extends AbstractRepo
{
    const PER_PAGE = 20;

    public function getModel(): string
    {
        return OrderDetail::class;
    }

    public function getItemsBySplitOrder(SplitOrder $order)
    {
        $params = [
            [
                'column' => 'split_order_id',
                'value' => $order->id,
            ]
        ];
        return $this->getAllBy($params);
    }
}
