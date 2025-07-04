<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Material;
use App\Models\MaterialPriceHistory;
use Carbon\Carbon;

class MaterialPriceHistorySeeder extends Seeder
{
    public function run()
    {
        $materials = Material::all();
        foreach ($materials as $material) {
            $basePrice = $material->srp_price ?? rand(100, 1000);
            // Seed 6-12 months of price history
            for ($i = 0; $i < rand(6, 12); $i++) {
                $date = Carbon::now()->subMonths($i);
                $price = $basePrice * (1 + (rand(-10, 10) / 100));
                MaterialPriceHistory::create([
                    'material_id' => $material->id,
                    'price' => $price,
                    'date' => $date,
                ]);
            }
        }
        $this->command->info('Material price history seeded successfully!');
        // DEMO/TEST DATA: All seeded price histories are for testing/demo purposes
    }
} 