<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Our notifications table has an auto-increment id; see the channel for details.
        $this->app->bind(\Illuminate\Notifications\Channels\DatabaseChannel::class, \App\Notifications\Channels\DatabaseChannel::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrap();

        // A fresh error reference per request (App\Support\SafeErrors).
        \App\Support\SafeErrors::reset();

        // Debug pages show stack traces, SQL and file paths: only ever on a developer machine.
        if (config('app.debug') && !$this->app->environment('local', 'testing')) {
            config(['app.debug' => false]);
            \Illuminate\Support\Facades\Log::error('APP_DEBUG is on outside the local environment; it was turned off for this request. Set APP_DEBUG=false in .env.');
        }

        // Behind TLS in production every generated URL (links, forms, signed URLs) uses https.
        if ($this->app->isProduction()) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        // Password policy for sign-up, reset and change (Password::defaults()).
        \Illuminate\Validation\Rules\Password::defaults(fn () => \Illuminate\Validation\Rules\Password::min(8)->letters()->numbers());

        // Named rate limiters (throttle:<name> in routes). Each is counted per route and per user (or IP),
        // so e.g. a burst of sign-ups doesn't also block password resets from the same address.
        $limit = fn (int $perMinute) => fn (\Illuminate\Http\Request $request) =>
            \Illuminate\Cache\RateLimiting\Limit::perMinute($perMinute)
                ->by(($request->route()?->getName() ?? $request->path()) . '|' . ($request->user()?->id ?: $request->ip()));
        \Illuminate\Support\Facades\RateLimiter::for('auth', $limit(5));       // sign-up, password reset / confirm
        \Illuminate\Support\Facades\RateLimiter::for('public-form', $limit(10)); // guest quotation form
        \Illuminate\Support\Facades\RateLimiter::for('search', $limit(60));    // LIKE searches
        // Uploads and imports: one shared budget per user across all upload routes.
        \Illuminate\Support\Facades\RateLimiter::for('uploads', fn (\Illuminate\Http\Request $request) =>
            \Illuminate\Cache\RateLimiting\Limit::perMinute(20)->by('uploads|' . ($request->user()?->id ?: $request->ip())));

        // Numeric ids only, so static paths like /materials/all or /budgets/alerts aren't captured as ids.
        \Illuminate\Support\Facades\Route::pattern('material', '[0-9]+');
        \Illuminate\Support\Facades\Route::pattern('contract', '[0-9]+');
        
        // Register custom middleware aliases
        $this->app['router']->aliasMiddleware('admin', \App\Http\Middleware\AdminMiddleware::class);
        $this->app['router']->aliasMiddleware('role', \App\Http\Middleware\RoleMiddleware::class);
    }
    
}
