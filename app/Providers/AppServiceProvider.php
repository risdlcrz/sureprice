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

        // Numeric ids only, so static paths like /materials/all or /budgets/alerts aren't captured as ids.
        \Illuminate\Support\Facades\Route::pattern('material', '[0-9]+');
        \Illuminate\Support\Facades\Route::pattern('contract', '[0-9]+');
        
        // Register custom middleware aliases
        $this->app['router']->aliasMiddleware('admin', \App\Http\Middleware\AdminMiddleware::class);
        $this->app['router']->aliasMiddleware('role', \App\Http\Middleware\RoleMiddleware::class);
    }
    
}
