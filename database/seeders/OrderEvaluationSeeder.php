<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PurchaseOrder;
use App\Models\OrderEvaluation;

class OrderEvaluationSeeder extends Seeder
{
    public function run()
    {
        $orders = PurchaseOrder::all();
        foreach ($orders as $order) {
            OrderEvaluation::updateOrCreate(
                ['order_id' => $order->id],
                [
                    'quality_rating' => rand(1, 5),
                    'has_complaints' => (bool)rand(0, 1),
                ]
            );
        }
        $this->command->info('Order evaluations seeded successfully!');
    }
} 