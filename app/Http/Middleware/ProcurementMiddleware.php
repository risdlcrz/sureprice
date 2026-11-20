<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProcurementMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login.form')->with('error', 'Please login first.');
        }

        $user = Auth::user();
        
        // Check role in users table first
        // Contractors work alongside procurement and share its area.
        if (in_array($user->role, ['procurement', 'contractor'], true)) {
            return $next($request);
        }
        
        // If not found in users table, check in employees table
        if ($user->employee && in_array($user->employee->role, ['procurement', 'contractor'], true)) {
            return $next($request);
        }

        return redirect()->route('login.form')->with('error', 'Unauthorized access. Procurement role required.');
    }
} 