<?php

namespace App\Services\Api\Mobile;

use App\Models\Coupon;
use App\Models\Shop;
use App\ObjectModels\CartInterface;
use App\ObjectModels\Cart;
use App\ObjectModels\Coupon as CouponModel;
use App\ObjectModels\ProductInterface;
use App\Services\CartService as WebCartService;
use App\Services\UserService;
use Illuminate\Support\Facades\Auth;
use App\Repositories\CartRepository;
use App\Services\ProductService as ProductCommonService;
use App\Services\ProductStockService as ProductStockCommonService;
use App\Repositories\ProductRepository;
use App\Services\CouponService;
use App\Http\Resources\Mobile\CouponResource;
use App\Services\AddressService;

class CartService
{
    protected $cartRepository;
    protected $productService;
    protected $productRepository;
    protected $productStockService;
    protected $couponService;
    protected $webCartService;

    public function __construct(
        CartRepository $cartRepository, 
        ProductCommonService $productService,
        ProductStockCommonService $productStockService,
        ProductRepository $productRepository, 
        CouponService $couponService,
        WebCartService $webCartService
    )
    {
        $this->cartRepository = $cartRepository;
        $this->productService = $productService;
        $this->productRepository = $productRepository;
        $this->productStockService = $productStockService;
        $this->couponService = $couponService;
        $this->webCartService = $webCartService;
    }


    public function index($selectedItems = [], $selectedVouchers = [], $selectedSelfCollectionAddresses = [])
    {
        $cart = $this->getCart();
        return $this->groupBySeller($cart, $selectedItems, $selectedVouchers, $selectedSelfCollectionAddresses);
    }

    private function groupBySeller($cart, $selectedItems = [], $selectedVouchers = [], $selectedSelfCollectionAddresses = []): array
    {
        $data = [];

        $coupons    = $this->couponService->getExtractUsableCouponBySeller($cart);
        $couponsYsg = $this->couponService->getExtractUsableCouponByAdmin();

        foreach($cart->getItems() as $item) {
            $sellerId = $item->getSellerId();
            $shopId = $item->getShopId();
            
            $data[$sellerId]['info'] = [
                'name' => $item->getShopName(),
                'sellerId' => $sellerId,
                'shopId' => $shopId,
                'shopSlug' => $item->getShopSlug(),
                'orderType' => $item->getOrderType(),
                'address' => '',
                'coupons' => CouponResource::collection($coupons[$shopId]),
                'selectedVoucher' => $selectedVouchers[$sellerId] ?? null,
                'selectedSelfCollectionAddress' => $selectedSelfCollectionAddresses[$sellerId] ?? null,
            ];

            $productId = $item->getId();
            $variantId = str_replace('v-', '', $item->getVariantId());
            
            $isSelected = $this->isItemSelected($productId, $variantId, $selectedItems);

            $data[$sellerId]['items'][] = [
                'productId' => $productId,
                'name' => $item->getName(),
                'slug' => $item->getSlug(),
                'variantId' => $variantId,
                'variation' => $item->getVariation(),
                'quantity' => $item->getQuantity(),
                'price' => $item->getTotalPrice(),
                'thumbnailImg' => url($item->getThumbnailImg()),
                'isSelected' => $isSelected,
            ];
        }

        $userService = app()->make(UserService::class);

        foreach($data as $userId => $value) {
            $shopId = $value['info']['shopId'];

            if ($value['info']['orderType'] == Shop::ORDER_TYPE_SELF_COLLECTION) {
                $data[$userId]['info']['shippingFee'] = 0;
                $data[$userId]['info']['addMoreForFreeShipping'] = 0;
            } else {
                $data[$userId]['info']['shippingFee'] = $cart->getShippingFeeByShopId($shopId);
                $data[$userId]['info']['addMoreForFreeShipping'] = $cart->getSubTotalRemainsForFreeShippingByShopId($shopId);
            }

            if ($value['info']['orderType'] >= Shop::ORDER_TYPE_SELF_COLLECTION) {
                $user = $userService->get($userId);
                $data[$userId]['info']['address'] = $user->addresses;
            }
        }

        return $data;
    }

    private function isItemSelected($productId, $variantId, $selectedItems): bool
    {
        if (empty($selectedItems)) {
            return false;
        }

        foreach ($selectedItems as $selectedItem) {
            if (!is_array($selectedItem)) {
                continue;
            }

            $selectedProductId = $selectedItem['product_id'] ?? null;
            $selectedVariantId = $selectedItem['variant_id'] ?? null;

            if ($selectedProductId == $productId && $selectedVariantId == $variantId) {
                return true;
            }
        }

        return false;
    }

