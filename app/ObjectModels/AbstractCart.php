<?php

namespace App\ObjectModels;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use App\Models\Shop;
use App\Services\UserService;
use App\Services\CouponService;
use App\Http\Resources\Mobile\CouponResource;
use App\Services\CartService;
use App\ObjectModels\Coupon as ObjectModelsCoupon;
use Serializable;

abstract class AbstractCart implements CartInterface, Serializable
{
    protected $items;
    protected $shippingFee = 0;
    protected $coupons;
    protected $shops = [];
    protected $discountBySeller = [];
    protected $discountByYsg = [];
    protected $cartId = null;
    const FREE_SHIPPING_ABOVE = 80;
    const SHIPPING_FEE = 2;
    const USE_COMBINE_SHIPPING = false;

    public function __construct()
    {
        $this->items = collect();
        $this->coupons = collect();
    }

    public function isSynced(): bool
    {
        return !!$this->cartId;
    }

    public function setCartId(string $cartId): void
    {
        $this->cartId = $cartId;
    }

    public function getCartId(): string
    {
        return $this->cartId;
    }

    public function setShopOrderType($sellerId, $order_type)
    {
        $this->shops[$sellerId]['order_type'] = $order_type;
    }

    public function getShopOrderType($sellerId)
    {
        return $this->shops[$sellerId]['order_type'];
    }

    public function setShopAddress($sellerId, $address)
    {
        $this->shops[$sellerId]['address'] = $address;
    }

    public function getShopAddress($sellerId)
    {
        return $this->shops[$sellerId]['address'];
    }

    public function setShopAddressTitle($sellerId, $address_title)
    {
        $this->shops[$sellerId]['address_title'] = $address_title;
    }

    public function getShopAddressTitle($sellerId)
    {
        return $this->shops[$sellerId]['address_title'] ?? '';
    }

    public function getItems()
    {
        return $this->items;
    }

    public function hasItemInExternalShipping(): bool
    {
        $result = false;

        foreach ($this->items as $item) {
            if (!$item->isInternalShipping()) {
                $result = true;
                break;
            }
        }

        return $result;
    }

    public function getItem(string $key)
    {
        if ($this->items->has($key)) {
            return $this->items->offsetGet($key);
        }

        return null;
    }

    public function getSubTotal($filter = [])
    {
        $total = 0;
        foreach($this->items as $item) {
            if (!$this->isItemInFilter($item, $filter)) continue;
            $total += $item->getTotalPrice();
        }

        return $total;
    }

    public function isItemInFilter($item, $filter)
    {
        if (!$this->isItemInSellers($filter, $item)) {
            return false;
        }

        if (!$this->isItemInCategories($filter, $item)) {
            return false;
        }

        $id = $item->isVariant() ? $item->getVariantId() : $item->getId();

        if (!empty($filter['productIds']) && !in_array($id, $filter['productIds'])) {
            return false;
        }

        return true;
    }

    public function getSubTotalByShopId($shopId)
    {
        $filter['shopId'] = $shopId;
        return $this->getSubTotal($filter);
    }

    private function isItemInSellers($filter, $item)
    {
        if (!empty($filter['shopId']) && $item->getShopId() !== $filter['shopId']) {
            return false;
        }

        if (!empty($filter['shopIds']) && !$this->inArray($filter, $item->getShopId(), 'shopIds')) {
            return false;
        }

        return true;
    }

    private function isItemInCategories($filter, $item)
    {
        if (empty($filter['categoryIds']) && empty($filter['subcategoryIds']) && empty($filter['subsubcategoryIds'])) {
            return true;
        }

        if (!empty($filter['subsubcategoryIds'])) {
            return $this->inArray($filter, $item->getSubSubCategoryId(), 'subsubcategoryIds');
        }

        if (!empty($filter['subcategoryIds'])) {
            return $this->inArray($filter, $item->getSubCategoryId(), 'subcategoryIds');
        }

        if (!empty($filter['categoryIds'])) {
            return $this->inArray($filter, $item->getCategoryId(), 'categoryIds');
        }

        return false;
    }

    private function inArray($filter, $id, $key = '')
    {
        return !empty($filter[$key]) && in_array($id, $filter[$key]);
    }

