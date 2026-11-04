<?php

namespace App\Http\Middleware;

use App\Services\ShopService;
use Closure;

class MicrositeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $sellerSlug = $request->route('seller_slug');
        if (!isMicrosite()) {
            return $this->initMicrositeSession($sellerSlug);
        } else {
            $shop = getMicrositeShop();
            if ($shop->slug !== $sellerSlug) {
                return $this->initMicrositeSession($sellerSlug);
            }
        }

        return $next($request);
    }

    public function initMicrositeSession(string $sellerSlug) {
        $shopService = app()->make(ShopService::class);
        $shop = $shopService->findBy('slug', $sellerSlug);
        $microsite = $shop->user->seller->microsite;

        if (
            !$shop
            || !$microsite
            || !$microsite->active
        ) {
            return redirect()->route('home');
        }

        session(['isMicrosite' => true]);
        session(['shop' => $shop]);
        session(['microsite' => $microsite]);
    }
}