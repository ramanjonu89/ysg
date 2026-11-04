<?php
namespace App\Services;

use App\Models\Product;
use App\Models\Seller;
use App\Models\Shop;
use App\Models\UserCorporateAccess;
use App\Repositories\BusinessSettingRepository;
use App\Repositories\CorporateAccessTokenRepository;
use App\Repositories\UserCorporateAccessRepository;
use App\Services\CorporateAccess\ShareService;
use App\Services\CorporateAccess\TokenService;
use App\Services\CorporateAccess\UserService;
use App\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class CorporateAccessService
{
    const DEF_SHARE_LIMIT = 5;
    const DEF_SHARE_ACCESS_DURATION = 86400;
    const DEF_SHARE_ACCESS_DURATION_TEXT = '24 hour';
    const SESSION_KEY = 'ca_code';

    protected $user;

    protected $userCorporateAccessRepo;
    protected $corporateAccessTokenRepo;
    protected $businessSettingRepository;

    protected $tokenService;
    protected $userService;
    protected $shareService;

    public function __construct(
        UserCorporateAccessRepository $userCorporateAccessRepo,
        CorporateAccessTokenRepository $corporateAccessTokenRepo,
        BusinessSettingRepository $businessSettingRepo,
        UserService $userService,
        TokenService $tokenService,
        ShareService $shareService
    )
    {
        $this->userCorporateAccessRepo = $userCorporateAccessRepo;
        $this->corporateAccessTokenRepo = $corporateAccessTokenRepo;
        $this->businessSettingRepository = $businessSettingRepo;
        $this->userService = $userService;
        $this->tokenService = $tokenService;
        $this->shareService = $shareService;
    }

    public function setUser(User $user) {
        $this->user = $user;
    }

    /**
     * @return bool
     */
    public function isSettingEnable(): bool
    {
        return $this->businessSettingRepository->isEnableCA();
    }

    /**
     * @param  string  $tokenStr
     * @return bool
     */
    public function isCAToken(string $tokenStr): bool
    {
        return $this->tokenService->isCAToken($tokenStr);
    }

    /**
     * @param  string  $tokenStr
     * @param  string|null  $recipientEmail
     * @return array
     */
    public function checkTokenKey(string $tokenStr, string $recipientEmail = null): array
    {
        $result = [
            'status' => true,
        ];

        if ($this->isSettingEnable() === false) {
            return [
                'status' => false,
                'message' => __('The referral programme is temporary unavailable.'),
            ];
        }

        if ($this->isValidToken($tokenStr, $recipientEmail)) {
            $rootUser = $this->getRootUserFromTokenKey($tokenStr);
            if ($this->getUserShareTimesLeft($rootUser) <= 0) {
                return [
                    'status' => false,
                    'message' => __('Oops: You\'ve exceeded your Corporate Sale Store Shared Access Limit.'),
                ];
            }
        } else {
            return [
                'status' => false,
                'message' => __('The referral link is invalid. Please contact cs@ysg.com.sg for assistance.'),

            ];
        }
        return $result;
    }

    /**
     * @param  string  $token
     * @return null|User
     */
    public function getRootUserFromTokenKey(string $token): ?User
    {
        return $this->tokenService->getRootUserFromTokenKey($token);
    }

    /**
     * @param  Seller  $seller
     * @return bool
     */
    public function isShopSettingEnable(Seller $seller): bool
    {
        return $seller->isActiveCA;
    }

    /**
     * Get list shop allow CA for user's email domain
     * @param  User  $user
     * @return Collection
     */
    public function getRootShops(User $user): Collection
    {
        return $this->userService->getRootShops($user);
    }

    /**
     * @param  User  $user
     * @return Collection
     */
    public function getSharedShops(User $user): Collection
    {
        return $this->userService->getSharedShops($user);
    }

    /**
     * @param  User  $user
     * @return Collection
     */
    public function getActivatedShops(User $user): Collection
    {
        return $this->userService->getActivatedShops($user);
    }

    /**
     * Get list of shop was shared by other user
     * @param  User  $user
     * @return Collection
     */
    public function getCAList(User $user): Collection
    {
        return $this->userService->getCAList($user);
    }

    /**
     * @param  User  $user
     * @return Collection
     */
    public function getActivatedCAList(User $user): Collection
    {
        return $this->userService->getActivatedCAList($user);
    }

    /**
     * Check if user has any CorporateAccess that can be use
     * @param  User  $user
     * @return bool
     */
    public function hasAny(User $user): bool
    {
        return $this->userService->hasAny($user);
    }

    /**
     * Check if user can access CSS page
     * @param  User  $user
     * @return bool
     */
    public function canViewCSSPage(User $user): bool
    {
        return $this->hasAny($user);
    }

    /**
     * Check if user has permission to access shop page
     * @param  User  $user
     * @param  Seller  $seller
     * @return bool
     */
    public function canViewShop(User $user, Seller $seller): bool
    {
        $rootShops = $this->getRootShops($user)->keyBy('id');
        $activatedShops = $this->getActivatedShops($user)->keyBy('id');
        $allowShops = $rootShops->union($activatedShops);
        $ids = $allowShops->pluck('id')->toArray();
        return in_array($seller->id, $ids);
    }

    /**
     * Check if user has permission to view product
     * @param  User  $user
     * @param  Product  $product
     * @return bool
     */
    public function canViewProduct(User $user, Product $product): bool
    {
        $seller = $product->user->seller;
        return $this->canViewShop($user, $seller);
    }

    /**
     * @param  User  $user
     * @return bool
     */
    public function canShare(User $user): bool
    {
        return $this->shareService->canShare($user);
    }

    /**
     * Get share limit
     * @param  User  $user
     * @return int
     */
    public function getUserShareLimit(User $user): int
    {
        return $this->shareService->getUserShareLimit($user, self::DEF_SHARE_LIMIT);
    }

    /**
     * Get nums of share
     * @param  User  $user
     * @return int
     */
    public function getUserShareTimes(User $user): int
    {
        return $this->shareService->getUserShareTimes($user);
    }

    /**
     * Get nums of share left
     * @param  User  $user
     * @return int
     */
    public function getUserShareTimesLeft(User $user):int
    {
        return $this->shareService->getUserShareTimesLeft($user, self::DEF_SHARE_LIMIT);
    }

    /**
     * @param  User  $user
     * @param  string  $recipientEmail
     * @return string
     */
    public function genShareLink(User $user, string $recipientEmail): string
    {
        return $this->shareService->genShareLink($user, $recipientEmail);
    }

    /**
     * @param  User  $user
     * @param  string  $recipientEmail
     * @return string
     */
    public function genRegistrationLink(User $user, string $recipientEmail): string
    {
        return $this->shareService->genRegistrationLink($user, $recipientEmail);
    }

    /**
     * @param  User  $user
     * @param  string  $recipientEmail
     * @return false|string
     */
    public function resignToken(User $user, string $recipientEmail) {
        return $this->tokenService->resign($user, $recipientEmail);
    }

    /**
     * @param  string  $token
     * @param  string|null  $recipientEmail
     * @return bool
     */
    public function isValidToken(string $token, string $recipientEmail = null): bool
    {
        return $this->tokenService->isValid($token, $recipientEmail);
    }

    /**
     * @param  User  $user
     * @param  string  $tokenStr
     * @return bool
     */
    public function useTokenKey(User $user, string $tokenStr): bool
    {
        return $this->tokenService->useTokenKey($user, $tokenStr, self::DEF_SHARE_ACCESS_DURATION);
    }

    /**
     * @param  User  $user
     * @param  UserCorporateAccess  $access
     * @return array
     */
    public function activeCA(User $user, UserCorporateAccess $access): array
    {
        $result = [
            'status' => false,
            'message' => __('Have error when active corporate access!')
        ];

        if ($access->user_id == $user->id) {
            if ($access->is_active) {
                $result = [
                    'status' => false,
                    'message' => __('Your corporate access has been activated already!')
                ];
            } else if ($this->getActivatedCAList($user)->count()) {
                $result = [
                    'status' => false,
                    'message' => __('You can only activate one corporate access at a time!')
                ];
            } else {
                $expireAt = Carbon::now()->addSeconds($access->duration);
                $data = [
                    'is_active' => true,
                    'expire_at' => $expireAt,
                ];
                $status = $this->userCorporateAccessRepo->update($access->id, $data);
                if ($status) {
                    $result = [
                        'status' => true,
                        'message' => __('Corporate Access Activated Successfully!'),
                        'expireAt' => $expireAt->format('Y/m/d H:i:s'),
                    ];
                }
            }
        }

        return $result;
    }

    public function useCode(Shop $shop, User $user, string $code): array
    {
        $result = [
            'status' => false,
            'message' => __('Have error when active corporate access code!')
        ];

        $seller = $shop->seller;
        if ($code && $seller->corporate_access_code && $seller->corporate_access_code == $code) {
            session([
                self::SESSION_KEY . '_' . $shop->id => $code
            ]);
            $result = [
                'status' => true,
                'message' => __('Active corporate access code success!')
            ];
        }
        return $result;
    }

    /**
     * Return list of sellers
     * @param  User  $user
     * @return Collection
     */
    public function getAllAllowShops(User $user): Collection
    {
        return $this->userService->getAllAllowShops($user);
    }
}