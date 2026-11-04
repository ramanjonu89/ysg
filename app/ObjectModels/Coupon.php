<?php
namespace App\ObjectModels;

use App\Models\Coupon as CouponModel;
use App\Models\Order;
use App\Models\Product;
use App\Models\Shop;
use App\Services\CouponService;
use Serializable;

class Coupon implements CouponInterface, Serializable
{
    const TYPE_CART_BASE        = 'cart_base';
    const TYPE_PRODUCT_BASE     = 'product_base';
    const TYPE_CATEGORY_BASE    = 'category_base';
    const TYPE_CASH_BASE        = 'cash_base';

    const OWNER_SELLER          = 'seller';
    const OWNER_YSG             = 'ysg';

    const DISCOUNT_TYPE_AMOUNT  = 'amount';
    const DISCOUNT_TYPE_PERCENT = 'percent';

    protected $id;
    protected $type;
    protected $owner;
    protected $code;
    protected $shopId;
    protected $shopName = '';
    protected $minBuy = null;
    protected $maxCoupon = null;
    protected $maxDiscount = null;
    protected $discount;
    protected $discountType;
    protected $discountLabel;
    protected $freeShipping = false;
    protected $productIds = [];
    protected $categoryIds = [];
    protected $subcategoryIds = [];
    protected $subsubcategoryIds = [];
    protected $shopIds = [];

    protected $onlyOnce = false;
    protected $usedUserIds = [];

    public function __construct(CouponModel $coupon)
    {
        $this->init($coupon);
    }

    public function init(CouponModel $coupon)
    {
        $this->setId($coupon->id);
        $this->setShopId($coupon->seller_id);
        $this->setCode($coupon->code);
        $this->setType($coupon->type);
        $this->setOwner($coupon->owner);
        $this->setDiscount($coupon->discount);
        $this->setDiscountType($coupon->discount_type);
        $this->setFreeShipping($coupon->free_shipping);
        $this->setDiscountLabel($coupon->discount_label);
        $this->setUsedUserIds($coupon->usages->pluck('id')->toArray());
        $this->setOnlyOnce($coupon->only_once);

        if (!$this->isYsgCoupon()) {
            $this->setShopName($coupon->shop->name);
        }
        
        $detail = json_decode($coupon->details);
        if (isset($detail->min_buy)) {
            $this->setMinBuy($detail->min_buy);
        }

        if (isset($detail->max_coupon)) {
            $this->setMaxCoupon($detail->max_coupon);
        }

        if (isset($detail->max_discount)) {
            $this->setMaxDiscount($detail->max_discount);
        }

        switch ($coupon->type) {
            case self::TYPE_PRODUCT_BASE:
                if (self::OWNER_YSG === $coupon->owner) {
                    if (isset($detail->products)) {
                        $this->productIds = $detail->products;
                    } else {
                        foreach($detail->info as $item) {
                            $this->productIds[] = $item->product_id;
                        }
                    }
                    
                } else {
                    $this->productIds = $detail->products;
                }
                break;

            case self::TYPE_CATEGORY_BASE:
                $categoryIds        = isset($detail->categoryIds) ? $detail->categoryIds : [];
                $subcategoryIds     = isset($detail->subcategoryIds) ? $detail->subcategoryIds : [];
                $subsubcategoryIds  = isset($detail->subsubcategoryIds) ? $detail->subsubcategoryIds : [];

                $this->categoryIds          = $categoryIds;
                $this->subcategoryIds       = $subcategoryIds;
                $this->subsubcategoryIds    = $subsubcategoryIds;
                break;

            case self::TYPE_CART_BASE || self::TYPE_CASH_BASE:
                $shopIds = isset($detail->sellerIds) ? $detail->sellerIds : [];
                $this->setShopIds($shopIds);

                break;

            default:
                break;
        }
    }

    /**
     * @return mixed
     */
    public function getProductIds()
    {
        return $this->productIds;
    }

