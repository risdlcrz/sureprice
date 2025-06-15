<?php

namespace App\Services;

use App\Models\Inventory;
use App\Models\Material;
use App\Models\PurchaseOrder;
use App\Models\Delivery;
use App\Models\SupplierEvaluation;
use App\Models\SupplierMetrics;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InventoryAutomationService
{
    public function processDelivery(Delivery $delivery)
    {
        try {
            DB::beginTransaction();

            // Update inventory for each delivery item
            foreach ($delivery->items as $item) {
                $inventory = Inventory::where('material_id', $item->material_id)->first();
                
                if (!$inventory) {
                    $inventory = new Inventory([
                        'material_id' => $item->material_id,
                        'quantity' => 0,
                        'unit' => $item->material->unit,
                        'minimum_threshold' => $item->material->minimum_stock,
                        'status' => 'active'
                    ]);
                }

                // Add delivered quantity to inventory
                $inventory->updateStock($item->quantity, 'add');
                $inventory->last_restock_date = now();
                $inventory->last_restock_quantity = $item->quantity;
                $inventory->save();

                // Update material's current stock
                $material = $item->material;
                $material->current_stock = $inventory->quantity;
                $material->save();
            }

            // Update supplier metrics
            $this->updateSupplierMetrics($delivery);

            // Create supplier evaluation if not exists
            if (!$delivery->supplierEvaluation) {
                $this->createSupplierEvaluation($delivery);
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error processing delivery: ' . $e->getMessage());
            throw $e;
        }
    }

    protected function updateSupplierMetrics(Delivery $delivery)
    {
        $metrics = SupplierMetrics::firstOrNew(['supplier_id' => $delivery->purchaseOrder->supplier_id]);
        
        $metrics->total_deliveries += 1;
        $metrics->ontime_deliveries += $delivery->is_on_time ? 1 : 0;
        
        // Calculate defect rate
        $defectRate = ($delivery->defective_units / max(1, $delivery->total_units)) * 100;
        $metrics->average_defect_rate = ($metrics->average_defect_rate * ($metrics->total_deliveries - 1) + $defectRate) / $metrics->total_deliveries;
        
        // Calculate cost variance
        $costVariance = (($delivery->actual_cost - $delivery->estimated_cost) / max(1, $delivery->estimated_cost)) * 100;
        $metrics->average_cost_variance = ($metrics->average_cost_variance * ($metrics->total_deliveries - 1) + $costVariance) / $metrics->total_deliveries;
        
        $metrics->save();
    }

    protected function createSupplierEvaluation(Delivery $delivery)
    {
        $metrics = $delivery->purchaseOrder->supplier->metrics;
        
        $evaluation = new SupplierEvaluation([
            'supplier_id' => $delivery->purchaseOrder->supplier_id,
            'delivery_speed_score' => $this->calculateDeliverySpeedScore($delivery),
            'delivery_ontime_ratio' => ($metrics->ontime_deliveries / max(1, $metrics->total_deliveries)) * 100,
            'quality_score' => $this->calculateQualityScore($delivery),
            'defect_ratio' => ($delivery->defective_units / max(1, $delivery->total_units)) * 100,
            'cost_variance_score' => $this->calculateCostVarianceScore($delivery),
            'cost_variance_ratio' => (($delivery->actual_cost - $delivery->estimated_cost) / max(1, $delivery->estimated_cost)) * 100,
            'performance_score' => $this->calculatePerformanceScore($delivery),
            'engagement_score' => 5.0, // Default score, can be updated manually
            'sustainability_score' => 5.0, // Default score, can be updated manually
            'final_score' => 0, // Will be calculated
            'evaluation_date' => now()
        ]);

        // Calculate final score
        $evaluation->final_score = $this->calculateFinalScore($evaluation);
        $evaluation->save();

        // Link evaluation to delivery
        $delivery->supplier_evaluation_id = $evaluation->id;
        $delivery->save();
    }

    protected function calculateDeliverySpeedScore(Delivery $delivery): float
    {
        if (!$delivery->is_on_time) {
            return 1.0;
        }
        
        $expectedDate = $delivery->purchaseOrder->expected_delivery_date;
        $actualDate = $delivery->delivery_date;
        
        if ($actualDate <= $expectedDate) {
            return 5.0;
        }
        
        $daysLate = $expectedDate->diffInDays($actualDate);
        return max(1.0, 5.0 - ($daysLate * 0.5));
    }

    protected function calculateQualityScore(Delivery $delivery): float
    {
        $defectRate = ($delivery->defective_units / max(1, $delivery->total_units)) * 100;
        
        if ($defectRate <= 1) {
            return 5.0;
        } elseif ($defectRate <= 5) {
            return 4.0;
        } elseif ($defectRate <= 10) {
            return 3.0;
        } elseif ($defectRate <= 15) {
            return 2.0;
        }
        
        return 1.0;
    }

    protected function calculateCostVarianceScore(Delivery $delivery): float
    {
        $variance = abs(($delivery->actual_cost - $delivery->estimated_cost) / max(1, $delivery->estimated_cost)) * 100;
        
        if ($variance <= 5) {
            return 5.0;
        } elseif ($variance <= 10) {
            return 4.0;
        } elseif ($variance <= 15) {
            return 3.0;
        } elseif ($variance <= 20) {
            return 2.0;
        }
        
        return 1.0;
    }

    protected function calculatePerformanceScore(Delivery $delivery): float
    {
        $deliveryScore = $this->calculateDeliverySpeedScore($delivery);
        $qualityScore = $this->calculateQualityScore($delivery);
        $costScore = $this->calculateCostVarianceScore($delivery);
        
        return ($deliveryScore + $qualityScore + $costScore) / 3;
    }

    protected function calculateFinalScore(SupplierEvaluation $evaluation): float
    {
        $weights = [
            'engagement_score' => 0.15,
            'delivery_speed_score' => 0.25,
            'performance_score' => 0.20,
            'quality_score' => 0.20,
            'cost_variance_score' => 0.10,
            'sustainability_score' => 0.10
        ];

        $finalScore = 0;
        foreach ($weights as $field => $weight) {
            $finalScore += $evaluation->$field * $weight;
        }

        return $finalScore;
    }
} 