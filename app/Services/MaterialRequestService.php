<?php

namespace App\Services;

use App\Models\Material;
use App\Models\MaterialRequest;
use App\Models\PurchaseRequest;
use App\Models\Stock;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class MaterialRequestService
{
    public function processMaterialRequest(MaterialRequest $materialRequest)
    {
        // Get warehouses
        $warehouseA = Warehouse::where('name', 'Warehouse A')->firstOrFail();
        $warehouseB = Warehouse::where('name', 'Warehouse B')->firstOrFail();

        $insufficientMaterials = [];

        foreach ($materialRequest->items as $item) {
            $material = Material::findOrFail($item->material_id);
            
            // Get total stock across both warehouses
            $stockA = Stock::where('warehouse_id', $warehouseA->id)
                         ->where('material_id', $material->id)
                         ->first();
                         
            $stockB = Stock::where('warehouse_id', $warehouseB->id)
                         ->where('material_id', $material->id)
                         ->first();

            $totalStock = ($stockA ? $stockA->current_stock : 0) + 
                         ($stockB ? $stockB->current_stock : 0);

            // Check if stock is insufficient
            if ($totalStock < $item->quantity) {
                $insufficientMaterials[] = [
                    'material' => $material,
                    'required' => $item->quantity,
                    'available' => $totalStock,
                    'shortage' => $item->quantity - $totalStock
                ];
            }
        }

        // If there are insufficient materials, create purchase request
        if (!empty($insufficientMaterials)) {
            return $this->createPurchaseRequest($insufficientMaterials, $materialRequest);
        }

        return null;
    }

    private function createPurchaseRequest(array $insufficientMaterials, MaterialRequest $materialRequest): ?PurchaseRequest
    {
        return DB::transaction(function () use ($insufficientMaterials, $materialRequest) {
            $purchaseRequest = PurchaseRequest::create([
                'contract_id' => $materialRequest->contract_id,
                'request_number' => 'PR-' . date('YmdHis'),
                'requested_by' => Auth::id(),
                'department' => 'Warehouse',
                'required_date' => now()->addDays(7),
                'purpose' => 'Stock replenishment for material request #' . $materialRequest->id,
                'status' => 'pending_admin_approval',
                'notes' => 'Automatically generated due to insufficient stock'
            ]);

            foreach ($insufficientMaterials as $item) {
                $purchaseRequest->items()->create([
                    'material_id' => $item['material']->id,
                    'description' => $item['material']->name,
                    'quantity' => $item['shortage'],
                    'unit' => $item['material']->unit,
                    'estimated_unit_price' => $item['material']->price,
                    'total_amount' => $item['shortage'] * $item['material']->price,
                    'notes' => 'Current stock: ' . $item['available'] . ', Required: ' . $item['required']
                ]);
            }

            return $purchaseRequest;
        });
    }
} 