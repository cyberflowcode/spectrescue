<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     * Check if user is logged in and has one of the allowed roles.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles  One or more allowed roles (e.g. 'admin', 'dispatcher', 'responder', 'citizen')
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // 1. Ensure user is logged in
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in to access this page.');
        }

        // 2. Ensure user has one of the authorized roles
        $user = Auth::user();
        $userRole = $user->role;

        if (!in_array($userRole, $roles)) {
            abort(403, 'Access denied. Your account role (' . ucfirst($userRole) . ') is not authorized to view this page.');
        }

        // 3. Keep online status & last activity timestamp fresh (throttled to once every 2 minutes)
        if (!$user->is_online || !$user->last_seen_at || $user->last_seen_at->lt(now()->subMinutes(2))) {
            $user->update([
                'is_online'    => true,
                'last_seen_at' => now(),
            ]);
        }

        return $next($request);
    }
}
