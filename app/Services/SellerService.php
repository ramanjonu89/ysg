<?php
namespace App\Services;

use App\Repositories\SellerRepository;
use App\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SellerService extends AbstractService
{
    protected $user;
    protected $repo;
    protected $userRepo;

    public function __construct(SellerRepository $repo)
    {
        $this->repo = $repo;
        parent::__construct($repo);
    }

    public function getByUser($userId)
    {
        $userService = app()->make(UserService::class);
        return $userService->get($userId);
    }

    /**
     * get list of seller for display at front end
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getListSeller(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->repo->getList([
            'verification_status' => 1,
            'display' => true,
            'company_type' => User::COMPANY_TYPE_FLAG_SHIP,
            'perPage' => 0,
        ]);
    }

    /**
     * get list of CA seller for display at front end
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getCAList(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->repo->getList([
            'verification_status' => 1,
            'company_type' => User::COMPANY_TYPE_PRIVATE_EVENT,
            'enable_corporate_domains' => 1,
            'perPage' => 0,
        ]);
    }

    /**
     * get list of seller for display at back end
     * @param array $params
     * @return mixed
     */
    public function getList(array $params = [])
    {
        return $this->repo->getList($params);
    }

    public function getAll()
    {
        return $this->repo->getAll();
    }

    /**
     * Update index of model
     * @param array $listIndex
     * @return bool
     */
    public function updateIndex(array $listIndex): bool
    {
        return (bool)$this->repo->updateIndex($listIndex);
    }

    /**
     * Toggle display of model
     * @param int $id
     * @return bool
     */
    public function toggleDisplay(int $id): bool
    {
        return (bool) $this->repo->toggleDisplay($id);
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Exception
     */
    public function updateByRequest($id, Request $request)
    {
        if (
            $request->company_type == User::COMPANY_TYPE_PRIVATE_EVENT
            && !$request->has('active_corporate_access_code')
            && !$request->has('enable_access_code')
            && !$request->has('enable_corporate_domains')
        ) {
            throw new \Exception(__('For Corporate Store, please enable either Corporate Domains or Corporate Access Code or both. For Private Sales Store, please enable Access Code (Private Sales). Corporate Domain is optional.'));
        }

        if ($request->has('active_corporate_access_code') && $request->has('enable_access_code')) {
            throw new \Exception(__('Corporate Access Code and Access Code cannot be enabled at the same time.'));
        }
        
        $seller = $this->repo->find($id);
        $userService = app()->make(UserService::class);
        $userService->updateByRequest($seller->user->id, $request);

        if ($request->hasFile('image')) {
            $seller->image = $request->image->store(
                config('constant.seller_image_path')
            );
        } elseif (!$request->previous_photo) {
            $seller->image = null;
        } else {
            $seller->image = $request->previous_photo;
        }

        if (isset($request->active_corporate_access_code)) {
            $code = $request->corporate_access_code;
            if ($code) {
                $exist = $this->repo->getByCACode($code, $id);
                if ($exist) {
                    throw new \Exception(__('Corporate Access Code must be unique'));
                } else {
                    $seller->active_corporate_access_code = true;
                    $seller->corporate_access_code = $request->corporate_access_code;
                }
            } else {
                throw new \Exception(__('Missing Corporate Access Code'));
            }
        } else {
            $seller->active_corporate_access_code = false;
        }

        $seller->sync_products_enabled = $request->has('sync_products_enabled');
        $seller->sync_stocks_enabled = $request->has('sync_stocks_enabled');
        $seller->sync_orders_enabled = $request->has('sync_orders_enabled');

        if (!$seller->save()) {
            throw new \Exception(__('There is an error when saving'));
        }

        return $seller;
    }

    public function getMainSellers()
    {
        $sellers = $this->repo->getSellers();
        if (isMicrosite()) {
            $shop = getMicrositeShop();

            $sellers = $this->repo->getSellersByShop($shop);
        }

        return $sellers;
    }

    public function getBestSellers()
    {
        return $this->repo->getBestSellers();
    }

    public function getRevenueThisMonth($sellerId)
    {
        $firstDayOfMonth = Carbon::now()->startOfMonth();
        return $this->repo->getRevenue($sellerId, $firstDayOfMonth);
    }

    public function getRevenueLastMonth($sellerId)
    {
        $firstDayOfLastMonth = new Carbon('first day of last month');
        $firstDayOfLastMonth->startOfMonth();
        $endDayOfLastMonth = new Carbon('last day of last month');
        $endDayOfLastMonth->endOfMonth();
        return $this->repo->getRevenue($sellerId, $firstDayOfLastMonth, $endDayOfLastMonth);
    }

    public function getTotalRevenue($sellerId)
    {
        return $this->repo->getRevenue($sellerId);
    }

    public function getUserIdQuery($sellerIds, $companyTypes, $micrositeUserIds, $filters = null)
    {
        if (!empty($filters['shopIds']) && empty($sellerIds)) {
            foreach ($filters['shopIds'] as  $shop) {
                $sellerIds[] = $shop->seller->id;
            }
        }

        $params = [
            'sellerIds'        => $sellerIds ?? [],
            'companyTypes'     => $companyTypes ?? [],
            'micrositeUserIds' => $micrositeUserIds ?? []
        ];

        return $this->repo->getUserIdQuery($params);
    }

    public function getListForExport($params = [])
    {
        return  $this->repo->getListForExport($params);
    }

    public function getSellersByDomain($CADomain)
    {
        return $this->repo->getSellersByDomain($CADomain);
    }
}