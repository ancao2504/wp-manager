<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\LoginHistory;
use Illuminate\Support\Facades\Auth;
use Browser;

class AuthHistory
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        
        // Only track authenticated requests
        if (Auth::check() && $request->route() && $request->route()->getName() === 'login') {
            $user = Auth::user();
            
            // Get browser and device info
            $browser = Browser::detect();
            
            // Create login history record
            LoginHistory::create([
                'user_id' => $user->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'device' => $browser->deviceFamily(),
                'browser' => $browser->browserName() . ' ' . $browser->browserVersion(),
            ]);
        }
        
        return $response;
    }
}
