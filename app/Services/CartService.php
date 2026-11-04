<?php

namespace App\Services;

use App\ObjectModels\CartInterface;
use App\ObjectModels\Cart;
use App\ObjectModels\Coupon;
use App\ObjectModels\TempCart;
use Illuminate\Support\Facades\Auth;
use App\Repositories\CartRepository;
use App\Repositories\ProductRepository;
class CartService
{
    const CART_SESSION_NAME = 'ysg_cart';

    protected $name;
    protected $tempName;

    protected $cartRepository;

    protected $productRepository;

    public function __construct($name = null, CartRepository $cartRepository, ProductRepository $productRepository)
    {
        if ($name == null) {
            $this->name = self::CART_SESSION_NAME;
            $this->tempName = self::CART_SESSION_NAME."_temp";
        } else {
            $this->name = $name;
            $this->tempName = $name."_temp";
        }

        $this->cartRepository = $cartRepository;
        $this->productRepository = $productRepository;
    }

    public static function createCartFromOrder($order): Cart
    {
        $cart = new Cart();
        $items = $order->orderDetails;
        foreach ($items as $item) {
            $cart->addItem($item, $item->quantity, $item->variation);
        }

        if (count($order->coupons) > 0) {
            foreach($order->coupons as $coupon) {
                $cart->addCoupon(new Coupon($coupon));
            }
        }

        return $cart;
    }

    public function getCart(): CartInterface
    {
        $cart = session($this->name) ?? new Cart();

        if (Auth::check() && !$cart->isSynced()) {
            $userId = Auth::user()->id;
            $dbCart = $this->cartRepository->findOrCreateCart($userId);

            $this->syncCartItems($cart, $dbCart);

            $cart->setCartId($dbCart->id);
        }
        $this->updateCart($cart);

        return $cart;
    }

    public function getTempCart(): CartInterface
    {
        $tempCart = session($this->tempName, null);
        if ($tempCart === null) {
            $tempCart = new TempCart();
            $this->updateTempCart($tempCart);
        }
        return $tempCart;
    }

    protected function syncCartItems(CartInterface $cart, $dbCart): void
    {
        $syncedItems = [];
        $dbCart->items->each(function ($item) use ($cart, &$syncedItems) {
            $itemKey = $item->variation . $item->product->id;
            $syncedItems[$itemKey] = $item;
            $cartItem = $cart->getItem($itemKey);

            $product = $this->productRepository->find($item->product_id);
            if (!$product) {
                // TODO: still add to cart session but mark as deleted
                return;
            }
            
            $quantity = $item->quantity;
            if ($cartItem) {
                $quantity = $item->quantity > $cartItem->getQuantity() 
                    ? $item->quantity 
                    : $cartItem->getQuantity();
            }
                
            if ($cartItem) {
                $item->quantity = $quantity;
                $item->save();
                $cartItem->setQuantity($quantity);
            } else {
                $cart->addItem($item->product, $item->quantity, $item->variation);
            }
        });


        foreach ($cart->getItems() as $key => $item) {
            if (empty($syncedItems[$key])) {
                $this->cartRepository->insertOrUpdateCartItem($dbCart->id, $item);
            }
        }
    }

    public function updateCart(CartInterface $cart): CartInterface
    {
        session([$this->name => $cart]);
        return $cart;
    }

    public function updateTempCart(CartInterface $tempCart): CartInterface
    {
        session([$this->tempName => $tempCart]);
        return $tempCart;
    }

    public function removeCartFromTempCart(): void
    {
        $cart = $this->getCart();
        $tempCart = $this->getTempCart();

        foreach ($tempCart->getItems() as $item) {
            $offsetName = $item->getOffsetName();
            $cart->removeItem($offsetName);
        }

        session()->remove($this->tempName);
        $this->updateCart($cart);
    }

    public function removeCartItem($itemOffset): void
    {
        try {
            $cart = $this->getCart();
            $tempCart = $this->getTempCart();
            $cartItem = $cart->getItem($itemOffset);

            $productId = $cartItem->getId();

            $cart->removeItem($itemOffset);
            $tempCart->removeItem($itemOffset);

            // $inCartQuantity = $cart->getCurrentQty($productId);
            // if ($inCartQuantity < $cartItem->getMinQty()) {
            //     foreach ($cart->getItems() as $itemKey => $item) {
            //         if ($item->getId() === $productId) {
            //             $cart->removeItem($itemKey);
            //             $tempCart->removeItem($itemKey);
            //         }
            //     }
            // }

            $this->updateTempCart($tempCart);

        } catch (\Exception $e) {
            // Handle exception if needed
        }
    }

    public function addTempCartItem($itemOffset, $orderType, $shippingAddress): void
    {
        $cart = $this->getCart();
        $tempCart = $this->getTempCart();
        $tempCart->syncItemFromCart($cart, $itemOffset, $orderType, $shippingAddress);
        $this->updateTempCart($tempCart);
    }

    public function removeTempCartItem($itemOffset): void
    {
        $tempCart = $this->getTempCart();
        $tempCart->removeItem($itemOffset);
        $this->updateTempCart($tempCart);
    }

    public function getItemsBySellerId(int $sellerId)
    {
        $cart = $this->getCart();
        return $cart->getItemsOnlyBySeller($sellerId);
    }

    public function toggleSelectAllFromCart($status, $sellerId, $orderTypeSeller): void
    {
        $items = $sellerId
                    ? $this->getItemsBySellerId($sellerId)
                    : $this->getCart()->getItems();
        foreach ($items as $item) {
            if(!$item->validateProductAvailability(true)) {
                $this->removeTempCartItem($item->getItemAction());
                continue;
            };

            $orderType = $orderTypeSeller[$item->getSellerId()] ?? null;
            $shippingAddress = $orderTypeSeller[$item->getSellerId()] ?? null;

            if ($status) {
                $this->addTempCartItem($item->getItemAction(), $orderType, $shippingAddress);
            } else {
                $this->removeTempCartItem($item->getItemAction());
            }
        }
    }
}
