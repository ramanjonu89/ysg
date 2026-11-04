<?php
namespace App\Http\Controllers\Auth;

use App\BusinessSetting;
use App\Http\Controllers\Controller;
use App\Mail\PostRegisterConfirmationEmail;
use App\Mail\WelcomeMailManager;
use App\Mail\WelcomeMailManegerSecond;
use App\User;
use Auth;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class VerificationController extends Controller
{
    /*
        |--------------------------------------------------------------------------
        | Email Verification Controller
        |--------------------------------------------------------------------------
        |
        | This controller is responsible for handling email verification for any
        | user that recently registered with the application. Emails may also
        | be re-sent if the user didn't receive the original email message.
        |
    */
    use VerifiesEmails;

    public $array;
    /**
     * Where to redirect users after verification.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    public function verification_confirmation($code)
    {
        $user = User::where('verification_code', $code)->first();
        if ($user != null) {
            if ($user->microsite != null) {
                session(['isMicrosite' => true]);
                session(['shop' => $user->microsite->seller->user->shop]);
                session(['microsite' => $user->microsite]);
            }
            $user->email_verified_at = Carbon::now();
            if (empty($user->referral_code_register)) {
                $user->referral_code_register = $user->id . '_' . Str::random(9);
            }
            
            $user->save();
            flash(__('Your account has been successfully verified.'))->success();
            //lets send welcome email
            // $user = Auth::user();
            $user->verification_code = encrypt($user->id);
            $user->save();
            $array['from'] = env('MAIL_FROM_ADDRESS');
            $array['view'] = 'emails.welcome';
            $array['subject'] = __('Hello & Welcome to ' . getAppName() . ' !');
            $array['name'] = $user->name;
            $array['email'] = $user->email;
            $array['content'] = 'Please click the button below to verify your email address.';
            $array['link'] = route('email.verification.confirmation', $user->verification_code);
            $array['linkProfile'] = route('profile');
            Mail::to($user->email)->send(new WelcomeMailManager($array));
            //Mail::to($user->email)->send(new WelcomeMailManegerSecond($array));
            $referralExcludeDomainsSetting = BusinessSetting::where('type', 'referral_exclude_domains')->first();
            $referralEnabledSetting = BusinessSetting::where('type', 'referral_exclude_domains')->first();
            $referralExcludeDomains = $referralExcludeDomainsSetting ? $referralExcludeDomainsSetting->toArray() : [];
            $referralExcludeDomains = Arr::get($referralExcludeDomains, 'value');
            $referralExcludeDomains = $referralExcludeDomains ? json_decode($referralExcludeDomains, true) : [];
            $userDomain = substr($user->email, strpos($user->email, '@'));
            if (
                !in_array($userDomain, $referralExcludeDomains) &&
                $referralEnabledSetting &&
                $referralEnabledSetting->value
            ) {
                Mail::to($user)->send(new PostRegisterConfirmationEmail($user));
            } else {
                Log::debug(sprintf('User domain: %s is excluded', $userDomain));
            }
        } else {
            flash(__('Your verification attempt is unsuccessful. Please try again.'))->error();
        }

        return redirect()->route('home');
    }
}