    public function getInternalShippingFee(): int
    {
        $fee = 0;
        $total = 0;

        foreach($this->items as $item) {
            if ($item->isInternalShipping()) {
                $total += $item->getTotalPrice();
            }
        }

        if (0 !== $total && $this->hasCoupons() && !$this->hasItemInExternalShipping()) {
            $total -= $this->getCouponDiscount();
        }

        if (0 !== $total && $total < self::FREE_SHIPPING_ABOVE) {
            $fee = self::SHIPPING_FEE;
        }

        return $fee;
    }

    public function getExternalShippingFee(): int
    {
        $fee = 0;
        $total = [];
        $shippingCostAbove = [];
        $shippingFee = [];

        foreach($this->items as $item) {
            if (!$item->isInternalShipping()) {
                $shippingCostAbove[$item->getShopId()] = $item->getFreeShippingAbove();
                $shippingFee[$item->getShopId()] = $item->getShippingFee();
                if (!isset($total[$item->getShopId()])) {
                    $total[$item->getShopId()] = 0;
                }
                $total[$item->getShopId()] += $item->getTotalPrice();
            }
        }

        foreach($total as $shopId => $price) {
            if ($price - $this->getCouponDiscountByShopId($shopId) < $shippingCostAbove[$shopId]) {
                $fee += $shippingFee[$shopId];
            }
        }

        return $fee;
    }

    public function getAllShippingFee(): int
    {
        $fee = 0;
        $total = [];
        $shippingCostAbove = [];
        $shippingFee = [];

        foreach($this->items as $item) {
            $shippingCostAbove[$item->getShopId()] = $item->getFreeShippingAbove();

            if (!isset($shippingFee[$item->getShopId()]) || $shippingFee[$item->getShopId()] !== 0) {
                $shippingFee[$item->getShopId()] = $this->calculateItemShippingFee($item);
            }

            if (!isset($total[$item->getShopId()])) {
                $total[$item->getShopId()] = 0;
            }
            $total[$item->getShopId()] += $item->getTotalPrice();
        }

        foreach($total as $shopId => $price) {
            if ($this->isSelfCollectionShop($shopId)) continue;
            if ($this->getCashCoupons()->count() && $price <= $this->getCouponDiscountByShopId($shopId, false)) continue;

            if ($price - $this->getCouponDiscountByShopId($shopId, false) < $shippingCostAbove[$shopId]) {
                $fee += $shippingFee[$shopId];
            }
        }

        return $fee;
    }

    public function isSelfCollectionShop($shopId)
    {
        $result = false;
        if (isset($this->shops[$shopId])) {
            if ($this->shops[$shopId]['order_type'] == Shop::ORDER_TYPE_SELF_COLLECTION) {
                return true;
            }
        }
        foreach ($this->items as $item) {
            if ($item->getShopId() == $shopId && $item->getOrderType() == Shop::ORDER_TYPE_SELF_COLLECTION) {
                $result = true;
                break;
            }
        }

        return $result;
    }

    public function getTotalSellers()
    {
        $sellers = [];

        foreach($this->items as $item) {
            $sellers[$item->getSellerId()] = 1;
        }

        return count($sellers);
    }

    public function getShippingFee(): float
    {
        if ($this->isFreeShippingAll()) {
            return 0;
        }

        if (self::USE_COMBINE_SHIPPING) {
            $fee = $this->getInternalShippingFee() + $this->getExternalShippingFee();
        } else {
            $fee = $this->getAllShippingFee();
        }
        return $fee;
    }

    private function calculateItemShippingFee($item): int
    {
        foreach ($this->coupons as $coupon) {
            if ($coupon->freeShipping()) {
                $filter = $this->buildProductFilterByCoupon($coupon);
                if ($this->isItemInFilter($item, $filter)) {
                    return 0;
                }
            }
        }
        return $item->getShippingFee();
    }

    private function isFreeShippingAll(): bool
    {
        foreach ($this->coupons as $coupon) {
            if ($coupon->freeShipping()) {
                if ($coupon->getType() == Coupon::TYPE_CART_BASE && count($coupon->getShopIds()) == 0) {
                    return true;
                }
            }
        }
        return false;
    }

