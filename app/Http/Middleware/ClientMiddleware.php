<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ClientMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login.form')->with('error', 'Please login first.');
        }

        try {
            // Get the authenticated user with company relationship
            $user = Auth::user();
            
            // Check if user is a company type
            if ($user->user_type !== 'company') {
                Auth::logout();
                return redirect()->route('login.form')->with('error', 'Invalid user type. Company access only.');
            }

            // Load company relationship if not already loaded
            if (!$user->relationLoaded('company')) {
                $user->load('company');
            }

            // Check if company exists
            if (!$user->company) {
                Auth::logout();
                return redirect()->route('login.form')->with('error', 'No company associated with this account.');
            }

            // Check company status
            if ($user->company->status !== 'approved') {
                Auth::logout();
                return redirect()->route('login.form')->with('error', 'Company account is not approved.');
            }

            // Check company designation
            if ($user->company->designation !== 'client') {
                Auth::logout();
                return redirect()->route('login.form')->with('error', 'Unauthorized access. Client access only.');
            }

            return $next($request);
        } catch (\Exception $e) {
            Auth::logout();
            return redirect()->route('login.form')->with('error', 'An error occurred. Please try again.');
        }
    }
} 