<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\Category;
use App\Models\Company;
use App\Models\Transaction;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    public function index()
    {
        $materials = Material::all();
        return view('admin.materials.index', compact('materials'));
    }

    public function show(Material $material)
    {
        $suppliers = Company::where('designation', 'supplier')->orderBy('company_name')->get();
        $linkedSupplierIds = $material->suppliers()->pluck('company_id')->toArray();

        return view('admin.materials.show', compact('material', 'suppliers', 'linkedSupplierIds'));
    }

    public function edit(Material $material)
    {
        $categories = Category::all();
        return view('admin.materials.edit', compact('material', 'categories'));
    }

    public function updateSuppliers(Request $request, Material $material)
    {
        $material->suppliers()->sync($request->input('suppliers', []));

        return redirect()->route('admin.materials.show', $material)
            ->with('success', 'Suppliers updated successfully.');
    }
} 