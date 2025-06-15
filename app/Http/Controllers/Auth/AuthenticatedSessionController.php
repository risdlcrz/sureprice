<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

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

            // Redirect based on user type
            if ($user->user_type === 'admin') {
                return redirect()->route('admin.dbadmin');
            } elseif ($user->user_type === 'employee') {
                if ($user->role === 'procurement') {
                    return redirect()->route('procurement.dashboard');
                } elseif ($user->role === 'warehousing') {
                    return redirect()->route('warehousing.dashboard');
                }
                // Default redirect for other employee roles if any
                return redirect()->route('home'); // Or a generic employee dashboard if exists
            } elseif ($user->user_type === 'company') {
                // Check if the company exists and its status
                $company = $user->company;
                if (!$company) {
                    return redirect()->route('pending.approval');
                }
                
                // Check company status
                if ($company->status === 'rejected') {
                    return redirect()->route('account.rejected');
                } elseif ($company->status !== 'approved') {
                    return redirect()->route('pending.approval');
                }
                
                // If approved, redirect based on designation
                if ($company->designation === 'client') {
                    return redirect()->route('client.dashboard');
                } elseif ($company->designation === 'supplier') {
                    return redirect()->route('supplier.dashboard');
                }
            }

            // Default redirect for other user types
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