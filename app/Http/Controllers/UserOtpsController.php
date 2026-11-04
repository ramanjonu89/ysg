<?php

namespace App\Http\Controllers;

use App\User;
use Exception;
use Carbon\Carbon;
use App\Models\{Address, UserOtp,
    BusinessSetting
};
use App\Mail\{SendOTPToMail,
    ReferralCoupon,
    ReferrerCoupon
};
use App\Notifications\EmailVerificationNotification;
use Illuminate\Support\Arr;
use App\Services\{
    CorporateAccessService,
    CouponService,
    MicrositeService,
    RegisterService,
    UserOtpService,
    UserService,
    SellerService,
    ShopService
};
use App\Support\PasswordPolicy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class UserOtpsController extends Controller
{
    /**
     * @param CouponService
     */
    protected $couponService;

    /**
     * @param MicrositeService
     */
    protected $microSiteService;

    /**
     * @param RegisterService
     */
    protected $registerService;

    /**
     * @param SellerService
     */
    protected $sellerService;

    /**
     * @param ShopService
     */
    protected $shopService;

    /**
     * @param UserService
     */
    protected $userService;

    /**
     * @param UserOtpService
     */
    protected $userOtpService;

    public function __construct(
        CouponService    $couponService,
        MicrositeService $microSiteService,
        SellerService    $sellerService,
        ShopService      $shopService,
        RegisterService  $registerService,
        UserService      $userService,
        UserOtpService   $userOtpService
    ) {
        $this->couponService    = $couponService;
        $this->microSiteService = $microSiteService;
        $this->registerService  = $registerService;
        $this->sellerService    = $sellerService;
        $this->shopService      = $shopService;
        $this->userService      = $userService;
        $this->userOtpService   = $userOtpService;
    }

    public function getListUsersAwaitingVerification(Request $request)
    {
        $keyword = $request->get('keyword', '');
        $params = [
            'keyword' => $keyword,
            'count' => UserOtp::SEND_LIMIT_EXCEEDED,
            'perPage' => 45
        ];
        $usersAwaitingVerify = $this->userOtpService->getList($params);
        return view('user_otp.index', compact('keyword', 'usersAwaitingVerify'));
    }

    public function createUserOtp(Request $request)
    {
        $this->validator($request->all())->validate();
        try {
            $userType = $request->get('type', '');
            $otpUserType = $userType === User::TYPE_SELLER ? User::TYPE_SELLER : User::TYPE_CUSTOMER;

            $userOtpExists = $this->userOtpService->getFirstWithConditions(['email' => $request->email, 'user_type' => $otpUserType]);
            $emailExists = $this->userService->getFirstWithConditions(['email' => $request->email]);

            if (isset($userOtpExists) && $userOtpExists->count === UserOtp::SEND_LIMIT_EXCEEDED) {
                flash(__('OTP has been sent 3 times. If you still haven\'t received it. Please contact your administrator for support.'))->warning();
                return back();
            }

            if ($userType === User::TYPE_SELLER && $emailExists){
                flash(__('The email already exists!'))->error();
                return back();
            }

            if (isset($userOtpExists)) {
                $this->resendOtp($request);
            } else {
                DB::beginTransaction();

                $data = $this->formatDataInputs($request);
                $userOtp = $this->userOtpService->create($data);
                $this->sendOTPToMail($userOtp);

                DB::commit();
            }
            flash(__('Please check your email to receive the OTP code'))->success();
            return redirect()->route('user_otps.form_access_otp', ['user_type' => $userType, 'email' => $request->email]);
        } catch (Exception $e) {
            DB::rollBack();
            flash('Sorry! Something went wrong')->error();
            return back();
        }
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        if (isset($data['type'])) {
            return Validator::make(
                $data, [
                    'name' => 'required|max:20',
                    'company_name' => 'required|max:20',
                    'country_code' => 'nullable|string',
                    'phone' => 'required|size:8|regex:/^[689]\d{7}$/',
                    'email' => 'email:rfc,dns'
                ],
                [
                    'phone.size' => __('The phone number must be 8 digits.'),
                    'phone.regex' => __('The phone number must start with 6, 8, or 9'),
                ]
            );
        };

        return Validator::make(
            $data,
            [
                'name'          => 'required|string|max:20',
                'email'         => 'email:rfc,dns|unique:App\User,email',
                'password' => [
                    'required',
                    'string',
                    'min:8',
                    'confirmed',
                    'regex:'. PasswordPolicy::regex(),
                ],
                'country_code'  => 'nullable|string',
                'phone'         => 'required|string',
            ],
            [
                'phone.required' => __('The phone field is required.'),
                'phone.regex' => __('The phone number must be between 4 and 15 digits.'),
                'password.required' => __('The password field is required.'),
                'password.regex' => __('The password must contain at least one uppercase letter, one lowercase letter, and one number.'),
            ]
        )->after(function ($validator) use ($data) {
            $countryCode = $data['country_code'] ?? null;
            $phone       = $data['phone'] ?? null;
            if (!$phone) {
                return;
            }

            if ($countryCode == Address::COUNTRY_CODE_SG) {
                if (!preg_match('/^[689]\d{7}$/', $phone)) {
                    $validator->errors()->add(
                        'phone',
                        __('For Singapore (+65), the phone number must be 8 digits and start with 6, 8, or 9.')
                    );
                }
            } else {
                if (!preg_match('/^[0-9]{4,15}$/', $phone)) {
                    $validator->errors()->add(
                        'phone',
                        __('The phone number must be between 4 and 15 digits.'));
                    return;
                }
            }
        });
    }

    Private function formatDataInputs(Request $request) {
        $data = [];
        $date_expired = Carbon::now()->addMinutes(3)->format('Y-m-d H:i:s');

        $original_otp = sprintf("%06d", rand(100000, 999999));

        $data['email'] = $request->email;
        $data['username'] = $request->name;
        $data['original_otp'] = $original_otp;
        $data['user_type'] = isset($request->type) ? User::TYPE_SELLER : User::TYPE_CUSTOMER;
        $data['expired'] = Carbon::createFromFormat('Y-m-d H:i:s', $date_expired)->timestamp;

        $dataRequest = $request->except('_token', 'g-recaptcha-response', 'password_confirmation');
        $dataRequest['password'] = Hash::make($dataRequest['password']);
        $data['user_details'] = json_encode($dataRequest);

        return $data;
    }

    public function formAuthenticationOtp(Request $request)
    {
        $email = $request->email;
        $userType = $request->get('user_type', User::TYPE_CUSTOMER);
        if (!$userType) {
            $userType = User::TYPE_CUSTOMER;
        }
        $userOtp = $this->userOtpService->getFirstWithConditions(['email' => $email, 'user_type' => $userType]);
        $countSend = $userOtp ? $userOtp->count : 0;
        return view('frontend.form_authentication_otp', compact('email', 'userOtp', 'countSend'));
    }

    public function countSendOtp(Request $request)
    {
        $email = $request->email;
        $userType = $request->get('user_type', User::TYPE_CUSTOMER);
        if (!$userType) {
            $userType = User::TYPE_CUSTOMER;
        }
        $userAwaitingVerify = $this->userOtpService->getFirstWithConditions(['email' => $email, 'user_type' => $userType]);
        return array(
            'count_send_otp' => $userAwaitingVerify->count,
        );
    }

    public function resendOtp(Request $request)
    {
        try {
            $response = [];
            $email = $request->email;
            $userType = $request->get('user_type', User::TYPE_CUSTOMER);
            if (!$userType) {
                $userType = User::TYPE_CUSTOMER;
            }
            $userOtp = $this->userOtpService->getFirstWithConditions(['email' => $email, 'user_type' => $userType]);
            $countSendOtp = $userOtp ? $userOtp->count : 0;
            if ($countSendOtp !== UserOtp::SEND_LIMIT_EXCEEDED) {
                $countSendOtp += 1;
                $data = [
                    'count' => $countSendOtp,
                    'original_otp' => sprintf("%06d", rand(100000, 999999)),
                    'expired' => Carbon::createFromFormat('Y-m-d H:i:s', Carbon::now()->addMinutes(3)->format('Y-m-d H:i:s'))->timestamp
                ];
                $userOtp = $this->userOtpService->update($userOtp->id, $data);

                $this->sendOTPToMail($userOtp);
                $response = [
                    'count'  => $countSendOtp,
                    'message' => 'Please check your email to receive the OTP code'
                ];
            }
        } catch (\Throwable $th) {
            $response = [
                'message' => 'Sorry! Something went wrong'
            ];
        }

        return json_encode($response);
    }

    public function registrationByAdmin(Request $request) {
        $data = $this->userOtpService->formatDataInputsRegister($request);
        $otpUserType = $request->user_type === User::TYPE_SELLER ? User::TYPE_SELLER : User::TYPE_CUSTOMER;
        $userOtp = $this->userOtpService->getFirstWithConditions(['original_otp' => $request->original_otp, 'email' => $data['email'], 'user_type' => $otpUserType]);
        if ($request->user_type === User::TYPE_SELLER) {
            // insert seller
            try {
                DB::beginTransaction();
                if ($data['password'] !== $data['password_confirmation']) {
                    flash(__('Sorry! Password did not match.'))->error();
                    return back();
                }
                $dataUser['name']      = $data['name'];
                $dataUser['email']     = $data['email'];
                $dataUser['phone']     = $data['phone'];
                $dataUser['user_type'] = User::TYPE_CUSTOMER;
                $dataUser['password']  = $data['password'];
                $user = $this->userService->create($dataUser);

                $shopExists = $this->shopService->getFirstWithConditions(['user_id' => $user->id]);
                if ($shopExists) {
                    flash(__('The Shop already exists!'))->error();
                    return back();
                }

                $dataShop['user_id'] = $user->id;
                $dataShop['name']    = $data['name'];
                $shop = $this->shopService->create($dataShop);
                $dataShopUpdate['slug'] = preg_replace('/\s+/', '-', $data['name']) . '-' . $shop->id;
                $shop = $this->shopService->update($shop->id, $dataShopUpdate);
                if (BusinessSetting::where('type', 'email_verification')->first()->value != 1) {
                    $user->email_verified_at = date('Y-m-d H:i:s');
                    $user->save();
                }
                try {
                    $this->shopService->sendMailToManagerSellerRegistration($data);
                } catch (\Exception $e) {
                    Log::error($e->getMessage());
                }
                // update type seller
                $dataUser['user_type'] = User::TYPE_SELLER;
                $this->userService->update($user->id, $dataUser);

                // insert seller
                $dataSeller['user_id'] = $user->id;
                $this->sellerService->create($dataSeller);

                if (isset($shop) && isset($userOtp)) {
                    $this->userOtpService->destroy($userOtp->id);
                }

                DB::commit();
                flash(__('Seller has been inserted successfully'))->success();
                return back();
            } catch (\Throwable $th) {
                DB::rollBack();
                flash(__('Sorry! Something went wrong'))->error();
                return back();
            }
        } else {
            try {
                DB::beginTransaction();
                $user = $this->registerService->createAccount($data);
                $this->registerService->activeByReferralCode($data, $user);
                $this->setSource($user);
                $ref = $data['ref'];
                if ($ref) {
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
                        $this->processReferral($user, $referrer);
                    }

                }
                $user->notify(new EmailVerificationNotification($user));
                if (BusinessSetting::where('type', 'email_verification')->first()->value == 0) {
                    $user->email_verified_at = date('Y-m-d H:i:s');
                    $user->save();
                }

                if (isset($user) && isset($userOtp)) {
                    $this->userOtpService->destroy($userOtp->id);
                }

                DB::commit();
                flash(__('Customer has been inserted successfully'))->success();
                return back();
            } catch (\Throwable $th) {
                DB::rollBack();
                flash(__('Sorry! Something went wrong'))->error();
                return back();
            }
        }
    }

    private function sendOTPToMail ($userOtp) {
        $array = [
            'subject'   => __('Your OTP to continue the registration in ' . getAppName() . ' !'),
            'from'      => env('MAIL_FROM_ADDRESS'),
            'name'      => $userOtp->username,
            'view'      => 'emails.send_otp_to_mail',
            'content'   => __('Welcome and to ensure this email belongs to you, please use the OTP below to continue with registration!'),
            'original_otp' => $userOtp->original_otp
        ];

        Mail::to($userOtp->email)->send(new SendOTPToMail($array));
    }

    private function setSource(User $user) {
        if (isMicrosite()) {
            $site = getMicrosite();
            $this->microSiteService->registerUser($user, $site);
        }
    }

    private function processReferral(User $user, User $referrer)
    {
        $referralEnabled = BusinessSetting::where('type', 'referral_enabled')->first();
        if (!$referralEnabled || !$referralEnabled->value) {
            return false;
        }
        $recipientEnabled = BusinessSetting::where('type', 'referral_recipient_enabled')->first();
        $senderEnabled = BusinessSetting::where('type', 'referral_sender_enabled')->first();
        $recipientEnabled = $recipientEnabled ? $recipientEnabled->value : false;
        $senderEnabled = $senderEnabled ? $senderEnabled->value : false;
        $referralCouponSetting = BusinessSetting::where('type', 'referral_coupon_id')->first();
        if (!$referralCouponSetting || !$referralCouponSetting->value || !$referrer) {
            return false;
        }
        $referralCoupon = $this->couponService->getFirstWithConditions(['id' => $referralCouponSetting->value]);
        if ($recipientEnabled) {
            Mail::to($user)->send(new ReferralCoupon($referralCoupon, $user));
        }
        $referrerCouponSetting = BusinessSetting::where('type', 'referrer_coupon_id')->first();
        if ($referrerCouponSetting && $referrerCouponSetting->value && $senderEnabled) {
            $referrerCoupon = $this->couponService->getFirstWithConditions(['id' => $referrerCouponSetting->value]);
            Mail::to($referrer)->send(new ReferrerCoupon($referrerCoupon, $referrer));
        }
    }

    public function destroy($id)
    {
        try {
            $this->userOtpService->destroy($id);

            flash(__('Customer has been deleted successfully'))->success();
            return redirect()->route('admin.list_users_awaiting_verify');
        } catch (\Throwable $th) {
            flash(__('Something went wrong'))->error();
            return back();
        }
    }
}
