<?php

namespace App\Services;

class SupplierSelectionService
{
    // Filter suppliers by material
    public function filterByMaterial($suppliers, $materialId)
    {
        return array_filter($suppliers, function($supplier) use ($materialId) {
            return in_array($materialId, $supplier['material_ids'] ?? []);
        });
    }

    // Brute-force KNN using current metrics
    public function recommend($suppliers, $projectFeatures, $k = 5)
    {
        $distances = [];
        foreach ($suppliers as $supplier) {
            $distance = 0;
            // Use only the selected metrics
            foreach (['on_time_delivery_rate', 'average_defect_rate', 'average_cost_variance'] as $key) {
                if (isset($projectFeatures[$key]) && isset($supplier[$key])) {
                    $distance += pow($supplier[$key] - $projectFeatures[$key], 2);
                }
            }
            $distances[] = ['supplier' => $supplier, 'distance' => sqrt($distance)];
        }
        usort($distances, function($a, $b) {
            return $a['distance'] <=> $b['distance'];
        });
        return array_slice($distances, 0, $k);
    }

    // Brute-force LP
    public function optimize($recommendedSuppliers, $budget)
    {
        $n = count($recommendedSuppliers);
        $bestScore = -INF;
        $bestCombo = [];

        for ($i = 1; $i < (1 << $n); $i++) {
            $combo = [];
            $totalCost = 0;
            $totalScore = 0;
            foreach (range(0, $n - 1) as $j) {
                if ($i & (1 << $j)) {
                    $supplier = $recommendedSuppliers[$j]['supplier'];
                    $totalCost += $supplier['cost'] ?? 0;
                    // Score can be a weighted sum of metrics (example)
                    $score = ($supplier['on_time_delivery_rate'] ?? 0) - ($supplier['average_defect_rate'] ?? 0) - abs($supplier['average_cost_variance'] ?? 0);
                    $totalScore += $score;
                    $combo[] = $supplier;
                }
            }
            if ($totalCost <= $budget && $totalScore > $bestScore) {
                $bestScore = $totalScore;
                $bestCombo = $combo;
            }
        }
        return $bestCombo;
    }
} 