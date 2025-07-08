<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SupplierMaterialController extends Controller
{
    public function index(Request $request)
    {
        $supplier = Auth::user()->company;
        if (!$supplier || $supplier->designation !== 'supplier') {
            abort(403, 'You are not associated with a supplier account.');
        }

        // Get all materials linked to this supplier, with inventory and category
        $materials = $supplier->materials()
            ->with(['inventory', 'category']) // eager load inventory and category
            ->paginate(10); // Use pagination for the materials list

        // Add price, total stock, SRP, base price, and approval status attributes for the view
        $materials->each(function ($material) {
            $material->price = $material->pivot->price ?? 0;
            $material->stock = (float) $material->inventory->sum('quantity');
            $material->srp_price = $material->srp_price ?? '-';
            $material->base_price = $material->base_price ?? '-';
            $material->approval_status = $material->approval_status ?? ($material->pivot->approval_status ?? 'pending');
        });

        return view('supplier.materials.index', compact('materials'));
    }

    public function create()
    {
        if (!Auth::user()->hasRole('supplier')) {
            abort(403, 'Unauthorized action.');
        }
        $supplier = Auth::user()->company;
        if (!$supplier || $supplier->designation !== 'supplier') {
            abort(403, 'You are not associated with a supplier account.');
        }
        $categories = \App\Models\Category::orderBy('name')->get();
        return view('supplier.materials.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $supplier = Auth::user()->company;
        if (!$supplier || $supplier->designation !== 'supplier') {
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
        $supplier = Auth::user()->company;
        if (!$supplier || $supplier->designation !== 'supplier' || !$supplier->materials->contains($material->id)) {
            abort(403, 'You are not authorized to edit this material.');
        }
        $pivotData = $supplier->materials()->where('material_id', $material->id)->first()->pivot;
        return view('supplier.materials.edit', compact('material', 'pivotData'));
    }

    public function update(Request $request, Material $material)
    {
        $supplier = Auth::user()->company;
        if (!$supplier || $supplier->designation !== 'supplier' || !$supplier->materials->contains($material->id)) {
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
        $supplier = Auth::user()->company;
        if (!$supplier || $supplier->designation !== 'supplier' || !$supplier->materials->contains($material->id)) {
            abort(403, 'You are not authorized to delete this material.');
        }
        $supplier->materials()->detach($material->id);
        return redirect()->route('supplier.materials.index')
            ->with('success', 'Material removed from your listings successfully.');
    }

    public function search(Request $request)
    {
        $supplier = Auth::user()->company;
        if (!$supplier || $supplier->designation !== 'supplier') {
            return response()->json([], 403);
        }
        $term = $request->input('term');

        $existingMaterialIds = $supplier->materials()->pluck('materials.id')->toArray();

        $materials = Material::where('name', 'LIKE', "%{$term}%")
            ->whereNotIn('id', $existingMaterialIds)
            ->select('id', 'name', 'code', 'unit', 'srp_price', 'base_price')
            ->limit(10)
            ->get();

        return response()->json($materials);
    }

    public function link(Request $request)
    {
        $supplier = Auth::user()->company;
        if (!$supplier || $supplier->designation !== 'supplier') {
            abort(403, 'You are not associated with a supplier account.');
        }
        $validated = $request->validate([
            'material_id' => 'required|exists:materials,id',
            'price' => 'required|numeric|min:0',
        ]);
        // Check if the material is already linked
        if ($supplier->materials()->where('material_id', $validated['material_id'])->exists()) {
            return redirect()->back()
                ->with('error', 'This material is already in your listings.');
        }
        $supplier->materials()->attach($validated['material_id'], [
            'price' => $validated['price'],
            'is_preferred' => false,
            'approval_status' => 'approved' // Automatically approve on link
        ]);
        return redirect()->route('supplier.materials.index')
            ->with('success', 'Material linked successfully.');
    }

    public function show(Material $material)
    {
        $suppliers = \App\Models\Supplier::orderBy('company_name')->get();
        $linkedSupplierIds = $material->suppliers()->pluck('suppliers.id')->toArray();
        return view('admin.materials.show', compact('material', 'suppliers', 'linkedSupplierIds'));
    }
} 
} 