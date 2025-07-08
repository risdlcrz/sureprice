<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
   public function run()
{
    // Remove the factory call completely
    // User::factory()->create([...]);

    // Add your admin seeder
    $this->call([
        EmployeeSeeder::class,
        CompanySeeder::class,
        AdminSeeder::class,
        SupplierMetricsSeeder::class,
        OrderEvaluationSeeder::class,
        MaterialSeeder::class,
        WarehouseSeeder::class,
        MaterialSupplierSeeder::class,
        MaterialSupplierPriceHistorySeeder::class,
        WarehouseStockSeeder::class,
        ScopeTypeSeeder::class,
        ScopeTypeMaterialSeeder::class,
        SyncEmployeesToUsersSeeder::class,
        CategorySeeder::class,
        OrderEvaluationSeeder::class,
        SupplierEvaluationSeeder::class,
        MaterialPriceHistorySeeder::class,
        TransactionSeeder::class,
    ]);
}
}