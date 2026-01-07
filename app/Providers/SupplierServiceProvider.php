<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\SupplierRankingService;
use App\Services\SupplierRecommendationService;

class SupplierServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(SupplierRankingService::class, function ($app) {
            return new SupplierRankingService();
        });

        $this->app->singleton(SupplierRecommendationService::class, function ($app) {
            return new SupplierRecommendationService(
                $app->make(SupplierRankingService::class)
            );
        });
    }
}