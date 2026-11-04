<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Api\Mobile\Users\SendEmailCodeRequest;
use App\Http\Resources\Mobile\SellerResource;
use App\Http\Resources\Mobile\UserCorporateAccessResource;
use App\Mail\{
    ShareAccessEmail,
    ShareAccessEmailForExistedUser
};
use App\Models\UserCorporateAccess;
use App\Services\Api\Mobile\UserService;
use App\Services\CorporateAccessService;
use App\Services\SellerService;
use App\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\{
    Auth, Log, Mail
};

class CorporateAccessController extends Controller
{
    protected $userService;
    protected $service;
    protected $sellerService;

    public function __construct(
        CorporateAccessService $service,
        UserService $userService,
        SellerService $sellerService
    )
    {
        $this->service      = $service;
        $this->userService  = $userService;
        $this->sellerService = $sellerService;
    }
    /**
     * @OA\Post(
     *     path="/api/mobile/v1/auth/user/corporate-access/send-mail",
     *     operationId="sendCorporateAccessMail",
     *     tags={"User"},
     *     summary="Send Corporate Access Invitation Emails",
     *     description="Send Corporate Sale Store Access invitations to a list of recipients. 
     *     - If a recipient already has an account, they will receive a direct access link.  
     *     - If they don't, they will receive a registration link.  
     *     - User must have Corporate Access enabled and available share slots left.",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         description="List of email addresses to send invitations to",
     *         @OA\JsonContent(
     *             required={"emails"},
     *             @OA\Property(
     *                 property="emails",
     *                 type="array",
     *                 description="Array of recipient email addresses",
     *                 @OA\Items(type="string", format="email", example="user@example.com")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successfully sent Corporate Access invitation emails",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Your Corporate Sale Store Access Pass has been successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="sent_emails",
     *                     type="array",
     *                     description="List of successfully sent email addresses",
     *                     @OA\Items(type="string", example="user1@example.com")
     *                 ),
     *                 @OA\Property(property="count", type="integer", description="Total number of sent emails", example=2)
     *             ),
     *             example={
     *                 "status": "success",
     *                 "message": "Your Corporate Sale Store Access Pass has been successfully",
     *                 "data": {
     *                     "sent_emails": {"user1@example.com", "user2@example.com"},
     *                     "count": 2
     *                 }
     *             }
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - Sender does not exist or invalid token",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Sender does not exist!")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=403,
     *         description="User cannot share or Corporate Access feature is disabled",
     *         @OA\JsonContent(
     *             oneOf={
     *                 @OA\Schema(
     *                     description="Corporate Access is disabled globally",
     *                     @OA\Property(property="status", type="string", example="error"),
     *                     @OA\Property(property="message", type="string", example="Corporate Access is disabled.")
     *                 ),
     *                 @OA\Schema(
     *                     description="User does not have permission to share Corporate Access",
     *                     @OA\Property(property="status", type="string", example="error"),
     *                     @OA\Property(property="message", type="string", example="You are not allowed to share Corporate Access.")
     *                 ),
     *                 @OA\Schema(
     *                     description="User has reached their sharing limit",
     *                     @OA\Property(property="status", type="string", example="error"),
     *                     @OA\Property(property="message", type="string", example="You have reached your sharing limit.")
     *                 )
     *             },
     *             example={
     *                 "status": "error",
     *                 "message": "Corporate Access is disabled."
     *             }
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Validation errors (invalid email format or missing field)",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Validation errors"),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 example={
     *                     "emails": {"The emails field is required."}
     *                 }
     *             )
     *         )
     *     )
     * )
     */

    public function sendMail(SendEmailCodeRequest $request)
    {
        $emails = $request->input('emails', []);
        $user = Auth::guard('api')->user();

        $corporateRefEnabled = $this->service->isSettingEnable();
        $canShareCA = $this->service->canShare($user);
        $corporateRefLeft = $this->service->getUserShareTimesLeft($user);

        if (!$corporateRefEnabled)  return $this->errorResponse('Corporate Access is disabled.', 403);
        if (!$canShareCA)           return $this->errorResponse('You are not allowed to share Corporate Access.', 403);
        if ($corporateRefLeft <= 0) return $this->errorResponse('You have reached your sharing limit.', 403);

        foreach ($emails as $email) {

            $validator = Validator::make(['email' => $email], [
                'email' => 'required|email:rfc,dns',
            ]);
            if ($validator->fails()) continue;

            $existedUser = $this->userService->getFirstWithConditions(['email' => $email]);
            if ($existedUser) {
                $referralLink = $this->service->genShareLink($user, $email);
                $emailTemplate = new ShareAccessEmailForExistedUser($user, CorporateAccessService::DEF_SHARE_ACCESS_DURATION_TEXT);
            } else {
                $referralLink = $this->service->genRegistrationLink($user, $email);
                $emailTemplate = new ShareAccessEmail($user, CorporateAccessService::DEF_SHARE_ACCESS_DURATION_TEXT);
            }

            $emailTemplate->setReferralLink($referralLink);
            try {
                Mail::to($email)->queue($emailTemplate);
            } catch (\Exception $e) {
                Log::error("Referral send failed for {$email}: " . $e->getMessage());
                continue;
            }
        }

        $data = [
            'sent_emails' => $emails,
            'count' => count($emails),
        ];

        return $this->successResponse('Your Corporate Sale Store Access Pass has been successfully', $data);
    }

