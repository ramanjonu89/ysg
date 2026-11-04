<?php
Route::domain('seller.' . env('APP_DOMAIN'))->group(function () {

    Route::prefix('v1/auth')->group(function() {
        Route::post('login', 'Api\SellerCenter\AuthController@login');
    });

    Route::middleware('auth:api')->group(function() {

        Route::prefix('v1')->group(function() {
            Route::put('coupons/{coupon}/switch', 'Api\SellerCenter\CouponController@toggleField');
            Route::apiResource('coupons', 'Api\SellerCenter\CouponController')->only(['index', 'store', 'update', 'show']);
            Route::get('products/select-options', 'Api\SellerCenter\ProductController@getSelectOptions');
            Route::get('products/for-voucher', 'Api\SellerCenter\ProductController@productsWithVariant');
            Route::get('products/allowed-attributes', 'Api\SellerCenter\ProductController@getAllowedAttributes');
            Route::get('products/{product}/allowed-attributes', 'Api\SellerCenter\ProductController@getProductAllowedAttributes');
            Route::post('products/import', 'Api\SellerCenter\ProductController@importByFile');
            Route::post('products/export', 'Api\SellerCenter\ProductController@exportExcel');
            Route::put('products/{product}/switch', 'Api\SellerCenter\ProductController@togglePublish');
            Route::put('products/{product}/update_product_price', 'Api\SellerCenter\ProductController@updateProductPrice');
            Route::put('products/{product}/update_product_stock', 'Api\SellerCenter\ProductController@updateProductStock');
            Route::put('products/update-sort', 'Api\SellerCenter\ProductController@updateSort');
            Route::put('products/{product}/update_product_discount', 'Api\SellerCenter\ProductController@updateProductDiscount');
            Route::apiResource('products', 'Api\SellerCenter\ProductController');
            Route::put('products/{product}/edit', 'Api\SellerCenter\ProductController@update');
//            Route::resource('products', 'Api\SellerCenter\ProductController');
            Route::get('/brands/by-seller', 'Api\SellerCenter\BrandController@getBySeller');
            Route::get('/brands/options', 'Api\SellerCenter\BrandController@getSelectOptions');
            Route::get('/categories/by-seller', 'Api\SellerCenter\CategoryController@getBySeller');
            Route::get('categories/tree', 'Api\SellerCenter\CategoryController@tree');

            Route::apiResource('orders', 'Api\SellerCenter\SplitOrderController')->only(['index', 'update', 'show']);
            Route::get('orders/{order}/items', 'Api\SellerCenter\SplitOrderController@items');
            Route::get('orders/{order}/invoice', 'Api\SellerCenter\SplitOrderController@invoice');
            Route::post('orders/export', 'Api\SellerCenter\OrderController@exportExcel');
        });

        Route::prefix('v1/reports')->group(function() {
            Route::get('/summary', 'Api\SellerCenter\ReportController@summary');
            Route::get('/business', 'Api\SellerCenter\ReportController@business');
            Route::post('/orders-chart-data', 'Api\SellerCenter\ReportController@ordersChartData');
        });


        Route::prefix('v1')->group(function() {
            Route::prefix('settings')->group(function() {

                Route::prefix('feature-products')->group(function() {
                    Route::get('', 'Api\SellerCenter\Setting\FeatureProductController@index');
                    Route::post('', 'Api\SellerCenter\Setting\FeatureProductController@store');
                    Route::post('search', 'Api\SellerCenter\Setting\FeatureProductController@search');
                    Route::delete('{product}', 'Api\SellerCenter\Setting\FeatureProductController@destroy');
                    Route::put('{product}/update-sort', 'Api\SellerCenter\Setting\FeatureProductController@updateSort');
                });

                Route::prefix('blocks')->group(function() {
                    Route::get('', 'Api\SellerCenter\Setting\BlockController@index');
                    Route::post('', 'Api\SellerCenter\Setting\BlockController@store');
                    Route::get('get-types', 'Api\SellerCenter\Setting\BlockController@getTypes');
                    Route::put('update-list-sort', 'Api\SellerCenter\Setting\BlockController@updateListSort');
                    Route::prefix('{block}')->group(function() {
                        Route::get('', 'Api\SellerCenter\Setting\BlockController@show');
                        Route::put('', 'Api\SellerCenter\Setting\BlockController@update');
                        Route::put('update-sort', 'Api\SellerCenter\Setting\BlockController@updateSort');
                        Route::delete('', 'Api\SellerCenter\Setting\BlockController@destroy');
                        Route::put('/switch', 'Api\SellerCenter\Setting\BlockController@togglePublish');
                        Route::prefix('items')->group(function() {
                            Route::get('', 'Api\SellerCenter\Setting\BlockController@items');
                            Route::post('', 'Api\SellerCenter\Setting\BlockController@storeItem');
                            Route::put('update-sort', 'Api\SellerCenter\Setting\BlockController@updateSortItem');
                            Route::prefix('{blockContent}')->group(function() {
                                Route::put('', 'Api\SellerCenter\Setting\BlockController@updateItem');
                                Route::delete('', 'Api\SellerCenter\Setting\BlockController@destroyItem');
                            });
                        });
                    });
                });
            });
        });
    });
});

