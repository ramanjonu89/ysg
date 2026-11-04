<?php

namespace App\Http\Middleware\Api\Mobile;

use App\Services\Api\Mobile\ProductService;
use App\Services\ShopService;
use App\Services\StoreAccessService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ShopAccess
{
    protected $productService;
    protected $storeAccessService;
    protected $shopService;

    public function __construct(
        ProductService $productService,
        StoreAccessService $storeAccessService,
        ShopService $shopService
    ) {
        $this->productService = $productService;
        $this->storeAccessService = $storeAccessService;
        $this->shopService = $shopService;
    }

    public function handle(Request $request, Closure $next)
    {
        $route  = $request->route();
        $prefix = $route->getAction('prefix');
        $isSeller = Str::contains($prefix, 'seller');
        if ($isSeller) {
            $seller = $request->route('seller');
            $slug   = $seller->user->shop->slug;
        } else {
            $productId  = $request->route('id');
            $product    = $this->productService->get($productId);
            $seller     = $product->user->seller;
            $slug       = $product->user->shop->slug;
        }

        $shop = $this->shopService->getBySlug($slug);

        if (!$shop || $seller->user->isExpired()) {
            return response()->json([
                'success' => false,
                'message' => 'Shop not found',
            ], 404);
        }

        if (!$this->shopService->isInPrivateEvent($shop)) {
            return $next($request);
        }

        $customer = Auth::guard('api')->user();
        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Please login using a valid account to access the Private/Corporate Sales Store!',
            ], 401);
        }

        $accessToken = $request->header('X-Access-Code-Token');
        if ($accessToken) {
            $payload = $this->storeAccessService->validateAccessToken($shop, $customer, $accessToken);
            if ($payload) {
                return $next($request);
            }
        }

        $hasAccess = $this->storeAccessService->hasAccess($shop, $customer);
        if ($hasAccess) {
            return $next($request);
        }

        if ($request->access_code) {
            $result = $this->storeAccessService->grantAccessByCode($shop, $customer, $request->access_code);
            if ($result['status'] === true) {
                $accessToken = $this->storeAccessService->generateAccessToken($shop, $customer);

                $response = $next($request);

                $token = $accessToken['token'];
                if ($token && $response instanceof \Illuminate\Http\JsonResponse) {
                    $original = $response->getData(true);
                    $original['access_token'] = $token;

                    return response()->json($original, $response->status());
                }

                return $response;
            }

            return response()->json([
                'success' => false,
                'message' => 'You have entered an invalid access code. Please try again or contact cs@ysg.com.sg for assistance.',
            ], 403);
        }

        if ($this->storeAccessService->isNeedAccessCode($shop) || $this->storeAccessService->isNeedCACode($seller)) {
            $accessCodeMeta = $this->getAccessCodeMeta($shop, $isSeller);
            return response()->json([
                'success' => false,
                'message' => 'Access code required',
                'data' => $accessCodeMeta
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => __('This store can only be accessed by companies enrolled in YSG’s Corporate Sales Store Employee Benefits Programme Programme. If you are still having difficulty logging in, please contact YSG at cs@ysg.com.sg'),
            ], 403);
        }

        return $next($request);
    }

    private function getAccessCodeMeta($shop, $isSeller)
    {
        if ($this->storeAccessService->isNeedAccessCode($shop)) {
            $title = $shop->access_code_message;
        } else {
            $title = __('You are trying to access '. $shop->name .', this shop required an access code, please fill bellow');
        }

        return [
            'access_code_path'      => $isSeller ? 'shop' : 'product',
            'access_code_required'  => true,
            'id'                    => $shop->user->seller->id,
            'title'                 => $title
        ];
    }
}
