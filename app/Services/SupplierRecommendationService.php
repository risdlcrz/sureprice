<?php

namespace App\Services;

use App\Models\Supplier;
use Illuminate\Support\Collection;

class SupplierRecommendationService
{
    protected $rankingService;
    protected $weights = [
        'engagement' => 0.15,
        'delivery' => 0.25,
        'performance' => 0.20,
        'quality' => 0.20,
        'cost' => 0.10,
        'sustainability' => 0.10
    ];

    public function __construct(SupplierRankingService $rankingService)
    {
        $this->rankingService = $rankingService;
    }

    public function getRecommendedSuppliers($material = null)
    {
        // Get suppliers with their evaluations and metrics
        $suppliers = Supplier::with(['evaluations', 'metrics', 'materials'])->get();
        
        // Calculate rankings using the existing ranking service
        $rankings = $this->rankingService->calculateRankings($suppliers);
        
        // If a specific material is selected, filter suppliers
        if ($material) {
            $rankings = $rankings->filter(function ($ranking) use ($material) {
                return $ranking['supplier']->materials->contains('id', $material->id);
            });
        }

        // Sort by score and take top suppliers
        $recommended = $rankings->sortByDesc('score')->take(5);

        // Format for recommendation display
        return $recommended->map(function ($ranking) {
            $supplier = $ranking['supplier'];
            $metrics = $supplier->metrics;
            $evaluation = $supplier->evaluations()->latest()->first();
            
            $deliveryScore = 0;
            $qualityScore = 0;
            $costScore = 0;
            
            if ($metrics) {
                // Calculate objective metrics scores (0-5 scale)
                $deliveryObj = ($metrics->total_deliveries > 0)
                    ? min(max(($metrics->ontime_deliveries / $metrics->total_deliveries) * 5, 0), 5)
                    : 0;
                $qualityObj = ($metrics->total_units > 0)
                    ? min(max((1 - ($metrics->defective_units / $metrics->total_units)) * 5, 0), 5)
                    : 0;
                $costObj = ($metrics->estimated_cost > 0)
                    ? min(max((1 - abs(($metrics->actual_cost - $metrics->estimated_cost) / $metrics->estimated_cost)) * 5, 0), 5)
                    : 0;

                if ($evaluation) {
                    // Combine subjective and objective scores
                    $deliveryScore = ($evaluation->delivery_speed_score * 0.5 + $deliveryObj * 0.5);
                    $qualityScore = ($evaluation->quality_score * 0.5 + $qualityObj * 0.5);
                    $costScore = ($evaluation->cost_variance_score * 0.5 + $costObj * 0.5);
                }
            }

            // Calculate on-time delivery rate percentage for display
            $onTimeDeliveryRate = $metrics && $metrics->total_deliveries > 0
                ? ($metrics->ontime_deliveries / $metrics->total_deliveries) * 100
                : 0;

            // Calculate defect rate percentage for display
            $defectRate = $metrics && $metrics->total_units > 0
                ? ($metrics->defective_units / $metrics->total_units) * 100
                : 0;

            // Calculate cost variance for display
            $costVariance = $metrics && $metrics->estimated_cost > 0
                ? abs(($metrics->actual_cost - $metrics->estimated_cost) / $metrics->estimated_cost)
                : 0;

            return [
                'supplier' => [
                    'name' => $supplier->company_name,
                    'on_time_delivery_rate' => round($onTimeDeliveryRate, 2),
                    'average_defect_rate' => round($defectRate, 2),
                    'average_cost_variance' => round($costVariance, 2),
                ],
                'score' => $ranking['score'],
                'rank' => $ranking['rank'],
                'distance' => 5 - $ranking['score'] // Convert score to distance (lower is better)
            ];
        })->sortBy('distance')->values();
    }
}