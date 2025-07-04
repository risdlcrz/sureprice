<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Supplier;
use App\Models\Material;

class MaterialSupplierSeeder extends Seeder
{
    public function run()
    {
        $materials = \App\Models\Material::all();
        $suppliers = \App\Models\Supplier::all();
        // DEMO/TEST DATA: All seeded supplier-material links are for testing/demo purposes
        foreach ($materials as $material) {
            foreach ($suppliers as $supplier) {
                $material->suppliers()->syncWithoutDetaching([
                    $supplier->id => [
                        'price' => rand(100, 1000),
                        'lead_time' => rand(1, 14) . ' days',
                        'is_preferred' => (bool)rand(0, 1),
                    ]
                ]);
            }
        }
    }
}