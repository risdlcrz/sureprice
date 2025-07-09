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
        if (Auth::check() && Auth::user()->hasRole('supplier')) {
            // Additional check for approval status if necessary
            $user = Auth::user();
            // Remove or comment out this block to allow approved suppliers to log in without approval check
            // if ($user->company && $user->company->status !== 'approved') {
            //     Auth::logout();
            //     return redirect()->route('login.form')->with('error', 'Your supplier account is not approved yet.');
            // }
            return $next($request);
        }

        return redirect()->route('login.form')->with('error', 'Unauthorized access. Supplier role required.');
    }
} 