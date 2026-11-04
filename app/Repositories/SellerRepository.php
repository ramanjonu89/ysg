<?php
namespace App\Repositories;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Seller;
use App\Models\Product;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class SellerRepository extends AbstractRepo
{
    const PER_PAGE = 15;

    public function getModel(): string
    {
        return Seller::class;
    }

    /**
     * get list of seller
     * @param array $params
     * @return mixed
     */
    public function getList(array $params)
    {
        $search = $params['search'] ?? null;
        $verificationStatus = $params['verification_status'] ?? null;
        $display = $params['display'] ?? null;
        $ids = $params['ids'] ?? null;
        $sortBy = $params['sort_by'] ?? 'index';
        $sortDirection = $params['sort_direction'] ?? 'asc';

        $query = $this->model::with('user');
        $query->whereHas('user', function (Builder $query) use ($search, $params) {
            $query->where('banned', '<>', '1');
            if ($search) {
                $query->where(function (Builder $query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');
                });
            }
            if (isset($params['company_type'])) {
                $query->where('company_type', '=', $params['company_type']);
            }
            if (isset($params['enable_corporate_domains'])) {
                $query->where('enable_corporate_domains', '=', $params['enable_corporate_domains']);
            }
        });

        if (isset($params['company_type']) && $params['company_type'] == User::COMPANY_TYPE_PRIVATE_EVENT) {
            $query->withDateUnexpired();
        }

        if ($ids) {
            $query->whereIn('id', $ids);
        }

        if ($verificationStatus != null) {
            $query->where('verification_status', $verificationStatus);
        }

        if ($display != null) {
            $query->where('display', $display);
        }

        $this->applySorting($query, $sortBy, $sortDirection);

        $perPage = $params['perPage'] ?? self::PER_PAGE;
        if ($perPage != 0) {
            return $query->paginate($perPage);
        }
        return $query->get();
    }

    private function applySorting($query, $sortBy, $sortDirection)
    {
        $sortDirection = strtolower($sortDirection) === 'desc' ? 'desc' : 'asc';
        
        switch ($sortBy) {
            case 'name':
                $query->leftJoin('users as sort_users', 'sellers.user_id', '=', 'sort_users.id')
                      ->orderBy('sort_users.name', $sortDirection)
                      ->select('sellers.*');
                break;
            case 'email':
                $query->leftJoin('users as sort_users', 'sellers.user_id', '=', 'sort_users.id')
                      ->orderBy('sort_users.email', $sortDirection)
                      ->select('sellers.*');
                break;
            case 'phone':
                $query->leftJoin('users as sort_users', 'sellers.user_id', '=', 'sort_users.id')
                      ->orderBy('sort_users.phone', $sortDirection)
                      ->select('sellers.*');
                break;
            case 'created_at':
                $query->orderBy('sellers.created_at', $sortDirection);
                break;
            case 'verification_status':
                $query->orderBy('sellers.verification_status', $sortDirection);
                break;
            case 'products_count':
                $query->leftJoin('products', 'sellers.user_id', '=', 'products.user_id')
                      ->groupBy('sellers.id')
                      ->orderByRaw('COUNT(products.id) ' . $sortDirection)
                      ->select('sellers.*');
                break;
            default:
                $query->orderBy('sellers.index', 'asc');
                $query->orderBy('sellers.created_at', 'desc');
                break;
        }
    }

    public function getAll()
    {
        return $this->model::with('user')->whereHas('user')->get();
    }

    /**
     * Update index of models
     * @param array $list
     * @return int
     */
    public function updateIndex(array $list): int
    {
        $ids = array_keys($list);
        $sellers = $this->model::whereIn('id', $ids)->get();

        $count = 0;
        foreach ($sellers as $seller) {
            $seller->index = $list[$seller->id];
            if ($seller->save()) {
                $count ++;
            }
        }
        return $count;
    }

    /**
     * Toggle display of models
     * @param int $id
     * @return mixed
     */
    public function toggleDisplay(int $id)
    {
        $seller = $this->model::find($id);
        $seller->display = !$seller->display;
        return $seller->save();
    }

    public function getShopCorporateDomains(int $id) {
        $seller = $this->model::find($id);
        if ($seller) {
            return $seller->corporate_domains;
        }
        return null;
    }

    public function getSellers()
    {
        return DB::table('users')
            ->leftJoin('sellers', 'users.id', 'sellers.user_id')
            ->whereIn('users.id', $validUserIds)
            ->select(['sellers.id', 'users.name'])
            ->orderBy('users.name')
            ->get();
    }

    public function getSellersByShop($shop)
    {
        $allBrandsAndBrands = $this->getFullProducts($shop);

        return $this->model
                ->select(['brands.id', 'brands.name'])
                ->whereIn('id', array_column($allBrandsAndBrands, 'brand_id'))
                ->get();
    }

    public function getBestSellers()
    {
        return $this->model->addSelect(['no_of_sales' => Product::selectRaw('sum(num_of_sale) as total')->whereColumn('user_id', 'sellers.user_id')->groupBy('user_id')])
        ->withDateUnexpired()
        ->orderBy('no_of_sales', 'DESC')
        ->where('verification_status', 1)
        ->where('display', true)
        ->limit(20)
        ->get();
    }

    public function getByCACode(string $code, int $id)
    {
        return $this->model::whereCorporateAccessCode($code)->where('id', '<>', $id)->first();
    }

    // TODO: need to be change after apply split order
    public function getRevenue(int $sellerId, $startDate = null, $endDate = null)
    {
        // $revenue = DB::select('SELECT * FROM order_details WHERE seller_id = :sellerId')
        $query = DB::table('order_details')->where('order_details.seller_id', $sellerId);

        $query->join('orders', function ($join) {
            $join->on('order_details.order_id', '=', 'orders.id')
                 ->where('orders.payment_status', Order::PAYMENT_STATUS_PAID);
        });

        if ($startDate != null) {
            $query->where('orders.created_at', '>=', $startDate);
        }

        if ($endDate != null) {
            $query->where('orders.created_at', '>=', $endDate);
        }

        return $query->sum('order_details.price');
    }

    /**
     * @param  User  $user
     * @param  array  $params
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getOrders(User $user, array $params = []): \Illuminate\Database\Eloquent\Collection
    {
        $query = Order::with('user');
        $query->whereIn('id', function ($query) use ($user) {
            $query->select('order_id')->from( (new OrderDetail())->getTable() )
                ->where('seller_id', $user->id)
                ->groupBy('order_id');
        });

        return $query->get();
    }

    public function getUserIdQuery($params)
    {
        $sellerIds = $params['sellerIds'];

        $userIdQuery =  DB::table('sellers')
                ->join('users', 'sellers.user_id', '=', 'users.id')
                ->where('sellers.verification_status', '=', 1)

                ->where(function ($q) use ($sellerIds) {
                    if (!empty($sellerIds)) {
                        $q->whereIn('sellers.id', $sellerIds);
                    }
                });

        if (!empty($params['companyTypes'])) {
            $userIdQuery->whereIn('users.company_type', $params['companyTypes']);
        }

        if (!isMicrosite()) {
            $userIdQuery->whereNotIn('users.id', $params['micrositeUserIds']);
        }

        $userIds        = $userIdQuery->pluck('users.id')->toArray();
        if (isMicrosite()) {
            $userIds    = [getMicrositeShop()->user->id];
        }

        return $userIds;
    }

    public function getListForExport($params)
    {
        $keyword = $params['keyword'] ?? null;
        $verification_status = $params['verification_status'] ?? null;

        $query = $this->buildListQuery();

        if ($keyword) {
            $query->when($keyword, function($query) use ($keyword) {
                return $query->where(function($q) use($keyword){
                    $q->where('users.name', 'like', '%' . $keyword . '%')
                        ->orWhere('users.email', 'like', '%' . $keyword . '%');
                });
            });
        }

        if ($verification_status != null) {
            $query->where('sellers.verification_status', $verification_status);
        }

        $perPage = $params['perPage'] ?? self::PER_PAGE;
        if ($perPage != 0) {
            $query->limit($perPage);
        }

        return $query;
    }

    public function buildListQuery()
    {
        return DB::table('sellers')
                    ->select(
                        'sellers.id',
                        'users.name as name',
                        'users.phone as phone',
                        'users.email as email',
                        'sellers.verification_status as verify',
                        'sellers.display as display',
                        'sellers.created_at as created_at',
                        DB::raw('COUNT(products.id) as numOfProducts'),
                        DB::raw('(SELECT SUM(price) FROM order_details
                                WHERE payment_status = "paid" AND delivery_status != "cancelled" AND 
                                seller_id = users.id) as total_sales')
                    )
                ->leftJoin('users', 'sellers.user_id', '=', 'users.id')  
                ->leftJoin('products', 'products.user_id', '=', 'users.id')
                ->whereNotNull('users.id')
                ->groupBy('sellers.id')   
                ->orderBy('sellers.created_at', 'desc');
    }

    public function getSellersByDomain($domain)
    {
        return $this->model
            ->withDateUnexpired()
            ->whereHas('corporate_domains', function($q) use ($domain) {
                return $q->where('domain', $domain);
            })
            ->where('display', 1)
            ->get();
    }
}