    /**
     * @OA\Get(
     *     path="/api/mobile/v1/auth/user/referral/access",
     *     operationId="getCorporateAccessDetails",
     *     tags={"User"},
     *     summary="Get Corporate Access details",
     *     description="Retrieve detailed information about the user's Corporate Access Pass, including active sellers, expiration status, and activation availability.",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Corporate Access details retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Corporate Access details retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="user_id",
     *                     type="string",
     *                     description="The unique identifier of the user",
     *                     example="42"
     *                 ),
     *                 @OA\Property(
     *                     property="sellers",
     *                     type="array",
     *                     description="List of sellers associated with the current corporate domain",
     *                     @OA\Items(ref="#/components/schemas/Seller")
     *                 ),
     *                 @OA\Property(
     *                     property="corporate_access_expire_time",
     *                     type="boolean",
     *                     example=true,
     *                     description="Indicates whether the active Corporate Access is still valid (true if not expired)"
     *                 ),
     *                 @OA\Property(
     *                     property="user_corporate_accesses",
     *                     type="array",
     *                     description="List of all Corporate Access records associated with the user",
     *                     @OA\Items(ref="#/components/schemas/UserCorporateAccess")
     *                 ),
     *                 @OA\Property(
     *                     property="can_activate",
     *                     type="boolean",
     *                     example=true,
     *                     description="Whether the user is eligible to activate another Corporate Access"
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=403,
     *         description="User does not have a Corporate Access Pass",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="You do not have Corporate Access Pass.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - user is not authenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="Unexpected server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Something went wrong while retrieving access details.")
     *         )
     *     )
     * )
     */
    public function access()
    {
        try {
            $user = Auth::user();
            $isCAPass = $this->service->getSharedShops($user)->count();

            if (!$isCAPass) {
                return $this->errorResponse('You do not have Corporate Access Pass.', 403);
            }

            $activeAccess = $user->getActiveCorporateAccess();
            $canActivate = true;
            $sellers = collect();
            $corporateAccessExpireTime = null;

            if ($activeAccess) {
                $canActivate = false;
                $corporateAccessExpireTime = $activeAccess->expire_at;
                $corporateAccessDomain = $activeAccess->domain;
                $sellers = $this->sellerService->getSellersByDomain($corporateAccessDomain);
            }

            $userCAccesses = $user->corporateAccesses;

            return $this->successResponse('Corporate Access details retrieved successfully', [
                'user_id' => $user->id,
                'sellers' => SellerResource::collection($sellers),
                'corporate_access_expire_time' => !empty($sellers) && $corporateAccessExpireTime && $corporateAccessExpireTime->isFuture(),
                'user_corporate_accesses' => UserCorporateAccessResource::collection($userCAccesses),
                'can_activate' => $canActivate,
            ]);
        } catch (\Exception $e) {            
            return $this->errorResponse('Something went wrong while retrieving access details', 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/mobile/v1/auth/user/corporate-access/{user}/activate/{access}",
     *     operationId="activateCorporateAccess",
     *     tags={"User"},
     *     summary="Activate a Corporate Access Pass",
     *     description="Activate a user's Corporate Access Pass. 
     *         Once successfully activated, the frontend should call `/api/mobile/v1/auth/user/referral/access` 
     *         to retrieve the updated Corporate Access details.",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="user",
     *         in="path",
     *         required=true,
     *         description="ID of the user activating the Corporate Access",
     *         @OA\Schema(type="integer", example=12)
     *     ),
     *
     *     @OA\Parameter(
     *         name="access",
     *         in="path",
     *         required=true,
     *         description="ID of the Corporate Access to activate",
     *         @OA\Schema(type="integer", example=5)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Corporate Access activated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Corporate Access Activated Successfully!"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="expire_at",
     *                     type="string",
     *                     format="date-time",
     *                     example="2025-12-31 23:59:59",
     *                     description="The expiration time of the activated Corporate Access"
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="note",
     *                 type="string",
     *                 example="After receiving status: success, call /api/mobile/v1/auth/user/referral/access to refresh data.",
     *                 description="Instruction for client applications to retrieve updated access details"
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Activation failed due to invalid access or conditions not met",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="This Corporate Access cannot be activated at the moment.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="User or Access not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="User or Access not found")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="Unexpected server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Something went wrong while activating Corporate Access.")
     *         )
     *     )
     * )
     */
    public function activateAccess(User $user, UserCorporateAccess $access)
    {
        try {
            if (!$user || !$access) {
                return $this->errorResponse(__('User or Access not found'), 404);
            }
    
            $result = $this->service->activeCA($user, $access);
            
            if (!$result['status']) {
                return $this->errorResponse($result['message'], 400);
            }
    
            session()->put('corporate_access_expire', $access->expire_at);
            session()->forget('has_unused_corporate_access');
    
            return $this->successResponse('Corporate Access Activated Successfully!', [
                'expire_at' => $result['expireAt']
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong while activating Corporate Access', 500);
        }
    }
}