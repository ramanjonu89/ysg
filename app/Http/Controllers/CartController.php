<?php
namespace App\Http\Controllers;

use App\Models\{Address,
    AgeRestrictedSetting,
    ProductStock
};
use App\Services\{AddressService,
    CartService,
    CategoryService,
    CountryService,
    CouponService,
    ProductService,
    ProductStockService,
    ShopService,
};
use Auth;
use Illuminate\Http\Request;
use Session;

class CartController extends Controller
{
    /**
     * @var AddressService
     */
    protected $addressService;

    /**
     * @var CartService
     */
    protected $cartService;

    /**
     * @var CategoryService
     */
    protected $categoryService;

    /**
     * @var CountryService
     */
    protected $countryService;


    /**
     * @var CouponService
     */
    protected $couponService;

    /**
     * @var ProductService
     */
    protected $productService;

    /**
     * @var ProductStockServivce
     */
    protected $productStockService;

    /**
     * @var ShopService
     */
    protected $shopService;

    public function __construct(
        AddressService $addressService,
        CartService    $cartService,
        CategoryService $categoryService,
        CountryService $countryService,
        CouponService  $couponService,
        ProductService $productService,
        ShopService    $shopService,
        ProductStockService $productStockService
    ) {
        $this->addressService = $addressService;
        $this->cartService    = $cartService;
        $this->categoryService = $categoryService;
        $this->countryService = $countryService;
        $this->couponService  = $couponService;
        $this->productService = $productService;
        $this->shopService    = $shopService;
        $this->productStockService = $productStockService;
    }


    public function loginForCart()
    {
        if (Auth::check()) {
            return redirect()->route('cart');
        }

        $link = session('previous_url');
        if ($link == null) {
            session(['previous_url' => url()->previous()]);
        }

        return view('frontend.login_for_cart');
    }


    public function index(Request $request)
    {
        $address = $request->session()->get('address');
        if (!Auth::check()) {
            return redirect()->route('cart.loginForCart');
        }

        $cart       = $this->cartService->getCart();
        $tempCart = $this->cartService->getTempCart();

        $countries  = $this->countryService->getList();
        $coupons    = $this->couponService->getExtractUsableCouponBySeller();
        $couponsYsg = $this->couponService->getExtractUsableCouponByAdmin();
        if (empty($address)) {
            $address    = $this->addressService->getFirstWithConditions(['user_id' => Auth::user()->id, 'set_default' => Address::SET_DEFAULT_ADDRESS]);
        }
        Session::put('countries', $countries);
        Session::put('address', $address);
        return view('frontend.view_cart', compact('address', 'cart', 'tempCart', 'coupons', 'couponsYsg'));
    }

    public function showCartModal(Request $request)
    {
        $product  = $this->productService->findBy('id', $request->get('id'));
        $category = $this->categoryService->findBy('id', $product->category_id);
        $isAgeRestricted = $category->is_age_restricted ?? 0;
        $isAdultImage = $isAgeRestricted === AgeRestrictedSetting::TWENTYONE_AND_ABOVE
            && session('is_confirm_age') !== AgeRestrictedSetting::TWENTYONE_AND_ABOVE;

        return view('frontend.partials.addToCart', compact('product', 'isAgeRestricted', 'isAdultImage'));
    }

    public function updateNavCart(Request $request)
    {
        $cart = $this->cartService->getCart();

        return view('frontend.partials.cart', compact('cart'));
    }

