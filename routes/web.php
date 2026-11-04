<?php
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::domain('seller.' . env('APP_DOMAIN'))->group(function () {
    Route::get('{any}', function () {
        return view('seller_center.index');
    })->where('any', '.*');

//    Route::any('{all}', function () {
//        return view('seller_center.index');
//    })->where('all', '^(?!api).*$');

    // .(?:jpg|png|gif)$
});
// Route::get('/read-file-php', 'Admin\TempController@createEnJsonFile')->name('read-file');

Auth::routes(['verify' => true]);
Route::get('/logout', '\App\Http\Controllers\Auth\LoginController@logout')->name('system.logout');
Route::get('/email/resend', 'Auth\VerificationController@resend')->name('verification.resend');
Route::get('/verification-confirmation/{code}', 'Auth\VerificationController@verification_confirmation')->name('email.verification.confirmation');
Route::get('/mail_callback', 'HomeController@mail_callback')->name('mail_callback');
Route::post('/language', 'LanguageController@changeLanguage')->name('language.change');
Route::post('/currency', 'CurrencyController@changeCurrency')->name('currency.change');
Route::get('/social-login/redirect/{provider}', 'Auth\LoginController@redirectToProvider')->name('social.login');
Route::get('/social-login/{provider}/callback', 'Auth\LoginController@handleProviderCallback')->name('social.callback');
Route::get('/users/login', 'HomeController@login')->name('user.login');
Route::get('/users/registration', 'HomeController@registration')->name('user.registration');
//Route::post('/users/login', 'HomeController@user_login')->name('user.login.submit');
Route::post('/users/login/cart', 'HomeController@cart_login')->name('cart.login.submit');
Route::post('/subcategories/get_subcategories_by_category', 'SubCategoryController@get_subcategories_by_category')->name('subcategories.get_subcategories_by_category');
Route::post('/subsubcategories/get_subsubcategories_by_subcategory', 'SubSubCategoryController@get_subsubcategories_by_subcategory')->name('subsubcategories.get_subsubcategories_by_subcategory');
Route::post('/subsubcategories/get_brands_by_subsubcategory', 'SubSubCategoryController@get_brands_by_subsubcategory')->name('subsubcategories.get_brands_by_subsubcategory');
Route::post('/subsubcategories/get_attributes_by_subsubcategory', 'SubSubCategoryController@get_attributes_by_subsubcategory')->name('subsubcategories.get_attributes_by_subsubcategory');
// YSG Page
Route::group(['middleware' => ['index', 'unbanned', 'force-change-password']], function() {
    Route::get('/', 'HomeController@index')->name('home');
    Route::get('/cart', 'CartController@index')->name('cart');

    Route::resource('shops', 'ShopController');
    Route::get('/track_your_order', 'HomeController@trackOrder')->name('orders.track');

    //MicroSite
    Route::get('/shop/{slug}', 'Frontend\MicrositeController@index')->name('microsite.index');
    Route::get('/shop/{slug}/{page}', 'Frontend\MicrositeController@page')->name('microsite.page');

    Route::get('/css-products', 'Frontend\CSSProductController@index')->name('products.css');


    Route::get('/product/{slug}/access-form', 'Frontend\SellerProductController@AccessCodeForm')->name('product.access-form');
    Route::group(['middleware' => ['shop-access']], function() {
        Route::get('/product/{slug}', 'Frontend\SellerProductController@product')->name('product');
    });
    Route::post('/product/check-stock', 'Frontend\SellerProductController@checkStock')->name('product.check_stock');

    Route::get('/search?category={category_slug}', 'HomeController@search')->name('products.category');
    Route::get('/search?subcategory={subcategory_slug}', 'HomeController@search')->name('products.subcategory');
    Route::get('/search?subsubcategory={subsubcategory_slug}', 'HomeController@search')->name('products.subsubcategory');
    Route::get('/search', 'HomeController@search')->name('search');

    // category

    Route::get('category/{category_slug}/{subcategory_slug?}/{subsubcategory_slug?}', 'HomeCategoryController@viewCategory')->name('category.view');
    // Route::get('category/{category_slug}', 'HomeCategoryController@viewCategory')->name('category.view');
    // Route::get('subcategory/{subcategory_slug}', 'HomeCategoryController@viewCategory')->name('subcategory.view');
    // Route::get('subsubcategory/{subsubcategory_slug}', 'HomeCategoryController@viewCategory')->name('subsubcategory.view');
});

