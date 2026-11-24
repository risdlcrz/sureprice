<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            abort(403, 'Unauthorized action.');
        }
        $user = Auth::user();
        // Client / supplier companies get no role access until an admin approves them.
        if ($user->user_type === 'company' && $user->company?->status !== 'approved') {
            abort(403, 'Company account is not approved.');
        }
        foreach ($roles as $role) {
            if ($user->hasRole($role)) {
                return $next($request);
            }
        }
        abort(403, 'Unauthorized action.');
    }
} 