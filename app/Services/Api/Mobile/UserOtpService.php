<?php
namespace App\Services\Api\Mobile;

use App\Models\UserOtp;
use App\Repositories\Api\Mobile\UserOtpRepository;
use App\Services\AbstractService;
use App\Services\UserMailService;
use Carbon\Carbon;

class UserOtpService extends AbstractService
{
    protected $repo;
    protected $mailService;

    public function __construct(UserOtpRepository $repo, UserMailService $mailService)
    {
        parent::__construct($repo);
        $this->mailService = $mailService;
    }

    public function sendOtpUpdateMail($user, $userOtpExists, $newEmail)
    {
        $now = Carbon::now();

        if ($userOtpExists) {
            $lastResendTime = Carbon::parse($userOtpExists->updated_at);
            $timeSinceLastResend = $now->diffInSeconds($lastResendTime);

            $details        = $userOtpExists->user_details ? json_decode($userOtpExists->user_details) : [];
            $previousEmail  = isset($details->newEmail) ? $details->newEmail : null;
            $countSendOtp   = isset($userOtpExists->count) ? (int) $userOtpExists->count : 0;

            if ($previousEmail === $newEmail) {
                if ($countSendOtp === UserOtp::SEND_LIMIT_EXCEEDED) {
                    return [
                        'status'  => false,
                        'message' => 'You have reached the maximum number of OTP resend attempts. Please try again later.',
                    ];
                }

                switch ($countSendOtp) {
                    case 1:
                        $timeoutSeconds = 60;
                        break;
                    case 2:
                    default:
                        $timeoutSeconds = 180;
                        break;
                }

                if ($timeSinceLastResend < $timeoutSeconds) {
                    $remainingTime = $timeoutSeconds - $timeSinceLastResend;

                    return [
                        'status'  => false,
                        'message' => "Please wait {$remainingTime} seconds before requesting a new OTP.",
                    ];
                }
            }

            if ($newEmail !== $previousEmail) {
                $countSendOtp = 1;
            } else {
                $countSendOtp++;
            }

            $data = $this->formatDataInputs($user, $newEmail);
            $data['count'] = $countSendOtp;

            $userOtp = $this->repo->update($userOtpExists->id, $data);
            $this->mailService->sendOTPToMail($userOtp, $newEmail);

            return [
                'status'  => true,
                'message' => 'Check your Email Inbox for the One-Time Password (OTP) in the Email Sent.'
            ];
        }

        $data = $this->formatDataInputs($user, $newEmail);
        $data['count'] = 1;

        $userOtp = $this->repo->create($data);
        $this->mailService->sendOTPToMail($userOtp, $newEmail);

        return [
            'status'  => true,
            'message' => 'Check your Email Inbox for the One-Time Password (OTP) in the Email Sent.'
        ];
    }

    private function formatDataInputs($user, $newEmail)
    {
        $originalOtp = rand(100000, 999999);
        $expiredAt = Carbon::now()->addMinutes(3);
    
        return [
            'email'         => $user->email,
            'username'      => $user->name,
            'original_otp'  => $originalOtp,
            'user_type'     => $user->user_type,
            'expired'       => $expiredAt->timestamp,
            'user_details'  => json_encode(['newEmail' => $newEmail]),
        ];
    }
}