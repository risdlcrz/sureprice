<?php

namespace App\Services;

use App\Models\Contract;
use App\Models\Stock;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StockService
{
    public function checkStockForContract(Contract $contract)
    {
        $results = [];
        
        foreach ($contract->items as $item) {
            $stock = Stock::where('item_id', $item->item_id)->first();
            
            $results[] = [
                'item_id' => $item->item_id,
                'item_name' => $item->item->name,
                'quantity_required' => $item->quantity,
                'quantity_available' => $stock ? $stock->quantity : 0,
                'status' => $this->getStockStatus($item->quantity, $stock ? $stock->quantity : 0)
            ];
        }
        
        return $results;
    }

    protected function getStockStatus($required, $available)
    {
        if ($available >= $required) {
            return 'available';
        } elseif ($available > 0) {
            return 'partial';
        } else {
            return 'unavailable';
        }
    }

    public function updateStockForContract(Contract $contract)
    {
        DB::beginTransaction();
        
        try {
            foreach ($contract->items as $item) {
                $stock = Stock::where('item_id', $item->item_id)->lockForUpdate()->first();
                
                if (!$stock) {
                    throw new \Exception("Stock record not found for item {$item->item_id}");
                }
                
                if ($stock->quantity < $item->quantity) {
                    throw new \Exception("Insufficient stock for item {$item->item_id}");
                }
                
                $stock->quantity -= $item->quantity;
                $stock->save();
                
                // Create stock movement record
                DB::table('stock_movements')->insert([
                    'item_id' => $item->item_id,
                    'contract_id' => $contract->id,
                    'quantity' => -$item->quantity,
                    'type' => 'contract_deduction',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
            
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Stock update failed: ' . $e->getMessage());
            throw $e;
        }
    }
} 