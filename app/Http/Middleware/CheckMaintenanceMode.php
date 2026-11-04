<?php

namespace App\Http\Middleware;

use App\BusinessSetting;
use Closure;
use Illuminate\Http\Request;

class CheckMaintenanceMode
{
    public function handle(Request $request, Closure $next)
    {
        $maintenanceSetting = BusinessSetting::where('type', 'maintenance_mode')->first();
        
        if ($maintenanceSetting && $maintenanceSetting->value == '1') {
            $maintenanceToken = env('MAINTENANCE_BYPASS_TOKEN');
            $cookieName = 'ysg_voop';
            
            $cookieValue = $request->cookie($cookieName);
            
            if ($cookieValue && $maintenanceToken && $cookieValue === hash('sha256', $maintenanceToken)) {
                return $next($request);
            }
            
            $queryToken = $request->query('token');
            
            if ($queryToken && $maintenanceToken && $queryToken === $maintenanceToken) {
                $response = $next($request);
                $host = $request->getHost();
                $domain = $this->getCookieDomain($host);
                
                return $response->cookie($cookieName, hash('sha256', $maintenanceToken), 60 * 24 * 30, '/', $domain);
            }
            
            return response()->view('maintenance', [], 503);
        }
        
        return $next($request);
    }
    
    private function getCookieDomain($host)
    {
        $parts = explode('.', $host);
        if (count($parts) >= 2) {
            return '.' . $parts[count($parts) - 2] . '.' . $parts[count($parts) - 1];
        }
        return null;
    }
}

