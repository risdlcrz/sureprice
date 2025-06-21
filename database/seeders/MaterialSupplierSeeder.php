<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Material;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;

class MaterialSupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $materials = Material::all();
        $suppliers = Supplier::all();

        if ($suppliers->isEmpty() || $materials->isEmpty()) {
            $this->command->info('No materials or suppliers found, skipping MaterialSupplierSeeder.');
            return;
        }

        foreach ($materials as $material) {
            // Assign a random number of suppliers to each material (e.g., 2 to 5)
            $assignedSuppliers = $suppliers->random(rand(2, min(5, $suppliers->count())));

            foreach ($assignedSuppliers as $supplier) {
                // Calculate a price variation based on the material's price (srp_price or base_price)
                $basePrice = $material->srp_price ?? $material->base_price ?? 100; // Fallback to 100 if both are null
                $variance = $basePrice * (rand(-15, 15) / 100); // +/- 15% variance
                $price = $basePrice + $variance;

                // Ensure price is not negative
                $price = max(0.01, $price);

                // Use updateOrCreate to prevent duplicates
                DB::table('material_supplier')->updateOrInsert(
                    [
                        'material_id' => $material->id,
                        'supplier_id' => $supplier->id,
                    ],
                    [
                        'price' => $price,
                        'lead_time' => rand(1, 14) . ' days',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }

        $this->command->info('Material-Supplier prices and lead times seeded successfully!');
    }
} 