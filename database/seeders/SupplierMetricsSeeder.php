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
        $topSuppliers = [
            'Bughaw Builders' => [
                'delivery_rate' => 98, // Matches 4.9 delivery score
                'defect_rate' => 0.01, // Matches 4.8 quality score
                'cost_variance' => 0.008, // Matches 4.6 cost score
            ],
            'Matatag Merchandising' => [
                'delivery_rate' => 94, // Matches 4.7 delivery score
                'defect_rate' => 0.02, // Matches 4.6 quality score
                'cost_variance' => 0.012, // Matches 4.4 cost score
            ],
            'Tibay Trading' => [
                'delivery_rate' => 90, // Matches 4.5 delivery score
                'defect_rate' => 0.03, // Matches 4.4 quality score
                'cost_variance' => 0.016, // Matches 4.2 cost score
            ],
        ];

        foreach ($suppliers as $supplier) {
            $metrics = $topSuppliers[$supplier->company_name] ?? null;
            
            $totalDeliveries = rand(80, 100);
            $totalUnits = rand(1000, 5000);
            $actualCost = rand(500000, 1000000);
            
            SupplierMetrics::updateOrCreate(
                ['supplier_id' => $supplier->id],
                [
                    'total_deliveries' => $totalDeliveries,
                    'ontime_deliveries' => $metrics ? 
                        round($totalDeliveries * ($metrics['delivery_rate'] / 100)) : 
                        rand((int)($totalDeliveries * 0.7), $totalDeliveries),
                    'average_defect_rate' => $metrics ? $metrics['defect_rate'] : rand(5, 15) / 100,
                    'average_cost_variance' => $metrics ? $metrics['cost_variance'] : rand(3, 10) / 100,
                    'total_units' => $totalUnits,
                    'defective_units' => $metrics ? 
                        round($totalUnits * $metrics['defect_rate']) : 
                        rand(0, (int)($totalUnits * 0.05)),
                    'actual_cost' => $actualCost,
                    'estimated_cost' => $metrics ? 
                        round($actualCost * (1 + $metrics['cost_variance'])) : 
                        $actualCost + rand(-5000, 10000),
                ]
            );
        }
        $this->command->info('Supplier metrics seeded successfully!');
    }
} 