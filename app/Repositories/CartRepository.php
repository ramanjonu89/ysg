<?php

namespace App\Repositories;

use App\Models\Cart;
use App\Models\CartItem;
use App\ObjectModels\CartItem as CartItemObject;
use Illuminate\Support\Facades\Auth;

class CartRepository extends AbstractRepo
{
    public function getModel(): string
    {
        return Cart::class;
    }

    public function findOrCreateCart($userId)
    {
        return Cart::firstOrCreate(['user_id' => $userId]);
    }
    
    public function getCartItems($cartId)
    {
        return CartItem::where('cart_id', $cartId)->get();
    }

    public function insertOrUpdateCartItem($cartId, CartItemObject $item)
    {
        return CartItem::updateOrCreate(['cart_id' => $cartId, 'product_id' => $item->getId(), 'variation' => $item->getVariation()], [
            'quantity' => $item->getQuantity(),
            'image' => $item->getThumbnailImg(),
            'name' => $item->getName(),
            'price' => $item->getPrice(),
            'variation' => $item->getVariation()
        ]);
    }

    public function deleteCartItem($cartId, $id, $variation = '')
    {
        return CartItem::where('cart_id', $cartId)->where('product_id', $id)->where('variation', $variation)->delete();
    }

    public function clearUserCart($userId)
    {
        return Cart::where('user_id', $userId)->delete();
    }
}