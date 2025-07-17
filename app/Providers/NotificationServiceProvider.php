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
            if (\Illuminate\Support\Facades\Auth::check()) {
                $unreadCount = NotificationService::getUnreadCount();
                $view->with('globalUnreadCount', $unreadCount);
                // Add unread messages count
                $user = \Illuminate\Support\Facades\Auth::user();
                $messagesUnreadCount = \App\Models\Message::whereHas('conversation', function($q) use ($user) {
                    if ($user->role === 'manager' || $user->user_type === 'admin') {
                        $q->where('admin_id', $user->id);
                    } elseif ($user->user_type === 'company' && $user->company && $user->company->designation === 'client') {
                        $q->where('client_id', $user->id);
                    } elseif ($user->user_type === 'company' && $user->company && $user->company->designation === 'supplier') {
                        $q->where('supplier_id', $user->company->id);
                    } else {
                        $q->where('id', 0); // No conversations
                    }
                })
                ->where('is_read', false)
                ->where('sender_id', '!=', $user->id)
                ->count();
                $view->with('messagesUnreadCount', $messagesUnreadCount);
            } else {
                $view->with('globalUnreadCount', 0);
                $view->with('messagesUnreadCount', 0);
            }
        });
    }
} 