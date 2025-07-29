<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;  // <--- You need this
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$guards): Response
    {
        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();
                
                // Redirect based on user type
                if ($user->user_type === 'company') {
                    $company = $user->company;
                    if ($company) {
                        if ($company->designation === 'client') {
                            return redirect()->route('landing.catalogue');
                        } elseif ($company->designation === 'supplier') {
                            return redirect()->route('supplier.dashboard');
                        }
                    }
                } elseif ($user->role === 'admin') {
                    return redirect()->route('admin.dashboard');
                } elseif ($user->role === 'manager') {
                    return redirect()->route('manager.dashboard');
                } elseif ($user->role === 'finance') {
                    return redirect()->route('finance.dashboard');
                } elseif ($user->user_type === 'employee') {
                    if ($user->role === 'procurement') {
                        return redirect()->route('procurement.dashboard');
                    } elseif ($user->role === 'warehousing') {
                        return redirect()->route('warehouse.dashboard');
                    }
                }
                
                // Default redirect
                return redirect()->route('landing.catalogue');
            }
        }

        return $next($request);
    }
}
