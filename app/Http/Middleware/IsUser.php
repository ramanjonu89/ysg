<?php
namespace App\Http\Middleware;

use Auth;
use Closure;

class IsUser
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::check() && (Auth::user()->user_type == 'customer' || Auth::user()->user_type == 'seller')) {
            if ((Auth::user()->user_type == 'seller') && (Auth::user()->company_id == null)) {
                flash("Our Customer Service Team will contact you shortly")->error();
                auth()->logout();

                return redirect()->route('user.login');
            }

            return $next($request);
        } else {
            session(['previous_url' => url()->current()]);

            return redirect()->route('user.login');
        }
    }
}

