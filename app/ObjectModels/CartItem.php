<?php

namespace App\ObjectModels;

use App\ObjectModels\CartItemInterface;
use App\ObjectModels\ProductInterface;
use App\Models\Product;
use App\Models\Shop;
use App\User;
use App\Services\ProductService;
use Serializable;

class CartItem implements CartItemInterface, Serializable
{
    protected $id;
    protected $name;
    protected $slug;
    protected $thumbnailImg;
    protected $tax;
    protected $taxType;
    protected $price;
    protected $minQty;
    protected $maxQty;
    protected $maxQtyStart;
    protected $maxQtyEnd;
    protected $quantity = 1;
    protected $order_type = Shop::ORDER_TYPE_DELIVERY;
    protected $shop_address = '';
    protected $shop_address_title = '';
    protected $shop_address_id = '';
    protected $userId;
    protected $sellerId;
    protected $shopId;
    protected $shopName;
    protected $shopSlug;
    protected $companyShippingType;
    protected $freeShippingAbove;
    protected $shippingFee;

    protected $variation = '';
    protected $variantId = null;

    protected $coupons; //colection of CouponInterface

    protected $categoryId;
    protected $subcategoryId;
    protected $subsubcategoryId;

    protected $productObject = null;
    protected $productService;

    public function __construct(ProductInterface $product, int $quantity = 1, string $variation = '')
    {
        $this->quantity = $quantity;
        $this->variation = $variation;
        $this->productService = app()->make(ProductService::class);
        $this->productObject = $this->productService->get($product->getId());

        $this->init($product);

        $this->coupons = collect();
    }

    public function init(ProductInterface $product)
    {
        $name = '' === $this->variation ? $product->getName() : $product->getName() . " ($this->variation)";
        $p = clone $product;

        $this->setId($product->getId());
        $this->setName($name);
        $this->setSlug($product->getSlug());
        $this->setThumbnailImg($product->getThumbnailImageOrder($this->variation));

        $this->setTaxType($product->getTaxType());
        $this->setTax($product->getTax());
        $this->setOrderType($product->getOrderType());

        $defaultAddress = $product->getDefaultAddress();
        $this->setShopAddress($defaultAddress ? $defaultAddress->fullAddress() : '');
        $this->setShopAddressTitle($defaultAddress ? $defaultAddress->title : '');
        $this->setDefaultIdAddress($defaultAddress ? $defaultAddress->id : '');

        $this->setSellerId($product->getSellerId());
        $this->setShopId($product->getShopId());
        $this->setShopName($product->getShopName());
        $this->setShopSlug($product->getShopSlug());
        $this->setCompanyShippingType($product->getCompanyShippingType());
        $this->setFreeShippingAbove($product->getFreeShippingAbove());
        $this->setShippingFee($product->getShippingFee());
        $this->setCategoryId($product->getCategoryId());
        $this->setSubCategoryId($product->getSubCategoryId());
        $this->setSubSubCategoryId($product->getSubSubCategoryId());

        $this->setMinQty($product->getMinQty());
        $this->setMaxQty($product->getMaxQty());
        $this->setMaxQtyStart($product->getMaxQtyStart());
        $this->setMaxQtyEnd($product->getMaxQtyEnd());

        $this->setVariantId($product->getVariantId($this->variation));
    }

    /**
     * @return mixed
     */
    public function getCurrentStock()
    {
        $productService = app()->make(ProductService::class);
        return $productService->getProductCurrentStock($this->getId(), $this->variation);
    }

    /**
     * @return mixed
     */
    public function getFreeShippingAbove()
    {
        return $this->freeShippingAbove;
    }

    /**
     * @param  mixed  $freeShippingAbove
     */
    public function setFreeShippingAbove($freeShippingAbove): void
    {
        $this->freeShippingAbove = $freeShippingAbove;
    }

    /**
     * @return mixed
     */
    public function getShippingFee()
    {
        return $this->shippingFee;
    }

    /**
     * @param  mixed  $shippingFee
     */
    public function setShippingFee($shippingFee): void
    {
        $this->shippingFee = $shippingFee;
    }

    /**
     * @return mixed
     */
    public function getVariantId()
    {
        return 'v-' . $this->variantId;
    }

    /**
     * @param  mixed  $variantId
     */
    public function setVariantId($variantId): void
    {
        $this->variantId = $variantId;
    }

    /**
     * @return mixed
     */
    public function getShopName()
    {
        return $this->shopName;
    }

    /**
     * @param  mixed  $shopName
     */
    public function setShopName($shopName): void
    {
        $this->shopName = $shopName;
    }

    /**
     * @return mixed
     */
    public function getTaxType()
    {
        return $this->taxType;
    }

    /**
     * @param  mixed  $taxType
     */
    public function setTaxType($taxType): void
    {
        $this->taxType = $taxType;
    }

    /**
     * @return mixed
     */
    public function getCompanyShippingType()
    {
        return $this->companyShippingType;
    }

