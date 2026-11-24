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
            // Pending / rejected companies go to their status page (User::homeRoute), not the supplier area.
            if ($user->company?->status !== 'approved') {
                return redirect()->route($user->homeRoute());
            }
            return $next($request);
        }

        return redirect()->route('login.form')->with('error', 'Unauthorized access. Supplier role required.');
    }
} 