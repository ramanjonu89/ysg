<?php

namespace App\ObjectModels;

use Illuminate\Support\Carbon;
use App\Models\Shop;
use App\Repositories\ProductRepository;
use Illuminate\Support\Facades\Cookie;

class TempCart extends AbstractCart
{
    public function setDefaultIdAddress($sellerId, $address_id)
    {
        $this->shops[$sellerId]['address_id'] = $address_id;
    }

    public function getDefaultIdAddress($sellerId)
    {
        return $this->shops[$sellerId]['address_id'] ?? '';
    }

    public function syncItemFromCart(CartInterface $cart, $offsetName, $orderType = null, $shippingAddressId = null)
    {
        $item = $cart->getItem($offsetName);
        if ($item != null) {
            $this->items->offsetSet($offsetName, $item);

            $this->removeAllCoupons();
            if (!isset($this->shops[$item->getShopId()])) {
                $sellers = $this->getItemsBySellerGroup();

                $shopAddressSelected = $sellers[$item->getSellerId()]['info']['addresses'][$shippingAddressId] ?? null;

                $this->shops[$item->getShopId()] = [
                    'address' => $shopAddressSelected ? $shopAddressSelected->fullAddress() : $item->getShopAddress(),
                    'address_id' => $shopAddressSelected->id ?? '',
                    'address_title' => $shopAddressSelected->title ?? '',
                    'order_type' => $orderType ? $orderType : $item->getDefaultOrderType(),
                    'title' => $item->getShopAddressTitle()
                ];
            }
        }

        return $item;
    }

    public function syncItemQty(CartItemInterface $cartItem)
    {
        $offsetName = $cartItem->getOffsetName();

        if ($this->items->offsetExists($offsetName)) {
            $qty = $cartItem->getQuantity();

            $item = $this->items->offsetGet($offsetName);
            $item->setQuantity($qty);
        }
    }

    public function addItem(ProductInterface $product, int $quantity, string $variation): ?CartItemInterface
    {
        $offsetName = $variation . $product->id;

        if ($this->items->offsetExists($offsetName)) {
            $item = $this->items->offsetGet($offsetName);
            $item->addQuantity($quantity);
        } else {
            $item = new CartItem($product, $quantity, $variation);
            $this->items->offsetSet($offsetName, $item);
        }

        $this->removeAllCoupons();
        $this->shops[$item->getShopId()] = [
            'order_type' => $item->getDefaultOrderType(),
            'address' => $item->getShopAddress(),
            'title' => $item->getShopAddressTitle()
        ];

        return $item;
    }

    public function hasItem(string $key)
    {
        if ($this->items->has($key)) {
            return true;
        }

        return false;
    }

    public function removeItem(string $key): bool
    {
        $item = $this->getItem($key);
        if ($item instanceof CartItemInterface) {
            try {
                $shopId = $item->getShopId();

                $this->removeAllCoupons();
                $this->items->pull($key);

                $totalPriceByShop = $this->getSubTotalByShopId($shopId);
                if ($totalPriceByShop <= 0) {
                    unset($this->shops[$shopId]);
                }

                return true;
            } catch (\Exception $e) {
            }
        }
        return false;
    }
    
    public function isAllShopsSelfCollection() {
        $sellers = $this->getItemsBySellerGroup();
        foreach($sellers as $sellerId => $seller) {
            if (!$this->isSelfCollectionShop($seller['info']['shopId'])) {
                return false;
            }
        }

        return true;
    }

    public function getShopAddressId($shopId) {
        if (isset($this->shops[$shopId])) {
            return $this->shops[$shopId]['address_id'] ?? null;
        }

        return null;
    }

    public function getTotalItemsBySeller($sellerId): int
    {
        $items = $this->items->filter(function ($item) use ($sellerId) {
            return $item->getSellerId() === $sellerId;
        });

        return $items->count();
    }

    public function isEnoughStock(ProductInterface $product, int $quantity, string $variation)
    {
        return true;
    }

    public function allItemsMeetMinQuantity(): bool
    {
        foreach ($this->items as $item) {
            if (!$this->validatePurchaseRules($item)['valid']) {
                return false;
            }
        }

        return true;
    }

    public function validatePurchaseRules($cartItem): array
    {
        $productRepository = app()->make(ProductRepository::class);
        $product = $productRepository->find($cartItem->getId());

        if (!$this->isOverMinQty($product, $cartItem)) {
            return [
                'valid' => false,
                'message' => __('purchase.minimum_quantity_required', ['value' => $cartItem->getMinQty()])
            ];
        }

        if (!$this->isBelowMaxQty($cartItem)) {
            return [
                'valid' => false,
                'message' => __('purchase.maximum_quantity_required', ['value' => $cartItem->getMaxQty()])
            ];
        }

        return [
            'valid' => true,
            'message' => __('OK')
        ];
    }

    public function isOverMinQty(ProductInterface $product, $item)
    {
        if (method_exists($product, 'isDigital') && $product->isDigital()) {
            return true;
        }

        $inCartQuantity = $this->getCurrentQty($item->getId());
        if ($inCartQuantity < $item->getMinQty()) {
            return false;
        }

        return true;
    }

    public function isBelowMaxQty($item): bool
    {
        $maxQty = $item->getMaxQty();

        if (!$maxQty || $maxQty <= 0) {
            return true;
        }

        $maxQtyStart = Carbon::parse($item->getMaxQtyStart());
        $maxQtyEnd = Carbon::parse($item->getMaxQtyEnd());

        if ($maxQtyStart->isPast() && $maxQtyEnd->isFuture()) {
            return $this->getCurrentQty($item->getId()) <= $maxQty;
        }

        return true;
    }

    public function updateDeliveryInfo()
    {
        $sellers = $this->getItemsBySellerGroup();
        foreach ($sellers as $sellerId => $seller) {
            $shopId = $seller['info']['shopId'];
            $orderTypeCookie = 'order_type_' . $sellerId;
            $addressCookie = 'address_' . $sellerId;
            if (Cookie::get($orderTypeCookie) !== null) {
                $orderType = $_COOKIE[$orderTypeCookie];
                Cookie::queue(Cookie::forget($orderTypeCookie));
                $this->setShopOrderType($shopId, $orderType);
            }

            if (Cookie::get($addressCookie) !== null) {
                $address = $seller['info']['addresses'][$_COOKIE[$addressCookie]];
                $this->setShopAddress($shopId, $address->fullAddress());
                $this->setShopAddressTitle($shopId, $address->title);
                $this->setDefaultIdAddress($shopId, $address->id);
                Cookie::queue(Cookie::forget($addressCookie));
            } else if ($seller['info']['orderType'] == Shop::ORDER_TYPE_SELF_COLLECTION) {
                $this->setShopOrderType($shopId, Shop::ORDER_TYPE_SELF_COLLECTION);
                $this->setShopAddress($shopId, $seller['info']['addresses'][0]->fullAddress());
                $this->setShopAddressTitle($shopId, $seller['info']['addresses'][0]->title);
                $this->setDefaultIdAddress($shopId, $seller['info']['addresses'][0]->id);
            }
        }

        return true;
    }
}
