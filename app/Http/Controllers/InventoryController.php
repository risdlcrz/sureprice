<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Material;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\LengthAwarePaginator;

class InventoryController extends Controller
{
    public function index()
    {
        $perPage = 10;
        $page = request()->get('page', 1);

        $warehouses = Warehouse::all();

        // backfill any materials that somehow missed an inventory record
        \App\Models\Material::doesntHave('inventory')->get()->each(function ($m) {
            \App\Models\Inventory::create([
                'material_id' => $m->id,
                'quantity' => 0,
                'unit' => $m->unit,
                'location' => null,
                'status' => 'active',
                'minimum_threshold' => 0,
            ]);
        });

        // also load inventory relation so we can link to the correct record
        $materials = \App\Models\Material::with(['category', 'stocks', 'inventory'])
            ->get()
            ->map(function ($material) use ($warehouses) {
                $totalStock = $material->stocks->sum('current_stock');
                $material->total_stock = $totalStock;
                // Add per-warehouse stock
                $material->warehouse_stocks = $warehouses->mapWithKeys(function ($warehouse) use ($material) {
                    $stock = $material->stocks->where('warehouse_id', $warehouse->id)->sum('current_stock');
                    return [$warehouse->id => $stock];
                });
                // easier access to primary inventory record (first)
                $material->primary_inventory = $material->inventory->first();
                return $material;
            });

        $lowStockItems = $materials->filter(function ($material) {
            $threshold = $material->minimum_stock ?? 0;
            return $material->total_stock < $threshold;
        })->count();
        $totalItems = $materials->count();
        $expiringItems = 0; // Not tracked in Stock table, keep as 0 or implement if needed

        // Paginate the collection manually
        $paginatedMaterials = new LengthAwarePaginator(
            $materials->forPage($page, $perPage),
            $materials->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('inventory.index', [
            'materials' => $paginatedMaterials,
            'warehouses' => $warehouses,
            'lowStockItems' => $lowStockItems,
            'expiringItems' => $expiringItems,
            'totalItems' => $totalItems,
        ]);
    }

    public function create()
    {
        $materials = Material::with('category')->get();
        return view('inventory.create', compact('materials'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'material_id' => 'required|exists:materials,id',
            'quantity' => 'required|numeric|min:0',
            'unit' => 'required|string',
            'location' => 'nullable|string',
            'batch_number' => 'nullable|string',
            'expiry_date' => 'nullable|date',
            'minimum_threshold' => 'required|numeric|min:0',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::transaction(function () use ($request) {
            $inventory = Inventory::create($request->except('status') + ['status' => $request->input('status', 'active')]);
            
            // Update material's current stock
            $material = Material::find($request->material_id);
            $material->current_stock = $request->quantity;
            $material->save();
            // Update Main Warehouse stock
            $mainWarehouse = \App\Models\Warehouse::where('name', 'Main Warehouse')->first();
            if ($mainWarehouse) {
                $stock = \App\Models\Stock::firstOrCreate([
                    'warehouse_id' => $mainWarehouse->id,
                    'material_id' => $material->id,
                ]);
                $stock->current_stock = $request->quantity;
                $stock->save();
            }
        });

        return redirect()->route('inventory.index')
            ->with('success', 'Inventory item created successfully.');
    }

    public function edit($id)
    {
        // manually resolve so we can handle missing records gracefully
        $inventory = Inventory::find($id);
        if (! $inventory) {
            // inventory entry doesn't exist; redirect to create page instead of throwing 404
            return redirect()->route('inventory.create')
                ->with('warning', 'Inventory record not found. Please create it.');
        }

        $materials = Material::with('category')->get();
        return view('inventory.edit', compact('inventory', 'materials'));
    }

    public function update(Request $request, Inventory $inventory)
    {
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|numeric|min:0',
            'unit' => 'required|string',
            'location' => 'nullable|string',
            'batch_number' => 'nullable|string',
            'expiry_date' => 'nullable|date',
            'status' => 'required|in:active,inactive,discontinued',
            'minimum_threshold' => 'required|numeric|min:0',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::transaction(function () use ($request, $inventory) {
            $inventory->update($request->all());
            
            // Update material's current stock
            $material = $inventory->material;
            $material->current_stock = $request->quantity;
            $material->save();
            // Update Main Warehouse stock
            $mainWarehouse = \App\Models\Warehouse::where('name', 'Main Warehouse')->first();
            if ($mainWarehouse) {
                $stock = \App\Models\Stock::firstOrCreate([
                    'warehouse_id' => $mainWarehouse->id,
                    'material_id' => $material->id,
                ]);
                $stock->current_stock = $request->quantity;
                $stock->save();
            }
        });

        return redirect()->route('inventory.index')
            ->with('success', 'Inventory item updated successfully.');
    }

    public function destroy(Inventory $inventory)
    {
        DB::transaction(function () use ($inventory) {
            // Update material's current stock
            $material = $inventory->material;
            $material->current_stock = 0;
            $material->save();
            
            $inventory->delete();
        });

        return redirect()->route('inventory.index')
            ->with('success', 'Inventory item deleted successfully.');
    }

    public function adjustStock(Request $request, Inventory $inventory)
    {
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|numeric',
            'operation' => 'required|in:add,subtract',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::transaction(function () use ($request, $inventory) {
            $inventory->updateStock(
                $request->quantity,
                $request->operation
            );
            
            // Update material's current stock
            $material = $inventory->material;
            $material->current_stock = $inventory->quantity;
            $material->save();
        });

        return redirect()->route('inventory.index')
            ->with('success', 'Stock adjusted successfully.');
    }

    public function lowStock()
    {
        $inventories = Inventory::with(['material.category'])
            ->lowStock()
            ->orderBy('quantity', 'asc')
            ->paginate(10);

        return view('inventory.low-stock', compact('inventories'));
    }

    public function expiring()
    {
        $inventories = Inventory::with(['material.category'])
            ->expiring()
            ->orderBy('expiry_date', 'asc')
            ->paginate(10);

        return view('inventory.expiring', compact('inventories'));
    }
} 