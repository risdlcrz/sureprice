<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\MaterialRequest;
use Illuminate\Http\Request;

class MaterialRequestApprovalController extends Controller
{
    public function index()
    {
        $materialRequests = MaterialRequest::with(['contract', 'user'])
            ->orderByDesc('created_at')
            ->paginate(15);
        return view('warehouse.material-requests', compact('materialRequests'));
    }

    public function approve(MaterialRequest $materialRequest)
    {
        if ($materialRequest->status !== 'pending') {
            return back()->with('error', 'This request is already approved or not pending.');
        }

        // Deduct stock for each item
        foreach ($materialRequest->items as $item) {
            if ($item->warehouse_id) {
                $stock = \App\Models\Stock::where('warehouse_id', $item->warehouse_id)
                    ->where('material_id', $item->material_id)
                    ->first();
                if ($stock && $stock->current_stock >= $item->quantity) {
                    $stock->current_stock -= $item->quantity;
                    $stock->save();
                    $item->fulfilled_quantity = $item->quantity;
                } else {
                    // Not enough stock, fulfill as much as possible
                    $fulfilled = $stock ? $stock->current_stock : 0;
                    if ($stock) {
                        $stock->current_stock = 0;
                        $stock->save();
                    }
                    $item->fulfilled_quantity = $fulfilled;
                }
                $item->save();

                // If this is the Main Warehouse, sync to admin Inventory
                $mainWarehouse = \App\Models\Warehouse::where('name', 'Main Warehouse')->first();
                if ($mainWarehouse && $item->warehouse_id == $mainWarehouse->id) {
                    $inventory = \App\Models\Inventory::firstOrCreate([
                        'material_id' => $item->material_id
                    ]);
                    $inventory->quantity = $stock ? $stock->current_stock : 0;
                    $inventory->save();
                }
            }
        }

        $materialRequest->status = 'approved';
        $materialRequest->save();
        return back()->with('success', 'Material request approved and stock deducted successfully.');
    }

    public function show(MaterialRequest $materialRequest)
    {
        $materialRequest->load(['contract', 'user', 'items.material']);
        return view('warehouse.material-requests-show', compact('materialRequest'));
    }
} 