// Microsite
Route::group(['middleware' => 'microsite', 'prefix' => 'shop/{seller_slug}/'], function() {
    Route::get('product/{slug}', 'HomeController@micrositeProduct')->name('microsite.product');
    Route::get('/search?category={category_slug}', 'HomeController@micrositeSearch')->name('microsite.products.category');
    Route::get('/search?subcategory={subcategory_slug}', 'HomeController@micrositeSearch')->name('microsite.products.subcategory');
    Route::get('/search?subsubcategory={subsubcategory_slug}', 'HomeController@micrositeSearch')->name('microsite.products.subsubcategory');
    Route::get('/search', 'HomeController@micrositeSearch')->name('microsite.search');

});

Route::post('/home/section/featured', 'HomeController@load_featured_section')->name('home.section.featured');
Route::post('/home/section/best_selling', 'HomeController@load_best_selling_section')->name('home.section.best_selling');
Route::post('/home/section/home_categories', 'HomeController@load_home_categories_section')->name('home.section.home_categories');
Route::post('/home/section/best_sellers', 'HomeController@load_best_sellers_section')->name('home.section.best_sellers');
//category dropdown menu ajax call
Route::post('/category/nav-element-list', 'HomeController@get_category_items')->name('category.elements');
//Flash Deal Details Page
Route::get('/flash-deal/{slug}', 'HomeController@flash_deal_details')->name('flash-deal-details');
Route::get('/sitemap.xml', function() {
    return base_path('sitemap.xml');
});
Route::get('/customer-products', 'CustomerProductController@customer_products_listing')->name('customer.products');
Route::get('/customer-products?subsubcategory={subsubcategory_slug}', 'CustomerProductController@search')->name('customer_products.subsubcategory');
Route::get('/customer-products?subcategory={subcategory_slug}', 'CustomerProductController@search')->name('customer_products.subcategory');
Route::get('/customer-products?category={category_slug}', 'CustomerProductController@search')->name('customer_products.category');
Route::get('/customer-products?city={city_id}', 'CustomerProductController@search')->name('customer_products.city');
Route::get('/customer-products?q={search}', 'CustomerProductController@search')->name('customer_products.search');
Route::get('/customer-product/{slug}', 'CustomerProductController@customer_product')->name('customer.product');
Route::get('/customer-packages', 'HomeController@premium_package_index')->name('customer_packages_list_show');

Route::get('/products', 'HomeController@listing')->name('products');
Route::post('/product/variant_price', 'HomeController@variant_price')->name('products.variant_price');

//Product list of Shop
Route::get('/shops/visit/{slug}/access-form', 'Frontend\SellerShopController@AccessCodeForm')->name('shop.visit.access-form');
Route::get('/shops/visit/{slug}/{type}/access-form', 'Frontend\SellerShopController@AccessCodeForm')->name('shop.visit.type.access-form');
Route::group(['middleware' => ['shop-access', 'unbanned', 'force-change-password']], function() {
    Route::get('/shops/visit/{slug}', 'Frontend\SellerShopController@visit')->name('shop.visit');
    Route::get('/shops/visit/{slug}/{type}', 'Frontend\SellerShopController@visit')->name('shop.visit.type');
});

Route::get('/be-a-seller', 'ShopController@create')->name('be.a.seller');

