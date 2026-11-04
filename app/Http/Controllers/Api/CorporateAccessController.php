<?php
namespace App\Http\Controllers\Api;

use App\Mail\ShareAccessEmail;
use App\Mail\ShareAccessEmailForExistedUser;
use App\Models\UserCorporateAccess;
use App\Services\CorporateAccessService;
use App\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Swift_RfcComplianceException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class CorporateAccessController extends Controller
{
    protected $service;

    public function __construct(CorporateAccessService $corporateAccessService)
    {
        $this->service = $corporateAccessService;
    }

    /**
     * @return bool
     */
    public function isCorporateAccessEnable(): bool
    {
        return true;
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     * @throws Exception
     */
    public function sendMail(Request $request): JsonResponse
    {
        $valid = $this->validateShare($request);
        if ($valid) {
            throw $valid;
        }
        $emails = $request->post('emails', []);
        $sender = Auth::user();

        foreach ($emails as $email) {
            $existedUser = User::where('email', $email)->first();
            if ($existedUser) {
                $referralLink = $this->service->genShareLink($sender, $email);
                $emailTemplate = new ShareAccessEmailForExistedUser($sender, CorporateAccessService::DEF_SHARE_ACCESS_DURATION_TEXT);
            } else {
                $referralLink = $this->service->genRegistrationLink($sender, $email);
                $emailTemplate = new ShareAccessEmail($sender, CorporateAccessService::DEF_SHARE_ACCESS_DURATION_TEXT);
            }
            $emailTemplate->setReferralLink($referralLink);
            try {
                Mail::to($email)->send($emailTemplate);
            } catch (Swift_RfcComplianceException $exception) {
                throw new Exception(sprintf(
                    __('Email "%s" is invalid'),
                    $email
                ));
            }
        }

        $responseMsg = sprintf(
            __('<h6 class="mb-4">Your Corporate Sale Store Access Pass has been successfully sent to the following email addresses:</h6>  <h6>%s</h6>'),
            implode(';', $emails)
        );

        return response()->json([
            'code' => 200,
            'message' => __($responseMsg)
        ]);
    }

    private function validateShare(Request $request)
    {
        $emails = $request->post('emails', []);
        if (empty($emails)) {
            return new BadRequestHttpException(__('Emails not found'));
        }
        $sender = Auth::user();
        if (is_null($sender)) {
            return new BadRequestHttpException(__('Sender does not exist!'));
        }

        return null;
    }

    /**
     * Do active Corporate Access
     * @param  User  $user
     * @param  UserCorporateAccess  $access
     * @return JsonResponse
     */
    public function activateAccess(User $user, UserCorporateAccess $access): JsonResponse
    {
        if (!$user || !$access) {
            throw new BadRequestHttpException(__('Invalid request!'));
        }

        $result = $this->service->activeCA($user, $access);
        if ($result['status'] == false) {
            throw new BadRequestHttpException($result['message']);
        }

        session()->put('corporate_access_expire', $access->expire_at);
        if (session()->has('has_unused_corporate_access')) {
            session()->pull('has_unused_corporate_access');
        }

        return response()->json([
            'code' => 200,
            'message' => __('Corporate Access Activated Successfully!'),
            'expire_at' => $result['expireAt']
        ]);
    }
}
