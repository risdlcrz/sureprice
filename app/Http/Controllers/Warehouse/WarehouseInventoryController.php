<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\Category;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WarehouseInventoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Material::with('category');

        // Apply filters
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('stock_status')) {
            switch ($request->stock_status) {
                case 'low':
                    $query->whereColumn('stock', '<', 'minimum_stock');
                    break;
                case 'out':
                    $query->where('stock', 0);
                    break;
                case 'normal':
                    $query->whereColumn('stock', '>=', 'minimum_stock');
                    break;
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $materials = $query->paginate(10);
        $categories = Category::all();

        return view('warehouse.inventory.index', compact('materials', 'categories'));
    }

    public function addStock(Request $request)
    {
        $request->validate([
            'material_id' => 'required|exists:materials,id',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string'
        ]);

        DB::transaction(function() use ($request) {
            $material = Material::findOrFail($request->material_id);
            $oldStock = $material->stock;
            $material->stock += $request->quantity;
            $material->save();

            // Record stock movement
            StockMovement::create([
                'material_id' => $material->id,
                'type' => 'in',
                'quantity' => $request->quantity,
                'previous_stock' => $oldStock,
                'new_stock' => $material->stock,
                'notes' => $request->notes,
                'reference_number' => 'STK-' . strtoupper(uniqid())
            ]);
        });

        return redirect()->route('warehouse.inventory.index')
            ->with('success', 'Stock added successfully');
    }

    public function updateStock(Request $request)
    {
        $request->validate([
            'material_id' => 'required|exists:materials,id',
            'adjustment_type' => 'required|in:add,remove,set',
            'quantity' => 'required|integer|min:0',
            'notes' => 'nullable|string'
        ]);

        DB::transaction(function() use ($request) {
            $material = Material::findOrFail($request->material_id);
            $oldStock = $material->stock;

            switch ($request->adjustment_type) {
                case 'add':
                    $material->stock += $request->quantity;
                    $type = 'in';
                    break;
                case 'remove':
                    if ($request->quantity > $material->stock) {
                        throw new \Exception('Cannot remove more stock than available');
                    }
                    $material->stock -= $request->quantity;
                    $type = 'out';
                    break;
                case 'set':
                    $material->stock = $request->quantity;
                    $type = $request->quantity > $oldStock ? 'in' : 'out';
                    break;
            }

            $material->save();

            // Record stock movement
            StockMovement::create([
                'material_id' => $material->id,
                'type' => $type,
                'quantity' => abs($request->quantity - $oldStock),
                'previous_stock' => $oldStock,
                'new_stock' => $material->stock,
                'notes' => $request->notes,
                'reference_number' => 'STK-' . strtoupper(uniqid())
            ]);
        });

        return redirect()->route('warehouse.inventory.index')
            ->with('success', 'Stock updated successfully');
    }

    public function history(Material $material)
    {
        $movements = StockMovement::where('material_id', $material->id)
            ->latest()
            ->paginate(15);

        return view('warehouse.inventory.history', compact('material', 'movements'));
    }
} 