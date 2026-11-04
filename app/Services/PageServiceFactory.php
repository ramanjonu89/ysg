<?php

namespace App\Services;

use App\Services\CustomPage\PageService;

class PageServiceFactory
{
    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function make(string $serviceName): PageInterface {
        switch ($serviceName) {
            case 'microsite':
                return app()->make(MicrositeService::class);
            case 'shop':
                return app()->make(ShopService::class);
            default:
                return app()->make(PageService::class);
        }
    }
}