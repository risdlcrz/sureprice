<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\Material;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $query = \App\Models\Company::where('designation', 'supplier');

        // Search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('company_name', 'like', "%$search%")
                  ->orWhere('contact_person', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%") ;
            });
        }

        // Sort
        $sort = $request->input('sort', 'company_name');
        $allowedSorts = ['company_name', 'contact_person', 'created_at'];
        if (!in_array($sort, $allowedSorts)) {
            $sort = 'company_name';
        }
        $query->orderBy($sort);

        // Per page
        $perPage = (int) $request->input('per_page', 10);
        if (!in_array($perPage, [10, 25, 50, 100])) {
            $perPage = 10;
        }

        $suppliers = $query->paginate($perPage)->appends($request->all());

        return view('admin.suppliers.index', compact('suppliers'));
    }

    public function edit(Supplier $supplier)
    {
        $scopeTypes = \App\Models\ScopeType::orderBy('name')->get();
        return view('admin.suppliers.form', compact('supplier', 'scopeTypes'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'email' => 'required|email|unique:suppliers,email,' . $supplier->id,
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'tax_number' => 'nullable|string|max:50',
            'registration_number' => 'nullable|string|max:50',
            'status' => 'required|in:active,inactive,pending',
            'materials' => 'nullable|array',
            'materials.*.price' => 'required|numeric|min:0',
            'materials.*.lead_time' => 'required|integer|min:0'
        ]);

        $supplier->update($validated);

        // Handle materials
        if ($request->has('materials')) {
            $materials = collect($request->input('materials'))->map(function ($item, $materialId) {
                return [
                    'material_id' => $materialId,
                    'price' => $item['price'],
                    'lead_time' => $item['lead_time']
                ];
            })->toArray();

            $supplier->materials()->sync($materials);
        } else {
            $supplier->materials()->detach();
        }

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier updated successfully');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier deleted successfully');
    }

    public function search(Request $request)
    {
        $query = $request->get('q');
        
        return Supplier::where('company_name', 'like', "%{$query}%")
            ->orWhere('contact_person', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->limit(10)
            ->get();
    }

    public function show(Supplier $supplier)
    {
        $supplier->load(['materials.category']);
        return view('admin.suppliers.show', compact('supplier'));
    }
} 