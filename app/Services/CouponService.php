<?php

namespace App\Services;

use App\Models\Coupon;
use App\ObjectModels\Coupon as ObjectModelsCoupon;
use App\Repositories\CouponRepository;
use App\Services\ProductStockService;
use App\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class CouponService extends AbstractService
{
    protected $repo;
    protected $shopService;
    protected $cartService;
    protected $productStockService;

    public function __construct(CouponRepository $repo, ShopService $shopService, CartService $cartService, ProductStockService $productStockService)
    {
        $this->shopService = $shopService;
        $this->cartService = $cartService;
        $this->productStockService = $productStockService;
        parent::__construct($repo);
    }

    public function getUsagesCount($id)
    {
        return $this->repo->getUsagesCount($id);
    }

    public function create($data) {
        $data['details'] = json_encode($data['details']);

        $data['start_date'] = Carbon::createFromFormat('Y-m-d', $data['start_date'])->startOfDay()->timestamp;
        $data['end_date'] = Carbon::createFromFormat('Y-m-d', $data['end_date'])->endOfDay()->timestamp;

        return $this->repo->create($data);
    }

    public function getListForSeller(User $user, Request $request)
    {
        $params = $this->buildParamsFromRequestForSeller($user, $request);

        return $this->repo->getList($params);
    }

    public function buildParamsFromRequestForSeller(User $user,Request $request): array
    {
        $params = $this->buildParamsFromRequest($request);
        $params['owner'] = 'seller';
        $params['user_id'] = $user->shop->id;

        $params['page'] = $request->input('page');
        $params['per_page'] = $request->input('per_page');

        if ($request->has('active')) {
            $params['active'] = $request->input('active');
        }

        if ($request->has('ongoing')) {
            $params['ongoing'] = $request->input('ongoing');
        }

        return $params;
    }

    public function buildParamsFromRequest(Request $request): array
    {
        $params = [];

        $params['page'] = $request->input('page');
        $params['per_page'] = $request->input('per_page');

        if ($request->has('code') && $request->input('code') != '') {
            $params['code'] = $request->input('code');
        }

        if ($request->has('start_date') && $request->input('start_date') != '') {
            $params['start_date'] = Carbon::createFromFormat('Y-m-d', $request->input('start_date'))->startOfDay()->timestamp;
        }

        if ($request->has('end_date') && $request->input('end_date') != '') {
            $params['end_date'] = Carbon::createFromFormat('Y-m-d', $request->input('end_date'))->endOfDay()->timestamp;
        }

        return $params;
    }

    public function extractUsableCoupon($coupons, $productIds = []): array
    {
        $result = [];

        foreach ($coupons as $coupon)
        {
            $details = json_decode($coupon->details);
            if (!$coupon->canUse()) {
                continue;
            }

            // This will show the coupon in Product detail page if any variant of product listed in coupon's product
            // if ($coupon->type === Coupon::TYPE_PRODUCT_BASE) {
            //     $filter = $this->buildProductsFilterByCoupon($coupon);
            //     if (!$this->isArrayIntersect($productIds, $filter['productIds'])) {
            //         continue;
            //     }
            // }

            if ($coupon->type === Coupon::TYPE_PRODUCT_BASE) {
                if (!$this->isArrayIntersect($productIds, $details->products)) {
                    continue;
                }
            }

            $result[] = $coupon;
        }

        return $result;
    }

    public function updateCoupon($id, $data) {
        $data['details'] = json_encode($data['details']);

        $data['start_date'] = Carbon::createFromFormat('Y-m-d', $data['start_date'])->startOfDay()->timestamp;
        $data['end_date'] = Carbon::createFromFormat('Y-m-d', $data['end_date'])->endOfDay()->timestamp;

        return $this->repo->update($id, $data);
    }

    public function filterOnlyUsableCoupons($coupons): array
    {
        $cart = $this->cartService->getTempCart();
        $cartClone = clone $cart;
        $validCoupons = [];

        foreach ($coupons as $coupon)
        {
            $coupon['is_can_apply'] = false;
            $couponObject = new ObjectModelsCoupon($coupon);
            $validationResult = $cartClone->simpleValidateCoupon($couponObject);
            if ($validationResult['valid']) {
                $coupon['is_can_apply'] = true;
            }

            $validCoupons[] = $coupon;
        }

        usort($validCoupons, function ($couponA, $couponB) {
            return $couponB['is_can_apply'] <=> $couponA['is_can_apply'];
        });

        return $validCoupons;
    }

    public function getExtractUsableCouponBySeller() {
        static $coupons = null;
        if ($coupons !== null) {
            return $coupons;
        }

        $coupons = [];
        $cart = $this->cartService->getCart();
        $sellers = $cart->getItemsBySellerGroup();
        foreach ($sellers as $seller) {
            $shopId = $seller['info']['shopId'];
            $shop = $this->shopService->get($shopId);
            $coupons[$shopId] = $this->filterOnlyUsableCoupons($shop->sellerCoupons);
        }

        return $coupons;
    }


    public function getExtractUsableCouponByAdmin() 
    {
        $coupons = $this->getYsgCouponList();

        return $this->filterOnlyUsableCoupons($coupons);
    }

    private function getYsgCouponList() {
        $params = [
            'owner'     => Coupon::OWNER_YSG,
            'is_active' => Coupon::STATUS_ACTIVE,
            'published' => Coupon::IS_PUBLISHED,
        ];

        return $this->repo->getYsgCouponList($params);
    }

    private function isArrayIntersect($filters, $items) 
    {
        return !empty(array_intersect($filters, $items));
    }

    public function getList($params)
    {
        return $this->repo->getList($params);
    }

    public function buildProductsFilterByCoupon($coupon)
    {
        $filter = [];

        switch ($coupon->type) {
            case Coupon::TYPE_CATEGORY_BASE:
                $filter = $this->buildFilterCategoryType($coupon);
                break;
            
            case Coupon::TYPE_PRODUCT_BASE:
                $filter['productIds'] = $coupon->productIds;
                foreach($coupon->productIds as $id) {
                    if (isVariantID($id)) {
                        try {
                            $variant = $this->productStockService->get(getVariantID($id));
                        } catch(Exception $e) {
                            continue;
                        }
                        
                        $filter['productIds'][] = $variant->product->id;
                    }
                }
                break;
            
            default:
                if ($coupon->isForShop()) {
                    $shopIds = [];
                    foreach ($coupon->shopIds as $shopId) {
                        if ($shopId == 0) break;
                        $shop = $this->shopService->findBy('id', $shopId);
                        $shopIds[] = $shop;
                    }

                    if (!empty($shopIds)) {
                        $filter['shopIds'] = $shopIds;
                    }
                }
                break;
        }

        return $filter;
    }

    private function buildFilterCategoryType($coupon)
    {
        $details = json_decode($coupon->details);
        if (!empty($details->subsubcategoryIds)) {
            $filter['subSubCateIds'] = $details->subsubcategoryIds;
            return $filter;
        }

        if (!empty($details->subcategoryIds)) {
            $filter['subCateIds'] = $details->subcategoryIds;
            return $filter;
        }

        if (!empty($details->categoryIds)) {
            $filter['cateIds'] = $details->categoryIds;
            return $filter;
        }
    }

    public function updatePublished(int $id): bool
    {
        return $this->repo->updatePublished($id);
    }

    public function toggleActive(int $id): bool
    {
        return $this->repo->toggleActive($id);
    }
}
