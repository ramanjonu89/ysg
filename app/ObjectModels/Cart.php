<?php

namespace App\ObjectModels;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Repositories\CartRepository;

class Cart extends AbstractCart
{
    public function isEnoughStock(ProductInterface $product, int $quantity, string $variation)
    {
        $item = $this->getItem($variation.$product->id);
        $inCartQuantity = null !== $item ? $item->getQuantity() : 0;
        if ($product->getStock($variation) < $quantity + $inCartQuantity) {
            return false;
        }
        return true;
    }

    public function isOverMinQty(ProductInterface $product, int $quantity, string $variation, $updateCart = null)
    {
        if (method_exists($product, 'isDigital') && $product->isDigital()) {
            return true;
        }

        $inCartQuantity = $this->getCurrentQty($product->id);
        if (!$updateCart && $quantity + $inCartQuantity < $product->min_qty) {
            return false;
        }

        $inCartQtyUpdated = $this->getUpdatedQty($product, $quantity, $variation);
        if ($updateCart && $inCartQtyUpdated < $product->min_qty) {
            return false;
        }

        return true;
    }

    public function isBelowMaxQty(ProductInterface $product, int $quantity, string $variation, $updateCart = null)
    {
        if ($product->max_qty && $product->max_qty > 0) {
            $maxQtyStart = Carbon::parse($product->max_qty_start);
            $maxQtyEnd = Carbon::parse($product->max_qty_end);

            if ($maxQtyStart->isPast() && $maxQtyEnd->isFuture()) {
                $inCartQuantity = $this->getCurrentQty($product->id);
                if (!$updateCart && $product->max_qty < $quantity + $inCartQuantity) {
                    return false;
                }

                $inCartQtyUpdated = $this->getUpdatedQty($product, $quantity, $variation);
                if ($updateCart && $product->max_qty < $inCartQtyUpdated) {
                    return false;
                }
            }
        }
        return true;
    }

    public function getUpdatedQty(ProductInterface $product, $qtyUpdate, $itemKey)
    {
        $qty = 0;
        foreach ($this->items as $key => $item) {
            if ($key == $itemKey) continue;

            if ($item->getId() === $product->getId()) {
                $qty += $item->getQuantity();
            }
        }

        return $qty + $qtyUpdate;
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

        if ($this->isSynced() && Auth::check()) {
            $cartRepository = app()->make(CartRepository::class);
            $cartRepository->insertOrUpdateCartItem($this->cartId, $item);
        }

        return $item;
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

                if ($this->isSynced() && Auth::check()) {
                    $cartRepository = app()->make(CartRepository::class);
                    $cartRepository->deleteCartItem($this->cartId, $item->getId(), $item->getVariation());
                }

                return true;
            } catch (\Exception $e) {
            }
        }

        return false;
    }

    public function getItemsOnlyBySeller($sellerId): array
    {
        return $this->getItemsBySellerGroup()[$sellerId]['items'] ?? [];
    }

    public function getTotalItemsBySeller($sellerID): int
    {
        $totalItems = $this->getItemsOnlyBySeller($sellerID);
        return count($totalItems);
    }

    public function isValidCartQuantity($item): bool
    {
        $currentStock = $item->getCurrentStock();
        if ($item->getQuantity() > $currentStock) {
            return false;
        }

        if(!$item->validateProductAvailability()) {
            return false;
        }

        return true;
    }

    public function isOutsideQuantityLimit($item): bool
    {
        return $this->isBelowMinimumQuantity($item) || $this->isAboveMaximumQuantity($item);
    }

    public function isBelowMinimumQuantity($item): bool
    {
        $inCartQuantity = $this->getCurrentQty($item->getId());
        $minQty = $item->getMinQty();

        return $inCartQuantity < $minQty;
    }

    public function isAboveMaximumQuantity($item): bool
    {
        $maxQty = $item->getMaxQty();

        if (!$maxQty || $maxQty <= 0) {
            return false;
        }

        $maxQtyStart = Carbon::parse($item->getMaxQtyStart());
        $maxQtyEnd = Carbon::parse($item->getMaxQtyEnd());

        if ($maxQtyStart->isPast() && $maxQtyEnd->isFuture()) {
            return $item->getQuantity() > $maxQty;
        }

        return false;
    }
}
