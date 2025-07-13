<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Services\NotificationService;

class NotificationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Share notification count with all views
        View::composer('*', function ($view) {
            if (auth()->check()) {
                $unreadCount = NotificationService::getUnreadCount();
                $view->with('globalUnreadCount', $unreadCount);
            } else {
                $view->with('globalUnreadCount', 0);
            }
        });
    }
} 