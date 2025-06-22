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
        $suppliers = Supplier::all();
        $materials = Material::all();
        foreach ($suppliers as $supplier) {
            foreach ($materials as $material) {
                DB::table('material_supplier')->updateOrInsert(
                    [
                        'material_id' => $material->id,
                        'supplier_id' => $supplier->id,
                    ],
                    [
                        'price' => $material->base_price ?? 0,
                        'lead_time' => '7 days',
                        'is_preferred' => 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }
    }
}