<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Supplier;
use App\Models\SupplierEvaluation;
use Carbon\Carbon;

class SupplierEvaluationSeeder extends Seeder
{
    public function run()
    {
        $suppliers = Supplier::all();
        foreach ($suppliers as $supplier) {
            // Seed 3-5 past evaluations per supplier
            for ($i = 0; $i < rand(3, 5); $i++) {
                $date = Carbon::now()->subMonths(rand(1, 12))->subDays(rand(0, 30));
                SupplierEvaluation::create([
                    'supplier_id' => $supplier->id,
                    'engagement_score' => min(rand(70, 100) / 10, 9.99),
                    'delivery_speed_score' => min(rand(60, 100) / 10, 9.99),
                    'delivery_ontime_ratio' => rand(80, 100),
                    'performance_score' => min(rand(70, 100) / 10, 9.99),
                    'quality_score' => min(rand(70, 100) / 10, 9.99),
                    'defect_ratio' => min(rand(0, 10), 9.99),
                    'cost_variance_score' => min(rand(70, 100) / 10, 9.99),
                    'cost_variance_ratio' => min(rand(0, 10), 9.99),
                    'sustainability_score' => min(rand(60, 100) / 10, 9.99),
                    'final_score' => min(rand(70, 100) / 10, 9.99),
                    'evaluation_date' => $date,
                ]);
            }
            // Seed a current evaluation
            SupplierEvaluation::create([
                'supplier_id' => $supplier->id,
                'engagement_score' => min(rand(70, 100) / 10, 9.99),
                'delivery_speed_score' => min(rand(60, 100) / 10, 9.99),
                'delivery_ontime_ratio' => rand(80, 100),
                'performance_score' => min(rand(70, 100) / 10, 9.99),
                'quality_score' => min(rand(70, 100) / 10, 9.99),
                'defect_ratio' => min(rand(0, 10), 9.99),
                'cost_variance_score' => min(rand(70, 100) / 10, 9.99),
                'cost_variance_ratio' => min(rand(0, 10), 9.99),
                'sustainability_score' => min(rand(60, 100) / 10, 9.99),
                'final_score' => min(rand(70, 100) / 10, 9.99),
                'evaluation_date' => Carbon::now(),
            ]);
        }
        $this->command->info('Supplier evaluations seeded successfully!');
        // DEMO/TEST DATA: All seeded evaluations are for testing/demo purposes
    }
} 