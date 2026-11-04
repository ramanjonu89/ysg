<?php
namespace App\Repositories;

use App\Models\Coupon;
use Illuminate\Support\Carbon;

class CouponRepository extends AbstractRepo
{
    public function getModel(): string
    {
        return Coupon::class;
    }

    public function getUsagesCount($id)
    {
        $coupon = $this->model->find($id);

        return $coupon->number_of_uses;
    }

    public function getList(array $params)
    {
        $query = $this->model->query();

        if (!empty($params['user_id'])) {
            $query->where('seller_id', $params['user_id']);
        }

        if (!empty($params['code'])) {
            $query->where('code', 'like', '%' . $params['code'] . '%');
        }

        if (!empty($params['start_date'])) {
            $query->where('start_date', '>=', $params['start_date']);
        }

        if (!empty($params['end_date'])) {
            $query->where('end_date', '<=', $params['end_date']);
        }

        if (!empty($params['owner'])) {
            $query->where('owner', $params['owner']);
        }

        if (isset($params['active'])) {
            $query->where('is_active', $params['active']);
        }

        if (!empty($params['type'])) {
            $query->whereIn('type', $params['type']);
        }

        if (isset($params['ongoing'])) {
            $now = strtotime('now');
            if (0 === (int)$params['ongoing']) {
                $query->where(function($q) use ($now) {
                    $q->where('start_date', '>', $now)
                        ->orWhere('end_date', '<', $now);
                });
            } else {
                $query->where(function($q) use ($now)  {
                    $q->where('start_date', '<=', $now)
                        ->where('end_date', '>=', $now);
                });
            }
        }

        $query->orderBy('id', 'desc');

        return $query->paginate($this->getLimit($params));
    }

    public function getYsgCouponList($params) {
        $query = $this->model->query();

        $query->leftJoin('coupon_usages', 'coupons.id', '=', 'coupon_usages.coupon_id');
        $query->where('owner', $params['owner']);
        $query->where('is_active', $params['is_active']);
        $query->where('published', $params['published']);

        $query->where('start_date',   '<=', time());
        $query->where('end_date', '>=', time());
        $query->where('type', '<>', Coupon::TYPE_CASH_BASE);

        $query->orderBy('created_at', 'desc');
        $query->groupBy('coupons.id');
        $query->havingRaw('COUNT(coupon_usages.id) < JSON_EXTRACT(coupons.details, "$.max_coupon")');

        return $query->get();
    }

    public function updatePublished($id): bool
    {
        $model = $this->model::find($id);
        if ($model) {
            $model->published = !$model->published;
            if ($model->save()) {
                return true;
            }
        }
        return false;
    }

    public function toggleActive($id): bool
    {
        $model = $this->model::find($id);
        if ($model) {
            $model->is_active = !$model->is_active;
            if ($model->save()) {
                return true;
            }
        }
        return false;
    }
}