Route::prefix('v1/auth')->group(function() {
    Route::post('login', 'Api\AuthController@login');
    Route::post('signup', 'Api\AuthController@signup');
    Route::post('social-login', 'Api\AuthController@socialLogin');
    Route::post('password/create', 'Api\PasswordResetController@create');
    Route::middleware('auth:api')->group(function() {
        Route::get('logout', 'Api\AuthController@logout');
        Route::get('user', 'Api\AuthController@user');
    });
});
Route::group(['prefix' => 'v1', 'middleware' => ['auth:api']], function() {
    // Share Corporate Access
    Route::post('corporate-access/send-mail', 'Api\CorporateAccessController@sendMail')->name('api.corporateAccess.send_mail');
    Route::post('corporate-access/{user}/activate/{access}', 'Api\CorporateAccessController@activateAccess')->name('api.corporateAccess.activate');

    // Share Normal Referral email
    Route::post('referral/send-mail', 'Api\ReferralController@sendMail')->name('api.referral.send_mail');
    Route::post('referral/send-code', 'Api\ReferralController@sendReferralCode')->name('api.referral.send_referral_code');
});
Route::prefix('v1')->group(function() {
    Route::apiResource('banners', 'Api\BannerController')->only('index');

//    Route::post('referral/activate-access/{user}/{access}', 'Api\ReferralController@activateAccess')->name('api.referral.access.activate');

    Route::get('brands/top', 'Api\BrandController@top');
    Route::apiResource('brands', 'Api\BrandController')->only('index');
    Route::apiResource('business-settings', 'Api\BusinessSettingController')->only('index');
    Route::get('categories/featured', 'Api\CategoryController@featured');
    Route::get('categories/home', 'Api\CategoryController@home');
    Route::apiResource('categories', 'Api\CategoryController')->only('index');
    Route::get('sub-categories/{id}', 'Api\SubCategoryController@index')->name('subCategories.index');
    Route::apiResource('colors', 'Api\ColorController')->only('index');
    Route::apiResource('currencies', 'Api\CurrencyController')->only('index');
    Route::apiResource('customers', 'Api\CustomerController')->only('show');
    Route::apiResource('general-settings', 'Api\GeneralSettingController')->only('index');
    Route::apiResource('home-categories', 'Api\HomeCategoryController')->only('index');
    Route::get('purchase-history/{id}', 'Api\PurchaseHistoryController@index')->middleware('auth:api');
    Route::get('purchase-history-details/{id}', 'Api\PurchaseHistoryDetailController@index')->name('purchaseHistory.details')->middleware('auth:api');
    Route::get('products/admin', 'Api\ProductController@admin');
    Route::get('products/seller', 'Api\ProductController@seller');
    Route::get('products/category/{id}', 'Api\ProductController@category')->name('api.products.category');
    Route::get('products/sub-category/{id}', 'Api\ProductController@subCategory')->name('products.subCategory');
    Route::get('products/sub-sub-category/{id}', 'Api\ProductController@subSubCategory')->name('products.subSubCategory');
    Route::get('products/brand/{id}', 'Api\ProductController@brand')->name('api.products.brand');
    Route::get('products/todays-deal', 'Api\ProductController@todaysDeal');
    Route::get('products/flash-deal', 'Api\ProductController@flashDeal');
    Route::get('products/featured', 'Api\ProductController@featured');
    Route::get('products/best-seller', 'Api\ProductController@bestSeller');
    Route::get('products/related/{id}', 'Api\ProductController@related')->name('products.related');
    Route::get('products/top-from-seller/{id}', 'Api\ProductController@topFromSeller')->name('products.topFromSeller');
    Route::get('products/search', 'Api\ProductController@search');
    Route::post('products/variant/price', 'Api\ProductController@variantPrice');
    Route::get('products/home', 'Api\ProductController@home');
    // Route::apiResource('products', 'Api\ProductController')->except(['store', 'update', 'destroy']);
    Route::get('carts/{id}', 'Api\CartController@index')->middleware('auth:api');
    Route::post('carts/add', 'Api\CartController@add')->middleware('auth:api');
    Route::post('carts/change-quantity', 'Api\CartController@changeQuantity')->middleware('auth:api');
    Route::apiResource('carts', 'Api\CartController')->only('destroy')->middleware('auth:api');
    Route::get('reviews/product/{id}', 'Api\ReviewController@index')->name('api.reviews.index');
    Route::get('shop/user/{id}', 'Api\ShopController@shopOfUser')->middleware('auth:api');
    Route::get('shops/details/{id}', 'Api\ShopController@info')->name('shops.info');
    Route::get('shops/products/all/{id}', 'Api\ShopController@allProducts')->name('shops.allProducts');
    Route::get('shops/products/top/{id}', 'Api\ShopController@topSellingProducts')->name('shops.topSellingProducts');
//    Route::get('shops/products/featured/{id}', 'Api\ShopController@featuredProducts')->name('shops.featuredProducts');
    Route::prefix('shops')->group(function() {
        Route::prefix('{shop}')->group(function() {
            Route::get('products/featured', 'Api\ShopController@featuredProducts')->name('shops.featuredProducts');
        });
    });

    Route::get('shops/products/new/{id}', 'Api\ShopController@newProducts')->name('shops.newProducts');
    Route::get('shops/brands/{id}', 'Api\ShopController@brands')->name('shops.brands');
    Route::apiResource('shops', 'Api\ShopController')->only('index');
    Route::apiResource('sliders', 'Api\SliderController')->only('index');
    Route::get('wishlists/{id}', 'Api\WishlistController@index')->middleware('auth:api');
    Route::post('wishlists/check-product', 'Api\WishlistController@isProductInWishlist')->middleware('auth:api');
    Route::apiResource('wishlists', 'Api\WishlistController')->except(['index', 'update', 'show'])->middleware('auth:api');
    Route::apiResource('settings', 'Api\SettingsController')->only('index');
    Route::get('policies/seller', 'Api\PolicyController@sellerPolicy')->name('policies.seller');
    Route::get('policies/support', 'Api\PolicyController@supportPolicy')->name('policies.support');
    Route::get('policies/return', 'Api\PolicyController@returnPolicy')->name('policies.return');
    Route::get('user/info/{id}', 'Api\UserController@info')->middleware('auth:api');
    Route::post('user/info/update', 'Api\UserController@updateName')->middleware('auth:api');
    // Route::get('user/shipping/address/{id}', 'Api\AddressController@addresses')->middleware('auth:api');
    // Route::post('user/shipping/create', 'Api\AddressController@createShippingAddress')->middleware('auth:api');
    // Route::get('user/shipping/delete/{id}', 'Api\AddressController@deleteShippingAddress')->middleware('auth:api');
    // Route::post('coupon/apply', 'Api\CouponController@apply')->middleware('auth:api');
    Route::post('payments/pay/stripe', 'Api\StripeController@processPayment')->middleware('auth:api');
    Route::post('payments/pay/paypal', 'Api\PaypalController@processPayment')->middleware('auth:api');
    // Route::post('payments/pay/wallet', 'Api\WalletController@processPayment')->middleware('auth:api');
    Route::post('payments/pay/cod', 'Api\PaymentController@cashOnDelivery')->middleware('auth:api');
    Route::post('order/store', 'Api\OrderController@store')->middleware('auth:api');
    // Route::get('wallet/balance/{id}', 'Api\WalletController@balance')->middleware('auth:api');
    // Route::get('wallet/history/{id}', 'Api\WalletController@walletRechargeHistory')->middleware('auth:api');
});
Route::post('oms/sync/delivery-status', 'Api\OrderController@syncOMSUpdateDeliveryStatus');
Route::post('v1/payment/nets/s2s', 'Api\PaymentController@eNetsSuccessCallback')->name('payment.nest.s2s');

