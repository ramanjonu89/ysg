<?php
namespace App\Repositories\Api\Mobile;

use App\Models\SplitOrder;
use App\Models\Order;
use App\Repositories\AbstractRepo;

class SplitOrderRepository extends AbstractRepo
{
    const PER_PAGE = 15;

    public function getModel(): string
    {
        return SplitOrder::class;
    }

    public function getByUserId($userId = null, int $perPage = self::PER_PAGE, int $page = 1, array $params = [])
    {
        $query = $this->model::query()
            ->with([
                'seller.shop',
                'orderDetails.product',
                'order'
            ]);
        if (!empty($userId)) { 
            $query->whereHas('order', function($query) use ($userId) {
                $query->where('user_id', $userId);
            });
        }

        // Apply filters
        $this->applyFilters($query, $params);

        return $query->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Apply filters to the query
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $params
     * @return void
     */
    private function applyFilters($query, array $params)
    {
        // Filter by delivery status
        if (!empty($params['delivery_status'])) {
            if ($params['delivery_status'] == 'order_placed') {
                $query->whereIn('delivery_status', [Order::SHIPPING_STATUS_PENDING, Order::SHIPPING_STATUS_ON_REVIEW]);
            } else {
                $query->where('delivery_status', $params['delivery_status']);
            }
            
        }

        if (!empty($params['keyword'])) {
            $keyword = $params['keyword'];
            $query->where(function($query) use ($keyword) {
                $query->whereHas('orderDetails', function($q) use ($keyword) {
                    $q->where('product_name', 'LIKE', $keyword . '%');
                })
                ->orWhereHas('seller', function($q) use ($keyword) {
                    $q->where('name', 'LIKE', $keyword . '%');
                })
                ->orWhereHas('order', function($q) use ($keyword) {
                    $q->where('code', 'LIKE', $keyword . '%');
                });
            });
        }

        if (!empty($params['payment_status'])) {
            $query->whereHas('order', function($query) use ($params) {
                $query->where('payment_status', $params['payment_status']);
            });
        }

        if (!empty($params['date_from'])) {
            $query->whereDate('created_at', '>=', $params['date_from']);
        }

        if (!empty($params['date_to'])) {
            $query->whereDate('created_at', '<=', $params['date_to']);
        }
    }
}