<?php
namespace App\Providers;

use App\Events\ProductCreated;
use App\Events\ProductUpdated;
use App\Listeners\MicrositeSMTPConfig;
use App\Listeners\SendOmsApiCreatedProduct;
use App\Listeners\SendOmsApiUpdateProduct;
use Illuminate\Auth\Events\Registered;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Models\SplitOrder;
use App\Observers\SplitOrderObserver;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        MessageSending::class => [
            MicrositeSMTPConfig::class,
        ],
        ProductCreated::class => [
            SendOmsApiCreatedProduct::class,
        ],
        ProductUpdated::class => [
            SendOmsApiUpdateProduct::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
        SplitOrder::observe(SplitOrderObserver::class);
    }
}

