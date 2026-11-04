<?php
namespace App\Http\Controllers\Auth;

use App\{
    BusinessSetting,
    CorporateReferral,
    Coupon,
    User,
    UserCorporateAccess
};
use App\Http\Controllers\Controller;
use App\Mail\{
    ReferralCoupon,
    ReferrerCoupon
};

use App\Services\{
    CorporateAccessService,
    MicrositeService,
    RegisterService,
    UserOtpService,
    UserService
};
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Mail;

class RegisterController extends Controller
{
    /*

    |--------------------------------------------------------------------------

    | Register Controller

    |--------------------------------------------------------------------------

    |

    | This controller handles the registration of new users as well as their

    | validation and creation. By default this controller uses a trait to

    | provide this functionality without requiring any additional code.

    |

    */
    use RegistersUsers;

    protected $microSiteService;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * @param RegisterService
     */
    protected $registerService;

    /**
     * @param UserOtpService
     */
    protected $userOtpService;

    /**
     * @param UserService
     */
    protected $userService;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        MicrositeService $microSiteService,
        RegisterService  $registerService,
        UserOtpService   $userOtpService,
        UserService      $userService
    ) {
        $this->microSiteService = $microSiteService;
        $this->registerService  = $registerService;
        $this->userOtpService   = $userOtpService;
        $this->userService      = $userService;
        $this->middleware('guest');
    }

    private function setSource(User $user) {
        if (isMicrosite()) {
            $site = getMicrosite();
            $this->microSiteService->registerUser($user, $site);
        }
    }

    public function register(Request $request)
    {
        $data = [];
        $userDataAuth = $this->userOtpService->getFirstWithConditions(['original_otp' => $request->otp, 'email' => $request->email, 'user_type' => User::TYPE_CUSTOMER]);
        if (!$userDataAuth) {
            flash(__('The OTP is not valid yet!'))->error();
            return back();
        }
        if (!$userDataAuth->isExpired()) {
            flash(__('The OTP has expired!'))->error();
            return redirect()->route('user.registration');
        }

        $data = $this->userOtpService->formatDataInputsRegister($userDataAuth);

        $user = $this->registerService->createAccount($data);

        if (isset($user)) {
            $this->userOtpService->destroy($userDataAuth->id);
        }

        $referralCode = $this->registerService->activeByReferralCode($data, $user);
        $this->setSource($user);

        $this->guard()->login($user);
        if (!$user->email) {
            return $this->registered($data, $user) ?: redirect($this->redirectPath());
        }

        $ref = $data['ref'];

        if (isset($ref)) {
            $ref = urldecode($ref);
            $CAService = app()->make(CorporateAccessService::class);
            if ($CAService->isCAToken($ref)) {
                $validateResult = $CAService->checkTokenKey($ref);
                if ($validateResult['status'] != false) {
                    $CAService->useTokenKey($user, $ref);
                }
            } else {
                $referrerData = base64_decode($ref);
                $referrerData = json_decode($referrerData, true);
                $referrerId = Arr::get($referrerData, 'referrer');
                $referrer = User::find($referrerId);
                $this->processReferral($request, $user, $referrer);
            }

        }
        if (BusinessSetting::where('type', 'email_verification')->first()->value == 0) {
            $user->email_verified_at = date('Y-m-d H:i:s');
            $user->save();
            auth()->logout();
            flash(__('Your registration is successful.'))->success();

            return $this->registered($data, $user) ?: redirect($this->redirectPath());
        }
        event(new Registered($user));
        auth()->logout();
        if (isset($data['referral_by'])) {
            if ($referralCode) {
                flash(__('Referral code entered correctly! <br> Please confirm your registration using the verification link sent to your email account.'))->success();
            } else {
                flash(__('Referral code entered incorrectly! <br> Please confirm your registration using the verification link sent to your email account.'))->success();
            }
        } else {
            flash(__('Please confirm your registration using the verification link sent to your email account.'))->success();
        }

        return $this->registered($data, $user) ?: redirect($this->redirectPath());
    }

    protected function registered($data, $user)
    {
        if ($user->email == null) {
            return redirect()->route('verification');
        } else {
            return redirect(isMicrosite() ? route('microsite.index', getMicrositeShop()->slug) : route('home'));
        }
    }

    private function processCorporateReferral(Request $request, User $user, string $referralDomain)
    {
        $referralEnabled = BusinessSetting::where('type', 'corporate_referral_enabled')->first();
        if (!$referralEnabled || !$referralEnabled->value) {
            flash(__('Corporate access sharing is disabled at the moment!'))->message();

            return false;
        }
        $corporateRefSetting = CorporateReferral::where('domain', $referralDomain)->first();
        if (!$corporateRefSetting) {
            flash(__('Corporate access sharing for this domain is not available!'))->message();

            return false;
        }
        $shareLimit = $corporateRefSetting->share_limit;
        $shareCount = $corporateRefSetting->share_count;
        if ($shareCount >= $shareLimit) {
            flash(__('Corporate access sharing for this domain has reached the limit!'))->message();

            return false;
        }
        $expireAfter = $corporateRefSetting->expire_after;
        $expireUnit = $corporateRefSetting->expire_time_unit;
        switch ($expireUnit) {
            case 'minute':
                $duration = $expireAfter * 60;
                break;
            case 'hour':
                $duration = $expireAfter * 3600;
                break;
            case 'day':
                $duration = $expireAfter * 86400;
                break;
            case 'default': // Case "second"
                $duration = $expireAfter;
                break;
        }
        $userCorporateAccess = new UserCorporateAccess();
        $userCorporateAccess->user_id = $user->id;
        $userCorporateAccess->domain = $referralDomain;
        $userCorporateAccess->duration = $duration;
        $userCorporateAccess->save();
        $corporateRefSetting->share_count += 1;
        $corporateRefSetting->save();
        // Email not enought info and ugly display, so disable for now
//        Mail::to($user)->send(new CorporateAccessSharing($user));
    }

    /**
     * @param Request $request
     * @param User $user
     * @param User $referrer
     * @return boolean|Application|RedirectResponse|Redirector
     */
    private function processReferral(Request $request, User $user, User $referrer)
    {
        $referralEnabled = BusinessSetting::where('type', 'referral_enabled')->first();
        if (!$referralEnabled || !$referralEnabled->value) {
            flash(__('The referral programme is temporary unavailable.'))->message();

            return false;
        }
        $recipientEnabled = BusinessSetting::where('type', 'referral_recipient_enabled')->first();
        $senderEnabled = BusinessSetting::where('type', 'referral_sender_enabled')->first();
        $recipientEnabled = $recipientEnabled ? $recipientEnabled->value : false;
        $senderEnabled = $senderEnabled ? $senderEnabled->value : false;
        $referralCouponSetting = BusinessSetting::where('type', 'referral_coupon_id')->first();
        if (!$referralCouponSetting || !$referralCouponSetting->value) {
            flash(__('Coupon setting'))->error();

            return $this->registered($request, $user) ?: redirect($this->redirectPath());
        }
        if (!$referrer) {
            flash(__('Referrer does not exist'))->error();

            return $this->registered($request, $user) ?: redirect($this->redirectPath());
        }
        $referralCoupon = Coupon::find($referralCouponSetting->value);
        if ($recipientEnabled) {
            Mail::to($user)->send(new ReferralCoupon($referralCoupon, $user));
        }
        $referrerCouponSetting = BusinessSetting::where('type', 'referrer_coupon_id')->first();
        if ($referrerCouponSetting && $referrerCouponSetting->value && $senderEnabled) {
            $referrerCoupon = Coupon::find($referrerCouponSetting->value);
            Mail::to($referrer)->send(new ReferrerCoupon($referrerCoupon, $referrer));
        }
    }
}

