<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Api\Mobile\Users\SendEmailCodeRequest;
use App\Mail\ShareReferralCode;
use App\Services\Api\Mobile\UserService;
use App\Services\ReferralService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ReferralController extends Controller
{
    protected $userService;
    protected $service;

    public function __construct(ReferralService $service, UserService $userService) {
        $this->userService  = $userService;
        $this->service      = $service;
    }
    /**
     * @OA\Post(
     *     path="/api/mobile/v1/auth/user/referral/send-code",
     *     operationId="sendReferralCode",
     *     tags={"User"},
     *     summary="Send referral code to email addresses",
     *     description="Send referral invitation links to multiple email addresses. Invalid or existing emails will be skipped.",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"emails"},
     *             @OA\Property(
     *                 property="emails",
     *                 type="array",
     *                 @OA\Items(type="string", format="email", example="user1@example.com")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Referral code sending result",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="sent_emails",
     *                     type="array",
     *                     @OA\Items(type="string", example="user1@example.com")
     *                 ),
     *                 @OA\Property(property="count", type="integer", example=1),
     *                 @OA\Property(property="message", type="string", example="Your referral code has been successfully sent.")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Sender does not exist!",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Sender does not exist!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation errors",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Validation errors"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function sendReferralCode(SendEmailCodeRequest $request)
    {
        try {
            $emails = $request->input('emails', []);
            $sender = Auth::user();
            $emailFilter = [];
        
            foreach ($emails as $email) {
                $user = $this->userService->getFirstWithConditions(['email' => $email]);
                if ($user) continue;

                $validator = Validator::make(['email' => $email], [
                    'email' => 'required|email:rfc,dns',
                ]);

                if ($validator->fails()) continue;

                try {
                    $referralLink = $this->service->genRegistrationLink($sender, $email);
        
                    $emailTemplate = new ShareReferralCode($sender);
                    $emailTemplate->setReferralLink($referralLink);
                    Mail::to($email)->queue($emailTemplate);
        
                    $emailFilter[] = $email;
                } catch (Exception $e) {
                    Log::error("Referral send failed for {$email}: " . $e->getMessage());
                    continue;
                }
            }
        
            $count = count($emailFilter);
            $message = $count > 0
                ? __('Your referral code has been successfully sent.')
                : __('Your referral code sending failed, because all emails are invalid or already exist!');
            
            $data = [
                'sent_emails' => $emailFilter,
                'count'       => $count,
            ];

            return $this->successResponse($message, $data);

        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong. Please try again.', [], 500);
        }
    }
}