    public function addToCart(Request $request)
    {
        $variantSku = '';
        $product = $this->productService->get($request->get('id'));
        $cart = $this->cartService->getCart();

        if ($this->productService->hasVariants($product)) {
            $variantOptions = [];
            foreach ($product->variants as $variant) {
                $requestName = 'attribute_id_' . $variant->attribute_id;
                $variantOptions[] = $request->get($requestName);
            }
            $variantSku = count($variantOptions) > 0 ? implode(ProductStock::SPLIT_SYMBOL, $variantOptions) : '';
        }

        $productQuantity = $request->get('quantity', 0);

        if (!$cart->isOverMinQty($product, $productQuantity, $variantSku)) {
            return view('frontend.partials.minQtyNotSatisfied', [
                'min_qty' => $product->min_qty
            ]);
        }

        if (!$cart->isBelowMaxQty($product, $productQuantity, $variantSku)) {
            return view('frontend.partials.maxQtyNotSatisfied', [
                'max_qty' => $product->max_qty
            ]);
        }

        if (!$cart->isEnoughStock($product, $productQuantity, $variantSku)) {
            return view('frontend.partials.outOfStockCart');
        }

        $item = $cart->addItem($product, $productQuantity, $variantSku);

        $tempCart = $this->cartService->getTempCart();
        $tempCart->syncItemQty($item);

        $qty = $request->quantity;

        if ($product->isProductForAdults()) {
            Session::put(['is_confirm_age' => AgeRestrictedSetting::TWENTYONE_AND_ABOVE]);
        }

        return view('frontend.partials.addedToCart', compact('item', 'qty'));
    }

    public function pickFromCart(Request $request)
    {
        $offsetName = $request->get('item_offset', null);
        $orderType  = $request->get('order_type', null);
        $shippingAddress = $request->get('shipping_address', null);
        if ($offsetName === null) {
            flash(__('Please select a product to add to cart.'))->warning();
            return;
        }
        $this->cartService->addTempCartItem($offsetName, $orderType, $shippingAddress);

    }

    public function unPickFromCart(Request $request)
    {
        $offsetName = $request->get('item_offset', null);
        if ($offsetName === null) {
            flash(__('Please select a product to add to cart.'))->warning();
            return;
        }
        $this->cartService->removeTempCartItem($offsetName);
    }

    public function toggleSelectAllFromCart(Request $request)
    {
        $status          = $request->get('status', '');
        $sellerId        = $request->get('seller_id', '');
        $orderTypeSeller = $request->get('order_type_seller', []);
        $shippingAddress = $request->get('shipping_address', []);

        $this->cartService->toggleSelectAllFromCart($status, $sellerId, $orderTypeSeller, $shippingAddress);
    }

    //removes from Cart
    public function removeFromCart(Request $request)
    {
        try {
            $key  = $request->get('key', '');
            $this->cartService->removeCartItem($key);
            $cart = $this->cartService->getCart();

        } catch (\Exception $e) {
            //TODO: YSG-163: log error
        }

        return response()->json([
            'success' => true,
            'cartCount' => $cart->getTotalItems()
        ]);
    }