// Cart Routes
Route::group(['prefix' => 'cart'], function() {
    Route::post('/nav-cart-items', 'CartController@updateNavCart')->name('cart.nav_cart');
    Route::post('/show-cart-modal', 'CartController@showCartModal')->name('cart.showCartModal');
    Route::post('/addtocart', 'CartController@addToCart')->name('cart.addToCart');
    Route::post('/removeFromCart', 'CartController@removeFromCart')->name('cart.removeFromCart');
    Route::post('/updateQuantity', 'CartController@updateQuantity')->name('cart.updateQuantity');
    Route::get('/loginForCart', 'CartController@loginForCart')->name('cart.loginForCart');
    Route::post('/remove-coupon-form-cart', 'CartController@removeCouponFormCart')->name('cart.remove_coupon_form_cart');
    Route::post('/update-view-cart', 'CartController@updateViewCart')->name('cart.update_view_cart');

    Route::post('/pickFromCart', 'CartController@pickFromCart')->name('cart.pickFromCart');
    Route::post('/unPickFromCart', 'CartController@unPickFromCart')->name('cart.unPickFromCart');
    Route::post('/toggle-select-all-from-cart', 'CartController@toggleSelectAllFromCart')->name('cart.select_all_from_cart');

    //Route::post('user/login', 'CartController@loginForCart')->name('cart.user.login');
});

//Checkout Routes
Route::group(['middleware' => ['checkout']], function() {
    // Route::get('/checkout', 'CheckoutController@get_shipping_info')->name('checkout.shipping_info');
    // Route::any('/checkout/delivery_info', 'CheckoutController@store_delivery_info')->name('checkout.store_shipping_infostore');
    Route::post('/checkout/store_shipping_infostore', 'CheckoutController@store_shipping_info')->name('checkout.store_shipping_infostore');
    Route::get('/checkout/delivery_info', 'CheckoutController@deliveryInfo')->name('checkout.delivery_info');
    Route::post('/checkout/store_delivery_info', 'CheckoutController@store_delivery_info')->name('checkout.store_delivery_info');
});
Route::get('/checkout/order-confirmed', 'CheckoutController@order_confirmed')->name('order_confirmed');

Route::get('/checkout/payment', 'CheckoutController@checkout')->name('payment.checkout');

Route::post('/checkout/payment', 'CheckoutController@checkout')->name('payment.checkout');

Route::post('/get_pick_ip_points', 'HomeController@get_pick_ip_points')->name('shipping_info.get_pick_ip_points');
Route::get('/checkout/payment_select', 'CheckoutController@get_payment_info')->name('checkout.payment_info');
Route::post('/checkout/apply_coupon_code', 'CheckoutController@apply_coupon_code')->name('checkout.apply_coupon_code');
Route::post('/checkout/remove_coupon_code', 'CheckoutController@remove_coupon_code')->name('checkout.remove_coupon_code');

// Payment Gateway Cybersource START
//Route::get('/payment/cybersource', 'CheckoutController@cybersource')->name('payment.cybersource');
Route::post('/payment/{paymentMethod}/callback', 'CheckoutController@successCallback')->name('payment.callback');
// Payment Gateway Octifi END

// Payment Gateway Octifi START
Route::get('/payment/{paymentMethod}/success', 'CheckoutController@successCallback')->name('payment.success');
Route::get('/payment/{paymentMethod}/failed', 'CheckoutController@failedCallback')->name('payment.failed');
// Payment Gateway Octifi END

// Test NETS Payment Gateway START
Route::get('/payment/nets/test', 'CheckoutController@checkoutNetsTest')->name('payment.nest.test');
// Test NETS Payment Gateway END

