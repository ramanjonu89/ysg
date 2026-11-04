<?php

namespace App\Http\Middleware;

use Closure;

class ForceChangePassword
{
    public function handle($request, Closure $next)
    {
        if (auth()->check() && session('force_change_password')) {
            if (!$request->routeIs(
                'user.change.password',
                'user.update.password',
                'logout'
            )) {
                return redirect()->route('user.change.password');
            }
        }

        return $next($request);
    }
}