    //updated the quantity for a cart item
    public function updateQuantity(Request $request)
    {
        $itemKey = $request->get('key');
        $cart = $this->cartService->getCart();
        $tempCart = $this->cartService->getTempCart();
        $cartItem = $cart->getItem($itemKey);

        $product = $this->productService->findBy('id', $cartItem->getId());
        $product_quantity = $product->current_stock;

        if (!empty($cartItem->getVariation())) {
            $productStock = $this->productStockService->getFirstWithConditions(['product_id' => $cartItem->getId(), 'variant' => $cartItem->getVariation()]);
            $product_quantity = $productStock->qty;
        }

        $valid = true;
        $request_quantity = $request->get('quantity');

        if ($product_quantity == 0) {
            flash(__('One or more of the products in your cart is temporary out of stock and will be removed.'))->warning();
            $cart->removeItem($itemKey);
            $tempCart->removeItem($itemKey);
            $valid = false;
        }

        // if ($product_quantity < $request_quantity) {
        //     $request_quantity = $product_quantity;
        //     flash(__('One or more of the products in your cart is currently low in stock and your order has been adjusted.'))->warning();
        //     $valid = false;
        // }

        // if (!$cart->isOverMinQty($product, $request_quantity, $itemKey, $updateCart = true)) {
        //     flash(__("The product requires a minimum order of $product->min_qty. Please update your cart."))->warning();
        //     $valid = false;
        // }

        // if (!$cart->isBelowMaxQty($product, $request_quantity, $itemKey, $updateCart = true)) {
        //     flash(__("The product requires a maximum order of $product->max_qty. Please update your cart."))->warning();
        //     $valid = false;
        // }

        if ($valid) {
            $cartItem->setQuantity($request_quantity);
            if ($cart->isSynced()) {
                $cartRepository = app()->make(\App\Repositories\CartRepository::class);
                $cartRepository->insertOrUpdateCartItem($cart->getCartId(), $cartItem);
            }
            $cart->removeAllCoupons();
        }

        /*$cart = $request->session()->get('cart', collect([]));
        $cart = $cart->map(function($object, $key) use ($request) {
            if ($key == $request->key) {
                $product = \App\Product::find($object['id']);
                if ($object['variant'] != null && $product->variant_product) {
                    $product_stock = $product->stocks->where('variant', $object['variant'])->first();
                    $quantity = $product_stock->qty;
                    if ($quantity >= $request->quantity) {
                        if ($request->quantity >= $product->min_qty) {
                            $object['quantity'] = $request->quantity;
                        }
                    }
                } elseif ($request->quantity >= $product->min_qty) {
                    $object['quantity'] = $request->quantity;
                }
            }

            return $object;
        });
        $request->session()->put('cart', $cart);
        $request->session()->forget('coupon_id');
        $request->session()->forget('coupon_discount');

        $cart = $this->cartService->getCart();*/


        // $tempCart->syncItemFromCart($cart, $itemKey);
        // $this->cartService->updateTempCart($tempCart);

        $coupons    = $this->couponService->getExtractUsableCouponBySeller();
        $couponsYsg = $this->couponService->getExtractUsableCouponByAdmin();

        $detailsView = view('frontend.partials.cart_details', compact('cart', 'tempCart', 'coupons', 'couponsYsg'))->render();
        $summaryView = view('frontend.partials.cart_summary', compact('cart', 'tempCart', 'couponsYsg'))->render();

        return array(
            'details' => $detailsView,
            'summary' => $summaryView,
        );
    }

    public function removeCouponFormCart(Request $request) 
    {
        $itemKey = $request->get('key');
        $cart = $this->cartService->getCart();
        $tempCart = $this->cartService->getTempCart();
        $coupons = $tempCart->getCoupons();
        $tempCart->removeCoupon($coupons[$itemKey]);
        $coupons    = $this->couponService->getExtractUsableCouponBySeller();
        $couponsYsg = $this->couponService->getExtractUsableCouponByAdmin();

        $detailsView = view('frontend.partials.cart_details', compact('cart', 'tempCart', 'coupons', 'couponsYsg'))->render();
        $summaryView = view('frontend.partials.cart_summary', compact('cart', 'tempCart', 'couponsYsg'))->render();

        return array(
            'details' => $detailsView,
            'summary' => $summaryView,
        );
    }

    public function updateViewCart(Request $request)
    {
        $cart = $this->cartService->getCart();
        $tempCart = $this->cartService->getTempCart();
        $coupons    = $this->couponService->getExtractUsableCouponBySeller();
        $couponsYsg = $this->couponService->getExtractUsableCouponByAdmin();

        $detailsView = view('frontend.partials.cart_details', compact('cart', 'tempCart', 'coupons', 'couponsYsg'))->render();
        $summaryView = view('frontend.partials.cart_summary', compact('cart', 'tempCart', 'couponsYsg'))->render();

        return array(
            'details' => $detailsView,
            'summary' => $summaryView,
        );
    }

    public function getSummaryBlock()
    {
        $cart = $this->cartService->getCart();
        $tempCart = $this->cartService->getTempCart();
        $couponsYsg = $this->couponService->getExtractUsableCouponByAdmin();
        return view('frontend.partials.cart_summary', compact('cart', 'tempCart', 'couponsYsg'));
    }

    public function updateDeliveryInfo()
    {
        try {
            $tempCart = $this->cartService->getTempCart();
            $tempCart->updateDeliveryInfo();
        } catch (\Exception $e) {
            //throw $th;
        }

        return 'OK';
    }
}

