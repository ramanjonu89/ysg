<?php
namespace App\Services;

use App\Repositories\BrandRepository;
use App\Repositories\SellerRepository;
use Illuminate\Support\Facades\Auth;

class BrandService extends AbstractService
{
    protected $sellerRepo;
    protected $sellerService;
    protected $repo;

    public function __construct(BrandRepository $repo, SellerRepository $sellerRepo, SellerService $sellerService)
    {
        $this->sellerRepo = $sellerRepo;
        $this->sellerService = $sellerService;
        parent::__construct($repo);
    }

    /**
     * get list of CA brands
     * @return \Illuminate\Support\Collection
     */
    public function getCAList(): \Illuminate\Support\Collection
    {
//        $sellers = $this->sellerService->getCAList();
//        return $this->getSellersBaseList($sellers);

        return $this->repo->getCorporatorBrands();
    }

    /**
     * get list of CA brands
     * @param  \Illuminate\Support\Collection  $sellers
     * @return \Illuminate\Support\Collection
     */
    public function getSellersBaseList(\Illuminate\Support\Collection $sellers): \Illuminate\Support\Collection
    {

        $list = collect();
        if ($sellers->count()) {

            $brandIds = collect();
            foreach ($sellers as $seller) {
                $products = $seller->user->products;
                $brandIds = $brandIds->concat($products->pluck('brand_id'));
            }
            $brandIds = $brandIds->unique();

            $params = [
                'brandIds' => $brandIds,
                'perPage' => 0,
            ];
            $list = $this->repo->getList($params);

            $list = $list->sortBy(function ($item) {
                return $item->name;
            });

        }
        return $list;
    }

    public function getMainBrandsArray()
    {
        $brands = $this->repo->getBrands();
        if (isMicrosite()) {
            $shop = getMicrositeShop();

            $brands = $this->repo->getBrandsByShop($shop);
        }

        return $brands->toArray();
    }

    public function getMainBrands()
    {
        $brands = $this->repo->getAllBrands();
        if (isMicrosite()) {
            $shop = getMicrositeShop();

            $brands = $this->repo->getBrandsByShop($shop);
        }

        return $brands;
    }

    public function getBrandsForProductList()
    {
        return $this->repo->getBrands();
    }

    public function getBrandsByUser($userId = null) {
        return $this->repo->getBrandsByUser($userId);
    }

    public function getTopBrands()
    {
        return $this->repo->getTopBrands();
    }

    public function getBrandsFilter($validProducts)
    {
        return $this->repo->getBrandsFilter($validProducts);
    }

    public function updateStatus(int $id): bool
    {
        return $this->repo->updateStatus($id);
    }

    public function getBrandsByShop($shop)
    {
        return $this->repo->getBrandsByShop($shop);
    }
}