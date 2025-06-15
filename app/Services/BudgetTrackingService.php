<?php

namespace App\Services;

use App\Models\Contract;
use App\Models\PurchaseOrder;
use App\Models\Delivery;
use App\Models\Material;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BudgetTrackingService
{
    public function calculateContractBudget(Contract $contract)
    {
        try {
            $totalBudget = $contract->items->sum('total');
            $totalSpent = $this->calculateTotalSpent($contract);
            $remainingBudget = $totalBudget - $totalSpent;
            $budgetUtilization = ($totalSpent / max(1, $totalBudget)) * 100;

            return [
                'total_budget' => $totalBudget,
                'total_spent' => $totalSpent,
                'remaining_budget' => $remainingBudget,
                'budget_utilization' => $budgetUtilization,
                'is_over_budget' => $remainingBudget < 0
            ];
        } catch (\Exception $e) {
            Log::error('Error calculating contract budget: ' . $e->getMessage());
            throw $e;
        }
    }

    public function calculateTotalSpent(Contract $contract)
    {
        return $contract->purchaseOrders()
            ->whereHas('deliveries', function($query) {
                $query->where('status', 'received');
            })
            ->with(['deliveries' => function($query) {
                $query->where('status', 'received');
            }])
            ->get()
            ->sum(function($po) {
                return $po->deliveries->sum('actual_cost');
            });
    }

    public function trackMaterialCosts(Material $material)
    {
        try {
            $purchaseOrders = PurchaseOrder::whereHas('items', function($query) use ($material) {
                $query->where('material_id', $material->id);
            })->with(['deliveries' => function($query) {
                $query->where('status', 'received');
            }])->get();

            $totalQuantity = 0;
            $totalCost = 0;
            $averageUnitCost = 0;

            foreach ($purchaseOrders as $po) {
                foreach ($po->deliveries as $delivery) {
                    $deliveryItem = $delivery->items()
                        ->where('material_id', $material->id)
                        ->first();

                    if ($deliveryItem) {
                        $totalQuantity += $deliveryItem->good_quantity;
                        $totalCost += ($deliveryItem->good_quantity * $delivery->actual_cost / $delivery->total_units);
                    }
                }
            }

            if ($totalQuantity > 0) {
                $averageUnitCost = $totalCost / $totalQuantity;
            }

            return [
                'total_quantity' => $totalQuantity,
                'total_cost' => $totalCost,
                'average_unit_cost' => $averageUnitCost
            ];
        } catch (\Exception $e) {
            Log::error('Error tracking material costs: ' . $e->getMessage());
            throw $e;
        }
    }

    public function generateBudgetReport(Contract $contract)
    {
        try {
            $budget = $this->calculateContractBudget($contract);
            $materialCosts = [];

            foreach ($contract->items as $item) {
                $material = $item->material;
                $costs = $this->trackMaterialCosts($material);
                
                $materialCosts[] = [
                    'material' => $material->name,
                    'estimated_quantity' => $item->quantity,
                    'estimated_cost' => $item->total,
                    'actual_quantity' => $costs['total_quantity'],
                    'actual_cost' => $costs['total_cost'],
                    'average_unit_cost' => $costs['average_unit_cost'],
                    'variance' => $costs['total_cost'] - $item->total
                ];
            }

            return [
                'contract' => [
                    'number' => $contract->contract_number,
                    'client' => $contract->client->name,
                    'total_budget' => $budget['total_budget'],
                    'total_spent' => $budget['total_spent'],
                    'remaining_budget' => $budget['remaining_budget'],
                    'budget_utilization' => $budget['budget_utilization'],
                    'is_over_budget' => $budget['is_over_budget']
                ],
                'materials' => $materialCosts
            ];
        } catch (\Exception $e) {
            Log::error('Error generating budget report: ' . $e->getMessage());
            throw $e;
        }
    }

    public function checkBudgetAlert(Contract $contract)
    {
        $budget = $this->calculateContractBudget($contract);
        
        if ($budget['budget_utilization'] >= 90) {
            return [
                'type' => 'warning',
                'message' => 'Contract budget utilization is at ' . number_format($budget['budget_utilization'], 2) . '%'
            ];
        }

        if ($budget['is_over_budget']) {
            return [
                'type' => 'danger',
                'message' => 'Contract is over budget by ' . number_format(abs($budget['remaining_budget']), 2)
            ];
        }

        return null;
    }
} 