    public function getShippingFeeByShopId($shopId): int
    {
        if ($this->isSelfCollectionShop($shopId)) {
            return 0;
        }

        $fee = 0;
        $total = 0;
        $shippingCostAbove = 0;
        $shippingFee = 0;
        $isInternalShipping = false;

        foreach($this->items as $item) {
            if ($item->getShopId() !== $shopId) {
                continue;
            }

            $isInternalShipping = $item->isInternalShipping();
            $shippingCostAbove = $item->getFreeShippingAbove();
            $shippingFee = $this->calculateItemShippingFee($item);
            if ($shippingFee === 0) {
                break;
            }
        }

        if (self::USE_COMBINE_SHIPPING && $isInternalShipping) {
            return $this->getInternalShippingFee();
        }

        $total = $this->getSubTotal(['shopId' => $shopId]);
        $couponDiscount = $this->getCouponDiscountByShopId($shopId, false);

        if ($this->getCashCoupons()->count() && $total <= $couponDiscount) {
            return 0;
        }

        if (($total - $couponDiscount) < $shippingCostAbove) {
            $fee = $shippingFee;
        }

        return $fee;
    }

    public function countAffectedSellerByCoupon(CouponInterface $coupon)
    {
        $sellerIds = [];
        $filter = $this->buildProductFilterByCoupon($coupon);

        foreach ($this->items as $item) {
            if (!$this->isItemInFilter($item, $filter)) continue;
            $sellerIds[$item->getSellerId()] = 1;
        }

        return count($sellerIds);
    }

    public function hasCoupons(): bool
    {
        return (bool)$this->coupons->count();
    }

    public function getCouponDiscount(): float
    {
        $total = 0;

        if (!$this->hasCoupons()) {
            return $total;
        }
        foreach($this->coupons as $coupon) {
            $total += $this->getCouponDiscountValue($coupon);
        }
        return $total;
    }

    public function getSubTotalAfterDiscountByShopId(int $shopId, bool $includeYsgDiscount = true): float
    {
        $total = $this->getSubTotal(['shopId' => $shopId]);
        $discount = $this->getCouponDiscountByShopId($shopId, $includeYsgDiscount);

        $result = $total - $discount;

        return $result < 0 ? 0 : $result;
    }

    public function getGrandTotalByShopId(int $shopId): float
    {
        $subTotal = $this->getSubTotalAfterDiscountByShopId($shopId);
        $shippingFee = $this->getShippingFeeByShopId($shopId);
        return $subTotal + $shippingFee;
    }

    public function getCouponDiscountByShopId(int $shopId, bool $includeYsgDiscount = true): float
    {
        $total = 0;
        $total += $this->getSellerCouponDiscountByShopId($shopId);
        $this->discountBySeller[$shopId] = $total;
        $ysgDiscount = $this->getYsgCouponDiscountByShopId($shopId);
        $this->discountByYsg[$shopId] = $ysgDiscount;
        if ($includeYsgDiscount) {
            $total += $ysgDiscount;
            $total += $this->getCashVoucherDiscountByShopId($shopId);
        }

        return $total;
    }

    public function getSellerCouponDiscountByShopId(int $shopId): float
    {
        if (isset($this->discountBySeller[$shopId]) && $this->discountBySeller[$shopId] != 0) {
            return $this->discountBySeller[$shopId];
        }

        $total = 0;

        if (!$this->hasCoupons()) {
            return $total;
        }

        foreach($this->coupons as $coupon) {
            if (!$coupon->isSellerCoupon()) continue;
            if ($coupon->getShopId() !== $shopId && !in_array($shopId, $coupon->getShopIds())) continue;
            $total += $this->getCouponDiscountValue($coupon, $shopId);
        }

        return $total;
    }

    public function getYsgCouponDiscountByShopId(int $shopId): float
    {
        if (isset($this->discountByYsg[$shopId]) && $this->discountByYsg[$shopId] != 0) {
            return $this->discountByYsg[$shopId];
        }

        $total = 0;

        if (!$this->hasCoupons()) {
            return $total;
        }

        foreach($this->coupons as $coupon) {
            if (!$coupon->isYsgCoupon()) continue;
            if ($coupon->isCashVoucher()) continue;

            $ysgDiscount = $this->getCouponDiscountValue($coupon);

            $resultsImpactRate = $this->getShopImpactRate($coupon, $shopId);
            if ($ysgDiscount > 0 && $resultsImpactRate['valid']) {
                $total += $ysgDiscount * $resultsImpactRate['value'];
            }
        }

        return $total;
    }

