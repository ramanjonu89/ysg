<?php
namespace App\Http\Controllers\Auth;

use App\Customer;
use App\Http\Controllers\Controller;
use App\Support\PasswordPolicy;
use App\User;
use CoreComponentRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Laravel\Socialite\Facades\Socialite;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    /*protected $redirectTo = '/';*/
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function mobileGetSocialRedirect($provider)
    {
        return Socialite::driver($provider)
            ->redirectUrl(env('APP_URL').'/mobile-social-login/'.$provider.'/callback')
            ->redirect();
    }

    public function mobileGetSocialTokenCallback(Request $request, $provider)
    {
        try {
            $socialUser = Socialite::driver($provider)
                ->redirectUrl(env('APP_URL').'/mobile-social-login/'.$provider.'/callback')
                ->stateless()
                ->user();

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'failed to retrieve user from provider',
                'error' => $e->getMessage(),
            ]);
        }

        // Lấy access token:
        $accessToken = $socialUser->token;
        // Lấy refresh token (nếu có):
        $refreshToken = $socialUser->refreshToken;

        return response()->json([
            'id' => $socialUser->getId(),
            'name' => $socialUser->getName(),
            'email' => $socialUser->getEmail(),
            'avatar' => $socialUser->getAvatar(),
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken
        ]);
    }

    /**
     * Redirect the user to the Google authentication page.
     *
     * @return Response|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    /**
     * Obtain the user information from Google.
     *
     * @param Request $request
     * @param $provider
     * @return RedirectResponse|Response
     */
    public function handleProviderCallback(Request $request, $provider)
    {
        try {
            if ($provider == 'twitter') {
                $user = Socialite::driver('twitter')->user();
            } else {
                $user = Socialite::driver($provider)->stateless()->user();
            }
        } catch (\Exception $e) {
            flash("There is an error with your login, Please try again.")->error();

            return redirect()->route('user.login');
        }
        // check if they're an existing user
        $existingUser = User::where('provider_id', $user->id)->orWhere('email', $user->email)->first();
        if ($existingUser) {
            // log them in
            auth()->login($existingUser, true);
        } else {
            // create a new user
            $newUser = new User;
            $newUser->name = $user->name;
            $newUser->email = $user->email;
            $newUser->email_verified_at = date('Y-m-d H:i:s');
            $newUser->provider_id = $user->id;
            $newUser->save();
            // $extension = pathinfo($user->avatar_original, PATHINFO_EXTENSION);
            // $filename = 'uploads/users/'.Str::random(5).'-'.$user->id.'.'.$extension;
            // $fullpath = 'public/'.$filename;
            // $file = file_get_contents($user->avatar_original);
            // file_put_contents($fullpath, $file);
            // $newUser->avatar_original = $filename;
            $customer = new Customer;
            $customer->user_id = $newUser->id;
            $customer->save();
            auth()->login($newUser, true);
        }
        if ($link = session('previous_url')) {
            session()->forget('previous_url');
            return redirect($link);
        }

        return redirect()->route('dashboard');
    }

    /**
     * Check user's role and redirect user based on their role
     * @return Application|Factory|RedirectResponse|Redirector|View
     */
    public function authenticated()
    {
        $backOffice = [
            'admin',
            'staff'
        ];
        if (!in_array(auth()->user()->user_type, $backOffice) 
            && (is_null(auth()->user()->email_verified_at) || !is_null(auth()->user()->new_email_verificiation_code))
        ) {
            $this->guard()->logout();
            flash(__('Please kindly verify your email before logging in'))->error();

            return redirect()->back();
        }
        /** @var User $currentUser */
        $currentUser = Auth::user();
        $accessToken = $currentUser->createToken('Client Login');
        session(['laravel_token' => $accessToken->accessToken]);
        $activatedCorporateAccess = $currentUser->getActiveCorporateAccess();
        $corporateAccesses = $currentUser->getUnusedCorporateAccess();
        if ($activatedCorporateAccess) {
            session()->put('corporate_access_expire', $activatedCorporateAccess->expire_at);
        }
        if (!$activatedCorporateAccess && count($corporateAccesses) > 0) {
            session()->put('has_unused_corporate_access', true);
        }
        if (in_array(auth()->user()->user_type, $backOffice)) {
            return redirect()->route('admin.dashboard');
        } else {
            $currentUser->update(['last_login' => date('Y-m-d H:i:s')]);
            if ($link = session('previous_url')) {
                session()->forget('previous_url');
                return redirect($link);
            }

            return redirect()->route('home');
        }
    }

    /**
     * Log the user out of the application.
     *
     * @param Request $request
     * @return Response
     */
    public function logout(Request $request)
    {
        if (auth()->user() != null && (auth()->user()->user_type == 'admin' || auth()->user()->user_type == 'staff')) {
            $redirect_route = route('login');
        } else {
            $redirect_route = getAppHomepageLink();
        }
        $this->guard()->logout();
        $request->session()->invalidate();

        return $this->loggedOut($request) ?: redirect($redirect_route);
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param Request $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        if (filter_var($request->get('email'), FILTER_VALIDATE_EMAIL)) {
            return $request->only($this->username(), 'password');
        }

        return ['phone' => $request->get('email'), 'password' => $request->get('password')];
    }

    /**
     * Get the failed login response instance.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        flash(__('Invalid email or password'))->error();

        return back();
    }

    /**
     * Attempt to log the user into the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function attemptLogin(Request $request)
    {
        $remember = $request->filled('remember');
        if ($this->guard()->attempt($this->credentials($request), $remember)) {
            $rawPassword = $request->input('password');
            if (!PasswordPolicy::pass($rawPassword)) {
                session(['force_change_password' => true]);
            }
            return true;
        }

        return false;
    }
}
