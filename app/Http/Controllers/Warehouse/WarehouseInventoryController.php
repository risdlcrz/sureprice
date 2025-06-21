<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\Category;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Warehouse;
use App\Models\Stock;

class WarehouseInventoryController extends Controller
{
    public function index(Request $request)
    {
        // Get all available warehouses
        $warehouses = \App\Models\Warehouse::all();
        if ($warehouses->isEmpty()) {
            abort(404, 'No warehouses found.');
        }
        // Redirect to first warehouse if none selected
        if (!$request->has('warehouse_id')) {
            return redirect()->route('warehouse.inventory.index', ['warehouse_id' => $warehouses->first()->id]);
        }
        $warehouseId = $request->input('warehouse_id');

        $materialsQuery = Material::with('category');
        // Apply filters
        if ($request->filled('category')) {
            $materialsQuery->where('category_id', $request->category);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $materialsQuery->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }
        $materials = $materialsQuery->get();
        // Build stocks collection for the selected warehouse
        $stocks = $materials->map(function($material) use ($warehouseId, $request) {
            $stock = Stock::where('warehouse_id', $warehouseId)
                ->where('material_id', $material->id)
                ->first();
            // If no stock record, create a virtual one with zeroes
            if (!$stock) {
                $stock = new Stock([
                    'warehouse_id' => $warehouseId,
                    'material_id' => $material->id,
                    'current_stock' => 0,
                    'threshold' => 0,
                ]);
                $stock->material = $material;
                $stock->warehouse = Warehouse::find($warehouseId);
            } else {
                $stock->material = $material;
            }
            // Stock status filter
            if ($request->filled('stock_status')) {
                switch ($request->stock_status) {
                    case 'low':
                        if (!($stock->current_stock < $stock->threshold)) return null;
                        break;
                    case 'out':
                        if (!($stock->current_stock == 0)) return null;
                        break;
                    case 'normal':
                        if (!($stock->current_stock >= $stock->threshold)) return null;
                        break;
                }
            }
            return $stock;
        })->filter()->values();
        // Paginate manually
        $perPage = 10;
        $page = $request->input('page', 1);
        $paginatedStocks = new \Illuminate\Pagination\LengthAwarePaginator(
            $stocks->forPage($page, $perPage),
            $stocks->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );
        $categories = \App\Models\Category::all();
        return view('warehouse.inventory.index', compact('paginatedStocks', 'categories', 'warehouses', 'warehouseId', 'materials'));
    }

    public function addStock(Request $request)
    {
        $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'material_id' => 'required|exists:materials,id',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string'
        ]);
        DB::transaction(function() use ($request) {
            $stock = Stock::firstOrCreate([
                'warehouse_id' => $request->warehouse_id,
                'material_id' => $request->material_id,
            ], [
                'current_stock' => 0,
                'threshold' => 0,
            ]);
            $oldStock = $stock->current_stock;
            $stock->current_stock += $request->quantity;
            $stock->save();
            StockMovement::create([
                'material_id' => $stock->material_id,
                'type' => 'in',
                'quantity' => $request->quantity,
                'previous_stock' => $oldStock,
                'new_stock' => $stock->current_stock,
                'notes' => $request->notes,
                'reference_number' => 'STK-' . strtoupper(uniqid()),
                'warehouse_id' => $stock->warehouse_id,
            ]);
        });
        return redirect()->route('warehouse.inventory.index', ['warehouse_id' => $request->warehouse_id])
            ->with('success', 'Stock added successfully');
    }

    public function updateStock(Request $request)
    {
        $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'material_id' => 'required|exists:materials,id',
            'adjustment_type' => 'required|in:add,remove,set',
            'quantity' => 'required|integer|min:0',
            'notes' => 'nullable|string'
        ]);
        DB::transaction(function() use ($request) {
            $stock = Stock::firstOrCreate([
                'warehouse_id' => $request->warehouse_id,
                'material_id' => $request->material_id,
            ], [
                'current_stock' => 0,
                'threshold' => 0,
            ]);
            $oldStock = $stock->current_stock;
            switch ($request->adjustment_type) {
                case 'add':
                    $stock->current_stock += $request->quantity;
                    $type = 'in';
                    break;
                case 'remove':
                    if ($request->quantity > $stock->current_stock) {
                        throw new \Exception('Cannot remove more stock than available');
                    }
                    $stock->current_stock -= $request->quantity;
                    $type = 'out';
                    break;
                case 'set':
                    $type = $request->quantity > $oldStock ? 'in' : 'out';
                    $stock->current_stock = $request->quantity;
                    break;
            }
            $stock->save();
            StockMovement::create([
                'material_id' => $stock->material_id,
                'type' => $type,
                'quantity' => abs($request->quantity - $oldStock),
                'previous_stock' => $oldStock,
                'new_stock' => $stock->current_stock,
                'notes' => $request->notes,
                'reference_number' => 'STK-' . strtoupper(uniqid()),
                'warehouse_id' => $stock->warehouse_id,
            ]);
        });
        return redirect()->route('warehouse.inventory.index', ['warehouse_id' => $request->warehouse_id])
            ->with('success', 'Stock updated successfully');
    }

    public function history(Request $request, $materialId)
    {
        $warehouseId = $request->input('warehouse_id');
        $stock = Stock::where('material_id', $materialId)
            ->when($warehouseId, function($q) use ($warehouseId) {
                $q->where('warehouse_id', $warehouseId);
            })
            ->first();
        $movements = StockMovement::where('material_id', $materialId)
            ->when($warehouseId, function($q) use ($warehouseId) {
                $q->where('warehouse_id', $warehouseId);
            })
            ->latest()
            ->paginate(15);
        return view('warehouse.inventory.history', [
            'stock' => $stock,
            'movements' => $movements,
            'warehouseId' => $warehouseId,
        ]);
    }
} 