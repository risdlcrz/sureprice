<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Supplier;
use App\Models\SupplierMetrics;

class SupplierMetricsSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = Supplier::all();
        foreach ($suppliers as $supplier) {
            SupplierMetrics::updateOrCreate(
                ['supplier_id' => $supplier->id],
                [
                    'total_deliveries' => rand(10, 100),
                    'ontime_deliveries' => rand(5, 100),
                    'average_defect_rate' => rand(0, 10) / 100, // 0% to 10%
                    'average_cost_variance' => rand(-5, 10) / 100, // -5% to +10%
                ]
            );
        }
        $this->command->info('Supplier metrics seeded successfully!');
    }
} 