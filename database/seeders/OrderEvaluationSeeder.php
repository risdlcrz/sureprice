<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OrderEvaluationSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = Supplier::all();
        foreach ($suppliers as $supplier) {
            $orderCount = rand(3, 8);
            for ($i = 0; $i < $orderCount; $i++) {
                $totalDeliveries = rand(5, 20);
                $ontimeDeliveries = rand((int)($totalDeliveries * 0.7), $totalDeliveries);
                $totalUnits = rand(100, 1000);
                $defectiveUnits = rand(0, (int)($totalUnits * 0.05));
                $actualCost = rand(10000, 50000);
                $estimatedCost = $actualCost + rand(-1000, 2000);
                DB::table('order_evaluations')->insert([
                    'supplier_id' => $supplier->id,
                    'ontime_deliveries' => $ontimeDeliveries,
                    'total_deliveries' => $totalDeliveries,
                    'defective_units' => $defectiveUnits,
                    'total_units' => $totalUnits,
                    'actual_cost' => $actualCost,
                    'estimated_cost' => $estimatedCost,
                    'order_date' => Carbon::now()->subMonths($i),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
        $this->command->info('Order evaluations seeded successfully!');
    }
} 