    public function getGrandTotal()
    {
        $subTotal = $this->getSubTotal();
        $shippingFee = $this->getShippingFee();
        $discount = $this->getCouponDiscount();
        $total = $subTotal + $shippingFee;

        $total -= $discount;
        $total = $total < 0 ? 0 : $total;

        return round((float)$total, 2);
    }

    public function getTotalItems(): int
    {
        if (!$this->items || $this->items->isEmpty()) {
            return 0;
        }

        $total = 0;

        foreach ($this->items as $item) {
            $total += $item->getQuantity();
        }

        return $total;
    }

    public function simpleValidateCoupon(CouponInterface $coupon): array
    {
        $filter = $this->buildProductFilterByCoupon($coupon);
        if ((Coupon::TYPE_CART_BASE === $coupon->getType() || $coupon->isCashVoucher()) && $coupon->forShop()) {
            $hasValidSellers = false;
            foreach($this->items as $item) {
                if ($this->isItemInSellers($filter, $item)) {
                    $hasValidSellers = true;
                    break;
                }
            }

            if (!$hasValidSellers) {
                return [
                    'valid' => false,
                    'message' => __('This coupon is for some specific sellers and your cart doesn\'t have any.'),
                ];
            }
        }

        if (Coupon::TYPE_PRODUCT_BASE === $coupon->getType()) {
            $hasValidProducts = false;
            foreach($this->items as $item) {
                $id = $item->isVariant() ? $item->getVariantId() : $item->getId();
                if (in_array($id, $coupon->getProductIds())) {
                    $hasValidProducts = true;
                    break;
                }
            }

            if (!$hasValidProducts) {
                return [
                    'valid' => false,
                    'message' => __('This coupon is for some specific products and your cart doesn\'t have any.')
                ];
            }
        }

        if(Coupon::TYPE_CATEGORY_BASE == $coupon->getType()){
            $hasValidCategories = false;
            foreach($this->items as $item) {
                if ($this->isItemInCategories($filter, $item)) {
                    $hasValidCategories = true;
                    break;
                }
            }

            if (!$hasValidCategories) {
                return [
                    'valid' => false,
                    'message' => __('This coupon is for some specific categories and your cart doesn\'t have any.')
                ];
            }
        }

        if (null !== $coupon->getMinBuy() && !$this->isPriceOverMinBuy($coupon)) {
            return [
                'valid' => false,
                'message' => __('Cart did not reach minimum value', ['coupon' => $coupon->getCode(), 'minimumValue' => single_price($coupon->getMinBuy())])
            ];
        }

        return [
            'valid' => true,
            'message' => __('Coupon is valid')
        ];
    }

    public function validateCoupon(CouponInterface $coupon): array
    {
        $result = $this->simpleValidateCoupon($coupon);

        if (!$result['valid']) {
            return $result;
        }

        if (!$coupon->isYsgCoupon()) {
            $ysgCoupon = $this->getYsgCouponObject();
            if (null !== $ysgCoupon && null !== $ysgCoupon->getMinBuy()) {
                $this->addCoupon($coupon);
                $isReachedMinBuy = $this->isPriceOverMinBuy($ysgCoupon);
                $this->removeCoupon($coupon);
                if (!$isReachedMinBuy) {
                    return [
                        'valid' => false,
                        'message' => __('Cart did not reach minimum value of YSG Coupon', ['coupon' => $ysgCoupon->getCode(), 'minimumValue' => single_price($ysgCoupon->getMinBuy())])
                    ];
                }
            }
        }

        if (!$coupon->canUse()) {
            return [
                'valid' => false,
                'message' => __('Coupon reached it max availability.')
            ];
        }

        if (!$coupon->canUseOnlyOnce()) {
            return [
                'valid' => false,
                'message' => __('Coupon already used, you can only use this once!')
            ];
        }

        return $result;
    }

    public function isPriceOverMinBuy(CouponInterface $coupon): bool
    {
        $filter = $this->buildProductFilterByCoupon($coupon);

        $totalPrice = $this->getSubTotal($filter);

        if ($coupon->isYsgCoupon()) {
            $totalPrice -= $this->getTotalSellerCouponsDiscount($filter);
        }

        return $totalPrice >= $coupon->getMinBuy();
    }

