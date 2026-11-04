<?php

namespace App\Providers;

use App\Http\View\Composers\CustomPage\{
    BlockCABrandListComposer,
    BlockComposer
};

use App\Http\View\Composers\{
    CartSummaryComposer,
    CustomerSideNavComposer,
    SellerRevenueComposer,

};
use App\Models\{
    Country,
    Language,
    NewSletter,
    GeneralSetting
};

use App\Services\{
    BannerService,
    BusinessSettingService,
    BrandService,
    CategoryService,
    NewSletterService
};

use Illuminate\Support\Facades\{
    DB,
    Schema,
    Session,
    View
};

use App\Repositories\BusinessSettingRepository;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        if (Schema::hasTable('general_settings') && Schema::hasTable('languages')) {
            $generalSetting     = GeneralSetting::first();
            $firstLanguage      = Language::first();
            $languageIds        = isset($generalSetting->language_ids) ? json_decode($generalSetting->language_ids) : (isset($firstLanguage) ? [$firstLanguage->id] : []);
            $systemLanguages    = Language::orderByRaw(DB::raw("FIELD(code, 'en') DESC"))->whereIn('id',$languageIds)->get();
            $languages          = isset($languageIds) && !empty($languageIds) ? Language::whereIn('id',$languageIds)->select('id','name','code')->get() : Language::select('id','name','code')->get();
            $defaultLanguage    = isset($generalSetting->default_language) ? $generalSetting->default_language : NULL;

            View::share('languages', $languages);
            View::share('defaultLanguage', $defaultLanguage);
            View::share('systemLanguages', $systemLanguages);
            View::share('generalSetting', $generalSetting);

        }

        if (Schema::hasTable('cities')) {
            $country    = Country::where(['status' => 1])->orderBy('name','asc')->first();
            $cities     = isset($country) ?  $country->childrens : null;

            View::share('cities', $cities);
        }

        $businessSetting = new BusinessSettingService(new BusinessSettingRepository());
        View::share('businessSetting', $businessSetting->setting);

        View::composer(['frontend.inc.logobar', 'frontend.inc.footer', 'frontend.inc.breadcrumb', 'frontend.product_filter.coupon_filter'], function($view) use ($generalSetting) {
            $logoUrl    = $generalSetting->logo ? my_asset($generalSetting->logo) : my_asset('frontend/images/logo/logo.png');
            $logoAlt    = env('APP_NAME');
            $logoLink   = route('home');

            if (Session::has('isMicrosite') && Session::get('isMicrosite')) {
                $shop       = Session::get('shop');
                $logoUrl    = my_asset($shop->logo);
                $logoAlt    = $shop->name;
                $logoLink   = route('microsite.index', ['slug' => $shop->slug]);
            }

            $view->with('logoUrl', $logoUrl);
            $view->with('logoAlt', $logoAlt);
            $view->with('logoLink', $logoLink);
        });

        View::composer(['frontend.inc.logobar', 'frontend.category.product_listing'], function($view) {
            $mainCategories = $this->app->make(CategoryService::class)->getMainCategories();
            $mainBrands     = $this->app->make(BrandService::class)->getMainBrands();

            $view->with('mainCategories', $mainCategories);
            $view->with('mainBrands', $mainBrands);
        });

        View::composer(['frontend.index'], function($view) {
            $bannerService  = $this->app->make(BannerService::class);
            $banners1       = $bannerService->getPublishedList(['position' => 1]);
            $banners2       = $bannerService->getPublishedList(['position' => 2]);
            $view->with('banners1', $banners1);
            $view->with('banners2', $banners2);
        });

        // Using class based composers...
        View::composer(
            'frontend.custom_page.page.index', BlockComposer::class
        );
        View::composer(
            'frontend.custom_page.page.ca_brand_list', BlockCABrandListComposer::class
        );

        View::composer(
            'frontend.inc.customer_side_nav', CustomerSideNavComposer::class
        );

        View::composer(
            ['frontend.partials.cart_summary', 'frontend.partials.cart', 'frontend.partials.cart_details'], CartSummaryComposer::class
        );

        View::composer(
            ['frontend.inc.mobile_menu', 'frontend.seller.dashboard'], SellerRevenueComposer::class
        );

        if (Schema::hasTable('business_settings')) {
            View::share('isCaptchaEnabled', $businessSetting->isCaptchaEnabled());
        }

        View::composer(['frontend.layouts.app'], function ($view) {
            $newsletterService = $this->app->make(NewSletterService::class);
            $newsletter = $newsletterService
                    ->getAllWithConditions(['status' => NewSletter::STATUS_ACTIVE])
                    ->filter(function ($newsletter) {
                        return !$newsletter->isExpired();
                    })
                    ->first();

            $view->with('newsletter', $newsletter);
        });

        // Using class based composers...
        // View::composer(
        //     'clubs', 'App\Http\View\Composers\ProfileComposer'
        // );

        // Using Closure based composers...
//        View::composer(['components/blocks.ClassHeading', 'components/blocks.ClubMap'], function ($view) {
//            $baseUrl = 'https://vgym.perfectgym.com/Api/v2/';
//            $client = $this->getInstance();
//            $clubs = $client->get($baseUrl . 'odata/Clubs?$filter=isDeleted eq false');
//
//            $view->with('clubs', $clubs->json()['value']);
//        });
//
//        View::composer('components/blocks.ClassHeading', function ($view) {
//            $baseUrl = 'https://vgym.perfectgym.com/Api/v2/';
//            $client = $this->getInstance();
//
//            $now = Carbon::now();
//            $startDate = clone $now;
//            $startDate = $startDate->firstOfMonth();
//            $endDate = clone $now;
//            $endDate = $endDate->addMonths(2);
//
//            $timetables = $client->get($baseUrl . 'odata/Classes?$expand=classType&$filter=startDate ge ' . urlencode($startDate->toIso8601String()) . ' and endDate le ' . urlencode($endDate->toIso8601String()));
//
//            $view->with('timetables', htmlspecialchars_decode(json_encode($timetables->json()['value'])));
//        });
    }
}