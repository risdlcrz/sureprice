<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class InventoryController extends Controller
{
    public function index()
    {
        $inventories = Inventory::with(['material.category'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $lowStockItems = Inventory::lowStock()->count();
        $expiringItems = Inventory::expiring()->count();
        $totalItems = Inventory::count();

        return view('inventory.index', compact('inventories', 'lowStockItems', 'expiringItems', 'totalItems'));
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
        });

        return redirect()->route('inventory.index')
            ->with('success', 'Inventory item created successfully.');
    }

    public function edit(Inventory $inventory)
    {
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