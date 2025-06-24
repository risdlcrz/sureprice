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
    }
} 