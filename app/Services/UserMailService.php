<?php
namespace App\Services;

use App\Mail\ChangePassword;
use Illuminate\Support\Facades\Mail;
use App\Mail\SecondEmailVerifyMailManager;
use App\Mail\SendOTPToMail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UserMailService
{
    public function sendEmailChangeVerificationMail($user, $newEmail)
    {
        $response = ['status' => 0, 'message' => 'Unknown'];
        $verification_code = Str::random(32);

        $array = [
            'subject' => 'Email Verification',
            'from' => env('MAIL_FROM_ADDRESS'),
            'content' => 'Verify your account',
            'link' => route('user.email_change.callback') . '?new_email_verificiation_code=' . $verification_code . '&email=' . $newEmail,
            'sender' => $user->name,
            'details' => "Email Second",
        ];

        $user->new_email_verificiation_code = $verification_code;
        $user->save();

        try {
            Mail::to($newEmail)->queue(new SecondEmailVerifyMailManager($array));
            $response['status'] = 1;
            $response['message'] = __("Your verification mail has been sent.");
        } catch (\Exception $e) {
            $response['message'] = $e->getMessage();
        }

        return $response;
    }

    public function sendUpdateEmailVerificationMailFromMobile($user, $newEmail)
    {
        $response = ['status' => 0, 'message' => 'Unknown'];
        $verification_code = Str::random(32);

        $array = [
            'subject' => 'Email Verification',
            'from' => env('MAIL_FROM_ADDRESS'),
            'content' => 'Verify your account',
            'link' => route('user.email_change.callback.mobile') . '?new_email_verificiation_code=' . $verification_code . '&email=' . $newEmail,
            'sender' => $user->name,
            'details' => "Email Second",
        ];

        $user->new_email_verificiation_code = $verification_code;
        $user->save();

        try {
            Mail::to($newEmail)->queue(new SecondEmailVerifyMailManager($array));
            $response['status'] = 1;
            $response['message'] = __("Your verification mail has been sent.");
        } catch (\Exception $e) {
            $response['message'] = $e->getMessage();
        }

        return $response;
    }

    public function sendChangePasswordNotificationMail($user)
    {
        $formattedDate = Carbon::now()->format('d-M-Y \a\t h:i A');

        $array = [
            'subject' => 'Your Password has been changed',
            'from' => env('MAIL_FROM_ADDRESS'),
            'name' => $user->name,
            'type' => "Your Password has been changed!",
            'content' => "You have successfully changed your password on {$formattedDate}",
            'view' => 'emails.change_password',
        ];

        try {
            Mail::to($user->email)->queue(new ChangePassword($array));
            $response['message'] = __("Password change notification mail sent successfully.");
        } catch (\Exception $e) {
            Log::error('Error sending change password notification mail: ' . $e->getMessage());
        }
    }

    public function sendOTPToMail($userOtp, $newEmail)
    {
        $response = ['status' => 0, 'message' => 'Unknown'];

        $array = [
            'subject' => 'Your OTP to verify your new email in YSG Singapore!',
            'from' => env('MAIL_FROM_ADDRESS'),
            'content' => 'We received a request to change your email address. To confirm this new email belongs to you, please use the OTP below to verify your email update.',
            'name' => $userOtp->username,
            'original_otp' => $userOtp->original_otp,
            'view' => 'emails.send_otp_to_mail',
        ];

        try {
            Mail::to($newEmail)->queue(new SendOTPToMail($array));
            $response['status'] = 1;
            $response['message'] = __("Your OTP mail has been sent.");
        } catch (\Exception $e) {
            Log::error('Error sending OTP email: ' . $e->getMessage());
        }

        return $response;
    }

}