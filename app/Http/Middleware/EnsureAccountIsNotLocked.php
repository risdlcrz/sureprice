<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * A signed-in user whose account gets locked is signed out on their next request:
 * writes and JSON requests get a 403 with the lock message, page visits go to the sign-in page.
 * Runs on every web request (bootstrap/app.php), so it also covers sessions opened before the lock.
 */
class EnsureAccountIsNotLocked
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (!$user || !$user->isLocked()) {
            return $next($request);
        }

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $message = AuthenticatedSessionController::lockedMessage();

        // Built here rather than abort(): only the safe message, never exception details, even with APP_DEBUG on.
        if ($request->expectsJson()) {
            return response()->json(['message' => $message], 403);
        }
        if (!$request->isMethodSafe()) {
            abort(403, $message);   // HTML error page: shows the message only
        }

        return redirect()->route('login')->withErrors(['login' => $message]);
    }
}