    /**
     * @param  mixed  $companyShippingType
     */
    public function setCompanyShippingType($companyShippingType): void
    {
        $this->companyShippingType = $companyShippingType;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param  mixed  $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param  mixed  $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    public function getProductService() {
        return $this->productService ?? app()->make(ProductService::class);
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        $product = $this->productObject === null ? $this->getProductService()->get($this->getId()) : $this->productObject;
        return $product->getPrice($this->variation);
    }

    /**
     * @param  mixed  $price
     */
    public function setPrice($price): void
    {
        $this->price = $price;
    }

    /**
     * @return mixed
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param  mixed  $slug
     */
    public function setSlug($slug): void
    {
        $this->slug = $slug;
    }

    /**
     * @return mixed
     */
    public function getThumbnailImg()
    {
        return $this->thumbnailImg;
    }

    /**
     * @param  mixed  $thumbnailImg
     */
    public function setThumbnailImg($thumbnailImg): self
    {
        $this->thumbnailImg = $thumbnailImg;

        return $this;
    }

    public function setUserId(int $id): self
    {
        $this->userId = $id;

        return $this;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setSellerId(int $id): self
    {
        $this->sellerId = $id;

        return $this;
    }

    public function getSellerId(): int
    {
        return $this->sellerId;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): int
    {
        $this->quantity = $quantity;

        return $this->quantity;
    }

    public function getMinQty()
    {
        return $this->minQty;
    }

    public function setMinQty($minQty)
    {
        $this->minQty = $minQty;
    }

    public function getMaxQty()
    {
        return $this->maxQty;
    }

    public function setMaxQty($maxQty)
    {
        $this->maxQty = $maxQty;
    }

    public function setMaxQtyStart($maxQtyStart)
    {
        $this->maxQtyStart = $maxQtyStart;
    }

    public function getMaxQtyStart()
    {
        return $this->maxQtyStart;
    }

    public function setMaxQtyEnd($maxQtyEnd)
    {
        $this->maxQtyEnd = $maxQtyEnd;
    }

    public function getMaxQtyEnd()
    {
        return $this->maxQtyEnd;
    }
    /**
     * @return mixed
     */
    public function getShopId()
    {
        return $this->shopId;
    }

    /**
     * @param  mixed  $shopId
     */
    public function setShopId($shopId): void
    {
        $this->shopId = $shopId;
    }


    public function getShopSlug()
    {
        return $this->shopSlug;
    }

    public function setShopSlug($shopSlug)
    {
        $this->shopSlug = $shopSlug;
    }

    public function setOrderType($order_type)
    {
        $this->order_type = $order_type;

        return $this;
    }

    public function getOrderType(): int
    {
        return $this->order_type;
    }

    public function getDefaultOrderType(): int
    {
        return $this->order_type == Shop::ORDER_TYPE_BOTH ? Shop::ORDER_TYPE_DELIVERY : $this->order_type;
    }

    public function isSelfCollection(): bool
    {
        return $this->order_type >= Shop::ORDER_TYPE_SELF_COLLECTION;
    }

    public function setShopAddress($shop_address)
    {
        $this->shop_address = $shop_address;

        return $this;
    }

    public function getShopAddress($includeTitle = false): ?string
    {
        if ($includeTitle) {
            return "<b>$this->shop_address_title - </b> $this->shop_address";
        }

        return $this->shop_address;
    }

    public function setShopAddressTitle($shop_address_title)
    {
        $this->shop_address_title = $shop_address_title;

        return $this;
    }

    public function getShopAddressTitle(): ?string
    {
        return $this->shop_address_title;
    }

    public function setDefaultIdAddress($shop_address_id)
    {
        $this->shop_address_id = $shop_address_id;

        return $this;
    }

    public function getDefaultIdAddress()
    {
        return $this->shop_address_id;
    }

    public function setTax($tax)
    {
        $this->tax = $tax;

        return $this;
    }

    public function getTax(): float
    {
        return $this->tax ?? 0;
    }

    public function getTaxAmount(): float {
        $tax = 0;

        if ($this->getTaxType() == 'percent') {
            $tax = ($this->getPrice() * $this->getTax()) / 100;
        } elseif ($this->getTaxType() == 'amount' && $this->getTax() !== null) {
            $tax = $this->getTax();
        }

        return $tax;
    }

    public function getTotalPrice()
    {
        $price = $this->getTaxAmount() + $this->getprice();

        return $price * $this->quantity;
    }

    public function addQuantity(int $quantity): int {
        return $this->setQuantity($this->quantity + $quantity);
    }

    public function isInternalShipping(): bool
    {
        return $this->getCompanyShippingType() === User::COMPANY_SHIPPING_TYPE_INTERNAL;
    }

    public function addCoupon(CouponInterface $coupon): void {
        if (Coupon::TYPE_PRODUCT_BASE !== $coupon->getType()) {
            return;
        }

        $this->coupons->offsetSet($coupon->getId(), $coupon);
    }

    public function getCouponDiscount() {
        $total = 0;

        if ($this->coupons->count() === 0) {
            return $total;
        }

        foreach($this->coupons as $coupon) {
            $total += $coupon->getDiscountAmount($this->getTax() + $this->getprice());
        }

        return $total;
    }

    public function removeAllCoupons()
    {
        $this->coupons = collect();
    }

    public function removeCoupon(CouponInterface $coupon) {
        return $this->coupons->pull($coupon->getId());
    }

    /**
     * @return string
     */
    public function getVariation(): string
    {
        return $this->variation;
    }

    /**
     * @param string $variation
     */
    public function setVariation(string $variation): void
    {
        $this->variation = $variation;
    }

    /**
     * @return string
     */
    public function getOffsetName(): string
    {
        return $this->variation . $this->id;
    }

    public function setCategoryId($categoryId)
    {
        $this->categoryId = $categoryId;

        return $this;
    }

    public function getCategoryId()
    {
        return $this->categoryId;
    }

    public function setSubCategoryId($subcategoryId)
    {
        $this->subcategoryId = $subcategoryId;

        return $this;
    }

    public function getSubCategoryId()
    {
        return $this->subcategoryId;
    }

    public function setSubSubCategoryId($subsubcategoryId)
    {
        $this->subsubcategoryId = $subsubcategoryId;

        return $this;
    }

    public function getSubSubCategoryId()
    {
        return $this->subsubcategoryId;
    }


    public function getItemAction() {
        if(empty($this->getVariation())) {
            return $this->getId();
        }

        return $this->getVariation() . $this->getId();
    }

    public function isVariant() {
        return '' !== $this->getVariation();
    }

    public function validateProductAvailability($selectCart = null): bool
    {
        if (!$this->getCurrentStock()) {
            return false;
        }

        $productService = app()->make(ProductService::class);
        if (!$productService->isEnableProduct($this->getId())) {
            return false;
        }

        if ($selectCart && $this->getCurrentStock() < $this->getQuantity()) {
            return false;
        }

        return true;
    }

    public function serialize()
    {
        return serialize([
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'thumbnailImg' => $this->thumbnailImg,
            'tax' => $this->tax,
            'taxType' => $this->taxType,
            'price' => $this->getPrice(),
            'minQty' => $this->minQty,
            'maxQty' => $this->maxQty,
            'maxQtyStart' => $this->maxQtyStart,
            'maxQtyEnd' => $this->maxQtyEnd,
            'quantity' => $this->quantity,
            'order_type' => $this->order_type,
            'shop_address' => $this->shop_address,
            'shop_address_title' => $this->shop_address_title,
            'shop_address_id' => $this->shop_address_id,
            'userId' => $this->userId,
            'sellerId' => $this->sellerId,
            'shopId' => $this->shopId,
            'shopName' => $this->shopName,
            'shopSlug' => $this->shopSlug,
            'companyShippingType' => $this->companyShippingType,
            'freeShippingAbove' => $this->freeShippingAbove,
            'shippingFee' => $this->shippingFee,
            'variation' => $this->variation,
            'variantId' => $this->variantId,
            'coupons' => $this->coupons,
            'categoryId' => $this->categoryId,
            'subcategoryId' => $this->subcategoryId,
            'subsubcategoryId' => $this->subsubcategoryId,
        ]);
    }

    public function unserialize($data)
    {
        $unserializedData = unserialize($data);
        $this->id = $unserializedData['id'];
        $this->name = $unserializedData['name'];
        $this->slug = $unserializedData['slug'];
        $this->thumbnailImg = $unserializedData['thumbnailImg'];
        $this->tax = $unserializedData['tax'];
        $this->taxType = $unserializedData['taxType'];
        $this->price = $unserializedData['price'];
        $this->minQty = $unserializedData['minQty'];
        $this->maxQty = $unserializedData['maxQty'];
        $this->maxQtyStart = $unserializedData['maxQtyStart'];
        $this->maxQtyEnd = $unserializedData['maxQtyEnd'];
        $this->quantity = $unserializedData['quantity'];
        $this->order_type = $unserializedData['order_type'];
        $this->shop_address = $unserializedData['shop_address'];
        $this->shop_address_title = $unserializedData['shop_address_title'];
        $this->shop_address_id = $unserializedData['shop_address_id'];
        $this->userId = $unserializedData['userId'];
        $this->sellerId = $unserializedData['sellerId'];
        $this->shopId = $unserializedData['shopId'];
        $this->shopName = $unserializedData['shopName'];
        $this->shopSlug = $unserializedData['shopSlug'];
        $this->companyShippingType = $unserializedData['companyShippingType'];
        $this->freeShippingAbove = $unserializedData['freeShippingAbove'];
        $this->shippingFee = $unserializedData['shippingFee'];
        $this->variation = $unserializedData['variation'];
        $this->variantId = $unserializedData['variantId'];
        $this->coupons = $unserializedData['coupons'];
        $this->categoryId = $unserializedData['categoryId'];
        $this->subcategoryId = $unserializedData['subcategoryId'];
        $this->subsubcategoryId = $unserializedData['subsubcategoryId'];
    }
}
