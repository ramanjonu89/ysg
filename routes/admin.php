<?php
/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Here is where you can register admin routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
use Illuminate\Support\Facades\Route;

Route::get('/tifpwysne', 'HomeController@admin_dashboard')->name('admin.dashboard')->middleware(['auth', 'admin']);
Route::group(['prefix' => 'tifpwysne', 'middleware' => ['auth', 'admin']], function() {

    Route::get('cronjob/{user}', 'Admin\CronjobController@hourly')->name('admin.cron.hourly');

    //Custom Pages
    Route::group(['prefix' => 'custom_page'], function() {
        Route::group(['prefix' => 'page'], function() {
            Route::get('', 'Admin\CustomPage\PageController@index')->name('admin.custom_page.page.index');
            Route::post('store', 'Admin\CustomPage\PageController@store')->name('admin.custom_page.page.store');
            Route::group(['prefix' => '{id}'], function() {
                Route::get('edit', 'Admin\CustomPage\PageController@edit')->name('admin.custom_page.page.edit');
                Route::post('update', 'Admin\CustomPage\PageController@update')->name('admin.custom_page.page.update');
                Route::get('destroy', 'Admin\CustomPage\PageController@destroy')->name('admin.custom_page.page.destroy');
                Route::group(['prefix' => 'block'], function() {
                    Route::get('', 'Admin\Api\CustomPage\PageController@blockList')->name('admin.api.custom_page.page.block');
                    Route::post('add', 'Admin\Api\CustomPage\PageController@addBlock')->name('admin.api.custom_page.page.block.add');
                    Route::delete('remove', 'Admin\Api\CustomPage\PageController@removeBlock')->name('admin.api.custom_page.page.block.remove');
                    Route::post('update_index', 'Admin\Api\CustomPage\PageController@updateBlockIndex')->name('admin.api.custom_page.page.block.update_index');
                });
            });
        });
        Route::group(['prefix' => 'block'], function() {
            Route::get('', 'Admin\CustomPage\BlockController@index')->name('admin.custom_page.block.index');
            Route::post('store', 'Admin\CustomPage\BlockController@store')->name('admin.custom_page.block.store');
            Route::post('change-type', 'Admin\CustomPage\BlockController@changeType')->name('admin.custom_page.block.change-type');
            Route::group(['prefix' => '{block}'], function() {
                Route::get('edit', 'Admin\CustomPage\BlockController@edit')->name('admin.custom_page.block.edit');
                Route::post('update', 'Admin\CustomPage\BlockController@update')->name('admin.custom_page.block.update');
                Route::get('destroy', 'Admin\CustomPage\BlockController@destroy')->name('admin.custom_page.block.destroy');
                Route::group(['prefix' => 'item'], function() {
                    Route::get('create', 'Admin\CustomPage\BlockContentController@create')->name('admin.custom_page.block.item.create');
                    Route::post('store', 'Admin\CustomPage\BlockContentController@store')->name('admin.custom_page.block.item.store');
                    Route::post('update_index', 'Admin\Api\CustomPage\BlockController@updateContentIndex')->name('admin.api.custom_page.block.item.update_index');
                    Route::post('add_product', 'Admin\Api\CustomPage\BlockContentController@addProduct')->name('admin.api.custom_page.block.item.add_product');
                    Route::group(['prefix' => '{content}'], function() {
                        Route::get('edit', 'Admin\CustomPage\BlockContentController@edit')->name('admin.custom_page.block.item.edit');
                        Route::post('update', 'Admin\CustomPage\BlockContentController@update')->name('admin.custom_page.block.item.update');
                        Route::get('destroy', 'Admin\CustomPage\BlockContentController@destroy')->name('admin.custom_page.block.item.destroy');
                    });
                });
            });
        });
    });

    // Seller list
    Route::group(['prefix' => 'sellers'], function() {
        // web
        Route::resource('microsites', 'Admin\MicrositeController', ['names' => 'admin.microsites'])->except(['delete', 'create', 'show']);
//        Route::get('/microsites', 'Admin\MicrositeController@index')->name('admin.microsite.index');
//        Route::get('/microsites/{microsite}', 'Admin\MicrositeController@edit')->name('admin.microsite.edit');
//        Route::post('/microsites', 'Admin\MicrositeController@store')->name('admin.microsite.store');
//        Route::put('/microsites/{microsite}', 'Admin\MicrositeController@update')->name('admin.microsite.update');
        Route::get('/microsites/destroy/{id}', 'Admin\MicrositeController@destroy')->name('admin.microsite.destroy');
        // api
        Route::post('/microsites/toggle-active', 'Admin\Api\MicrositeController@toggleActive')->name('admin.api.microsites.toggle_active');
        Route::post('/update_index', 'Admin\Api\SellerListController@updateIndex')->name('admin.api.sellers.update_index');
        Route::post('/toggle_display', 'Admin\Api\SellerListController@toggleDisplay')->name('admin.api.sellers.toggle_display');
        Route::group(['prefix' => 'microsites/{id}/block'], function () {
            Route::post('add',
                'Admin\Api\MicrositeController@addBlock')->name('admin.api.microsites.block.add');
            Route::delete('remove',
                'Admin\Api\MicrositeController@removeBlock')->name('admin.api.microsites.block.remove');
            Route::post('update_index',
                'Admin\Api\MicrositeController@updateBlockIndex')->name('admin.api.microsites.block.update_index');
        });
        Route::group(['prefix' => 'shops/{id}'], function () {
            Route::put('', 'Admin\ShopController@update')->name('admin.sellers.shops.update');

            Route::group(['prefix' => 'block'], function () {
                Route::post('add',
                    'Admin\Api\ShopController@addBlock')->name('admin.api.shop.block.add');
                Route::delete('remove',
                    'Admin\Api\ShopController@removeBlock')->name('admin.api.shop.block.remove');
                Route::post('update_index',
                    'Admin\Api\ShopController@updateBlockIndex')->name('admin.api.shop.block.update_index');
            });
        });

    });

//    Route::put('shops/{id}', 'Admin\ShopController@update')->name('admin.shop.update');

    // Payment Setting
    Route::post('/payment-setting/toggle-active', 'Admin\Api\PaymentSettingController@toggleActive')->name('admin.api.payment_setting.toggle_active');
    Route::post('/product-list', 'Admin\Api\ProductListController@index')->name('admin.api.product-list');



    //Frontend setting - Home
    Route::group(['prefix' => 'frontend_settings'], function() {
        Route::get('home', 'Admin\FrontendSettingHomeController@index')->name('frontend_settings.home.index');
    });
    Route::group(['prefix' => 'api/frontend_settings'], function() {
        Route::group(['prefix' => 'home_slider'], function() {
            Route::post('update_index', 'Admin\Api\FrontendSetting\HomeSliderController@updateIndex')->name('admin.api.frontend_setting.home_slider.update_index');
            Route::post('{id}/toggle_publish', 'Admin\Api\FrontendSetting\HomeSliderController@togglePublish')->name('admin.api.frontend_setting.home_slider.toggle_publish');
        });
        Route::group(['prefix' => 'home_banner'], function() {
            Route::post('update_index', 'Admin\Api\FrontendSetting\HomeBannerController@updateIndex')->name('admin.api.frontend_setting.home_banner.update_index');
            Route::post('{id}/toggle_publish', 'Admin\Api\FrontendSetting\HomeBannerController@togglePublish')->name('admin.api.frontend_setting.home_banner.toggle_publish');
        });
    });


    Route::post('/products/onlyMicrosite', 'ProductController@updateOnlyMicrosite')->name('admin.products.only_microsite');

    Route::get('/referral', 'ReferralController@adminIndex')->name('admin.referral.index');
    Route::get('/referral/reset/domain/{domain}', 'ReferralController@domainReset')->name('admin.referral.domain_reset');
    Route::get('/referral/reset/domain/{domain}/users', 'ReferralController@domainUserList')->name('admin.referral.domain_users');
    Route::get('/referral/reset/user/{user}', 'ReferralController@userReset')->name('admin.referral.user_reset');
    Route::post('/referral/save', 'ReferralController@save')->name('admin.referral.save');

    Route::resource('brands', 'BrandController');
    Route::resource('categories', 'CategoryController')->only('index','create','store','edit','update','destroy');
    Route::get('/categories/export-all', 'CategoryController@exportAll')->name('admin.categories.export-all');
    Route::get('/seller/export-all', 'Admin\SellerController@exportAll')->name('admin.sellers.export-all');

    Route::resource('subcategories', 'SubCategoryController')->only('index','create','store','edit','update','destroy');
    Route::resource('subsubcategories', 'SubSubCategoryController')->only('index','create','store','update','edit','destroy');

    // category
    Route::group(['prefix' => 'categories'], function() {
        Route::post('/status', 'CategoryController@updateStatus')->name('categories.status');
        Route::post('/featured', 'CategoryController@updateFeatured')->name('categories.featured');
    });
    
    // sub category
    Route::group(['prefix' => 'subcategories'], function() {
        Route::post('/status', 'SubCategoryController@updateStatus')->name('subcategories.status');
    });
    
    // sub sub category
    Route::group(['prefix' => 'subsubcategories'], function() {
        Route::post('/status', 'SubSubCategoryController@updateStatus')->name('subsubcategories.status');
    });
   
    // brands
    Route::group(['prefix' => 'brands'], function() {
        Route::post('/status', 'BrandController@updateStatus')->name('brands.status');
    });
    
    // products
    Route::group(['prefix' => 'products'], function() {
        Route::get('/admin', 'ProductController@admin_products')->name('products.admin');
        Route::get('/seller', 'ProductController@seller_products')->name('products.seller');
        Route::get('/create', 'ProductController@create')->name('products.create');
        Route::get('/admin/{id}/edit', 'ProductController@admin_product_edit')->name('products.admin.edit');
        Route::get('/seller/{id}/edit', 'ProductController@seller_product_edit')->name('products.seller.edit');
        Route::post('/todays_deal', 'ProductController@updateTodaysDeal')->name('products.todays_deal');
        Route::post('/get_products_by_subcategory', 'ProductController@get_products_by_subcategory')->name('products.get_products_by_subcategory');
        Route::post('/get_products_by_subsubcategory', 'ProductController@get_products_by_subsubcategory')->name('products.get_products_by_subsubcategory');
    });
   
    Route::resource('sellers', 'Admin\SellerController');
    Route::get('sellers_ban/{id}', 'Admin\SellerController@ban')->name('sellers.ban');
    Route::get('sellers_sync/{user}', 'Admin\SellerController@sync')->name('sellers.oms_sync');
    Route::get('/sellers/destroy/{id}', 'Admin\SellerController@destroy')->name('sellers.destroy');
    Route::get('/sellers/view/{id}/verification', 'Admin\SellerController@show_verification_request')->name('sellers.show_verification_request');
    Route::get('/sellers/approve/{id}', 'Admin\SellerController@approve_seller')->name('sellers.approve');
    Route::get('/sellers/reject/{id}', 'Admin\SellerController@reject_seller')->name('sellers.reject');
    Route::get('/sellers/login/{id}', 'Admin\SellerController@login')->name('sellers.login');
    Route::post('/sellers/payment_modal', 'Admin\SellerController@payment_modal')->name('sellers.payment_modal');
    Route::get('/seller/payments', 'PaymentController@payment_histories')->name('sellers.payment_histories');
    Route::get('/seller/payments/show/{id}', 'PaymentController@show')->name('sellers.payment_history');
    Route::get('/customers/export-all', 'CustomerController@exportAll')->name('customers.export-all');
    Route::resource('customers', 'CustomerController');
    Route::get('customers_ban/{customer}', 'CustomerController@ban')->name('customers.ban');
    Route::get('/customers/login/{id}', 'CustomerController@login')->name('customers.login');
    Route::get('/customers/destroy/{id}', 'CustomerController@destroy')->name('customers.destroy');
    Route::resource('newsletters', 'NewsletterController');
    Route::post('/newsletters/send', 'NewsletterController@send')->name('newsletters.send');
    Route::post('/newsletters/update_status', 'NewsletterController@updateStatus')->name('newsletters.update_status');
    Route::get('/newsletters/destroy/{id}', 'NewsletterController@destroy')->name('newsletters.destroy');
    Route::resource('profile', 'ProfileController');
    Route::post('/business-settings/update', 'BusinessSettingsController@update')->name('business_settings.update');
    Route::post('/business-settings/update/activation', 'BusinessSettingsController@updateActivationSettings')->name('business_settings.update.activation');
    Route::get('/activation', 'BusinessSettingsController@activation')->name('activation.index');
    Route::get('/payment-method', 'BusinessSettingsController@payment_method')->name('payment_method.index');
    Route::get('/file_system', 'BusinessSettingsController@file_system')->name('file_system.index');
    Route::get('/social-login', 'BusinessSettingsController@social_login')->name('social_login.index');
    Route::get('/smtp-settings', 'BusinessSettingsController@smtp_settings')->name('smtp_settings.index');
    Route::get('/google-analytics', 'BusinessSettingsController@google_analytics')->name('google_analytics.index');
    Route::get('/google-recaptcha', 'BusinessSettingsController@google_recaptcha')->name('google_recaptcha.index');
    Route::get('/facebook-chat', 'BusinessSettingsController@facebook_chat')->name('facebook_chat.index');
    Route::post('/env_key_update', 'BusinessSettingsController@env_key_update')->name('env_key_update.update');
    Route::post('/payment_method_update', 'BusinessSettingsController@payment_method_update')->name('payment_method.update');
    Route::post('/google_analytics', 'BusinessSettingsController@google_analytics_update')->name('google_analytics.update');
    Route::post('/google_recaptcha', 'BusinessSettingsController@google_recaptcha_update')->name('google_recaptcha.update');
    Route::post('/facebook_chat', 'BusinessSettingsController@facebook_chat_update')->name('facebook_chat.update');
    Route::post('/facebook_pixel', 'BusinessSettingsController@facebook_pixel_update')->name('facebook_pixel.update');
    Route::get('/currency', 'CurrencyController@currency')->name('currency.index');
    Route::post('/currency/update', 'CurrencyController@updateCurrency')->name('currency.update');
    Route::post('/your-currency/update', 'CurrencyController@updateYourCurrency')->name('your_currency.update');
    Route::get('/currency/create', 'CurrencyController@create')->name('currency.create');
    Route::post('/currency/store', 'CurrencyController@store')->name('currency.store');
    Route::post('/currency/currency_edit', 'CurrencyController@edit')->name('currency.edit');
    Route::post('/currency/update_status', 'CurrencyController@update_status')->name('currency.update_status');
    Route::get('/verification/form', 'BusinessSettingsController@seller_verification_form')->name('seller_verification_form.index');
    Route::post('/verification/form', 'BusinessSettingsController@seller_verification_form_update')->name('seller_verification_form.update');
    Route::get('/vendor_commission', 'BusinessSettingsController@vendor_commission')->name('business_settings.vendor_commission');
    Route::post('/vendor_commission_update', 'BusinessSettingsController@vendor_commission_update')->name('business_settings.vendor_commission.update');
    Route::resource('/languages', 'LanguageController');
    Route::post('/languages/update_rtl_status', 'LanguageController@update_rtl_status')->name('languages.update_rtl_status');
    Route::get('/languages/destroy/{id}', 'LanguageController@destroy')->name('languages.destroy');
    Route::get('/languages/{id}/edit', 'LanguageController@edit')->name('languages.edit');
    Route::post('/languages/{id}/update', 'LanguageController@update')->name('languages.update');
    Route::post('/languages/key_value_store', 'LanguageController@key_value_store')->name('languages.key_value_store');
    Route::post('/frontend_settings/home/top_10', 'HomeController@top_10_settings')->name('top_10_settings.store');
    Route::get('/sellerpolicy/{type}', 'PolicyController@index')->name('sellerpolicy.index');
    Route::get('/returnpolicy/{type}', 'PolicyController@index')->name('returnpolicy.index');
    Route::get('/supportpolicy/{type}', 'PolicyController@index')->name('supportpolicy.index');
    Route::get('/terms/{type}', 'PolicyController@index')->name('terms.index');
    Route::get('/privacypolicy/{type}', 'PolicyController@index')->name('privacypolicy.index');
    //Policy Controller
    Route::post('/policies/store', 'PolicyController@store')->name('policies.store');
    Route::group(['prefix' => 'frontend_settings'], function() {
        Route::resource('sliders', 'SliderController');
        Route::get('/sliders/destroy/{id}', 'SliderController@destroy')->name('sliders.destroy');
        Route::post('/sliders/update_status', 'SliderController@update_status')->name('sliders.update_status');
        Route::resource('home_banners', 'BannerController');
        Route::get('/home_banners/create/{position}', 'BannerController@create')->name('home_banners.create');
        Route::post('/home_banners/update_status', 'BannerController@update_status')->name('home_banners.update_status');
        Route::get('/home_banners/destroy/{id}', 'BannerController@destroy')->name('home_banners.destroy');
        Route::resource('home_categories', 'HomeCategoryController');
        Route::get('/home_categories/destroy/{id}', 'HomeCategoryController@destroy')->name('home_categories.destroy');
        Route::post('/home_categories/update_status', 'HomeCategoryController@update_status')->name('home_categories.update_status');
        Route::post('/home_categories/get_subsubcategories_by_category', 'HomeCategoryController@getSubSubCategories')->name('home_categories.get_subsubcategories_by_category');
    });
    Route::resource('roles', 'RoleController');
    Route::get('/roles/destroy/{id}', 'RoleController@destroy')->name('roles.destroy');
    Route::resource('staffs', 'StaffController');
    Route::get('/staffs/destroy/{id}', 'StaffController@destroy')->name('staffs.destroy');

    Route::resource('flash_deals', 'FlashDealController');
    Route::group(['prefix' => 'flash_deals'], function() {

        Route::get('{flashDeal}/products', 'FlashDealController@productList')->name('flash_deals.products');
        Route::post('{flashDeal}/products/add', 'FlashDealController@productListAddItems')->name('flash_deals.products.add');
        Route::post('{flashDeal}/products/{itemId}', 'FlashDealController@productListViewItem')->name('flash_deals.products.view');
        Route::post('{flashDeal}/products/{itemId}/update', 'FlashDealController@productListUpdateItem')->name('flash_deals.products.update');
        Route::post('{flashDeal}/products/{itemId}/remove', 'FlashDealController@productListRemoveItem')->name('flash_deals.products.remove');

        Route::get('destroy/{id}', 'FlashDealController@destroy')->name('flash_deals.destroy');
        Route::post('update_status', 'FlashDealController@update_status')->name('flash_deals.update_status');
        Route::post('update_featured', 'FlashDealController@update_featured')->name('flash_deals.update_featured');
//        Route::post('product_discount', 'FlashDealController@product_discount')->name('flash_deals.product_discount');
//        Route::post('product_discount_edit', 'FlashDealController@product_discount_edit')->name('flash_deals.product_discount_edit');
    });


    Route::get('/orders', 'OrderController@admin_orders')->name('orders.index.admin');
    Route::get('/orders/{id}/show', 'OrderController@show')->name('orders.show');
    Route::get('/sales/{id}/show', 'OrderController@sales_show')->name('sales.show');
    Route::get('/orders/destroy/{id}', 'OrderController@destroy')->name('orders.destroy');
    Route::post('/orders/{id}/restock', 'OrderController@restock')->name('orders.restock');
    Route::get('/sales', 'OrderController@sales')->name('sales.index');
    Route::get('/sales_today', 'OrderController@today_sales')->name('sales_today.index');
    Route::resource('links', 'LinkController');
    Route::get('/links/destroy/{id}', 'LinkController@destroy')->name('links.destroy');
    Route::resource('generalsettings', 'GeneralSettingController');
    Route::get('/logo', 'GeneralSettingController@logo')->name('generalsettings.logo');
    Route::post('/logo', 'GeneralSettingController@storeLogo')->name('generalsettings.logo.store');
    Route::get('/color', 'GeneralSettingController@color')->name('generalsettings.color');
    Route::post('/color', 'GeneralSettingController@storeColor')->name('generalsettings.color.store');
    Route::resource('seosetting', 'SEOController');
    Route::post('/pay_to_seller', 'CommissionController@pay_to_seller')->name('commissions.pay_to_seller');
    //Reports
    Route::get('/stock_report', 'ReportController@stock_report')->name('stock_report.index');
    Route::get('/in_house_sale_report', 'ReportController@in_house_sale_report')->name('in_house_sale_report.index');
    Route::get('/seller_report', 'ReportController@seller_report')->name('seller_report.index');
    Route::get('/seller_sale_report', 'ReportController@seller_sale_report')->name('seller_sale_report.index');
    Route::get('/heinemann_sales_report', 'ReportController@heinemann_sales_report')->name('heinemann_sales_report.index');
    Route::get('/shipping_report', 'ReportController@shipping_report')->name('shipping_report.index');
    Route::get('/coupon_report', 'ReportController@coupon_report')->name('coupon_report.index');
    Route::get('/wish_report', 'ReportController@wish_report')->name('wish_report.index');
    Route::get('/reorganise_products', 'ReportController@reorganise_products')->name('reorganise_products');
    Route::get('/reorganise_products_report', 'ReportController@reorganise_products_report')->name('reorganise_products_report');
    Route::post('/reorganise_products_upload', 'ReportController@reorganise_products_upload')->name('reorganise_products_upload');

    //Coupons
    Route::resource('coupon', 'CouponController');
    Route::group(['prefix' => 'coupon'], function() {
        Route::post('/get_form', 'CouponController@get_coupon_form')->name('coupon.get_coupon_form');
        Route::post('/get_form_edit', 'CouponController@get_coupon_form_edit')->name('coupon.get_coupon_form_edit');
        Route::get('/destroy/{id}', 'CouponController@destroy')->name('coupon.destroy');
        Route::post('/coupon/published', 'CouponController@updatePublished')->name('coupons.published');
        Route::post('/coupon/toggle_active', 'CouponController@toggleActive')->name('coupons.toggle_active');
        Route::get('/export-all', 'CouponController@exportAll')->name('coupon.export_all');
    });

    //Reviews
    Route::get('/reviews', 'ReviewController@index')->name('reviews.index');
    Route::post('/reviews/published', 'ReviewController@updatePublished')->name('reviews.published');

    //Review Categories
    Route::resource('review_categories', 'ReviewCategoryController');
    Route::get('/review_categories/destroy/{id}', 'ReviewCategoryController@destroy')->name('review_categories.destroy');
    Route::post('/review_categories/status', 'ReviewCategoryController@updateStatus')->name('review_categories.status');
    //Support_Ticket
    Route::get('support_ticket/', 'SupportTicketController@admin_index')->name('support_ticket.admin_index');
    Route::get('support_ticket/{id}/show', 'SupportTicketController@admin_show')->name('support_ticket.admin_show');
    Route::post('support_ticket/reply', 'SupportTicketController@admin_store')->name('support_ticket.admin_store');
    //Pickup_Points
    Route::resource('pick_up_points', 'PickupPointController');
    Route::get('/pick_up_points/destroy/{id}', 'PickupPointController@destroy')->name('pick_up_points.destroy');
    Route::get('orders_by_pickup_point', 'OrderController@order_index')->name('pick_up_point.order_index');
    Route::get('/orders_by_pickup_point/{id}/show', 'OrderController@pickup_point_order_sales_show')->name('pick_up_point.order_show');
    Route::get('invoice/admin/{order_id}', 'InvoiceController@admin_invoice_download')->name('admin.invoice.download');
    //conversation of seller customer
    Route::get('conversations', 'ConversationController@admin_index')->name('conversations.admin_index');
    Route::get('conversations/{id}/show', 'ConversationController@admin_show')->name('conversations.admin_show');
    Route::post('/sellers/profile_modal', 'Admin\SellerController@profile_modal')->name('sellers.profile_modal');
    Route::post('/sellers/approved', 'Admin\SellerController@updateApproved')->name('sellers.approved');
    Route::resource('attributes', 'AttributeController');
    Route::get('/attributes/destroy/{id}', 'AttributeController@destroy')->name('attributes.destroy');
    Route::resource('addons', 'AddonController');
    Route::post('/addons/activation', 'AddonController@activation')->name('addons.activation');
    Route::get('/customer-bulk-upload/index', 'CustomerBulkUploadController@index')->name('customer_bulk_upload.index');
    Route::post('/bulk-user-upload', 'CustomerBulkUploadController@user_bulk_upload')->name('bulk_user_upload');
    Route::post('/bulk-customer-upload', 'CustomerBulkUploadController@customer_bulk_file')->name('bulk_customer_upload');
    Route::get('/user', 'CustomerBulkUploadController@pdf_download_user')->name('pdf.download_user');
    //Customer Package
    Route::resource('customer_packages', 'CustomerPackageController');
    Route::get('/customer_packages/destroy/{id}', 'CustomerPackageController@destroy')->name('customer_packages.destroy');
    //Classified Products
    Route::get('/classified_products', 'CustomerProductController@customer_product_index')->name('classified_products');
    Route::post('/classified_products/published', 'CustomerProductController@updatePublished')->name('classified_products.published');
    //Shipping Configuration
    Route::get('/shipping_configuration', 'BusinessSettingsController@shipping_configuration')->name('shipping_configuration.index');
    Route::post('/shipping_configuration/update', 'BusinessSettingsController@shipping_configuration_update')->name('shipping_configuration.update');
    Route::resource('pages', 'PageController');
    Route::get('/pages/destroy/{id}', 'PageController@destroy')->name('pages.destroy');
    Route::resource('countries', 'CountryController');
    Route::post('/countries/status', 'CountryController@updateStatus')->name('countries.status');

    // localesettings
    Route::resource('localesettings', 'LocaleSettingsController');

    Route::get('/report/histories', 'Admin\HistoryController@index')->name('report.histories');
    Route::get('/report/histories/export', 'Admin\HistoryController@exportExcel')->name('report.histories.export');
    Route::get('/ajax/history/{history}', 'Admin\Ajax\HistoryController@get')->name('ajax.hitories.show');

    // bulk update/delete corporate seller store access domain/email
    Route::get('/css-access', 'Admin\CorporateSalesStoreController@css_access')->name('admin.css.access');
    Route::post('/css-access/bulk_update', 'Admin\CorporateSalesStoreController@bulk_update')->name('admin.css.bulk_update');

    // user waiting auth by admin
    Route::get('/list_users_awaiting_verify', 'UserOtpsController@getListUsersAwaitingVerification')->name('admin.list_users_awaiting_verify');
    Route::get('/user_otps/destroy/{id}', 'UserOtpsController@destroy')->name('admin.user_otps.destroy');
    Route::get('/user_otps/registration_by_admin', 'UserOtpsController@registrationByAdmin')->name('admin.user_otps.registration_by_admin');

    // age restricted settings
    Route::get('/age-restricted-settings', 'Admin\AgeRestrictedSettingsController@index')->name('age_restricted.index');
    
    Route::post('/age-restricted-settings/add-content', 'Admin\AgeRestrictedSettingsController@addContent')->name('age_restricted.add_content');

    // contact forms
    Route::get('/contact-forms', 'ContactFormController@index')->name('contact_forms.index');
    Route::get('/contact-forms/{id}', 'ContactFormController@show')->name('contact_forms.show');
    Route::delete('/contact-forms/{id}', 'ContactFormController@destroy')->name('contact_forms.destroy');
    Route::post('/contact-forms/{id}/mark-as-read', 'ContactFormController@markAsRead')->name('contact_forms.mark_as_read');
    Route::post('/contact-forms/{id}/mark-as-unread', 'ContactFormController@markAsUnread')->name('contact_forms.mark_as_unread');
    // Elasticsearch Dev Tools
    Route::group(['prefix' => 'elasticsearch'], function () {
        Route::get('/dev-tools', 'Admin\ElasticsearchDevToolController@index')->name('admin.elasticsearch.dev_tools');
        Route::post('/execute', 'Admin\ElasticsearchDevToolController@executeQuery')->name('admin.elasticsearch.execute');
        Route::get('/cluster-info', 'Admin\ElasticsearchDevToolController@getClusterInfo')->name('admin.elasticsearch.cluster-info');
        Route::post('/parse-query', 'Admin\ElasticsearchDevToolController@parseQuery')->name('admin.elasticsearch.parse-query');
    });
});
