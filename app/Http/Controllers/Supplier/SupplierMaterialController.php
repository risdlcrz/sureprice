<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupplierMaterialController extends Controller
{
    public function index(Request $request)
    {
        $supplier = Auth::user()->supplier; // Get the authenticated supplier

        if (!$supplier) {
            abort(403, 'You are not associated with a supplier account.');
        }

        $query = $supplier->materials(); // Get materials associated with this supplier

        // Add filters and search if needed, similar to other index methods
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('code', 'like', '%' . $search . '%');
            });
        }

        $materials = $query->paginate(10);

        return view('supplier.materials.index', compact('materials'));
    }

    public function create()
    {
        // Ensure the authenticated user is a supplier
        if (!Auth::user()->hasRole('supplier')) {
            abort(403, 'Unauthorized action.');
        }

        return view('supplier.materials.create');
    }

    public function store(Request $request)
    {
        $supplier = Auth::user()->supplier;

        if (!$supplier) {
            abort(403, 'You are not associated with a supplier account.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:materials,code',
            'description' => 'nullable|string',
            'unit' => 'required|string|max:50',
            'price' => 'required|numeric|min:0',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        // Create the material (can be shared among suppliers initially, or link directly)
        // For now, let's assume a material is created globally and then associated with the supplier
        $material = Material::create($validated);

        // Attach the material to the supplier with the specific price
        $supplier->materials()->attach($material->id, [
            'price' => $validated['price'],
            'is_preferred' => false // Default to not preferred when created by supplier
        ]);

        return redirect()->route('supplier.materials.index')
            ->with('success', 'Material added successfully.');
    }

    public function edit(Material $material)
    {
        $supplier = Auth::user()->supplier;

        if (!$supplier || !$supplier->materials->contains($material->id)) {
            abort(403, 'You are not authorized to edit this material.');
        }
        
        // Get the pivot data (price) for this supplier and material
        $pivotData = $supplier->materials()->where('material_id', $material->id)->first()->pivot;

        return view('supplier.materials.edit', compact('material', 'pivotData'));
    }

    public function update(Request $request, Material $material)
    {
        $supplier = Auth::user()->supplier;

        if (!$supplier || !$supplier->materials->contains($material->id)) {
            abort(403, 'You are not authorized to update this material.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => ['required', 'string', 'max:255', 'unique:materials,code,' . $material->id],
            'description' => 'nullable|string',
            'unit' => 'required|string|max:50',
            'price' => 'required|numeric|min:0',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        // Update the material's core data
        $material->update($validated);

        // Update the pivot table data (price for this supplier)
        $supplier->materials()->updateExistingPivot($material->id, [
            'price' => $validated['price'],
        ]);

        return redirect()->route('supplier.materials.index')
            ->with('success', 'Material updated successfully.');
    }

    public function destroy(Material $material)
    {
        $supplier = Auth::user()->supplier;

        if (!$supplier || !$supplier->materials->contains($material->id)) {
            abort(403, 'You are not authorized to delete this material.');
        }

        // Detach the material from the supplier (don't delete the material itself if others use it)
        $supplier->materials()->detach($material->id);

        return redirect()->route('supplier.materials.index')
            ->with('success', 'Material removed from your listings successfully.');
    }
} 