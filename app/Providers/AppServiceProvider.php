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

        // Numeric ids only, so static paths like /materials/all or /budgets/alerts aren't captured as ids.
        \Illuminate\Support\Facades\Route::pattern('material', '[0-9]+');
        \Illuminate\Support\Facades\Route::pattern('contract', '[0-9]+');
        
        // Register custom middleware aliases
        $this->app['router']->aliasMiddleware('admin', \App\Http\Middleware\AdminMiddleware::class);
        $this->app['router']->aliasMiddleware('role', \App\Http\Middleware\RoleMiddleware::class);
    }
    
}
