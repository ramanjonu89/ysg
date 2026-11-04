<?php
namespace App\Repositories;

use App\Models\Product;
use App\Models\Shop;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class ShopRepository extends AbstractRepo
{
    const PER_PAGE = 15;

    public function getModel(): string
    {
        return Shop::class;
    }

    /**
     * get list of models
     * @param array $params
     * @return mixed
     */
    public function getList(array $params)
    {
        $search = $params['search'] ?? null;
        $verificationStatus = $params['verification_status'] ?? null;

        $query = $this->model::query()
            ->leftJoin('sellers', 'shops.user_id', '=', 'sellers.user_id');

        $query->whereHas('user', function (Builder $query) use ($verificationStatus) {
            $query->where('banned', '<>', '1');
        });

        if ($verificationStatus != null) {
            $query->where('sellers.verification_status', $verificationStatus);
        }

        if ($search) {
            $query->where('shops.name', 'like', '%' . $search . '%');
        }

        $query->orderBy('sellers.index', 'asc');
        $query->orderBy('shops.created_at', 'desc');

        $perPage = $params['perPage'] ?? self::PER_PAGE;
        if ($perPage != 0) {
            return $query->paginate($perPage);
        }

        return $query->get();
    }

    public function getBySlug($slug) {
        return $this->model->where('slug', $slug)->first();
    }

    public function getBySlugWithReviews($slug) {
        return $this->model->where('slug', $slug)->withCount('user.productReviews')->first();
    }

    public function getProducts($shop, $params = [])
    {
        $name = $params['name'] ?? null;
        $ids = $params['ids'] ?? null;
        $notIds = $params['notIds'] ?? null;

        $productQuery = Product::microsite()->onlyUnexpiredSellers()
            ->categoryEnabled()->brandEnabled()->subCategoryEnabled()
            ->subSubCategoryEnabled()
            ->select('*', DB::raw("CASE
                WHEN (!isnull(discount) AND discount_type = 'amount') THEN (unit_price - discount)
                WHEN (!isnull(discount) AND discount_type = 'percent') THEN (unit_price * (100 - discount) / 100)
                ELSE unit_price END AS final_price"))
            ->where('user_id', $shop->user->id)
            ->where('published', 1)
            // ->where('current_stock', '>', 0)
            ;

        if ($name) {
            $productQuery->where('name', 'like', '%'.$name.'%');
        }

        if ($ids) {
            $productQuery->whereIn('id', $ids);
        }

        if ($notIds) {
            $productQuery->whereNotIn('id', $notIds);
        }

        $perPage = $params['perPage'] ?? 12;
        if ($perPage != 0) {
            return $productQuery->paginate($perPage);
        }

        return $productQuery->get();
    }

    public function getSelectOptionsArray()
    {
        return DB::table('shops')
            ->select(['id','user_id', 'name'])
            ->where('logo', '<>', null)->get();
    }

    public function getListForCoupon(array $params)
    {
        $verificationStatus = $params['verification_status'] ?? null;

        $query = $this->model::query()
            ->leftJoin('sellers', 'shops.user_id', '=', 'sellers.user_id')
            ->leftJoin('users', 'shops.user_id', '=', 'users.id')
            ->whereNotNull('users.id');

        $query->whereHas('user', function (Builder $query) use ($verificationStatus) {
            $query->where('banned', '<>', '1');
        });

        if ($verificationStatus != null) {
            $query->where('sellers.verification_status', $verificationStatus);
        }
        $query->select('shops.*');
        $query->orderBy('shops.name', 'asc');

        return $query->get();
    }
}
