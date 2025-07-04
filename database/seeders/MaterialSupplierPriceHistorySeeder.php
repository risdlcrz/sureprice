<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Material;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MaterialSupplierPriceHistorySeeder extends Seeder
{
    public function run(): void
    {
        $materials = Material::all();
        $suppliers = Supplier::all();
        foreach ($materials as $material) {
            foreach ($suppliers as $supplier) {
                $basePrice = rand(100, 1000);
                $months = rand(6, 12);
                for ($i = 0; $i < $months; $i++) {
                    $date = Carbon::now()->subMonths($i);
                    $price = $basePrice * (1 + (rand(-10, 10) / 100));
                    DB::table('material_supplier_price_histories')->insert([
                        'material_id' => $material->id,
                        'supplier_id' => $supplier->id,
                        'price' => $price,
                        'date' => $date,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
        $this->command->info('Material supplier price histories seeded successfully!');
    }
} 