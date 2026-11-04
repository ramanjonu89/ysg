<?php

namespace App\ObjectModels;

interface CouponInterface
{
    public function getType(): string;

    public function getOwner(): string;

    public function getCode(): string;
    
    public function getShopId(): int;

    public function getMinBuy(): float;

    public function getMaxCoupon(): int;

    public function getShopIds();

    public function getMaxDiscount();

    public function getDiscount();

    public function getDiscountType();

    public function getDiscountAmount($price);

    public function getUsage(): int;

    public function canUse(): bool;

    public function freeShipping(): bool;

    public function canUseOnlyOnce(): bool;

    public function getCategoryIds();

    public function getSubCategoryIds();

    public function getSubSubCategoryIds();

    public function getOwnerName();

    public function forShop();

    public function getProductIds();
    
    public function isYsgCoupon();

    public function isSellerCoupon();

    public function isCashVoucher();
}
