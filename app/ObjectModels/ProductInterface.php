<?php

namespace App\ObjectModels;

use App\Models\Product;
use App\Models\Seller;
use App\User;

interface ProductInterface
{
    public function getId(): int;

    public function getName(): string;

    public function getSlug(): string;

    public function getThumbnailImg(): string;

    public function getTaxType(): ?string;

    public function getTax(): ?float;

    public function getPrice(string $variation = '');

    public function getSellerId();

    public function getShopId();

    public function getShopName();

    public function getCompanyShippingType();

    public function getFreeShippingAbove();

    public function getShippingFee();

    public function getCategoryId();

    public function getSubCategoryId();

    public function getSubSubCategoryId();

    public function getShopSlug();

    public function getMinQty();

    public function getMaxQty();

    public function getMaxQtyStart();

    public function getMaxQtyEnd();

    public function getThumbnailImageOrder(string $variantSku);

    public function getOrderType();

    public function getDefaultAddress();

    public function getVariantId(string $variantSku = '');
}