    public function buildProductFilterByCoupon(CouponInterface $coupon, $shopId = null)
    {
        $filter = [];

        if ($coupon->forShop()) {
            $filter['shopId'] = $coupon->getShopId();
            $filter['shopIds'] = $coupon->getShopIds();
        }
        if ($shopId !== null) {
            $filter['shopId'] = $shopId;
        }

        if ($coupon->getType() == Coupon::TYPE_PRODUCT_BASE) {
            $filter['productIds'] = $coupon->getProductIds();
        }

        if ($coupon->getType() == Coupon::TYPE_CATEGORY_BASE) {
            $filter['categoryIds'] = $coupon->getCategoryIds();
            $filter['subcategoryIds'] = $coupon->getSubCategoryIds();
            $filter['subsubcategoryIds'] = $coupon->getSubSubCategoryIds();
        }

        return $filter;
    }

    public function addCoupon(CouponInterface $coupon)
    {
        unset($this->discountBySeller[$coupon->getShopId()]);
        if ($coupon->isYsgCoupon() && !$coupon->isCashVoucher()) {
            foreach ($this->coupons as $c) {
                if ($c->isYsgCoupon() && !$c->isCashVoucher()) {
                    $this->removeCoupon($c);
                }
            }
        }

        if (Coupon::OWNER_SELLER === $coupon->getOwner()) {
            foreach ($this->coupons as $c) {
                if ($c->isSellerCoupon() && $coupon->getShopId() === $c->getShopId()) {
                    $this->removeCoupon($c);
                }
            }
        }

        if (Coupon::TYPE_PRODUCT_BASE === $coupon->getType()) {
            foreach($this->items as $item) {
                $id = $item->isVariant() ? $item->getVariantId() : $item->getId();
                if (in_array($id, $coupon->getProductIds())) {
                    $item->addCoupon($coupon);
                }
            }
        }

        $this->coupons->offsetSet($coupon->getId(), $coupon);
    }

    public function removeAllCoupons()
    {
        $this->discountBySeller = [];
        $this->discountByYsg = [];
        foreach($this->items as $item) {
            $item->removeAllCoupons();
        }

        $this->coupons = collect();
    }

    public function removeCoupon(CouponInterface $coupon)
    {
        $this->discountBySeller = [];
        $this->discountByYsg = [];
        foreach($this->items as $item) {
            $item->removeCoupon($coupon);
        }

        return $this->coupons->pull($coupon->getId());
    }

    public function getShopCoupon($shopId): string
    {
        foreach ($this->getCoupons() as $c) {
            if (Coupon::OWNER_SELLER === $c->getOwner() && $shopId === $c->getShopId()) {
                return $c->getCode();
            }
        }

        return '';
    }

    public function getYsgCoupon(): string
    {
        $ysgCoupon = [];
        foreach ($this->getCoupons() as $c) {
            if (Coupon::OWNER_YSG === $c->getOwner()) {
                $ysgCoupon[] = $c->getCode();
            }
        }

        if (!empty($ysgCoupon)) {
           return implode('; ', $ysgCoupon);
        } else {
            return '';
        }
    }

    public function getYsgCouponObject(): ?CouponInterface
    {
        $items = $this->getCoupons()->filter(function ($coupon) {
            return $coupon->isYsgCoupon();
        });

        return $items->isEmpty() ? null : $items->first();
    }

    public function validate(): bool
    {
        $valid = true;
        foreach ($this->items as $item) {
            if ($item->getCurrentStock() < $item->getQuantity()) {
                $valid = false;
            }
        }

        return $valid;
    }

    public function isOnlySelfCollection(): bool
    {
        $result = true;
        foreach($this->items as $item) {
            if (!$item->isSelfCollection()) {
                $result = false;
                break;
            }
        }

        return $result;
    }