// Mobile API Routes
Route::prefix('mobile/v1')->group(function() {
    Route::prefix('auth')->group(function() {
        Route::post('login', 'Api\Mobile\AuthController@login');
        Route::post('register', 'Api\Mobile\AuthController@register');
        Route::post('send-otp', 'Api\Mobile\AuthController@sendOtp');
        Route::post('resend-otp', 'Api\Mobile\AuthController@resendOtp');
        Route::post('social-login', 'Api\Mobile\AuthController@socialLogin');

        // Forgot Password
        Route::prefix('forgot-password')->group(function() {
            Route::post('send-otp', 'Api\Mobile\ForgotPasswordController@sendOtp');
            Route::post('verify-otp', 'Api\Mobile\ForgotPasswordController@verifyOtp');
            Route::post('reset-password', 'Api\Mobile\ForgotPasswordController@resetPassword');
        });

        Route::middleware('auth:api')->group(function() {
            Route::get('logout', 'Api\Mobile\AuthController@logout');
            Route::get('user', 'Api\Mobile\AuthController@user');

            Route::post('product/wishlist', 'Api\Mobile\WishlistController@store');
            Route::get('product/wishlists', 'Api\Mobile\WishlistController@getWishlistItems');

            Route::prefix('user')->group(function() {
                Route::post('password/verify', 'Api\Mobile\UserController@verifyPassword');
                Route::post('change-password', 'Api\Mobile\UserController@changePassword');
                Route::delete('destroy/{id}', 'Api\Mobile\UserController@destroy');
                Route::post('update-profile', 'Api\Mobile\UserController@updateProfile');
                Route::post('send-otp-email-update', 'Api\Mobile\UserController@sendOtpForEmailUpdate');
                Route::post('update-email', 'Api\Mobile\UserController@updateEmail');
                Route::get('avatars', 'Api\Mobile\UserController@getListAvatars');

                Route::post('corporate-access/send-mail', 'Api\Mobile\CorporateAccessController@sendMail');
                Route::post('referral/send-code', 'Api\Mobile\ReferralController@sendReferralCode');
                Route::get('referral/access', 'Api\Mobile\CorporateAccessController@access');
                Route::post('corporate-access/{user}/activate/{access}', 'Api\Mobile\CorporateAccessController@activateAccess');

                // address routes
                Route::get('addresses', 'Api\Mobile\AddressController@getAddresses');
                Route::prefix('address')->group(function() {
                    Route::post('/', 'Api\Mobile\AddressController@addAddress');
                    Route::post('{address}/set-default', 'Api\Mobile\AddressController@setDefault');
                    Route::post('{address}/update', 'Api\Mobile\AddressController@update');
                    Route::delete('{address}/destroy', 'Api\Mobile\AddressController@destroy');
                    Route::get('countries', 'Api\Mobile\AddressController@getCountries');
                    Route::get('cities/{country_id}', 'Api\Mobile\AddressController@getCities');
                });
            });

        });
    });

    Route::prefix('home')->group(function() {
        Route::get('popup-image', 'Api\Mobile\HomeController@popupImage');
        Route::get('slider-block', 'Api\Mobile\HomeController@slider_block');
        Route::get('shop-by-stores', 'Api\Mobile\HomeController@shopByStores');
        Route::get('best-selling', 'Api\Mobile\HomeController@bestSellingProducts');
        Route::get('featured', 'Api\Mobile\HomeController@getFeaturedProducts');
        Route::get('blocks', 'Api\Mobile\HomeController@getHomepageBlocks');
        Route::get('categories', 'Api\Mobile\HomeController@getHomeCategories');
    });

    Route::prefix('notifications')->group(function () {
        Route::post('token', 'Api\Mobile\NotificationController@storeToken');
        Route::delete('token', 'Api\Mobile\NotificationController@removeToken');
        Route::post('send-to-device', 'Api\Mobile\NotificationController@sendToDevice');

        Route::middleware('auth:api')->group(function () {
            Route::post('test', 'Api\Mobile\NotificationController@sendTest');
        });
    });

    Route::prefix('categories')->group(function() {
        Route::get('top', 'Api\Mobile\HomeController@getTopCategories');
    });

    Route::prefix('brands')->group(function() {
        Route::get('top', 'Api\Mobile\HomeController@getTopBrands');
    });

    Route::middleware('auth:api')->group(function() {
        Route::get('hello-world', 'Api\Mobile\HelloWorldController@index');
    });

    Route::prefix('brands')->group(function() {
        Route::get('', 'Api\Mobile\BrandController@index');
        Route::get('{id}', 'Api\Mobile\BrandController@get');
    });

    Route::prefix('categories')->group(function() {
        Route::get('', 'Api\Mobile\CategoryController@index');
        Route::get('{id}', 'Api\Mobile\CategoryController@get');
    });
    Route::prefix('products')->group(function () {
        Route::get('{id}', 'Api\Mobile\ProductController@get')
            ->middleware('mobile-shop-access');

        Route::get('{id}/reviews', 'Api\Mobile\ProductController@getReviews');
        Route::post('search', 'Api\Mobile\ProductController@search');
        Route::get('{product}/related-products', 'Api\Mobile\ProductController@getRelatedProducts');
        Route::get('{product}/top-selling', 'Api\Mobile\ProductController@getTopSellingProducts');
        Route::middleware('auth:api')->post('{id}/reviews', 'Api\Mobile\ReviewController@store');
    });

    Route::prefix('reviews')->group(function() {
        Route::get('', 'Api\Mobile\ReviewController@list');
    });

    Route::prefix('sellers')->group(function() {
        Route::get('', 'Api\Mobile\SellerController@index');
    });

    Route::group([
        'prefix' => 'seller',
        'middleware' => ['mobile-shop-access']
    ], function () {
        Route::get('{seller}/profile', 'Api\Mobile\SellerShopController@getStoreProfile');
        Route::get('{seller}/blocks', 'Api\Mobile\SellerShopController@getShopBlocks');
        Route::get('{seller}/featured-products', 'Api\Mobile\SellerShopController@getFeaturedProducts');
        Route::post('{seller}/products/search', 'Api\Mobile\SellerShopController@search');
        Route::get('{seller}/brands', 'Api\Mobile\SellerShopController@getBrandsByShop');
        Route::get('{seller}/categories', 'Api\Mobile\SellerShopController@getCategoriesByShop');
    });

    Route::group(['prefix' => 'cart', 'middleware' => ['auth:api']], function() {
        Route::post('', 'Api\Mobile\CartController@getCart');
        Route::post('add-item', 'Api\Mobile\CartController@addItem');
        Route::post('remove-item', 'Api\Mobile\CartController@removeItem');
        Route::post('update-quantity', 'Api\Mobile\CartController@updateQuantity');
    });

    Route::prefix('coupon')->group(function() {
        Route::get('{code}', 'Api\Mobile\CouponController@getByCode');
    });

    Route::prefix('payments')->group(function() {
        Route::get('', 'Api\Mobile\PaymentController@index');
    });

    Route::group(['middleware' => ['auth:api']], function() {
        Route::post('checkout', 'Api\Mobile\CheckoutController@checkout');
        Route::get('split-orders', 'Api\Mobile\SplitOrderController@index');
        Route::get('split-orders/details', 'Api\Mobile\SplitOrderController@purchaseHistoryDetails');
        Route::get('orders/{orderId}', 'Api\Mobile\OrderController@show');
        Route::get('checkout/{paymentType}/{orderId}', 'Api\Mobile\CheckoutController@paymentGateway');
    });

    Route::get('order/confirmed/{orderId}', 'Api\Mobile\CheckoutController@orderConfirmed')->name('api.mobile.order.confirmed');
    Route::get('order/failed/{orderId}', 'Api\Mobile\CheckoutController@orderFailed')->name('api.mobile.order.failed');

    Route::post('contact-form', 'Api\Mobile\ContactFormController@store');

    Route::get('policy', 'Api\Mobile\PolicyController@show');
    
    Route::get('custom-page', 'Api\Mobile\CustomPageController@getCustomPageBySlug');
    Route::get('static-page/faqs', 'Api\Mobile\CustomPageController@getFaqsPage');
    Route::get('static-page/term-conditions', 'Api\Mobile\CustomPageController@getTermConditionsPage');

    Route::get('track-your-orders', 'Api\Mobile\SplitOrderController@trackOrder');
    Route::get('age-restricted/get-content', 'Api\Mobile\AgeRestrictedSettingsController@getContent');

    Route::get('user/address/countries', 'Api\Mobile\AddressController@getCountries');

});

// Fallback Route for Invalid Routes
Route::fallback(function() {
    return response()->json([
        'data' => [],
        'success' => false,
        'status' => 404,
        'message' => 'Invalid Route'
    ]);
});