//Paypal START
// No use Paypal anymore
//Route::get('/paypal/payment/done', 'PaypalController@getDone')->name('payment.done');
//Route::get('/paypal/payment/cancel', 'PaypalController@getCancel')->name('payment.cancel');
//Paypal END
// SSLCOMMERZ Start
Route::get('/sslcommerz/pay', 'PublicSslCommerzPaymentController@index');
Route::POST('/sslcommerz/success', 'PublicSslCommerzPaymentController@success');
Route::POST('/sslcommerz/fail', 'PublicSslCommerzPaymentController@fail');
Route::POST('/sslcommerz/cancel', 'PublicSslCommerzPaymentController@cancel');
Route::POST('/sslcommerz/ipn', 'PublicSslCommerzPaymentController@ipn');
//SSLCOMMERZ END
//Stipe Start
Route::get('stripe', 'StripePaymentController@stripe');
Route::post('stripe', 'StripePaymentController@stripePost')->name('stripe.post');
//Stripe END
Route::get('/compare', 'CompareController@index')->name('compare');
Route::get('/compare/reset', 'CompareController@reset')->name('compare.reset');
Route::post('/compare/addToCompare', 'CompareController@addToCompare')->name('compare.addToCompare');
Route::resource('subscribers', 'SubscriberController');
Route::get('/brands', 'HomeController@all_brands')->name('brands.all');
Route::get('/categories', 'HomeController@all_categories')->name('categories.all');
Route::get('/search?q={search}', 'HomeController@search')->name('suggestion.search');
Route::post('/ajax-search', 'HomeController@ajax_search')->name('search.ajax');
Route::post('/config_content', 'HomeController@product_content')->name('configs.update_status');
Route::get('/sellerpolicy', 'HomeController@sellerpolicy')->name('sellerpolicy');
Route::get('/returnpolicy', 'HomeController@returnpolicy')->name('returnpolicy');
Route::get('/supportpolicy', 'HomeController@supportpolicy')->name('supportpolicy');
Route::get('/terms', 'HomeController@terms')->name('terms');
Route::get('/privacypolicy', 'HomeController@privacypolicy')->name('privacypolicy');
Route::get('/email-change-mobile', 'HomeController@email_change_callback_mobile')->name('user.email_change.callback.mobile');
Route::group(['middleware' => ['user', 'verified', 'unbanned']], function() {

    // Active Corporate Access for exist user
    Route::get('/corporate-access/active/{token}', 'Frontend\CorporateAccessController@activeToken')->name('corporateAccess.active.token');

    // Referral
    Route::get('/referral/access', 'Frontend\CorporateAccessController@access')->name('referral.access');


    Route::get('/home', 'HomeController@dashboard')->name('dashboard');
    Route::get('/dashboard', 'HomeController@dashboard')->name('dashboard');
    Route::get('/profile', 'HomeController@profile')->name('profile');
    Route::post('/new-user-verification', 'HomeController@new_verify')->name('user.new.verify');
    Route::post('/new-user-email', 'HomeController@update_email')->name('user.change.email');
    Route::get('/email-change', 'HomeController@email_change_callback')->name('user.email_change.callback');
    Route::post('/customer/update-profile', 'HomeController@customer_update_profile')->name('customer.profile.update');
    Route::delete('/customer/delete-account/{id}', 'HomeController@customer_delete_account')->name('customer.profile.destroy');
    Route::post('/seller/update-profile', 'HomeController@seller_update_profile')->name('seller.profile.update');
    Route::resource('purchase_history', 'PurchaseHistoryController');
    Route::post('/purchase_history/details', 'PurchaseHistoryController@purchase_history_details')->name('purchase_history.details');
    Route::get('/purchase_history/destroy/{id}', 'PurchaseHistoryController@destroy')->name('purchase_history.destroy');
    Route::resource('wishlists', 'WishlistController');
    Route::post('/wishlists/remove', 'WishlistController@remove')->name('wishlists.remove');
    Route::get('/wallet', 'WalletController@index')->name('wallet.index');
    Route::post('/recharge', 'WalletController@recharge')->name('wallet.recharge');
    Route::resource('support_ticket', 'SupportTicketController');
    Route::post('support_ticket/reply', 'SupportTicketController@seller_store')->name('support_ticket.seller_store');
    Route::post('/customer_packages/purchase', 'CustomerPackageController@purchase_package')->name('customer_packages.purchase');
    Route::resource('customer_products', 'CustomerProductController');
    Route::post('/customer_products/published', 'CustomerProductController@updatePublished')->name('customer_products.published');
    Route::post('/customer_products/status', 'CustomerProductController@updateStatus')->name('customer_products.update.status');
    Route::get('digital_purchase_history', 'PurchaseHistoryController@digital_index')->name('digital_purchase_history.index');
});
Route::get('/customer_products/destroy/{id}', 'CustomerProductController@destroy')->name('customer_products.destroy');
Route::group(['prefix' => 'seller', 'middleware' => ['seller', 'verified', 'user']], function() {
    Route::get('/products', 'HomeController@seller_product_list')->name('seller.products');
    Route::get('/product/upload', 'HomeController@show_product_upload_form')->name('seller.products.upload');
    Route::get('/product/{id}/edit', 'HomeController@show_product_edit_form')->name('seller.products.edit');
    Route::resource('payments', 'PaymentController');
    Route::get('/shop/apply_for_verification', 'ShopController@verify_form')->name('shop.verify');
    Route::post('/shop/apply_for_verification', 'ShopController@verify_form_store')->name('shop.verify.store');
    Route::get('/reviews', 'ReviewController@seller_reviews')->name('reviews.seller');
    //digital Product
    Route::get('/digitalproducts', 'HomeController@seller_digital_product_list')->name('seller.digitalproducts');
    Route::get('/digitalproducts/upload', 'HomeController@show_digital_product_upload_form')->name('seller.digitalproducts.upload');
    Route::get('/digitalproducts/{id}/edit', 'HomeController@show_digital_product_edit_form')->name('seller.digitalproducts.edit');

    Route::prefix('products')->group(function() {
        Route::prefix('{product}')->group(function() {
            Route::post('featured', 'ProductController@updateFeatured')->name('seller.products.featured');
        });
    });
});
Route::group(['middleware' => ['auth']], function() {
    Route::post('/products/store/', 'ProductController@store')->name('products.store');
    Route::post('/products/update/{id}', 'ProductController@update')->name('products.update');
    Route::get('/products/destroy/{id}', 'ProductController@destroy')->name('products.destroy');
    Route::get('/products/duplicate/{id}', 'ProductController@duplicate')->name('products.duplicate');
    Route::post('/products/sku_combination', 'ProductController@sku_combination')->name('products.sku_combination');
    Route::post('/products/sku_combination_edit', 'ProductController@sku_combination_edit')->name('products.sku_combination_edit');
//    Route::post('/products/featured', 'ProductController@updateFeatured')->name('products.featured');
    Route::prefix('products')->group(function() {
        Route::prefix('{product}')->group(function() {
            Route::post('featured', 'ProductController@updateFeatured')->name('admin.products.featured');
        });
    });

    Route::post('/products/published', 'ProductController@updatePublished')->name('products.published');
    Route::get('invoice/customer/{order_id}', 'InvoiceController@customer_invoice_download')->name('customer.invoice.download');
    Route::get('invoice/seller/{order_id}', 'InvoiceController@seller_invoice_download')->name('seller.invoice.download');
    Route::resource('orders', 'OrderController');
    Route::get('/orders/destroy/{id}', 'OrderController@destroy')->name('orders.destroy');
    Route::post('/orders/details', 'OrderController@order_details')->name('orders.details');
    Route::get('/orders/resync/{id}', 'OrderController@order_resync')->name('orders.resync');
    Route::post('/orders/update_delivery_status', 'OrderController@update_delivery_status')->name('orders.update_delivery_status');
    Route::post('/orders/update_delivery_status_details', 'OrderController@update_delivery_status_details')->name('orders.update_delivery_status_details');
    Route::post('/orders/update_payment_status', 'OrderController@update_payment_status')->name('orders.update_payment_status');
    Route::resource('/reviews', 'ReviewController');
    Route::resource('/withdraw_requests', 'SellerWithdrawRequestController');
    Route::get('/withdraw_requests_all', 'SellerWithdrawRequestController@request_index')->name('withdraw_requests_all');
    Route::post('/withdraw_request/payment_modal', 'SellerWithdrawRequestController@payment_modal')->name('withdraw_request.payment_modal');
    Route::post('/withdraw_request/message_modal', 'SellerWithdrawRequestController@message_modal')->name('withdraw_request.message_modal');
    Route::resource('conversations', 'ConversationController');
    Route::get('/conversations/destroy/{id}', 'ConversationController@destroy')->name('conversations.destroy');
    Route::post('conversations/refresh', 'ConversationController@refresh')->name('conversations.refresh');
    Route::resource('messages', 'MessageController');
    //Product Bulk Upload
    Route::get('/product-bulk-upload/index', 'ProductBulkUploadController@index')->name('product_bulk_upload.index');
    Route::post('/bulk-product-upload', 'ProductBulkUploadController@bulk_upload')->name('bulk_product_upload');
    Route::get('/product-csv-download/{type}', 'ProductBulkUploadController@import_product')->name('product_csv.download');
    Route::get('/vendor-product-csv-download/{id}', 'ProductBulkUploadController@import_vendor_product')->name('import_vendor_product.download');
    Route::group(['prefix' => 'bulk-upload/download'], function() {
        Route::get('/category', 'ProductBulkUploadController@pdf_download_category')->name('pdf.download_category');
        Route::get('/sub_category', 'ProductBulkUploadController@pdf_download_sub_category')->name('pdf.download_sub_category');
        Route::get('/sub_sub_category', 'ProductBulkUploadController@pdf_download_sub_sub_category')->name('pdf.download_sub_sub_category');
        Route::get('/brand', 'ProductBulkUploadController@pdf_download_brand')->name('pdf.download_brand');
        Route::get('/seller', 'ProductBulkUploadController@pdf_download_seller')->name('pdf.download_seller');
    });
    //Product Export
    Route::get('/product-bulk-export', 'ProductBulkUploadController@export')->name('product_bulk_export.index');
    Route::resource('digitalproducts', 'DigitalProductController');
    Route::get('/digitalproducts/destroy/{id}', 'DigitalProductController@destroy')->name('digitalproducts.destroy');
    Route::get('/digitalproducts/download/{id}', 'DigitalProductController@download')->name('digitalproducts.download');

    Route::get('/user/change-password', function () {
        return view('frontend.customer.change_password');
    })->name('user.change.password');

    Route::post('/user/update-password', 'HomeController@update_password')->name('user.update.password');
});