    public function getCart(): CartInterface
    {
        $cart = new Cart();

        $userId = Auth::user()->id;
        $dbCart = $this->cartRepository->findOrCreateCart($userId);

        $this->createFromDB($cart, $dbCart);

        $cart->setCartId($dbCart->id);

        return $cart;
    }

    protected function createFromDB(CartInterface $cart, $dbCart): void
    {
        $dbCart->items->each(function ($item) use ($cart) {
            $product = $this->productRepository->find($item->product_id);
            if (!$product) {
                // TODO: still add to cart session but mark as deleted
                return;
            }

            $cart->addItem($item->product, $item->quantity, $item->variation);
        });

    }

    public function addProduct($productId, $quantity, $productStockId = null): array
    {
        $result = [
            'status' => false,
            'message' => '',
        ];

        $product = $this->productService->get($productId);
        if (!$product) {
            $result['message'] = 'Product not found';
            return $result;
        }

        if ($productStockId) {
            $productStock = $this->productStockService->get($productStockId);
            if (!$productStock) {
                $result['message'] = 'Variant is not found';
                return $result;
            }
            $variantSku = $productStock->variant;
        } else {
            $variantSku = '';
        }

        $cart = $this->getCart();

        if (!$cart->isOverMinQty($product, $quantity, $variantSku)) {
            $result['message'] = 'This product is not over minimum quantity';
            return $result;
        }

        if (!$cart->isBelowMaxQty($product, $quantity, $variantSku)) {
            $result['message'] = 'This product is over maximum quantity';
            return $result;
        }

        if ($product->getStock($variantSku) == 0) {
            $result['message'] = 'This product is out of stock';
            return $result;
        }

        if (!$cart->isEnoughStock($product, $quantity, $variantSku)) {
            $result['message'] = 'You can only purchase up to ' . $product->getStock($variantSku) . ' quantities';
            return $result;
        }

        try {
            $item = $cart->addItem($product, $quantity, $variantSku);
            $result['status'] = true;
        } catch (\Exception $e) {
            $result['message'] = $e->getMessage();
        }

        return $result;
    }

    public function removeProduct($productId, $productStockId = null): array
    {
        $result = [
            'status' => false,
            'message' => '',
        ];

        $product = $this->productService->get($productId);
        if (!$product) {
            $result['message'] = 'Product not found';
            return $result;
        }

        if ($productStockId) {
            $productStock = $this->productStockService->get($productStockId);
            if (!$productStock) {
                $result['message'] = 'Product stock not found';
                return $result;
            }
            $itemOffset = $productStock->variant.$product->id;
        } else {
            $itemOffset = $product->id;
        }

        try {
            $cart = $this->getCart();
            $cart->removeItem($itemOffset);

            $result['status'] = true;
        } catch (\Exception $e) {
            // Handle exception if needed
        }

        return $result;
    }

    public function getProduct($productId)
    {
        return $this->productService->get($productId);
    }

    public function validateProductBeforeAddToCart(ProductInterface $product, int $quantity, string $variantSku, Cart $cart): array
    {
        $result = [
            'valid' => true,
            'message' => '',
        ];

        if (!$cart->isOverMinQty($product, $quantity, $variantSku)) {
            $result['valid'] = false;
            $result['message'] = 'This product requires a minimum order of ' . $product->min_qty;
            return $result;
        }

        if (!$cart->isBelowMaxQty($product, $quantity, $variantSku)) {
            $result['valid'] = false;
            $result['message'] = 'This product requires a maximum order of ' . $product->max_qty;
            return $result;
        }

        if ($product->getStock($variantSku) == 0) {
            $result['valid'] = false;
            $result['message'] = 'This product is out of stock';
            return $result;
        }

        if ($product->getStock($variantSku) < $quantity) {
            $result['valid'] = false;
            $result['message'] = 'Product ' . $product->name . ' can only purchase up to ' . $product->getStock($variantSku) . ' quantities';
            return $result;
        }

        return $result;
    }

