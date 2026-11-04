<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    /*

    |--------------------------------------------------------------------------

    | Password Reset Controller

    |--------------------------------------------------------------------------

    |

    | This controller is responsible for handling password reset emails and

    | includes a trait which assists in sending these notifications from

    | your application to your users. Feel free to explore this trait.

    |

    */
    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Send a reset link to the given user.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function sendResetLinkEmail(Request $request)
    {
        $email = $request->email;

        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {

            // We will send the password reset link to this user. Once we have attempted
            // to send the link, we will examine the response then see the message we
            // need to show to the user. Finally, we'll send out a proper response.
            $response = $this->broker()->sendResetLink(
                $this->credentials($request)
            );

            if ($response == Password::RESET_LINK_SENT) {
                $response = [
                    'status'  => 1,
                    'message' => 'Send password reset link successfully!'
                ];
            } else {
                $response = [
                    'status'  => 2,
                    'message' => 'Email is incorrect, please try again!'
                ];
            }
        } else {
            $response = [
                'status'  => 2,
                'message' => 'Invalid email format, please try again!'
            ];
        }

        return json_encode($response);
    }
}

