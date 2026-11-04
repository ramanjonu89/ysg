<?php
namespace App\Http\Middleware;
use Closure;

class HomeMiddleware
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

        $request->session()->forget('isMicrosite');
        $request->session()->forget('shop');
        $request->session()->forget('microsite');

        return $next($request);
    }
}
