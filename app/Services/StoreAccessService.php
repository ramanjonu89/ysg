<?php

namespace App\Services;

use Auth;
use App\Models\Shop;
use App\Models\Seller;
use App\User;
use Carbon\Carbon;

class StoreAccessService
{
    const ACCESS_KEY = 'ac_code';

    /**
     * @param Shop
     */
    protected $shop;

    /**
     * @param ShopService
     */
    protected $shopService;

    /**
     * @param CorporateAccessService
     */
    protected $corporateAccessService;

    public function __construct(Shop $shop, ShopService $shopService, CorporateAccessService $corporateAccessService)
    {
        $this->shop = $shop;
        $this->shopService = $shopService;
        $this->corporateAccessService = $corporateAccessService;
    }

    public function grantAccessByCode(Shop $shop, User $customer, string $code): array
    {
        $result = [
            'status' => false,
            'message' => __('Have error when active code!')
        ];

        try {
            if ($this->isNeedAccessCode($shop)) {
                $result = $this->useCode($shop, $customer, $code);
            }

            if ($result['status'] == false || $this->isNeedCACode($shop->seller)) {
                $result = $this->corporateAccessService->useCode($shop, $customer, $code);
            }

        } catch (\Exception $e) {

        }

        return $result;
    }

    public function useCode(Shop $shop, User $user, string $code): array
    {
        $result = [
            'status' => false,
            'message' => __('Have error when active access code!')
        ];
        if ($code && $shop->access_code && (strval($shop->user->id == "2843") ? strtolower($code) == strtolower($shop->access_code) : ($code == $shop->access_code))) {
            session([
                self::ACCESS_KEY . '_' . $shop->id => $code
            ]);
            $result = [
                'status' => true,
                'message' => __('Active access code success!')
            ];
        }
        return $result;
    }

    /**
     * @param  Shop  $shop
     * @param  User|null  $customer
     * @return bool
     */
    public function hasAccess(Shop $shop, ?User $customer): bool
    {
        if (!$this->shopService->isPrivate($shop)) {
            return true;
        }

        if ($customer == null) { // need to sign in first
            return false;
        }

        $seller = $shop->seller;

        if ($this->checkAccessCode($shop, $customer)) {
            return true;
        }

        if ($this->checkCACode($shop, $customer)) {
            return true;
        }

        if ($this->checkCADomain($seller, $customer)) {
            return true;
        }

        return false;
    }

    public function checkAccessCode(Shop $shop, ?User $customer): bool
    {
        if ($this->isNeedAccessCode($shop)) {
            $code = $shop->access_code;
            $acCode = session(self::ACCESS_KEY . '_' . $shop->id);
            return strval($shop->user->id == "2843") ? strtolower($code) == strtolower($acCode) : ($code == $acCode);
        }
        return false;
    }

    public function checkCADomain(Seller $seller, ?User $customer): bool
    {
        if ($this->isNeedCADomain($seller)) {
            $sellerIDs = $this->corporateAccessService->getAllAllowShops($customer)->pluck('id')->toArray();
            if (in_array($seller->id, $sellerIDs)) {
                return true;
            }
        }
        return false;
    }

    public function checkCACode(Shop $shop, ?User $customer): bool
    {
        $seller = $shop->seller;
        if ($this->isNeedCACode($seller)) {
            $code = $seller->corporate_access_code;
            $caCode = session(CorporateAccessService::SESSION_KEY . '_' . $shop->id);
            return ($code == $caCode);
        }
        return false;
    }

    public function isNeedCADomain(Seller $seller): bool
    {
        return $seller->enable_corporate_domains;
    }

    public function isNeedCACode(Seller $seller): bool
    {
        return $seller->active_corporate_access_code;
    }

    public function isNeedAccessCode(Shop $shop): bool
    {
        return $shop->enable_access_code && ($shop->access_code != null);
    }

    public function isPendingEvent(Shop $shop): bool
    {
        $startDate = Carbon::parse($shop->user->company_start_date)->startOfDay();
        $now = Carbon::now();
        return $this->shopService->isPrivate($shop) && $startDate->gt($now);
    }

    public function isExpiredEvent(Shop $shop): bool
    {
        $endDate = Carbon::parse($shop->user->company_end_date)->endOfDay();
        $now = Carbon::now();
        return $this->shopService->isPrivate($shop) && $now->gt($endDate);
    }

    public function isNeedDomain(Shop $shop): bool
    {

        $isValidDomain = $this->validateCorporateDomain($shop);

        return $shop->user->enable_corporate_domains === 1 && !$isValidDomain;
    }

    public function validateCorporateDomain(Shop $shop)
    {
        $seller = Seller::where('user_id', $shop->user_id)->first();
        if ($seller->user->company_type !== User::COMPANY_TYPE_PRIVATE_EVENT || $seller->user->enable_corporate_domains !== 1) {
            return false;
        }
        if ($seller->corporate_domains()->count() === 0) {
            return true;
        }
        $user = Auth::user();
        if (!$user) {
            return false;
        }
        $emailDomain = substr($user->email, strpos($user->email, "@"));
        $activeCorporateAccess = $user->getActiveCorporateAccess();
        if (
            $activeCorporateAccess &&
            $activeCorporateAccess->expire_at &&
            $activeCorporateAccess->expire_at->greaterThan(Carbon::now())
        ) {
            $emailDomain = $activeCorporateAccess->domain;
        }
        $corporateDomains = $seller->corporate_domains()->select('domain')->pluck('domain');
        if (!$corporateDomains->contains($emailDomain)) {
            return false;
        }

        return true;
    }

    /**
     * @param  User|null  $customer
     * @param  Shop  $shop
     * @return bool
     */
    public function validateCorporateAccessCode(?User $customer, Shop $shop): bool
    {
        if ($customer) {
            $seller = $shop->user->seller;
            if ($seller->user->company_type !== User::COMPANY_TYPE_PRIVATE_EVENT || !$seller->active_corporate_access_code) {
                return false;
            }

            $sellerCode = $seller->corporate_access_code;

            $corporateAccesses = $customer->corporateAccesses->where('is_active', true);
            foreach ($corporateAccesses as $item) {
                if ($item->code == $sellerCode) {
                    return true;
                }
            }
        }

        return false;
    }

    public function generateAccessToken($shop, $customer)
    {
        // $expiresAt = now()->addHours(1)->timestamp;
        $expiresAt = now()->addMinutes(5)->timestamp;

        $payload = json_encode([
            'shop_id' => $shop->id,
            'customer_id' => $customer->id,
            'expires_at' => $expiresAt
        ]);

        $base64Payload = base64_encode($payload);
        $signature = hash_hmac('sha256', $base64Payload, config('app.key'));

        return [
            'token' => $base64Payload . '.' . $signature,
            'expires_at' => date('Y-m-d H:i:s', $expiresAt)
        ];
    }

    public function validateAccessToken($shop, $customer, $token)
    {
        if (!str_contains($token, '.')) return false;

        [$base64Payload, $signature] = explode('.', $token, 2);

        $expected = hash_hmac('sha256', $base64Payload, config('app.key'));

        if (!hash_equals($expected, $signature)) {
            return false;
        }

        $payload = json_decode(base64_decode($base64Payload), true);

        if (!$payload) {
            return false;
        }

        if ($payload['shop_id'] != $shop->id || $payload['customer_id'] != $customer->id) {
            return false;
        }

        if ($payload['expires_at'] < time()) {
            return false;
        }

        return $payload;
    }

}