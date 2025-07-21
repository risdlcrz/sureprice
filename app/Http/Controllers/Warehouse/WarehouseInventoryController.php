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
use App\Models\Supplier;
use App\Models\Activity;

class WarehouseInventoryController extends Controller
{
    private function logPageView($description, $modelType = null, $modelId = null)
    {
        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'viewed',
            'description' => $description,
            'model_type' => $modelType,
            'model_id' => $modelId
        ]);
    }

    public function index(Request $request)
    {
        $this->logPageView('Viewed Warehouse Inventory Index');
        // Get all available warehouses
        $warehouses = \App\Models\Warehouse::all();
        if ($warehouses->isEmpty()) {
            return response()->view('warehouse.no-warehouses');
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
        $suppliers = \App\Models\Supplier::all();
        $categories = \App\Models\Category::all();

        // Get all stocks for this warehouse, keyed by material_id
        $stocks = \App\Models\Stock::where('warehouse_id', $warehouseId)->get()->keyBy('material_id');

        // Build rows for the view, matching admin inventory logic
        $rows = $materials->map(function($material) use ($stocks, $warehouseId) {
            $stock = $stocks->get($material->id);
            return (object)[
                'material' => $material,
                'current_stock' => $stock ? $stock->current_stock : 0,
                'threshold' => $stock ? $stock->threshold : ($material->minimum_stock ?? 0),
                'warehouse_id' => $warehouseId,
                'stock_id' => $stock ? $stock->id : null,
                'supplier_id' => $stock ? $stock->supplier_id : null,
                // add other fields as needed
            ];
        });

        // Paginate manually
        $page = request()->get('page', 1);
        $perPage = 15;
        $paginatedRows = new \Illuminate\Pagination\LengthAwarePaginator(
            $rows->forPage($page, $perPage),
            $rows->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('warehouse.inventory.index', [
            'paginatedStocks' => $paginatedRows, // keep the same variable name for the view
            'categories' => $categories,
            'warehouses' => $warehouses,
            'warehouseId' => $warehouseId,
            'materials' => $materials,
            'suppliers' => $suppliers,
        ]);
    }

    public function addStock(Request $request)
    {
        $this->logPageView('Viewed Add Stock Page');
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
            // Sync to admin Inventory if Main Warehouse
            $mainWarehouse = \App\Models\Warehouse::where('name', 'Main Warehouse')->first();
            if ($mainWarehouse && $mainWarehouse->id == $request->warehouse_id) {
                $inventory = \App\Models\Inventory::firstOrCreate([
                    'material_id' => $request->material_id
                ]);
                $inventory->quantity = $stock->current_stock;
                $inventory->save();
            }
        });
        return redirect()->route('warehouse.inventory.index', ['warehouse_id' => $request->warehouse_id])
            ->with('success', 'Stock added successfully');
    }

    public function updateStock(Request $request)
    {
        $this->logPageView('Viewed Update Stock Page');
        $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'material_id' => 'required|exists:materials,id',
            'adjustment_type' => 'required|in:add,remove,set',
            'quantity' => 'required|integer|min:0',
            'notes' => 'nullable|string'
        ]);
        try {
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
                // Sync to admin Inventory if Main Warehouse
                $mainWarehouse = \App\Models\Warehouse::where('name', 'Main Warehouse')->first();
                if ($mainWarehouse && $mainWarehouse->id == $request->warehouse_id) {
                    $inventory = \App\Models\Inventory::firstOrCreate([
                        'material_id' => $request->material_id
                    ]);
                    $inventory->quantity = $stock->current_stock;
                    $inventory->save();
                }
            });
        } catch (\Exception $e) {
            return redirect()->route('warehouse.inventory.index', ['warehouse_id' => $request->warehouse_id])
                ->with('error', $e->getMessage());
        }
        return redirect()->route('warehouse.inventory.index', ['warehouse_id' => $request->warehouse_id])
            ->with('success', 'Stock updated successfully');
    }

    public function history(Request $request, $materialId)
    {
        $this->logPageView('Viewed Stock History Page', \App\Models\Material::class, $materialId);
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
        $material = Material::find($materialId);
        return view('warehouse.inventory.history', [
            'stock' => $stock,
            'movements' => $movements,
            'warehouseId' => $warehouseId,
            'material' => $material,
        ]);
    }
} 