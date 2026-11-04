<?php
namespace App\Providers;

use App\Extensions\UrlGeneratorExtension;

use App\Repositories\BannerRepository;
use App\Repositories\BrandRepository;
use App\Repositories\BusinessSettingRepository;
use App\Repositories\CorporateAccessTokenRepository;
use App\Repositories\CorporateReferralRepository;
use App\Repositories\CustomPage\BlockRepository;
use App\Repositories\PaymentSettingRepository;
use App\Repositories\SellerCenterSettingRepository;
use App\Repositories\SellerCorporateDomainRepository;
use App\Repositories\SellerRepository;
use App\Repositories\SliderRepository;
use App\Repositories\UserCorporateAccessRepository;
use App\Services\BannerService;
use App\Services\BrandService;
use App\Services\CorporateAccessService;
use App\Services\CustomPage\BlockService;
use App\Services\PaymentSettingService;
use App\Services\SellerCenterSettingService;
use App\Services\SellerService;
use App\Services\SliderService;
use Illuminate\Contracts\Routing\UrlGenerator as UrlGeneratorContract;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;


use function GuzzleHttp\json_decode;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
//        $this->app->singleton('PaymentSettingService');

        $this->app->singleton('sellerCenterSetting', function () {
            return new SellerCenterSettingService(new SellerCenterSettingRepository());
        });

        $this->app->singleton('paymentSetting', function () {
            return new PaymentSettingService(new PaymentSettingRepository());
        });

        $this->app->singleton('SellerService', function () {
            return new SellerService(new SellerRepository());
        });

        $this->app->singleton('BannerService', function () {
            return new BannerService(new BannerRepository());
        });

        $this->app->singleton('SliderService', function () {
            return new SliderService(new SliderRepository());
        });

        $this->app->singleton('BrandService', function () {
            return new BrandService(
                new BrandRepository(), new SellerRepository(), resolve('SellerService')
            );
        });

        $this->app->singleton('BlockService', function () {
            return new BlockService(new BlockRepository(), new SellerRepository(), resolve('BrandService'));
        });

//        $this->app->singleton('CorporateAccess.UserService', function () {
//            return new UserService(new CorporateAccessTokenRepository(), new SellerRepository(), new SellerCorporateDomainRepository());
//        });


        $this->registerUrlGenerator();
    }

    protected function registerUrlGenerator()
    {
        $this->app->singleton('url', function ($app) {
            $routes = $app['router']->getRoutes();

            // The URL generator needs the route collection that exists on the router.
            // Keep in mind this is an object, so we're passing by references here
            // and all the registered routes will be available to the generator.
            $app->instance('routes', $routes);

            return new UrlGeneratorExtension(
                $routes, $app->rebinding(
                    'request', $this->requestRebinder()
                ), $app['config']['app.asset_url']
            );
        });

        $this->app->extend('url', function (UrlGeneratorContract $url, $app) {
            // Next we will set a few service resolvers on the URL generator so it can
            // get the information it needs to function. This just provides some of
            // the convenience features to this URL generator like "signed" URLs.
            $url->setSessionResolver(function () {
                return $this->app['session'] ?? null;
            });

            $url->setKeyResolver(function () {
                return $this->app->make('config')->get('app.key');
            });

            // If the route collection is "rebound", for example, when the routes stay
            // cached for the application, we will need to rebind the routes on the
            // URL generator instance so it has the latest version of the routes.
            $app->rebinding('routes', function ($app, $routes) {
                $app['url']->setRoutes($routes);
            });

            return $url;
        });
    }

    /**
     * Get the URL generator request rebinder.
     *
     * @return \Closure
     */
    protected function requestRebinder()
    {
        return function ($app, $request) {
            $app['url']->setRequest($request);
        };
    }
}

