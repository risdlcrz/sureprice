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
<<<<<<< HEAD
            return redirect()->route('login');
        }

        $user = Auth::user();
        if ($user->role !== 'admin') {
            return redirect()->route('pending.approval')
                ->with('error', 'You do not have permission to access this area.');
=======
            return redirect()->route('login')
                ->with('error', 'Please log in to access this area.');
        }

        $user = Auth::user();
        
        if (!$user->isAdmin()) {
            return redirect()->route('pending.approval')
                ->with('error', 'You do not have permission to access this area.');
        }

        if ($user->status === 'rejected') {
            return redirect()->route('account.rejected')
                ->with('error', 'Your account has been rejected. Please contact support.');
        }

        if ($user->status === 'pending') {
            return redirect()->route('pending.approval')
                ->with('error', 'Your account is pending approval.');
>>>>>>> 4b5c70f61c2ec44f89d856e84edc9911d93ebe3e
        }

        return $next($request);
    }
}
