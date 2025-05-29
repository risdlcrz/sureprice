<?php

namespace App\Http\Controllers;

use App\Models\Material;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    public function index()
    {
        $materials = Material::with('suppliers')
            ->orderBy('name')
            ->paginate(10);

        return view('admin.materials.index', compact('materials'));
    }

    public function create()
    {
        return view('admin.materials.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'unit' => 'required|string|max:50',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|numeric|min:0'
        ]);

        Material::create($validated);

        return redirect()->route('materials.index')
            ->with('success', 'Material created successfully');
    }

    public function edit(Material $material)
    {
        return view('admin.materials.form', compact('material'));
    }

    public function update(Request $request, Material $material)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'unit' => 'required|string|max:50',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|numeric|min:0'
        ]);

        $material->update($validated);

        return redirect()->route('materials.index')
            ->with('success', 'Material updated successfully');
    }

    public function destroy(Material $material)
    {
        $material->delete();

        return redirect()->route('materials.index')
            ->with('success', 'Material deleted successfully');
    }

    public function search(Request $request)
    {
        $query = $request->get('q');
        
        return Material::where('name', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->limit(10)
            ->get();
    }

    public function suppliers(Material $material)
    {
        return $material->suppliers()
            ->where('is_preferred', true)
            ->with('pivot')
            ->get();
    }
} 