    public function getItemsBySellerGroup(): array
    {
        $data = [];

        foreach($this->items as $item) {
            $data[$item->getSellerId()]['info'] = [
                'name' => $item->getShopName(),
                'companyShippingType' => $item->getCompanyShippingType(),
                'freeShippingAbove' => $item->getFreeShippingAbove(),
                'shippingFee' => $item->getShippingFee(),
                'shopId' => $item->getShopId(),
                'shopSlug' => $item->getShopSlug(),
                'orderType' => $item->getOrderType(),
                'address' => $item->getShopAddress(),
                'address_id' => $item->getDefaultIdAddress(),
                'addresses' => [],
            ];

            $data[$item->getSellerId()]['items'][] = $item;
        }

        $userService = app()->make(UserService::class);

        foreach($data as $userId => $value) {
            if ($value['info']['orderType'] == Shop::ORDER_TYPE_SELF_COLLECTION) {
                $data[$userId]['info']['shippingFee'] = 0;
                $data[$userId]['info']['defaultShippingType'] = Shop::ORDER_TYPE_SELF_COLLECTION_KEY;
            } else {
                $data[$userId]['info']['defaultShippingType'] = Shop::ORDER_TYPE_DELIVERY_KEY;
            }

            if ($value['info']['orderType'] >= Shop::ORDER_TYPE_SELF_COLLECTION) {
                $user = $userService->get($userId);
                $data[$userId]['info']['addresses'] = $user->addresses;
            }
        }

        return $data;
    }

    public function getCoupons(): Collection
    {
        return $this->coupons;
    }

    public function getSubTotalAfterSellerDiscount($filter, $shopId = null)
    {
        $sellerSubTotal = $this->getSubTotal($filter);
        if ($sellerSubTotal === 0) {
            return 0;
        }

        if ($shopId !== null && isset($this->discountBySeller[$shopId])) {
            return $sellerSubTotal - $this->discountBySeller[$shopId];
        }
        return $sellerSubTotal - $this->getTotalSellerCouponsDiscount($filter);
    }

    public function getTotalSellerCouponsDiscount($ysgFilter = null)
    {
        $total = 0;

        if (!$this->hasCoupons()) {
            return $total;
        }

        foreach($this->getCoupons() as $coupon) {
            if ($coupon->isYsgCoupon()) continue;

            $couponFilter = $this->buildProductFilterByCoupon($coupon);
            $filter = $this->buildCommonPointFilter($ysgFilter, $couponFilter);

            if (empty($filter)) continue;

            $total += $coupon->getDiscountAmount($this->getSubTotal($filter));
        }

        return $total;
    }

    public function buildCommonPointFilter($ysgFilter, $sellerFilter, $shopId = null)
    {
        $filter = [];
        $commonItems = [];
        foreach ($this->getItems() as $item) {
            if ($this->isItemInFilter($item, $ysgFilter) && $this->isItemInFilter($item, $sellerFilter) && ($shopId === null || $item->getShopId() === $shopId)) {
                $commonItems[] = $item->isVariant() ? $item->getVariantId() : $item->getId();
            }
        }

        if (!empty($commonItems)) {
            $filter['productIds'] = $commonItems;
        }

        return $filter;
    }

    public function getCouponDiscountValue(Coupon $coupon, $shopId = null)
    {
        $filter = $this->buildProductFilterByCoupon($coupon, $shopId);

        if ($coupon->isYsgCoupon()) {
            $price = $this->getSubTotalAfterSellerDiscount($filter, $shopId);
        } else {
            $price = $this->getSubTotal($filter);
        }

        if ($coupon->isCashVoucher()) {
            $price = $this->getSubTotalAfterNormalDiscount($filter, $coupon);
        }

        return $coupon->getDiscountAmount($price);
    }

    public function getSubTotalAfterNormalDiscount($filter, $coupon)
    {
        $price = $this->getSubTotalAfterSellerDiscount($filter) - $this->getTotalYsgCouponDiscount($filter);

        return $price;
    }

    public function getSubTotalRemainsForFreeShippingByShopId($shopId): float
    {
        $shippingCostAbove = 0;
        foreach($this->items as $item) {
            if ($shopId === $item->getShopId()) {
                $shippingCostAbove = $item->getFreeShippingAbove();
                break;
            }
        }

        $subTotal = $this->getSubTotalAfterDiscountByShopId($shopId, false);
        $subTotalRemains = $shippingCostAbove - $subTotal;

        return $subTotalRemains > 0 ? $subTotalRemains : 0;
    }

    public function getShopImpactRate(Coupon $coupon, $shopId = null)
    {
        $result = [
            'valid' => false,
            'value' => 1
        ];
        $shopAffected = [];

        $ysgFilter = $this->buildProductFilterByCoupon($coupon);
        $subTotal = $this->getSubTotal($ysgFilter) - $this->getTotalSellerCouponsDiscount($ysgFilter);

        $filter = $this->buildCommonPointFilter($ysgFilter, null, $shopId);
        $subTotalByShop = $this->getSubTotal($filter) - $this->getTotalSellerCouponsDiscount($filter);

        foreach ($this->items as $item) {
            if (!$this->isItemInFilter($item, $ysgFilter)) continue;

            $shopAffected[$item->getShopId()] = $item->getShopId();
        }

        if (in_array($shopId, $shopAffected)) {
            return [
                'valid' => true,
                'value' => $subTotalByShop / $subTotal
            ];
        }

        return $result;
    }

