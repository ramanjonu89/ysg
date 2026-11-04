<?php

namespace App\ObjectModels;

interface CartInterface
{
    public function addItem(ProductInterface $product, int $quantity, string $variation);

    public function removeItem(string $key);

    public function getItems();

    public function getItem(string $key);

    public function getSubTotal();

    public function getInternalShippingFee();

    public function getExternalShippingFee();

    public function getShippingFee(): float;

    public function getCouponDiscount();

    public function getGrandTotal();

    public function getTotalItems();

    public function validateCoupon(CouponInterface $coupon);

    public function addCoupon(CouponInterface $coupon);

    public function removeAllCoupons();

    public function getCoupons();

    public function removeCoupon(CouponInterface $coupon);

    // public function getCurrentQty($productId);

    // public function isOverMinQty(ProductInterface $product, int $quantity, string $variation);

    // public function isBelowMaxQty(ProductInterface $product, int $quantity, string $variation);

//    public function isEnoughStock(ProductInterface $product, int $quantity, string $variation);

    public function isSynced();

    public function getCartId();

    public function setCartId(string $cartId);

    public function setShopOrderType($sellerId, $order_type);

    public function getShopOrderType($sellerId);

    public function setShopAddress($sellerId, $address);

    public function getShopAddress($sellerId);

    public function setShopAddressTitle($sellerId, $address_title);

    public function toArray(): array;

    public function toGroupedArray($selectedItems = [], $selectedVouchers = [], $selectedSelfCollectionAddresses = []): array;

}
