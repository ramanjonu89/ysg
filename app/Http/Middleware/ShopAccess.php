<?php

namespace App\Http\Middleware;

use App\Services\ShopService;
use App\Services\StoreAccessService;
use Closure;
use Illuminate\Http\Request;

class ShopAccess
{
    /**
     * @param StoreAccessService
     */
    protected $storeAccessService;

    /**
     * @param ShopService
     */
    protected $shopService;

    public function __construct(
        StoreAccessService $storeAccessService,
        ShopService $shopService
    )
    {
        $this->storeAccessService = $storeAccessService;
        $this->shopService = $shopService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $shop = $this->shopService->getShopFromRoute($request);

        if (!$shop) {
            abort(404);
            return false;
        }

        if (!$this->shopService->isInPrivateEvent($shop)) {
            return $next($request);
        }

        $customer = $request->user();
        if (is_null($customer)) {
            flash(__('Please login using a valid account to access the Private/Corporate Sales Store!'))->error();
            return redirect()->route('user.login');
        }

        $hasAccess = $this->storeAccessService->hasAccess($shop, $customer);

        if ($hasAccess) {
            return $next($request);
        }

        if ($request->access_code) {
            $result = $this->storeAccessService->grantAccessByCode($shop, $customer, $request->access_code);
            if ($result['status'] == true) {
                return $next($request);
            }
            flash(__('You have entered an invalid access code. Please try again or contact cs@ysg.com.sg for assistance.'))->error();
        }

        $route = $request->route();
        $seller = $shop->seller;
        if ($this->storeAccessService->isNeedAccessCode($shop) || $this->storeAccessService->isNeedCACode($seller)) {
            return redirect()->route($route->getName().'.access-form', $route->parameters());
        } elseif ($this->storeAccessService->isNeedCADomain($seller)) {
            flash(__('This store can only be accessed by companies enrolled in YSG’s Corporate Sales Store Employee Benefits Programme Programme. If you are still having difficulty logging in, please contact YSG at cs@ysg.com.sg'))->error();
            return redirect()->route('home');
        }

        return $next($request);
    }

}
