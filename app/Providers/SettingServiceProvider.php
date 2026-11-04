<?php
namespace App\Providers;

use App\Models\BusinessSetting;
use App\Repositories\CurrencyRepository;
use App\Services\CurrencyService;
use Illuminate\Support\ServiceProvider;

class SettingServiceProvider extends ServiceProvider
{

    /**
     * @return void
     * @todo caching these configs
     */
    public function boot()
    {
        $businessSetting = BusinessSetting::pluck('value', 'type')->all();
        config()->set('business_setting', $businessSetting);

        $currencyService = resolve('CurrencyService');
        config()->set('currency_code', $currencyService->getCode());
        config()->set('currency_symbol', $currencyService->getSymbol());
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('CurrencyService', function () {
            return new CurrencyService(
                new CurrencyRepository()
            );
        });
    }
}

