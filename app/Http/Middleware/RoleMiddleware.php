<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $role
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, $role): Response
    {
        if (!Auth::check()) {
            return redirect('login');
        }

        $user = Auth::user();
        
        // Check if using Spatie Permission package
        if (method_exists($user, 'hasRole')) {
            if ($user->hasRole($role)) {
                return $next($request);
            }
        } 
        // Check if the role is stored directly on the user model
        else if (isset($user->role) && $user->role === $role) {
            return $next($request);
        }
        
        return redirect('/')->with('error', 'You do not have permission to access this page.');
    }
}
