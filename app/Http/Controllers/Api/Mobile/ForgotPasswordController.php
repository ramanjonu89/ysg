<?php
namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Api\Mobile\Users\ForgotPasswordSendOtpRequest;
use App\Http\Requests\Api\Mobile\Users\ForgotPasswordVerifyOtpRequest;
use App\Http\Requests\Api\Mobile\Users\ForgotPasswordResetRequest;
use App\Models\UserOtp;
use App\User;
use App\Mail\SendOTPToMail;
use App\Services\UserOtpService;
use App\Services\UserMailService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordController extends Controller
{
    protected $userOtpService;
    protected $userMailService;

    public function __construct(
        UserOtpService  $userOtpService,
        UserMailService $userMailService
    ) {
        $this->userOtpService  = $userOtpService;
        $this->userMailService = $userMailService;
    }

    /**
     * @OA\Post(
     *     path="/api/mobile/v1/auth/forgot-password/send-otp",
     *     operationId="forgotPasswordSendOtp",
     *     tags={"Forgot Password"},
     *     summary="Send OTP for forgot password",
     *     description="Send OTP code to user's email for password reset",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com")
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
     *                 @OA\Property(property="count", type="integer", description="Number of OTP sent attempts", example=1),
     *                 @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *                 @OA\Property(property="expires_at", type="string", format="date-time", description="OTP expiration time"),
     *                 @OA\Property(property="timeout_seconds", type="integer", description="Seconds to wait before resending (60 for 1st, 180 for subsequent)", example=60),
     *                 @OA\Property(property="message", type="string", description="Status message", example="OTP sent successfully.")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation error or OTP limit exceeded"),
     *     @OA\Response(response=429, description="Too many requests - timeout not reached"),
     *     @OA\Response(response=500, description="Server error")
     * )
     */
    public function sendOtp(ForgotPasswordSendOtpRequest $request)
    {
        try {
            $email = $request->email;
            $user = User::where('email', $email)->first();

            if (!$user) {
                return $this->errorResponse('No account found with this email address.', [], 422);
            }

            $userOtpExists = $this->userOtpService->getFirstWithConditions([
                'email'     => $email,
                'user_type' => 'forgot_password',
            ]);

            $timeoutSeconds = 60;
            if ($userOtpExists) {
                if ($userOtpExists->count >= UserOtp::SEND_LIMIT_EXCEEDED) {
                    return $this->errorResponse(
                        'OTP has been sent ' . UserOtp::SEND_LIMIT_EXCEEDED . ' times. Please contact administrator for support.',
                        [],
                        422
                    );
                }

                // Enforce cooldown between resends
                $lastResendTime = Carbon::parse($userOtpExists->updated_at);
                $timeSinceLastResend = Carbon::now()->diffInSeconds($lastResendTime);

                $timeoutSeconds = $userOtpExists->count === 1 ? 60 : 180;

                if ($timeSinceLastResend < $timeoutSeconds) {
                    $remainingTime = $timeoutSeconds - $timeSinceLastResend;
                    return $this->errorResponse(
                        "Please wait {$remainingTime} seconds before resending OTP",
                        [],
                        429
                    );
                }

                // Resend with new OTP
                $userOtp = $this->userOtpService->update($userOtpExists->id, [
                    'count'        => $userOtpExists->count + 1,
                    'original_otp' => sprintf("%06d", rand(100000, 999999)),
                    'expired'      => Carbon::now()->addMinutes(30)->timestamp,
                ]);
            } else {
                // First time sending OTP
                $userOtp = $this->userOtpService->create([
                    'email'        => $email,
                    'username'     => $user->name,
                    'original_otp' => sprintf("%06d", rand(100000, 999999)),
                    'user_type'    => 'forgot_password',
                    'expired'      => Carbon::now()->addMinutes(30)->timestamp,
                    'count'        => 1,
                ]);
            }

            $this->sendOTPToMail($userOtp);

            $countSendOtp = $userOtp->count;
            $dateExpired = Carbon::createFromTimestamp($userOtp->expired);

            return $this->successResponse('Please check your email to receive the OTP code', [
                'count'           => $countSendOtp,
                'email'           => $email,
                'expires_at'      => $dateExpired->toISOString(),
                'timeout_seconds' => $timeoutSeconds,
                'message'         => 'Please check your email to receive the OTP code',
            ]);

        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong. Please try again.', [], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/mobile/v1/auth/forgot-password/verify-otp",
     *     operationId="forgotPasswordVerifyOtp",
     *     tags={"Forgot Password"},
     *     summary="Verify OTP for forgot password",
     *     description="Verify the OTP code sent to user's email",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "otp"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="otp", type="string", example="123456")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OTP verified successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="OTP verified successfully")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Invalid or expired OTP"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function verifyOtp(ForgotPasswordVerifyOtpRequest $request)
    {
        try {
            $userOtp = $this->userOtpService->getFirstWithConditions([
                'email'        => $request->email,
                'original_otp' => $request->otp,
                'user_type'    => 'forgot_password',
            ]);

            if (!$userOtp) {
                return $this->errorResponse('The OTP is not valid', [], 400);
            }

            if (!$userOtp->isExpired()) {
                return $this->errorResponse('The OTP has expired', [], 400);
            }

            return $this->successResponse('OTP verified successfully');

        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong. Please try again.', [], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/mobile/v1/auth/forgot-password/reset-password",
     *     operationId="forgotPasswordResetPassword",
     *     tags={"Forgot Password"},
     *     summary="Reset password with verified OTP",
     *     description="Reset user's password after OTP verification",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "otp", "password", "password_confirmation"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="otp", type="string", example="123456"),
     *             @OA\Property(property="password", type="string", format="password", example="NewPassword1"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="NewPassword1")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Password reset successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Password has been reset successfully")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Invalid or expired OTP"),
     *     @OA\Response(response=422, description="Validation error"),
     *     @OA\Response(response=500, description="Server error")
     * )
     */
    public function resetPassword(ForgotPasswordResetRequest $request)
    {
        try {
            $userOtp = $this->userOtpService->getFirstWithConditions([
                'email'        => $request->email,
                'original_otp' => $request->otp,
                'user_type'    => 'forgot_password',
            ]);

            if (!$userOtp) {
                return $this->errorResponse('The OTP is not valid', [], 400);
            }

            if (!$userOtp->isExpired()) {
                return $this->errorResponse('The OTP has expired', [], 400);
            }

            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return $this->errorResponse('No account found with this email address.', [], 400);
            }

            $user->password = Hash::make($request->password);
            $user->save();

            // Delete the OTP record after successful password reset
            $this->userOtpService->destroy($userOtp->id);

            // Send password change notification email
            $this->userMailService->sendChangePasswordNotificationMail($user);

            return $this->successResponse('Password has been reset successfully');

        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong. Please try again.', [], 500);
        }
    }

    private function sendOTPToMail($userOtp)
    {
        $array['subject'] = __('Your OTP to reset password in ' . getAppName() . ' !');
        $array['from'] = env('MAIL_FROM_ADDRESS');
        $array['name'] = $userOtp->username;
        $array['view'] = 'emails.send_otp_forgot_password';
        $array['original_otp'] = $userOtp->original_otp;
        Mail::to($userOtp->email)->send(new SendOTPToMail($array));
    }
}
