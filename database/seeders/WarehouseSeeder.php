<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Warehouse;

class WarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Warehouse::firstOrCreate([
            'name' => 'Main Warehouse',
        ], [
            // Add more fields here if your warehouses table requires them
        ]);
    }
} 