    /**
     * @return mixed
     */
    public function getCategoryIds()
    {
        return $this->categoryIds;
    }

    /**
     * @return mixed
     */
    public function getSubCategoryIds()
    {
        return $this->subcategoryIds;
    }

    /**
     * @return mixed
     */
    public function getSubSubCategoryIds()
    {
        return $this->subsubcategoryIds;
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
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param  mixed  $type
     */
    public function setType($type): void
    {
        $this->type = $type;
    }

    public function setOwner(string $owner)
    {
        $this->owner = $owner;
    }

    public function getOwner(): string
    {
        return $this->owner;
    }

    /**
     * @return mixed
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @param  mixed  $code
     */
    public function setCode($code): void
    {
        $this->code = $code;
    }

    /**
     * @return mixed
     */
    public function getShopId(): int
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

    /**
     * @return mixed
     */
    public function getShopName(): string
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
    public function getMinBuy(): float
    {
        return $this->minBuy == null ? 0 : $this->minBuy;
    }

    /**
     * @param  mixed  $minBuy
     */
    public function setMinBuy($minBuy): void
    {
        $this->minBuy = $minBuy;
    }

    /**
     * @return mixed
     */
    public function getMaxCoupon(): int
    {
        return $this->maxCoupon == null ? 99999 : $this->maxCoupon;
    }

    /**
     * @param  mixed  $maxCoupon
     */
    public function setMaxCoupon($maxCoupon): void
    {
        $this->maxCoupon = $maxCoupon;
    }

    /**
     * @return mixed
     */
    public function getMaxDiscount()
    {
        return $this->maxDiscount;
    }

    /**
     * @param  mixed  $maxDiscount
     */
    public function setMaxDiscount($maxDiscount): void
    {
        $this->maxDiscount = $maxDiscount;
    }

    /**
     * @return mixed
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * @param  mixed  $discount
     */
    public function setDiscount($discount): void
    {
        $this->discount = $discount;
    }

    /**
     * @return mixed
     */
    public function getDiscountType()
    {
        return $this->discountType;
    }

    /**
     * @param  mixed  $discountType
     */
    public function setDiscountType($discountType): void
    {
        $this->discountType = $discountType;
    }

    public function setDiscountLabel(string $discountLabel)
    {
        $this->discountLabel = $discountLabel;
    }

    public function getDiscountLabel()
    {
        return $this->discountLabel;
    }

    /**
     * @return mixed
     */
    public function getShopIds()
    {
        return $this->shopIds;
    }

    /**
     * @param  mixed  $shopIds
     */
    public function setShopIds($shopIds): void
    {
        $this->shopIds = $shopIds;
    }

    public function getDiscountAmount($price)
    {
        $total = 0;
        if (self::DISCOUNT_TYPE_PERCENT === $this->getDiscountType()) {
            $total = ($price * $this->getDiscount()) / 100;
        } elseif (self::DISCOUNT_TYPE_AMOUNT === $this->getDiscountType() && $this->getDiscount() !== null) {
            $total = $this->getDiscount();
        }
        if ($this->getMaxDiscount() !== null
            && $this->getMaxDiscount() !== 0
            && $total > $this->getMaxDiscount()
        ) {
            $total = $this->getMaxDiscount();
        }

        return $total > $price ? $price : $total;
    }

    public function getUsage(): int
    {
        $couponService = app()->make(CouponService::class);
        return $couponService->getUsagesCount($this->getId());
    }

    public function canUse(): bool
    {
        if (null === $this->getMaxCoupon()) {
            return true;
        }

        if ($this->getMaxCoupon() > $this->getUsage())
        {
            return true;
        }

        return false;
    }

    public function forShop(): bool
    {
        return 0 !== $this->getShopId() || count($this->getShopIds()) > 0;
    }

    /**
     * @param  bool  $freeShipping
     */
    public function setFreeShipping(bool $freeShipping): void
    {
        $this->freeShipping = $freeShipping;
    }

    /**
     * @return bool
     */
    public function freeShipping(): bool
    {
        return $this->freeShipping;
    }

    /**
     * @param  mixed  $onlyOnce
     */
    public function isOnlyOnce(): bool
    {
        return $this->onlyOnce;
    }

    /**
     * @param  mixed  $onlyOnce
     */
    public function setOnlyOnce($onlyOnce): void
    {
        $this->onlyOnce = $onlyOnce;
    }

    /**
     * @param  mixed  $usedUserIds
     */
    public function getUsedUserIds():array
    {
        return $this->usedUserIds;
    }

    /**
     * @param  mixed  $usedUserIds
     */
    public function setUsedUserIds($usedUserIds)
    {
        $this->usedUserIds = $usedUserIds;
    }

    public function canUseOnlyOnce():bool
    {
        $countCouponPaid = Order::where([
            'user_id'           => auth()->user()->id,
            'payment_status'    => 'paid',
            'coupon_code'       => $this->getCode()
        ])->count();

        if ($this->isOnlyOnce() === false)
        {
            return true;
        }

        if ($this->isOnlyOnce() === true && ($countCouponPaid === 0 || !in_array(auth()->user()->id, $this->getUsedUserIds())))
        {
            return true;
        }
        return false;
    }

    public function isSellerCoupon() : bool
    {
        return self::OWNER_SELLER === $this->owner;
    }

    public function isYsgCoupon(): bool
    {
        return self::OWNER_YSG === $this->getOwner();
    }

    public function getOwnerName()
    {
        return $this->isYsgCoupon() ? 'YSG' : $this->getShopName();
    }

    public function isCashVoucher()
    {
        return $this->getType() === Coupon::TYPE_CASH_BASE;
    }

    public function serialize()
    {
        return serialize([
            'id' => $this->id,
            'type' => $this->type,
            'owner' => $this->owner,
            'code' => $this->code,
            'shopId' => $this->shopId,
            'shopName' => $this->shopName,
            'minBuy' => $this->minBuy,
            'maxCoupon' => $this->maxCoupon,
            'maxDiscount' => $this->maxDiscount,
            'discount' => $this->discount,
            'discountType' => $this->discountType,
            'discountLabel' => $this->discountLabel,
            'freeShipping' => $this->freeShipping,
            'productIds' => $this->productIds,
            'categoryIds' => $this->categoryIds,
            'subcategoryIds' => $this->subcategoryIds,
            'subsubcategoryIds' => $this->subsubcategoryIds,
            'shopIds' => $this->shopIds,
            'onlyOnce' => $this->onlyOnce,
            'usedUserIds' => $this->usedUserIds,
        ]);
    }

    public function unserialize($data)
    {
        $unserializedData = unserialize($data);
        $this->id = $unserializedData['id'];
        $this->type = $unserializedData['type'];
        $this->owner = $unserializedData['owner'];
        $this->code = $unserializedData['code'];
        $this->shopId = $unserializedData['shopId'];
        $this->shopName = $unserializedData['shopName'];
        $this->minBuy = $unserializedData['minBuy'];
        $this->maxCoupon = $unserializedData['maxCoupon'];
        $this->maxDiscount = $unserializedData['maxDiscount'];
        $this->discount = $unserializedData['discount'];
        $this->discountType = $unserializedData['discountType'];
        $this->discountLabel = $unserializedData['discountLabel'];
        $this->freeShipping = $unserializedData['freeShipping'];
        $this->productIds = $unserializedData['productIds'];
        $this->categoryIds = $unserializedData['categoryIds'];
        $this->subcategoryIds = $unserializedData['subcategoryIds'];
        $this->subsubcategoryIds = $unserializedData['subsubcategoryIds'];
        $this->shopIds = $unserializedData['shopIds'];
        $this->onlyOnce = $unserializedData['onlyOnce'];
        $this->usedUserIds = $unserializedData['usedUserIds'];
    }
}
