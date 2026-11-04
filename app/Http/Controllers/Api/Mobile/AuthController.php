<?php
namespace App\Http\Controllers\Api\Mobile;

use App\Customer;
use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Api\Mobile\Users\LoginRequest;
use App\Http\Requests\Api\Mobile\Users\SocialLoginRequest;
use App\Http\Resources\Mobile\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\User;
use Laravel\Socialite\Facades\Socialite;
use App\Models\UserOtp;
use App\Services\{
    MicrositeService,
    RegisterService,
    UserOtpService,
    UserService
};
use App\Mail\SendOTPToMail;
use App\Models\Address;
use App\Notifications\EmailVerificationNotification;
use App\Support\PasswordPolicy;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use App\Models\BusinessSetting;

/**
 * @OA\Info(
 *     title="YSG Mobile API",
 *     version="1.0.0"
 * )
 *
 * @OA\Tag(
 *     name="Auth",
 *     description="Mobile API endpoints for authentication"
 * )
 */
class AuthController extends Controller
{
    protected $microSiteService;
    protected $registerService;
    protected $userOtpService;
    protected $userService;

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
    }
    /**
     * @OA\Post(
     *     path="/api/mobile/v1/auth/login",
     *     operationId="mobileLogin",
     *     tags={"Auth"},
     *     summary="Mobile user login",
     *     description="Authenticate mobile user with email and password",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Login successful"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9..."),
     *                 @OA\Property(
     *                     property="user",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="John Doe"),
     *                     @OA\Property(property="email", type="string", example="user@example.com"),
     *                     @OA\Property(property="country_code", type="string", nullable=true, example="+65"),
     *                     @OA\Property(property="phone", type="string", nullable=true, example="0123456789"),
     *                     @OA\Property(property="avatar", type="string", example="https://example.com/uploads/users/avatar.jpg")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid credentials or email not verified",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Invalid Credentials")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Email not verified",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Email has not been verified. Please check your email to verify your account.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="The email field is required."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function login(LoginRequest $request)
    {
        $authResult = $this->attemptAuth($request);

        if ($authResult === 'email_not_verified') {
            return $this->errorResponse('Email has not been verified. Please check your email to verify your account.', [], 403);
        }

        if (!$authResult) {
            return $this->errorResponse('Invalid Credentials');
        }

        $user = Auth::user();

        if ($user) {
            $data['token'] = Auth::user()->createToken('Mobile')->accessToken;
            $data['user'] = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'country_code' => $user->country_code ?? Address::COUNTRY_CODE_SG,
                'phone' => $user->phone,
                'avatar' => url($user->avatar_original),
            ];
            return $this->successResponse('Login successful', $data);
        }

        return $this->errorResponse('This is user only!!!');
    }

    /**
     * @OA\Get(
     *     path="/api/mobile/v1/auth/logout",
     *     operationId="mobileLogout",
     *     tags={"Auth"},
     *     summary="Mobile user logout",
     *     description="Logout mobile user and revoke access token",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Logout successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Successfully logged out")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     )
     * )
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return $this->successResponse('Successfully logged out');
    }

    /**
     * @OA\Get(
     *     path="/api/mobile/v1/auth/user",
     *     operationId="mobileUser",
     *     tags={"Auth"},
     *     summary="Get current user information",
     *     description="Retrieve authenticated user's profile information with counters and corporate access data",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="User information retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="User information retrieved"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="user",
     *                     type="object",
     *                     description="User basic information",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="John Doe"),
     *                     @OA\Property(property="email", type="string", example="john@example.com"),
     *                     @OA\Property(property="phone", type="string", nullable=true, example="0123456789"),
     *                     @OA\Property(property="avatar_original", type="string", nullable=true, example="https://example.com/uploads/users/avatar.jpg"),
     *                     @OA\Property(property="referral_code_register", type="string", nullable=true, example="ABC123XYZ"),
     *                     @OA\Property(property="is_update_email", type="boolean", example=false),
     *                     @OA\Property(
     *                          property="corporate_access",
     *                          type="object",
     *                          @OA\Property(property="can_share", type="boolean", example=true),
     *                          @OA\Property(property="share_limit", type="integer", example=5),
     *                          @OA\Property(property="share_left", type="integer", example=3)
     *                     ),
     *                     @OA\Property(property="is_corporate_access_pass", type="boolean", example=false)
     *                 ),
     *                 @OA\Property(
     *                     property="counters",
     *                     type="object",
     *                     description="Summary counters for user's activities",
     *                     @OA\Property(property="cart", type="integer", example=10),
     *                     @OA\Property(property="wishlist", type="integer", example=2),
     *                     @OA\Property(property="orders", type="integer", example=5)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     )
     * )
     */
    public function user(Request $request)
    {
        $user = $request->user();
        return $this->successResponse('User information retrieved', new UserResource($user));
    }

    /**
     * @OA\Post(
     *     path="/api/mobile/v1/auth/send-otp",
     *     operationId="mobileSendOtp",
     *     tags={"Auth"},
     *     summary="Send OTP for registration",
     *     description="Send OTP code to email for user registration verification",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password","password_confirmation"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="password123"),
     *             @OA\Property(property="country_code", type="string", example="+84"),
     *             @OA\Property(property="phone", type="string", example="1234567890"),
     *             @OA\Property(property="receive_special_offers", type="integer", example=1),
     *             @OA\Property(property="referral_by", type="string", example="REF123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OTP sent successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Please check your email to receive the OTP code"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="email", type="string", example="user@example.com"),
     *                 @OA\Property(property="expires_at", type="string", format="date-time", example="2024-01-01T12:30:00Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="OTP limit exceeded or email exists",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="OTP has been sent 3 times. Please contact administrator for support.")
     *         )
     *     )
     * )
     */
    public function sendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email:rfc,dns|unique:App\User,email',
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:'. PasswordPolicy::regex(),
            ],
            'phone' => 'nullable|string',
            'country_code' => 'nullable|string',
            'receive_special_offers' => 'nullable|integer|in:0,1',
            'referral_by' => 'nullable|string'
        ], [
            'password.regex' => __('The password must contain at least one uppercase letter, one lowercase letter, and one number.'),
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', $validator->errors(), 422);
        }

        try {
            $userOtpExists = $this->userOtpService->getFirstWithConditions(['email' => $request->email, 'user_type' => User::TYPE_CUSTOMER]);
            $emailExists = $this->userService->getFirstWithConditions(['email' => $request->email]);

            if ($userOtpExists && $userOtpExists->count === UserOtp::SEND_LIMIT_EXCEEDED) {
                return $this->errorResponse('OTP has been sent 3 times. Please contact administrator for support.', [], 422);
            }

            if ($emailExists) {
                return $this->errorResponse('The email already exists!', [], 422);
            }

            if ($userOtpExists) {
                $this->resendOtpForMobile($userOtpExists);
            } else {
                $data = $this->formatDataInputsForMobile($request);
                $userOtp = $this->userOtpService->create($data);
                $this->sendOTPToMail($userOtp);
            }

            $dateExpired = Carbon::now()->addMinutes(30);

            return $this->successResponse('Please check your email to receive the OTP code', [
                'email' => $request->email,
                'expires_at' => $dateExpired->toISOString()
            ]);

        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong. Please try again.', [], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/mobile/v1/auth/resend-otp",
     *     operationId="mobileResendOtp",
     *     tags={"Auth"},
     *     summary="Resend OTP for registration",
     *     description="Resend OTP code to email if user didn't receive the previous one",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OTP resent successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Please check your email to receive the OTP code"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="count", type="integer", example=2),
     *                 @OA\Property(property="email", type="string", example="user@example.com"),
     *                 @OA\Property(property="expires_at", type="string", format="date-time", example="2024-01-01T12:30:00Z"),
     *                 @OA\Property(property="timeout_seconds", type="integer", example=180),
     *                 @OA\Property(property="message", type="string", example="Resend OTP after 3 minutes")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Email not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="No pending OTP found for this email")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="OTP limit exceeded",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="OTP has been sent 3 times. Please contact administrator for support.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=429,
     *         description="Too many requests - timeout not reached",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Please wait 45 seconds before resending OTP")
     *         )
     *     )
     * )
     */
    public function resendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email:rfc,dns'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', $validator->errors(), 422);
        }

        try {
            $email = $request->email;
            $userAwaitingVerify = $this->userOtpService->getFirstWithConditions(['email' => $email, 'user_type' => User::TYPE_CUSTOMER]);

            if (!$userAwaitingVerify) {
                return $this->errorResponse('No pending OTP found for this email', [], 404);
            }

            $countSendOtp = $userAwaitingVerify->count;

            if ($countSendOtp >= UserOtp::SEND_LIMIT_EXCEEDED) {
                return $this->errorResponse('OTP has been sent ' . UserOtp::SEND_LIMIT_EXCEEDED . ' times. Please contact administrator for support.', [], 422);
            }

            $lastResendTime = Carbon::parse($userAwaitingVerify->updated_at);
            $now = Carbon::now();
            $timeSinceLastResend = $now->diffInSeconds($lastResendTime);

            $timeoutSeconds = 0;
            $message = '';

            if ($countSendOtp === 1) {
                $timeoutSeconds = 60;
                if ($timeSinceLastResend < $timeoutSeconds) {
                    $remainingTime = $timeoutSeconds - $timeSinceLastResend;
                    return $this->errorResponse("Please wait {$remainingTime} seconds before resending OTP", [], 429);
                }
                $message = 'Resend OTP after 1 minute';
            } elseif ($countSendOtp === 2) {
                $timeoutSeconds = 180;
                if ($timeSinceLastResend < $timeoutSeconds) {
                    $remainingTime = $timeoutSeconds - $timeSinceLastResend;
                    return $this->errorResponse("Please wait {$remainingTime} seconds before resending OTP", [], 429);
                }
                $message = 'Resend OTP after 3 minutes';
            } else {
                return $this->errorResponse('OTP has been sent 3 times. Please contact administrator for support.', [], 422);
            }

            $countSendOtp += 1;
            $data['count'] = $countSendOtp;
            $data['original_otp'] = sprintf("%06d", rand(100000, 999999));
            $userOtp = $this->userOtpService->update($userAwaitingVerify->id, $data);

            $this->sendOTPToMail($userOtp);

            $dateExpired = Carbon::now()->addMinutes(30);

            return $this->successResponse('Please check your email to receive the OTP code', [
                'count' => $countSendOtp,
                'email' => $email,
                'expires_at' => $dateExpired->toISOString(),
                'timeout_seconds' => $timeoutSeconds,
                'message' => $message
            ]);

        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong. Please try again.', [], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/mobile/v1/auth/register",
     *     operationId="mobileRegister",
     *     tags={"Auth"},
     *     summary="Complete user registration",
     *     description="Complete user registration after OTP verification",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","otp"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="otp", type="string", example="123456")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Registration successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Registration successful"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9..."),
     *                 @OA\Property(
     *                     property="user",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="John Doe"),
     *                     @OA\Property(property="email", type="string", example="user@example.com"),
     *                     @OA\Property(property="user_type", type="string", example="customer")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid or expired OTP",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="The OTP is not valid or has expired")
     *         )
     *     )
     * )
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required|string|size:6'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', $validator->errors(), 422);
        }

        try {
            $userDataAuth = $this->userOtpService->getFirstWithConditions([
                'original_otp' => $request->otp,
                'email' => $request->email,
                'user_type' => User::TYPE_CUSTOMER
            ]);


            if (!$userDataAuth) {
                return $this->errorResponse('The OTP is not valid', [], 400);
            }

            if (!$userDataAuth->isExpired()) {
                return $this->errorResponse('The OTP has expired', [], 400);
            }

            $data = $this->userOtpService->formatDataInputsRegister($userDataAuth);
            $user = $this->registerService->createAccount($data);

            if ($user) {
                $user->email_verified_at = date('Y-m-d H:i:s');
                $user->save();
                $this->userOtpService->destroy($userDataAuth->id);
                $this->registerService->activeByReferralCode($data, $user);
                $this->setSource($user);

                $token = $user->createToken('Mobile')->accessToken;
                
                return $this->successResponse('Registration successful', [
                    'token' => $token,
                    'user' => $user
                ]);
            }

            return $this->errorResponse('Registration failed. Please try again.', [], 500);

        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong. Please try again....' . $e->getMessage(), [], 500);
        }
    }

    private function formatDataInputsForMobile(Request $request)
    {
        $data = [];
        $dateExpired = Carbon::now()->addMinutes(30)->format('Y-m-d H:i:s');
        $originalOtp = sprintf("%06d", rand(100000, 999999));

        $data['email'] = $request->email;
        $data['username'] = $request->name;
        $data['original_otp'] = $originalOtp;
        $data['user_type'] = User::TYPE_CUSTOMER;
        $data['expired'] = Carbon::createFromFormat('Y-m-d H:i:s', $dateExpired)->timestamp;

        $dataRequest = $request->all();
        $dataRequest['password'] = Hash::make($dataRequest['password']);
        unset($dataRequest['password_confirmation']);
        $data['user_details'] = json_encode($dataRequest);

        return $data;
    }

    private function resendOtpForMobile($userOtp)
    {
        $email = $userOtp->email;
        $countSendOtp = $userOtp->count;

        if ($countSendOtp !== UserOtp::SEND_LIMIT_EXCEEDED) {
            $lastResendTime = Carbon::parse($userOtp->updated_at);
            $now = Carbon::now();
            $timeSinceLastResend = $now->diffInSeconds($lastResendTime);

            $timeoutSeconds = 0;
            if ($countSendOtp === 1) {
                $timeoutSeconds = 60;
            } elseif ($countSendOtp === 2) {
                $timeoutSeconds = 180;
            }

            if ($timeSinceLastResend >= $timeoutSeconds) {
                $countSendOtp += 1;
                $data = [
                    'count' => $countSendOtp,
                    'original_otp' => sprintf("%06d", rand(100000, 999999)),
                    'expired' => Carbon::createFromFormat('Y-m-d H:i:s', Carbon::now()->addMinutes(3)->format('Y-m-d H:i:s'))->timestamp
                ];

                $userOtp = $this->userOtpService->update($userOtp->id, $data);
                $this->sendOTPToMail($userOtp);
            }
        }
    }

    private function sendOTPToMail($userOtp)
    {
        $array['subject']       = __('Your OTP to continue the registration in ' . getAppName() . ' !');
        $array['from']          = env('MAIL_FROM_ADDRESS');
        $array['name']          = $userOtp->username;
        $array['view']          = 'emails.send_otp_to_mail';
        $array['original_otp']  = $userOtp->original_otp;
        $array['content']       = 'Welcome and to ensure this email belongs to you, please use the OTP below to continue with registration!';

        Mail::to($userOtp->email)->send(new SendOTPToMail($array));
    }

    private function setSource(User $user)
    {
        if (isMicrosite()) {
            $site = getMicrosite();
            $this->microSiteService->registerUser($user, $site);
        }
    }

    private function attemptAuth(LoginRequest $request)
    {
        $email = $request->get('email');
        $password = $request->get('password');
        $credentials = [
            'email' => $email,
            'password' => $password,
        ];

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            if ($user && (is_null($user->email_verified_at) || !is_null($user->new_email_verificiation_code))) {
                Auth::logout();
                return 'email_not_verified';
            }
            return true;
        }

        return false;
    }

    /**
     * @OA\Post(
     *     path="/api/mobile/v1/auth/social-login",
     *     operationId="mobileSocialLogin",
     *     tags={"Auth"},
     *     summary="Social login for mobile users",
     *     description="Authenticate mobile user using social login (Google, Facebook, etc.)",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"provider", "access_token"},
     *             @OA\Property(property="provider", type="string", enum={"google", "facebook"}, example="google"),
     *             @OA\Property(property="access_token", type="string", example="your_access_token_here")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Social login successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Social login successful"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9..."),
     *                 @OA\Property(
     *                     property="user",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="John Doe"),
     *                     @OA\Property(property="email", type="string", example="user@example.com"),
     *                     @OA\Property(property="avatar", type="string", example="uploads/users/avatar.jpg")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Invalid social token",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Invalid social token")
     *         )
     *     )
     * )
     */

    public function socialLogin(SocialLoginRequest $request)
    {
        $provider = $request->provider;
        $token = $request->access_token;

        try {
            $socialUser = Socialite::driver($provider)->stateless()->userFromToken($token);

            $user = User::where('provider_id', $socialUser->id)->orWhere('email', $socialUser->email)->first();
            // check if not an existing user
            if (!$user) {
                // create a new user
                $user = new User;
                $user->name = $socialUser->getName();
                $user->email = $socialUser->getEmail();
                $user->email_verified_at = date('Y-m-d H:i:s');
                $user->provider_id = $socialUser->getId();
                $user->save();

                $customer = new Customer;
                $customer->user_id = $user->id;
                $customer->save();
            }

            Auth::guard()->login($user);
            $data['token'] = Auth::user()->createToken('Mobile')->accessToken;
            $data['user'] = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => $user->avatar,
            ];
            return $this->successResponse($data);

        } catch (\Exception $e) {
            dump($e->getMessage());
        }

        return $this->errorResponse('Invalid social token', [], 422);
    }

}