    public function updateQuantity($productId, $quantity, $productStockId = null): array
    {
        $result = [
            'status' => false,
            'message' => '',
        ];

        $product = $this->productService->get($productId);
        if (!$product) {
            $result['message'] = 'Product not found';
            return $result;
        }

        if ($productStockId) {
            $productStock = $this->productStockService->get($productStockId);
            if (!$productStock) {
                $result['message'] = 'Variant not found';
                return $result;
            }
            $variantSku = $productStock->variant;
            $itemKey = $variantSku . $productId;
        } else {
            $variantSku = '';
            $itemKey = $productId;
        }

        $cart = $this->getCart();
        $cartItem = $cart->getItem($itemKey);
        
        if (!$cartItem) {
            $result['message'] = 'Item not found in cart';
            return $result;
        }

        if ($quantity == 0) {
            $cart->removeItem($itemKey);
            $result['status'] = true;
            $result['message'] = 'Item removed from cart';
            return $result;
        }

        $product_quantity = $product->current_stock;

        if (!empty($variantSku)) {
            $productStock = $this->productStockService->getFirstWithConditions([
                'product_id' => $productId, 
                'variant' => $variantSku
            ]);
            if ($productStock) {
                $product_quantity = $productStock->qty;
            }
        }

        if ($product_quantity == 0) {
            $cart->removeItem($itemKey);
            $result['message'] = 'Product ' . $product->name . ' in your cart is temporary out of stock and will be removed.';
            return $result;
        }

        if ($product_quantity < $quantity) {
            $result['message'] = 'You can only purchase up to ' . $product_quantity . ' quantities';
            return $result;
        }

        try {
            $cartItem->setQuantity($quantity);
            
            if ($cart->isSynced()) {
                $this->cartRepository->insertOrUpdateCartItem($cart->getCartId(), $cartItem);
            }
            
            $result['status'] = true;
        } catch (\Exception $e) {
            $result['message'] = $e->getMessage();
        }

        return $result;
    }

    public function buildTempCartFromRequest($selectedItems, $selectedVouchers = [], $selectedSelfCollectionAddresses = []): array
    {
        $result = [
            'success' => true,
            'message' => '',
        ];

        $cart = $this->webCartService->getCart();
        $tempCart = $this->webCartService->getTempCart();

        foreach ($selectedItems as $selectedItem) {
            $productId = $selectedItem['product_id'] ?? null;
            $variation = $selectedItem['variation'] ?? null;

            if ($productId) {
                $offsetName = ($variation ?: '') . $productId;
                $cartItem = $cart->getItem($offsetName);

                if ($cartItem) {
                    $product = $this->getProduct($productId);
                    if ($product) {
                        $quantity = $cartItem->getQuantity();
                        $variantSku = $cartItem->getVariation();

                        $validation = $this->validateProductBeforeAddToCart($product, $quantity, $variantSku, $cart);

                        if (!$validation['valid']) {
                            $result['success'] = false;
                            $result['message'] = $validation['message'];
                            return $result;
                        }
                    }
                }

                if (method_exists($tempCart, 'syncItemFromCart')) {
                    $tempCart->syncItemFromCart($cart, $offsetName);
                }
            }
        }

        $sellerCoupons = [];
        foreach ($selectedVouchers as $voucherCode) {
            $coupon = $this->couponService->getFirstWithConditions(['code' => $voucherCode, 'is_active' => Coupon::STATUS_ACTIVE]);

            if ($coupon === null) {
                $result['success'] = false;
                $result['message'] = 'The coupon code ' . $voucherCode . ' is invalid!';
                return $result;
            }

            if ($coupon->isExpired()) {
                $result['success'] = false;
                $result['message'] = 'The coupon code ' . $voucherCode . ' has expired.';
                return $result;
            }

            if ($coupon->inValid()) {
                $result['success'] = false;
                $result['message'] = 'The coupon code ' . $voucherCode . ' is not valid yet.';
                return $result;
            }

            if ($coupon->owner === Coupon::OWNER_SELLER && $coupon->seller_id) {
                if (isset($sellerCoupons[$coupon->seller_id])) {
                    $result['success'] = false;
                    $result['message'] = 'Only one coupon per seller is allowed. You have selected multiple coupons from the same seller.';
                    return $result;
                }
                $sellerCoupons[$coupon->seller_id] = $voucherCode;
            }

            $couponModel = new CouponModel($coupon);
            $validation = $tempCart->validateCoupon($couponModel);

            if ($validation['valid'] === false) {
                $result['success'] = false;
                $result['message'] = $validation['message'];
                return $result;
            }

            $tempCart->addCoupon($couponModel);
        }

        if (!empty($selectedSelfCollectionAddresses)) {
            foreach ($selectedSelfCollectionAddresses as $sellerId => $pickupPointId) {
                $userService = app()->make(UserService::class);
                $user = $userService->get($sellerId);
                $shopId = $user->shop->id;

                $addressService = app()->make(AddressService::class);
                $address = $addressService->get($pickupPointId);

                $tempCart->setShopOrderType($shopId, Shop::ORDER_TYPE_SELF_COLLECTION);
                $tempCart->setShopAddress($shopId, $address->fullAddress());
                $tempCart->setShopAddressTitle($shopId, $address->title);
                $tempCart->setDefaultIdAddress($shopId, $address->id);
            }
        }

        $this->webCartService->updateTempCart($tempCart);

        return $result;
    }

}