    public function getCouponDiscountAfterNormalCoupon(): float
    {
        $total = 0;

        if (!$this->hasCoupons()) {
            return $total;
        }
        foreach($this->getCoupons() as $coupon) {
            if ($coupon->isCashVoucher()) continue;
            $total += $this->getCouponDiscountValue($coupon);
        }
        return $total;
    }

    public function getGrandTotalAfterNormalCoupon()
    {
        $subTotal = $this->getSubTotal();
        $shippingFee = $this->getShippingFee();
        $discountAfterNormalCoupon = $this->getCouponDiscountAfterNormalCoupon();

        $total = $subTotal - $discountAfterNormalCoupon;
        $total = $total < 0 ? 0 : $total;
        $total += $shippingFee;

        return round((float)$total, 2);
    }

    public function getTotalYsgCouponDiscount($cashFilter = null)
    {
        $total = 0;
        if (!$this->hasCoupons()) {
            return $total;
        }

        if (!empty($cashFilter['shopIds'])) {
            foreach($cashFilter['shopIds'] as $shopId) {
                if (isset($this->discountByYsg[$shopId]) && $this->discountByYsg[$shopId] != 0) {
                    $total += $this->discountByYsg[$shopId];
                }
            }
        }

        return $total;
    }

    public function getCashVoucherDiscountByShopId(int $shopId): float
    {
        $total = 0;

        if (!$this->hasCoupons()) {
            return $total;
        }

        foreach($this->coupons as $coupon) {
            if (!$coupon->isYsgCoupon()) continue;
            if (!$coupon->isCashVoucher()) continue;

            $cashDiscount = $this->getCouponDiscountValue($coupon);
            $resultsImpactRate = $this->getShopImpactRate($coupon, $shopId);
            if ($resultsImpactRate['valid'] && $cashDiscount) {
                $total += $cashDiscount * $resultsImpactRate['value'];
            }
        }

        return $total;
    }

    public function hasNormalCoupons(): bool
    {
        return (bool)$this->coupons->filter(function ($coupon) {
            return !$coupon->isCashVoucher();
        })->count();
    }

    public function hasYsgCoupons(): bool
    {
        return $this->coupons->contains(function($coupon) {
            return $coupon->isYsgCoupon();
        });
    }

    public function hasSellerCoupons(): bool
    {
        return $this->coupons->contains(function($coupon) {
            return $coupon->isSellerCoupon();
        });
    }

    public function getSellersDiscount()
    {
        return collect($this->discountBySeller)->sum();
    }

    public function getCashCoupons()
    {
        return $this->coupons->filter->isCashVoucher()->values();
    }

    public function isShowSelectAddress()
    {
        $hasNoSelfCollection = false;
        foreach($this->shops as $id => $shop) {
            if ($this->getShopOrderType($id) != Shop::ORDER_TYPE_SELF_COLLECTION) {
                $hasNoSelfCollection = true;
                break;
            }
        }

        return $hasNoSelfCollection;
    }

    public function isShowCollectionAddress($shopId)
    {
        return $this->getShopOrderType($shopId) == Shop::ORDER_TYPE_SELF_COLLECTION;
    }

    public function getTotalItemsByCart(): int
    {
        return $this->items->count();
    }

    public function getCurrentQty($productId): int
    {
        $qty = 0;
        foreach ($this->items as $item) {
            if ($item->getId() === $productId) {
                $qty += $item->getQuantity();
            }
        }

        return $qty;
    }

    public function serialize()
    {
        return serialize([
            'items' => $this->items,
            'shippingFee' => $this->shippingFee,
            'coupons' => $this->coupons,
            'shops' => $this->shops,
            'discountBySeller' => $this->discountBySeller,
            'discountByYsg' => $this->discountByYsg,
            'cartId' => $this->cartId,
        ]);
    }