Route::get('/instamojo/payment/pay-success', 'InstamojoController@success')->name('instamojo.success');
Route::post('rozer/payment/pay-success', 'RazorpayController@payment')->name('payment.rozer');
//Route::get('/paystack/payment/callback', 'PaystackController@handleGatewayCallback');
Route::get('/vogue-pay', 'VoguePayController@showForm');
Route::get('/vogue-pay/success/{id}', 'VoguePayController@paymentSuccess');
Route::get('/vogue-pay/failure/{id}', 'VoguePayController@paymentFailure');

// address
Route::resource('addresses', 'AddressController');
Route::group(['prefix' => 'addresses'], function() {
    Route::get('/destroy/{id}', 'AddressController@destroy')->name('addresses.destroy');
    Route::get('/set_default/{id}/{userId?}', 'AddressController@set_default')->name('addresses.set_default');
    Route::post('/list_delivery_address', 'AddressController@listDeliveryAddresses')->name('address.list_delivery_address');
    Route::post('/add_delivery_address', 'AddressController@addDeliveryAddress')->name('address.add_delivery_address');
    Route::post('/select_delivery_address', 'AddressController@selectDeliveryAddress')->name('address.select_delivery_address');
});

//payhere below
Route::get('/payhere/checkout/testing', 'PayhereController@checkout_testing')->name('payhere.checkout.testing');
Route::get('/payhere/wallet/testing', 'PayhereController@wallet_testing')->name('payhere.checkout.testing');
Route::get('/payhere/customer_package/testing', 'PayhereController@customer_package_testing')->name('payhere.customer_package.testing');
Route::any('/payhere/checkout/notify', 'PayhereController@checkout_notify')->name('payhere.checkout.notify');
Route::any('/payhere/checkout/return', 'PayhereController@checkout_return')->name('payhere.checkout.return');
Route::any('/payhere/checkout/cancel', 'PayhereController@chekout_cancel')->name('payhere.checkout.cancel');
Route::any('/payhere/wallet/notify', 'PayhereController@wallet_notify')->name('payhere.wallet.notify');
Route::any('/payhere/wallet/return', 'PayhereController@wallet_return')->name('payhere.wallet.return');
Route::any('/payhere/wallet/cancel', 'PayhereController@wallet_cancel')->name('payhere.wallet.cancel');
Route::any('/payhere/seller_package_payment/notify', 'PayhereController@seller_package_notify')->name('payhere.seller_package_payment.notify');
Route::any('/payhere/seller_package_payment/return', 'PayhereController@seller_package_payment_return')->name('payhere.seller_package_payment.return');
Route::any('/payhere/seller_package_payment/cancel', 'PayhereController@seller_package_payment_cancel')->name('payhere.seller_package_payment.cancel');
Route::any('/payhere/customer_package_payment/notify', 'PayhereController@customer_package_notify')->name('payhere.customer_package_payment.notify');
Route::any('/payhere/customer_package_payment/return', 'PayhereController@customer_package_return')->name('payhere.customer_package_payment.return');
Route::any('/payhere/customer_package_payment/cancel', 'PayhereController@customer_package_cancel')->name('payhere.customer_package_payment.cancel');


