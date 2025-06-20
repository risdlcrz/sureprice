<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupplierMiddleware
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
        
        // Check if user is a company with supplier designation
        if ($user->user_type === 'company' && $user->company && $user->company->designation === 'supplier') {
            // Check supplier approval status
            if ($user->company->status !== 'approved') {
                Auth::logout();
                return redirect()->route('login.form')->with('error', 'Your account is not approved yet.');
            }
            return $next($request);
        }
        
        // Check role in users table (for backward compatibility)
        if ($user->role === 'supplier') {
            // Check supplier approval status
            if ($user->supplier && $user->supplier->status !== 'approved') {
                Auth::logout();
                return redirect()->route('login.form')->with('error', 'Your account is not approved yet.');
            }
            return $next($request);
        }
        
        // If not found in users table, check in employees table
        if ($user->employee && $user->employee->role === 'supplier') {
            return $next($request);
        }

        return redirect()->route('login.form')->with('error', 'Unauthorized access. Supplier role required.');
    }
} 