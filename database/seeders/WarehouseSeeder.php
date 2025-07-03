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
            'name' => 'Warehouse A',
        ]);
        Warehouse::firstOrCreate([
            'name' => 'Warehouse B',
        ]);
    }
} 