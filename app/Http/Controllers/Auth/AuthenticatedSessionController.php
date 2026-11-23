<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

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
    /** The one message for a wrong login or password: it never says whether the account exists or is locked. */
    private const INVALID = 'These credentials do not match our records.';

    /** Attempts per login + IP address per minute before sign-in is throttled. */
    private const ATTEMPTS_PER_MINUTE = 5;

    public function store(Request $request)
    {
        // Validate input - 'login' can be email or username
        $credentials = $request->validate([
            'login' => 'required|string|max:255',
            'password' => 'required|string',
        ]);

        $throttleKey = Str::lower($credentials['login']) . '|' . $request->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, self::ATTEMPTS_PER_MINUTE)) {
            return $this->failed($request, 'Too many sign-in attempts. Please try again in ' . RateLimiter::availableIn($throttleKey) . ' seconds.', 429);
        }

        // Determine if input is email or username
        $fieldType = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $user = User::where($fieldType, $credentials['login'])->first();

        // Check the password even for unknown logins, so the response time doesn't reveal which accounts exist.
        $passwordOk = Hash::check($credentials['password'], $user?->password ?? self::dummyHash());

        if (!$user || !$passwordOk) {
            RateLimiter::hit($throttleKey, 60);
            // Counts toward the automatic lock (User::MAX_FAILED_LOGINS); the response stays the same either way.
            $user?->registerFailedLogin();

            return $this->failed($request, self::INVALID, 422);
        }

        // Correct password on a locked account: only now say it's locked (the internal reason is not shown).
        if ($user->isLocked()) {
            return $this->failed($request, self::lockedMessage(), 403);
        }

        RateLimiter::clear($throttleKey);
        $user->clearFailedLogins();
        Auth::login($user);
        $request->session()->regenerate();

        $user->last_login_at = now();
        $user->save();

        // A client who started a quotation as a guest goes back to finish it.
        if ($user->isClientCompany() && $user->company->status === 'approved'
            && $request->session()->has('redirect_after_login') && $request->session()->has('guest_quotation_data')) {
            return redirect()->route('client.quotation.create');
        }

        return redirect()->route($user->homeRoute());
    }

    /** Shown to someone who signed in correctly but whose account is locked (also used by EnsureAccountIsNotLocked). */
    public static function lockedMessage(): string
    {
        return 'This account has been locked. Please contact support at ' . config('app.support_email') . ' to restore access.';
    }

    private function failed(Request $request, string $message, int $jsonStatus)
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => $message], $jsonStatus);
        }

        return back()->withErrors(['login' => $message])->onlyInput('login');
    }

    private static function dummyHash(): string
    {
        static $hash = null;

        return $hash ??= Hash::make(Str::random(32));
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