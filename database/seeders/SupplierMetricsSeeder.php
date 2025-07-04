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
                    'total_deliveries' => $totalDeliveries = rand(10, 100),
                    'ontime_deliveries' => $ontimeDeliveries = rand((int)($totalDeliveries * 0.7), $totalDeliveries),
                    'average_defect_rate' => $defectRate = rand(0, 5) / 100, // 0% to 5%
                    'average_cost_variance' => $costVariance = rand(-2, 5) / 100, // -2% to +5%
                    // The following fields are for modal display and scoring, add if not present in your table:
                    'total_units' => $totalUnits = rand(100, 1000),
                    'defective_units' => $defectiveUnits = rand(0, (int)($totalUnits * 0.05)),
                    'actual_cost' => $actualCost = rand(10000, 50000),
                    'estimated_cost' => $estimatedCost = $actualCost + rand(-1000, 2000),
                ]
            );
        }
        $this->command->info('Supplier metrics seeded successfully!');
    }
} 