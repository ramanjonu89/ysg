<?php
namespace App\Services\CorporateAccess;

use App\Repositories\CorporateReferralRepository;
use App\Repositories\UserCorporateAccessRepository;
use App\User;

class ShareService
{
    protected $userService;
    protected $tokenService;

    protected $corporateReferralRepo;
    protected $userCorporateAccessRepo;

    public function __construct(
        CorporateReferralRepository $corporateReferralRepo,
        UserCorporateAccessRepository $userCorporateAccessRepo,

        UserService $userService,
        TokenService $tokenService
    )
    {
        $this->corporateReferralRepo = $corporateReferralRepo;
        $this->userCorporateAccessRepo = $userCorporateAccessRepo;

        $this->userService = $userService;
        $this->tokenService = $tokenService;
    }

    /**
     * @param  User  $user
     * @return bool
     */
    public function canShare(User $user): bool
    {
        return (bool)$this->userService->getRootShops($user)->count();
    }

    /**
     * Get share limit
     * @param  User  $user
     * @param  int  $default
     * @return int
     */
    public function getUserShareLimit(User $user, int $default): int
    {
        $model = $this->corporateReferralRepo->getByDomain($user->domain);
        if ($model) {
            return $model->share_limit;
        }
        return $default;
    }

    /**
     * Get nums of share
     * @param  User  $user
     * @return int
     */
    public function getUserShareTimes(User $user): int
    {
        return $this->userCorporateAccessRepo->getUserShareTimes($user);
    }

    /**
     * Get nums of share left
     * @param  User  $user
     * @param  int  $default
     * @return int
     */
    public function getUserShareTimesLeft(User $user, int $default):int
    {
        return $this->getUserShareLimit($user, $default) - $this->getUserShareTimes($user);
    }

    /**
     * @param  User  $user
     * @param  string  $recipientEmail
     * @return string
     */
    public function genShareLink(User $user, string $recipientEmail): string
    {
        $token = $this->tokenService->resign($user, $recipientEmail);
        if ($token) {
            return route('corporateAccess.active.token', $token);
        }

        return route('dashboard');
    }

    /**
     * @param  User  $user
     * @param  string  $recipientEmail
     * @return string
     */
    public function genRegistrationLink(User $user, string $recipientEmail): string
    {
        $token = $this->tokenService->resign($user, $recipientEmail);
        if ($token) {
            return route('user.registration', ['ref' => $token, 'referral_register_code' => $user->referral_code_register]);
        }
        return route('user.registration');
    }
}