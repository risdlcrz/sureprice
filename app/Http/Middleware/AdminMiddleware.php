<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login.form');
        }

        $user = Auth::user();
        
        if (!$user || $user->user_type !== 'admin') {
            if ($user && $user->user_type === 'company') {
                return redirect()->route('pending.approval');
            }
            return redirect()->route('login.form')->with('error', 'Admin privileges required');
        }

        return $next($request);
    }
}
