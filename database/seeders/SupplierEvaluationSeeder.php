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
        $topSuppliers = [
            'Bughaw Builders' => [
                'delivery_speed_score' => 4.9, // Highest weight (25%)
                'quality_score' => 4.8, // Second highest (20%) 
                'performance_score' => 4.8, // Also 20%
                'engagement_score' => 4.7, // 15%
                'cost_variance_score' => 4.6, // 10%
                'sustainability_score' => 4.6, // 10%
            ],
            'Matatag Merchandising' => [
                'delivery_speed_score' => 4.7,
                'quality_score' => 4.6,
                'performance_score' => 4.6,
                'engagement_score' => 4.5,
                'cost_variance_score' => 4.4,
                'sustainability_score' => 4.4,
            ],
            'Tibay Trading' => [
                'delivery_speed_score' => 4.5,
                'quality_score' => 4.4,
                'performance_score' => 4.4,
                'engagement_score' => 4.3,
                'cost_variance_score' => 4.2,
                'sustainability_score' => 4.2,
            ],
        ];

        foreach ($suppliers as $supplier) {
            // For top suppliers, use predefined scores
            $scores = $topSuppliers[$supplier->company_name] ?? null;
            
            // Seed 3-5 past evaluations per supplier
            for ($i = 0; $i < rand(3, 5); $i++) {
                $date = Carbon::now()->subMonths(rand(1, 12))->subDays(rand(0, 30));
                SupplierEvaluation::create([
                    'supplier_id' => $supplier->id,
                    'engagement_score' => $scores ? $scores['engagement_score'] : rand(30, 45) / 10,
                    'delivery_speed_score' => $scores ? $scores['delivery_speed_score'] : rand(30, 45) / 10,
                    'delivery_ontime_ratio' => $scores ? 90 : rand(70, 85),
                    'performance_score' => $scores ? $scores['performance_score'] : rand(30, 45) / 10,
                    'quality_score' => $scores ? $scores['quality_score'] : rand(30, 45) / 10,
                    'defect_ratio' => $scores ? 0.03 : rand(5, 15) / 100,
                    'cost_variance_score' => $scores ? $scores['cost_variance_score'] : rand(30, 45) / 10,
                    'cost_variance_ratio' => $scores ? 0.02 : rand(5, 15) / 100,
                    'sustainability_score' => $scores ? $scores['sustainability_score'] : rand(30, 45) / 10,
                    'final_score' => $scores ? 4.45 : rand(30, 45) / 10,
                    'evaluation_date' => $date,
                ]);
            }
            // Seed a current evaluation
            SupplierEvaluation::create([
                'supplier_id' => $supplier->id,
                'engagement_score' => rand(30, 50) / 10,
                'delivery_speed_score' => rand(30, 50) / 10,
                'delivery_ontime_ratio' => rand(80, 100),
                'performance_score' => rand(30, 50) / 10,
                'quality_score' => rand(30, 50) / 10,
                'defect_ratio' => rand(0, 5) / 10,
                'cost_variance_score' => rand(30, 50) / 10,
                'cost_variance_ratio' => rand(0, 5) / 10,
                'sustainability_score' => rand(30, 50) / 10,
                'final_score' => rand(30, 50) / 10,
                'evaluation_date' => Carbon::now(),
            ]);
        }
        $this->command->info('Supplier evaluations seeded successfully!');
        // DEMO/TEST DATA: All seeded evaluations are for testing/demo purposes
    }
} 