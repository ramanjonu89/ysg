<?php
namespace App\Services\CorporateAccess;

use App\BusinessSetting;
use App\Models\CorporateAccessToken;
use App\Repositories\CorporateAccessTokenRepository;
use App\Repositories\UserCorporateAccessRepository;
use App\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class TokenService
{
    protected $userCorporateAccessRepo;
    protected $corporateAccessTokenRepo;

    public function __construct(
        UserCorporateAccessRepository $userCorporateAccessRepo,
        CorporateAccessTokenRepository $corporateAccessTokenRepo
    )
    {
        $this->userCorporateAccessRepo = $userCorporateAccessRepo;
        $this->corporateAccessTokenRepo = $corporateAccessTokenRepo;
    }

    /**
     * @param  string  $key
     * @return bool
     */
    public function isCAToken(string $key): bool
    {
        $referrerData = base64_decode($key);
        $referrerData = json_decode($referrerData, true);
        $referralType = Arr::get($referrerData, 'type');
        if ($referralType != BusinessSetting::REFERRAL_TYPE_CORPORATE) {
            return false;
        }
        return true;
    }

    /**
     * @param  User  $user
     * @param  string  $token
     * @return bool|CorporateAccessToken
     */
    public function active(User $user, string $token)
    {
        try {
            $token = $this->corporateAccessTokenRepo->getByToken($token);
            if ($token) {
                $params = [
                    'activated_by' => $user->id,
                    'activated_at' => Carbon::now()->addDay()
                ];
                $this->corporateAccessTokenRepo->update($token->token, $params);
                return $token;
            }

        } catch (\Exception $e) {
            return false;
        }
        return false;
    }

    /**
     * @param  User  $user
     * @param  string  $recipientEmail
     * @return false|string
     */
    public function resign(User $user, string $recipientEmail) {
        try {
            $token = $this->corporateAccessTokenRepo->genToken($user->domain, $recipientEmail, $user->id);
        } catch (\Exception $e) {
            return false;
        }
        if ($token !== '') {
            $data = [
                'token' => $token,
                'created_by' => $user->id,
            ];
            if ($this->corporateAccessTokenRepo->create($data)) {
                return $token;
            }
        }
        return false;
    }

    /**
     * @param  string  $token
     * @param  string|null  $recipientEmail
     * @return bool
     */
    public function isValid(string $token, string $recipientEmail = null): bool
    {
        $token = $this->corporateAccessTokenRepo->getByToken($token);
        if ($token && $token->activated_at === null) {
            if ($recipientEmail) {
                $tokenData = $this->decodeToken($token->token);
                if (isset($tokenData['email']) && $tokenData['email'] == $recipientEmail) {
                    return true;
                }
            } else return true;
        }
        return false;
    }

    public function decodeToken(string $token) {
        $json = base64_decode($token);
        return json_decode($json, true);
    }

    /**
     * @param  string  $token
     * @return null|User
     */
    public function getRootUserFromTokenKey(string $token): ?User
    {
        $decodeData = $this->decodeToken($token);
        if (isset($decodeData['referrer'])) {
            return User::find($decodeData['referrer']);
        }
        return null;
    }

    /**
     * @param  User  $user
     * @param  string  $tokenStr
     * @param  int  $duration
     * @return bool
     */
    public function useTokenKey(User $user, string $tokenStr, int $duration): bool
    {
        if ($this->isValid($tokenStr)) {
            $token = $this->active($user, $tokenStr);
            if ($token !== false) {
                $rootUser = $token->createBy;
                $data = [
                    'user_id' => $user->id,
                    'user_level' => 2,
                    'parent_id' => $rootUser->id,
                    'domain' => $rootUser->domain,
                    'duration' => $duration
                ];
                $status = $this->userCorporateAccessRepo->create($data);
                if ($status) {
                    return true;
                }
            }
        }
        return false;
    }
}