<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRoleAccessMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $allowedRoles = ['admin', 'seo-specialist', 'deputy-admin', 'manager'];
        
        // Check if user is authenticated
        if (!Auth::check()) {
            abort(404);
        }
        
        // Get the user's role safely
        $userRole = auth()->user()->role;
        
        // Check if role exists and is in allowed roles
        if (empty($userRole) || !in_array($userRole, $allowedRoles)) {
            abort(404);
        }

        return $next($request);
    }
}