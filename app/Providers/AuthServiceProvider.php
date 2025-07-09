<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        //
    ];

    public function boot()
    {
        $this->registerPolicies();

        Gate::define('approve-as-admin', function ($user) {
            return $user && $user->hasRole('admin');
        });

        Gate::define('approve-as-supplier', function ($user) {
            return $user && $user->hasRole('supplier');
        });

        // Material Request Gates
        Gate::define('approve-material-requests', function ($user) {
            return $user && ($user->hasRole('admin') || $user->hasRole('manager') || $user->hasRole('warehousing'));
        });

        Gate::define('reject-material-requests', function ($user) {
            return $user && ($user->hasRole('admin') || $user->hasRole('manager') || $user->hasRole('warehousing'));
        });

        Gate::define('complete-material-requests', function ($user) {
            return $user && ($user->hasRole('admin') || $user->hasRole('manager') || $user->hasRole('warehousing'));
        });
    }
} 