Route::get('/changeLanguage/{code}', 'HomeController@changeLanguage')->name('changeLanguage');


Route::get("/get-cities/{country_id}", 'HomeController@getCities')->name('get-cities');


Route::get('user_otps/form_authentication_otp', 'UserOtpsController@formAuthenticationOtp')->name('user_otps.form_access_otp');
Route::post('user_otps/create_user_otp', 'UserOtpsController@createUserOtp')->name('user_otps.create_user_otp');
Route::post('user_otps/count_send_otp', 'UserOtpsController@countSendOtp')->name('user_otps.count_send_otp');
Route::post('user_otps/resend_otp', 'UserOtpsController@resendOtp')->name('user_otps.resend_otp');

Route::get('/age-restricted-settings/get-content', 'Admin\AgeRestrictedSettingsController@getContent')->name('age_restricted.get_content');
Route::get('/age-restricted-settings/get-form-confirm', 'Admin\AgeRestrictedSettingsController@getFormConfirmAgeRestricted')->name('age_restricted.get_form_confirm');
Route::post('/age-restricted-settings/confirm-age-restricted', 'Admin\AgeRestrictedSettingsController@ConfirmAgeRestricted')->name('age_restricted.confirm_age_restricted');
Route::post('/age-restricted-settings/sync-age-confirmation', 'Admin\AgeRestrictedSettingsController@syncAgeConfirmation')->name('age_restricted.sync_age_confirmation');

Route::get('/cart/summary', 'CartController@getSummaryBlock')->name('cart.summary');
Route::get('/cart/updateDeliveryInfo', 'CartController@updateDeliveryInfo')->name('cart.updateDeliveryInfo');

Route::get('/mobile-social-login/redirect/{provider}', 'Auth\LoginController@mobileGetSocialRedirect')->name('mobile.social.redirect');
Route::get('/mobile-social-login/{provider}/callback', 'Auth\LoginController@mobileGetSocialTokenCallback')->name('mobile.social.get-token');

Route::get('/{slug}', 'PageController@show_custom_page')->name('custom-pages.show_custom_page');