    public function unserialize($data)
    {
        $unserializedData = unserialize($data);
        $this->items = $unserializedData['items'];
        $this->shippingFee = $unserializedData['shippingFee'];
        $this->coupons = $unserializedData['coupons'];
        $this->shops = $unserializedData['shops'];
        $this->discountBySeller = $unserializedData['discountBySeller'];
        $this->discountByYsg = $unserializedData['discountByYsg'];
        $this->cartId = $unserializedData['cartId'];
    }

    public function toArray(): array
    {
        return [
            'items' => $this->items,
            'shippingFee' => $this->shippingFee,
            'coupons' => $this->coupons,
            'shops' => $this->shops,
        ];
    }

    public function toGroupedArray($selectedItems = [], $selectedVouchers = [], $selectedSelfCollectionAddresses = []): array
    {
        $data = [
            'sellers' => [],
            'info' => [
                'totalItems' => 0,
            ],
        ];
        $couponService = app()->make(CouponService::class);

        $coupons = $couponService->getExtractUsableCouponBySeller();
        
        foreach($this->getItems() as $item) {
            $sellerId = $item->getSellerId();
            $shopId = $item->getShopId();
            $selectedCoupon = $this->getSelectedCoupon($coupons[$shopId] ?? [], $selectedVouchers);

            $productId = $item->getId();
            $variantId = str_replace('v-', '', $item->getVariantId());

            $isSelected = $this->isItemSelected($productId, $item->getVariation(), $selectedItems);
            $key = $item->getVariation() . $productId;

            $data['sellers'][$sellerId]['items'][] = [
                'key' => $key,
                'productId' => $productId,
                'name' => $item->getName(),
                'slug' => $item->getSlug(),
                'variantId' => $variantId,
                'variation' => $item->getVariation(),
                'quantity' => $item->getQuantity(),
                'price' => $item->getTotalPrice(),
                'thumbnailImg' => url($item->getThumbnailImg()),
                'isSelected' => $isSelected,
            ];

            $data['sellers'][$sellerId]['info'] = [
                'name' => $item->getShopName(),
                'sellerId' => $sellerId,
                'shopIdForShippingFeeCalculation' => $shopId,
                'shopSlug' => $item->getShopSlug(),
                'orderType' => $item->getOrderType(),
                'address' => '',
                'coupons' => CouponResource::collection($coupons[$shopId] ?? []),
                'selectedVoucher' => $selectedCoupon,
                'selectedSelfCollectionAddress' => $selectedSelfCollectionAddresses[$sellerId] ?? null,
            ];
        }

        $userService = app()->make(UserService::class);

        foreach($data['sellers'] as $userId => $value) {
            $user = $userService->get($userId);
            $data['sellers'][$userId]['info']['shopId'] = $user->seller->id;

            if ($value['info']['orderType'] >= Shop::ORDER_TYPE_SELF_COLLECTION) {
                $data['sellers'][$userId]['info']['address'] = $user->addresses ?? [];
            }
        }

        $data['info'] = [
            'totalItems' => $this->getTotalItems(),
        ];

        return $data;
    }

    private function isItemSelected($productId, $variation, $selectedItems): bool
    {
        if (empty($selectedItems)) {
            return false;
        }

        $result = false;

        foreach ($selectedItems as $selectedItem) {
            if (!is_array($selectedItem)) {
                continue;
            }

            $selectedProductId = $selectedItem['product_id'] ?? null;
            $selectedVariation = $selectedItem['variation'] ?? null;

            if ($selectedVariation == null) {
                $result = $selectedProductId == $productId;
            } else {
                $result = $selectedProductId == $productId && $selectedVariation == $variation;
            }

            if ($result) {
                break;
            }
        }

        return $result;
    }

    public function getSelectedCoupon($coupons, $selectedVouchers)
    {
        $selectedCoupon = null;
        $cart = app(CartService::class)->getTempCart();
        foreach($coupons as $coupon) {
            if (in_array($coupon->code, $selectedVouchers)) {
                $couponObject = new ObjectModelsCoupon($coupon);
                $discount = single_price($cart->getCouponDiscountValue($couponObject));
                $selectedCoupon['discount'] = $discount;
                $selectedCoupon['code'] = $coupon->code;
                $selectedCoupon['title'] = __('coupon.title', ['offValue' => $coupon->discount_label]);
                break;
            }
        }
        return $selectedCoupon;
    }
}

