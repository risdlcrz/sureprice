<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Warehouse;

class WarehouseSeeder extends Seeder
{
    public function run()
    {
        Warehouse::firstOrCreate(['name' => 'Warehouse A']);
        Warehouse::firstOrCreate(['name' => 'Warehouse B']);
    }
} 