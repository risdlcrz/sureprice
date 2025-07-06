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

    /**
     * Segment suppliers by material and rank them within each material group
     * @param array $suppliers All suppliers with their materials and metrics
     * @param array $projectFeatures Project requirements/features
     * @return array Material-segmented suppliers with rankings
     */
    public function segmentSuppliersByMaterial($suppliers, $projectFeatures = [])
    {
        $materialSegments = [];
        
        // Group suppliers by material
        foreach ($suppliers as $supplier) {
            foreach ($supplier['material_ids'] ?? [] as $materialId) {
                if (!isset($materialSegments[$materialId])) {
                    $materialSegments[$materialId] = [];
                }
                
                // Add supplier to this material segment
                $materialSegments[$materialId][] = [
                    'id' => $supplier['id'],
                    'name' => $supplier['name'],
                    'material_id' => $materialId,
                    'price' => $supplier['price'] ?? $supplier['cost'] ?? 0,
                    'on_time_delivery_rate' => $supplier['on_time_delivery_rate'] ?? 0,
                    'average_defect_rate' => $supplier['average_defect_rate'] ?? 0,
                    'average_cost_variance' => $supplier['average_cost_variance'] ?? 0,
                    'overall_score' => $this->calculateOverallScore($supplier, $projectFeatures),
                ];
            }
        }
        
        // Rank suppliers within each material segment
        foreach ($materialSegments as $materialId => &$materialSuppliers) {
            $materialSuppliers = $this->rankSuppliersInSegment($materialSuppliers);
        }
        
        return $materialSegments;
    }

    /**
     * Rank suppliers within a material segment by different criteria
     * @param array $suppliers Suppliers for a specific material
     * @return array Ranked suppliers with badges and categories
     */
    public function rankSuppliersInSegment($suppliers)
    {
        if (empty($suppliers)) {
            return [];
        }

        // Calculate rankings for each category
        $rankings = [
            'overall_best' => $this->rankByOverallScore($suppliers),
            'cheapest' => $this->rankByPrice($suppliers),
            'fastest_delivery' => $this->rankByDeliveryRate($suppliers),
            'least_defects' => $this->rankByDefectRate($suppliers),
        ];

        // Add badges and rankings to each supplier
        foreach ($suppliers as &$supplier) {
            $supplier['badges'] = [];
            $supplier['rankings'] = [];
            
            // Add badges based on rankings
            foreach ($rankings as $category => $rankedSuppliers) {
                $rank = array_search($supplier['id'], array_column($rankedSuppliers, 'id')) + 1;
                $supplier['rankings'][$category] = $rank;
                
                // Add badges for top performers
                if ($rank === 1) {
                    switch ($category) {
                        case 'overall_best':
                            $supplier['badges'][] = 'Overall Best';
                            break;
                        case 'cheapest':
                            $supplier['badges'][] = 'Cheapest';
                            break;
                        case 'fastest_delivery':
                            $supplier['badges'][] = 'Fastest Delivery';
                            break;
                        case 'least_defects':
                            $supplier['badges'][] = 'Least Defects';
                            break;
                    }
                }
            }
        }

        return $suppliers;
    }

    /**
     * Rank suppliers by overall score (KNN-based)
     */
    private function rankByOverallScore($suppliers)
    {
        usort($suppliers, function($a, $b) {
            return $b['overall_score'] <=> $a['overall_score'];
        });
        return $suppliers;
    }

    /**
     * Rank suppliers by price (lowest first)
     */
    private function rankByPrice($suppliers)
    {
        usort($suppliers, function($a, $b) {
            return $a['price'] <=> $b['price'];
        });
        return $suppliers;
    }

    /**
     * Rank suppliers by delivery rate (highest first)
     */
    private function rankByDeliveryRate($suppliers)
    {
        usort($suppliers, function($a, $b) {
            return $b['on_time_delivery_rate'] <=> $a['on_time_delivery_rate'];
        });
        return $suppliers;
    }

    /**
     * Rank suppliers by defect rate (lowest first)
     */
    private function rankByDefectRate($suppliers)
    {
        usort($suppliers, function($a, $b) {
            return $a['average_defect_rate'] <=> $b['average_defect_rate'];
        });
        return $suppliers;
    }

    /**
     * Calculate overall score for a supplier using KNN principles
     */
    private function calculateOverallScore($supplier, $projectFeatures)
    {
        $score = 0;
        $weights = [
            'on_time_delivery_rate' => 0.4,
            'average_defect_rate' => 0.3,
            'average_cost_variance' => 0.3,
        ];

        foreach ($weights as $metric => $weight) {
            if (isset($supplier[$metric]) && isset($projectFeatures[$metric])) {
                // Normalize the difference (lower is better for KNN)
                $difference = abs($supplier[$metric] - $projectFeatures[$metric]);
                $normalizedScore = max(0, 100 - $difference);
                $score += $normalizedScore * $weight;
            }
        }

        return $score;
    }

    /**
     * Get suppliers for a specific material with rankings
     * @param array $materialSegments Segmented suppliers by material
     * @param int $materialId Material ID
     * @param string $rankingCategory Category to sort by (overall_best, cheapest, fastest_delivery, least_defects)
     * @return array Ranked suppliers for the material
     */
    public function getSuppliersForMaterial($materialSegments, $materialId, $rankingCategory = 'overall_best')
    {
        if (!isset($materialSegments[$materialId])) {
            return [];
        }

        $suppliers = $materialSegments[$materialId];
        
        // Sort by the specified ranking category
        usort($suppliers, function($a, $b) use ($rankingCategory) {
            return $a['rankings'][$rankingCategory] <=> $b['rankings'][$rankingCategory];
        });

        return $suppliers;
    }

    /**
     * Optimize supplier selection across multiple materials using linear programming
     * @param array $materialSegments Segmented suppliers by material
     * @param array $materialQuantities Required quantities for each material
     * @param float $totalBudget Total available budget
     * @return array Optimal supplier selection with cost breakdown
     */
    public function optimizeMultiMaterialSelection($materialSegments, $materialQuantities, $totalBudget)
    {
        $optimalSelection = [];
        $totalCost = 0;
        $costBreakdown = [];
        $remainingBudget = $totalBudget;

        foreach ($materialQuantities as $materialId => $quantity) {
            if (!isset($materialSegments[$materialId])) {
                continue;
            }

            $suppliers = $materialSegments[$materialId];
            $bestSupplier = null;
            $bestValue = -1;

            // Find the best supplier for this material within budget
            foreach ($suppliers as $supplier) {
                $supplierCost = $supplier['price'] * $quantity;
                
                if ($supplierCost <= $remainingBudget) {
                    // Calculate value score (overall score / cost ratio)
                    $valueScore = $supplier['overall_score'] / max(1, $supplierCost);
                    
                    if ($valueScore > $bestValue) {
                        $bestValue = $valueScore;
                        $bestSupplier = $supplier;
                    }
                }
            }

            if ($bestSupplier) {
                $supplierCost = $bestSupplier['price'] * $quantity;
                $optimalSelection[$materialId] = [
                    'supplier' => $bestSupplier,
                    'quantity' => $quantity,
                    'cost' => $supplierCost,
                    'unit_price' => $bestSupplier['price'],
                ];
                
                $totalCost += $supplierCost;
                $remainingBudget -= $supplierCost;
                
                $costBreakdown[$materialId] = [
                    'material_name' => "Material ID: $materialId", // You can enhance this with actual material names
                    'supplier_name' => $bestSupplier['name'],
                    'quantity' => $quantity,
                    'unit_price' => $bestSupplier['price'],
                    'total_cost' => $supplierCost,
                    'badges' => $bestSupplier['badges'],
                ];
            }
        }

        return [
            'optimal_selection' => $optimalSelection,
            'total_cost' => $totalCost,
            'remaining_budget' => $remainingBudget,
            'budget_utilization' => ($totalCost / $totalBudget) * 100,
            'cost_breakdown' => $costBreakdown,
            'fits_budget' => $totalCost <= $totalBudget,
        ];
    }

    // Brute-force KNN using current metrics (keeping for backward compatibility)
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

    // Brute-force LP (keeping for backward compatibility)
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