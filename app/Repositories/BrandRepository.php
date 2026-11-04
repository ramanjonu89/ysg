<?php
namespace App\Repositories;

use App\Models\Brand;
use App\Repositories\MicrositeRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class BrandRepository extends AbstractRepo
{
    const PER_PAGE = 15;

    public function getModel(): string
    {
        return Brand::class;
    }

    /**
     * get list of brands
     * @param array $params
     * @return mixed
     */
    public function getList(array $params)
    {
        $search = $params['search'] ?? null;
        $brandIds = $params['brandIds'] ?? null;

        $query = $this->model::query();
        if ($search) {
            $query->where(function (Builder $query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('slug', 'like', '%' . $search . '%');
            });
        }

        if ($brandIds) {
            $query->whereIn('id', $brandIds);
        }

        $query->orderBy('created_at', 'desc');

        $perPage = $params['perPage'] ?? self::PER_PAGE;
        if ($perPage != 0) {
            return $query->paginate($perPage);
        }

        return $query->get();
    }


    public function getBrands()
    {
        return $this->model->select(['brands.id', 'brands.name'])->get();
    }

    public function getAllBrands()
    {
        $micrositeRepo = app()->make(MicrositeRepository::class);
        $userIdsHasActiveMicrosite = $micrositeRepo->getArrayUserIdHasActiveMicrosite();
        $filter = ['onlyActiveCategories' => true];
        $filter = ['exceptUserIds' => $userIdsHasActiveMicrosite];

        $allBrands = $this->getFullProducts($filter);

        return $this->model
                ->select(['brands.id', 'brands.name', 'brands.logo'])
                ->where('status', '=', 1)
                ->whereIn('id', array_column($allBrands, 'brand_id'))
                ->get();
    }

    public function getBrandsByShop($shop)
    {
        $filter['userIds'] = [$shop->user->id];
        $allBrands = $this->getFullProducts($filter);

        return $this->model
                // ->select(['brands.id', 'brands.name', 'brands.logo'])
                ->whereIn('id', array_column($allBrands, 'brand_id'))
                ->orderby('name', 'asc')
                ->get();
    }

    public function getFullProducts(array $filter = [])
    {
        $query = DB::table('products')
                ->where('published', 1)
                // ->where('current_stock', '>',  0)
                ->select(['brand_id'])
                ->distinct('brand_id');

        if (isset($filter['onlyActiveCategories'])) {
            $query->leftJoin('users', 'users.id', '=', 'products.user_id')
                    ->leftJoin('sellers', 'users.id', '=', 'sellers.user_id')
                    ->where('users.company_type', 1)
                    ->where('sellers.verification_status', 1);
        }

        if (!empty($filter['userIds'])) {
            $query->whereIn('user_id', $filter['userIds']);
        }

        if (isset($filter['onlyMicrosite'])) {
            $query->where('only_microsite', $filter['onlyMicrosite']);
        }

        if (!empty($filter['categoryId'])) {
            $query->where('category_id', $filter['categoryId']);
        }

        if (!empty($filter['exceptUserIds'])) {
            $query->whereNotIn('user_id', $filter['exceptUserIds']);
        }

        return $query->get()->toArray();
    }

    public function getBrandsByUser($userId = null) {
        $query = DB::table('products')
            ->select('brands.id', 'brands.name')
            ->join('brands', 'products.brand_id', '=', 'brands.id' )
            ->distinct();
        if ($userId) {
            $query->where('products.user_id', $userId);
        }
        return $query->get();
    }

    public function getCorporatorBrands()
    {
        return DB::table('products')
            ->select('brands.id', 'brands.name')
            ->join('brands', 'products.brand_id', '=', 'brands.id' )
            ->where('brands.status', 1)
            ->whereIn('products.user_id', function($query) {
                $query->select('users.id')
                    ->from('users')
                    ->join('sellers', 'sellers.user_id', '=', 'users.id')
                    ->where('users.company_type', 2)
                    ->where('users.enable_corporate_domains', 1)
                    ->where('sellers.verification_status', 1)
                ;
            })
            ->orderBy('brands.name','asc')
            ->distinct()
            ->get();
    }

    public function getTopBrands()
    {
        return Brand::where('top', 1)
                    ->where('status', 1)
                    ->get();
    }

    public function getBrandsFilter($validProducts)
    {
        $allBrands = $validProducts
                    ->whereNotNull('products.brand_id')
                    ->select('brand_id')
                    ->distinct()
                    ->get()
                    ->toArray();

        $brands = DB::table('brands')
                    ->whereIn('id', array_column($allBrands, 'brand_id'))
                    ->select(['brands.id', 'brands.name'])
                    ->get()
                    ->toArray();

        return $brands;
    }

    public function updateStatus($id): bool
    {
        $model = $this->model::find($id);
        if ($model) {
            $model->status = !$model->status;
            if ($model->save()) {
                return true;
            }
        }
        return false;
    }

}
