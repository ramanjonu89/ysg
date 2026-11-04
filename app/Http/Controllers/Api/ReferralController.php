<?php
namespace App\Http\Controllers\Api;

use App\BusinessSetting;
use App\Coupon;
use App\Mail\ShareReferralCode;
use App\Mail\ShareReferralEmail;
use App\Models\UserCorporateAccess;
use App\Services\ReferralService;
use App\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Swift_RfcComplianceException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ReferralController extends Controller
{
    protected $service;

    public function __construct(ReferralService $referralService)
    {
        $this->service =  $referralService;
    }
    private function genNormalReferralLink($sender) {
        $referralEnabled = BusinessSetting::where('type', 'referral_enabled')->first();
        $referralData = [
            'referrer' => $sender->id,
            'type' => BusinessSetting::REFERRAL_TYPE_NORMAL
        ];
        $encryptedData = base64_encode(json_encode($referralData));
        $referralLink = route('user.registration', [
            'ref' => $encryptedData
        ]);
        return $referralLink;
    }

    public function sendMail(Request $request)
    {
        // check email
        $emails = $request->get('emails', []);
        if (empty($emails)) {
            throw new BadRequestHttpException(__('Emails not found'));
        }

        // check coupon setting
        $referralCoupon = BusinessSetting::where('type', 'referral_coupon_id')->first();
        $referralCoupon = Coupon::find($referralCoupon->value);
        if (!$referralCoupon) {
            throw new BadRequestHttpException(__('Referral voucher is not set!'));
        }

        $sender = Auth::user();
        $referralLink = $this->genNormalReferralLink($sender);

        $emailTemplate = new ShareReferralEmail($sender);
        $emailTemplate->setReferralLink($referralLink);
        $emailTemplate->setCoupon($referralCoupon);

        foreach ($emails as $email) {
            try {
                Mail::to($email)->send($emailTemplate);
            } catch (Swift_RfcComplianceException $exception) {
                throw new \Exception(sprintf(
                    __('Email "%s" is invalid'),
                    $email
                ));
            }
        }

        $responseMsg = sprintf(
            __('<h6 class="mb-4">Your voucher has been successfully sent to your following nominated email addresses:</h6>  <h6>%s</h6>'),
            implode(';', $emails)
        );

        return response()->json([
            'code' => 200,
            'message' => __($responseMsg)
        ]);
    }

    public function activateAccess(User $user, UserCorporateAccess $access)
    {
        if (!$user || !$access) {
            throw new BadRequestHttpException(__('Can\'t find user or corporate access'));
        }
        if ($access->expire_at) {
            throw new BadRequestHttpException(__('Your corporate access has been expired or activated already!'));
        }
        $currentActiveAccess = UserCorporateAccess
            ::select('id')
            ->where('is_active', true)
            ->where('user_id', $user->id)
            ->whereDate('expire_at', '>', Carbon::now())
            ->first();
        if ($currentActiveAccess) {
            throw new BadRequestHttpException(__('You can only activate one corporate access at a time!'));
        }
        $access->is_active = true;
        $access->expire_at = Carbon::now()->addDay();
        $access->save();
        UserCorporateAccess
            ::where('is_active', true)
            ->where('user_id', $user->id)
            ->whereDate('expire_at', '<', Carbon::now())
            ->update([
                'is_active' => false
            ]);
        session()->put('corporate_access_expire', $access->expire_at);
        if (session()->has('has_unused_corporate_access')) {
            session()->pull('has_unused_corporate_access');
        }

        return response()->json([
            'code' => 200,
            'message' => __('Corporate Access Activated Successfully!'),
            'expire_at' => $access->expire_at->format('Y/m/d H:i:s')
        ]);
    }

    public function sendReferralCode(Request $request)
    {
        $response = $this->validateShare($request);
        if ($response) {
            return json_encode($response);
        }

        $emails = $request->post('emails', []);
        $sender = Auth::user();
        $countEmail = count($emails);
        $regex_email = "/^[a-z0-9][a-z0-9\.]{1,28}[a-z0-9]@[a-z0-9][a-z0-9\.]{1,}(\.[a-z0-9]{2,4}){1,2}$/";
        if ($countEmail == 1) {
            $email = reset($emails);
            $existedUser = User::where('email', $email)->first();
            if ($existedUser) {
                $response = [
                    'status'  => 0,
                    'message' => sprintf(__('This email "%s" already exists on the system!'), $email)
                ];
            }

            if (!preg_match($regex_email, $email)){
                $response = [
                    'status'  => 0,
                    'message' => sprintf(__('Invalid email "%s" format, please try again!'), $email)
                ];
            }
            if ($response) {
                return json_encode($response);
            }
        }
        foreach ($emails as $email) {
            try {
                $existedUser = User::where('email', $email)->first();
                if ($existedUser) continue;
                if (!preg_match($regex_email, $email)) continue;

                $emailFilter[] = $email;
                $referralLink = $this->service->genRegistrationLink($sender, $email);
                $emailTemplate = new ShareReferralCode($sender);

                $emailTemplate->setReferralLink($referralLink);
                Mail::to($email)->send($emailTemplate);
            } catch (Swift_RfcComplianceException $e) {
                throw new Exception(sprintf(
                    __('Email "%s" is invalid'),
                    $email
                ));
            }
        }

        if (isset($emailFilter)) {
            $response = [
                'status'  => 1,
                'message' => sprintf(
                    __('<h6 class="mb-4">Your referral code has been successfully sent to the following email addresses:</h6>  <h6>%s</h6>'),
                    implode(';', $emailFilter))
            ];
        } else {
            $response = [
                'status'  => 0,
                'message' => 'Your referral code sent failed, because all emails are invalid!'
            ];
        }

        return json_encode($response);
    }

    private function validateShare(Request $request)
    {
        $emails = $request->post('emails', []);
        if (empty($emails)) {
            $response = [
                'status'  => 0,
                'message' => 'Emails not found!'
            ];

            return $response;
        }
        $sender = Auth::user();
        if (is_null($sender)) {
            $response = [
                'status'  => 0,
                'message' => 'Sender does not exist!'
            ];

            return $response;
        }

        return null;
    }
}
