<?php

namespace App\ObjectModels;

use App\Models\Product;
use App\Models\Seller;
use App\User;

interface CartItemInterface
{
    public function getId();
    
    public function getSellerId(): int;

    public function getQuantity(): int;
    
    public function addQuantity(int $quantity): int;

    public function setQuantity(int $quantity): int;

    public function getMinQty();

    public function getMaxQty();

    public function getMaxQtyStart();

    public function getMaxQtyEnd();

    public function isInternalShipping(): bool;

    public function getTax(): float;

    public function getTaxAmount(): float;

    public function getTotalPrice();

    public function addCoupon(CouponInterface $coupon);
    
    public function getCouponDiscount();

    public function getShopId();

    public function getShopSlug();

    public function getShopName();

    public function getFreeShippingAbove();

    public function getCompanyShippingType();
    
    public function getShippingFee();

    public function removeAllCoupons();

    public function isVariant();

    public function getVariantId();
    
    public function getOrderType();

    public function getDefaultOrderType();
    
    public function isSelfCollection();
    
    public function getShopAddress();

    public function getDefaultIdAddress();

    public function getVariation();
}
