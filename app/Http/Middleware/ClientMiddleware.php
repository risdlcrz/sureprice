<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
            Log::info('ClientMiddleware: Not authenticated');
            return redirect()->route('login.form')->with('error', 'Please login first.');
        }

        try {
            // Get the authenticated user with company relationship
            $user = Auth::user();
            
            // Check if user is a company type
            if ($user->user_type !== 'company') {
                Log::info('ClientMiddleware: Not company user_type', ['user_id' => $user->id, 'user_type' => $user->user_type]);
                Auth::logout();
                return redirect()->route('login.form')->with('error', 'Invalid user type. Company access only.');
            }

            // Load company relationship if not already loaded
            if (!$user->relationLoaded('company')) {
                $user->load('company');
            }

            // Check if company exists
            if (!$user->company) {
                Log::info('ClientMiddleware: No company associated', ['user_id' => $user->id]);
                Auth::logout();
                return redirect()->route('login.form')->with('error', 'No company associated with this account.');
            }

            // Remove or comment out this block to allow approved clients to log in without approval check
            // if ($user->company->status !== 'approved') {
            //     Auth::logout();
            //     return redirect()->route('login.form')->with('error', 'Company account is not approved.');
            // }

            // Check company designation
            if ($user->company->designation !== 'client') {
                Log::info('ClientMiddleware: Not client designation', ['user_id' => $user->id, 'designation' => $user->company->designation]);
                Auth::logout();
                return redirect()->route('login.form')->with('error', 'Unauthorized access. Client access only.');
            }

            return $next($request);
        } catch (\Exception $e) {
            Log::info('ClientMiddleware: Exception', ['error' => $e->getMessage()]);
            Auth::logout();
            return redirect()->route('login.form')->with('error', 'An error occurred. Please try again.');
        }
    }
} 