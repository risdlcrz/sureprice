<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create()
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request)
    {
        // Validate input - 'login' can be email or username
        $credentials = $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        // Determine if input is email or username
        $fieldType = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // Attempt to authenticate using the proper field
        if (Auth::attempt([$fieldType => $credentials['login'], 'password' => $credentials['password']])) {
            $request->session()->regenerate();

            $user = Auth::user();
            $user->last_login_at = now();
            $user->save();

            Log::info('User authenticated', [
                'user_id' => $user->id,
                'email' => $user->email,
                'user_type' => $user->user_type,
                'role' => $user->role
            ]);

            // Redirect based on user type
            if ($user->role === 'manager') {
                Log::info('Redirecting to manager dashboard');
                return redirect()->route('manager.dashboard'); // Manager dashboard
            } elseif ($user->role === 'admin') {
                Log::info('Redirecting to admin dashboard');
                return redirect()->route('admin.dashboard'); // Admin dashboard (oversight)
            } elseif ($user->role === 'finance') {
                Log::info('Redirecting to finance dashboard');
                return redirect()->route('finance.dashboard');
            } elseif ($user->user_type === 'employee') {
                if ($user->role === 'procurement') {
                    Log::info('Redirecting to procurement dashboard');
                    return redirect()->route('procurement.dashboard');
                } elseif ($user->role === 'warehousing') {
                    Log::info('Redirecting to warehouse dashboard');
                    return redirect()->route('warehouse.dashboard');
                }
                // Default redirect for other employee roles if any
                Log::info('Redirecting to home (employee)');
                return redirect()->route('home'); // Or a generic employee dashboard if exists
            } elseif ($user->user_type === 'company') {
                // Check if the company exists and its status
                $company = $user->company;
                Log::info('Company user login', [
                    'user_id' => $user->id,
                    'company_exists' => $company ? true : false,
                    'company_status' => $company ? $company->status : null,
                    'company_designation' => $company ? $company->designation : null
                ]);
                
                if (!$company) {
                    Log::info('No company associated, redirecting to pending approval');
                    return redirect()->route('pending.approval');
                }
                
                // Check company status
                if ($company->status === 'rejected') {
                    Log::info('Company rejected, redirecting to account rejected');
                    return redirect()->route('account.rejected');
                } elseif ($company->status !== 'approved') {
                    Log::info('Company not approved, redirecting to pending approval');
                    return redirect()->route('pending.approval');
                }
                
                // If approved, redirect based on designation
                if ($company->designation === 'client') {
                    // If they had a saved quotation form (guest), send them back to complete it
                    if ($request->session()->has('redirect_after_login') && $request->session()->has('guest_quotation_data')) {
                        Log::info('Client approved, redirecting to quotation create to complete guest submission');
                        return redirect()->route('client.quotation.create');
                    }
                    Log::info('Client approved, redirecting to landing page');
                    return redirect()->route('landing.catalogue');
                } elseif ($company->designation === 'supplier') {
                    Log::info('Supplier approved, redirecting to supplier dashboard');
                    return redirect()->route('supplier.dashboard');
                }
            }

            // Default redirect for other user types
            Log::info('Default redirect to pending approval');
            return redirect()->route('pending.approval');
        }

        // Failed authentication
        return back()->withErrors([
            'login' => 'Invalid credentials',
        ])->onlyInput('login');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
} 