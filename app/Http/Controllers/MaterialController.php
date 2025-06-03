<?php

namespace App\Http\Controllers;

use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MaterialController extends Controller
{
    public function index(Request $request)
    {
        $query = Material::with(['category', 'suppliers']);

        // Search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('code', 'like', "%$search%")
                  ->orWhere('description', 'like', "%$search%")
                  ->orWhereHas('category', function($cat) use ($search) {
                      $cat->where('name', 'like', "%$search%")
                          ->orWhere('slug', 'like', "%$search%")
                          ->orWhere('description', 'like', "%$search%") ;
                  });
            });
        }

        // Category filter
        if ($request->filled('category')) {
            $category = $request->input('category');
            $query->whereHas('category', function($q) use ($category) {
                $q->where('slug', $category);
            });
        }

        // Sorting
        $sort = $request->input('sort', 'name');
        $allowedSorts = ['name', 'code', 'base_price', 'created_at'];
        if (!in_array($sort, $allowedSorts)) {
            $sort = 'name';
        }
        $query->orderBy($sort);

        // Per page
        $perPage = (int) $request->input('per_page', 10);
        if (!in_array($perPage, [10, 25, 50, 100])) {
            $perPage = 10;
        }

        $materials = $query->paginate($perPage)->appends($request->all());

        return view('admin.materials.index', compact('materials'));
    }

    public function create()
    {
        $suppliers = \App\Models\Supplier::orderBy('company_name')->get();
        return view('admin.materials.form', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:materials',
            'description' => 'nullable|string',
            'category' => 'required|string|in:construction,electrical,plumbing,finishing,tools,other',
            'unit' => 'required|string|max:50',
            'base_price' => 'required|numeric|min:0',
            'specifications' => 'nullable|string',
            'images.*' => 'nullable|image|max:2048',
            'suppliers' => 'nullable|array',
            'suppliers.*' => 'exists:suppliers,id',
        ]);

        // Get the category ID based on the slug
        $category = DB::table('categories')->where('slug', $validated['category'])->first();
        if (!$category) {
            return back()->withErrors(['category' => 'Invalid category selected'])->withInput();
        }

        // Create the material
        $material = Material::create([
            'name' => $validated['name'],
            'code' => $validated['code'],
            'description' => $validated['description'],
            'unit' => $validated['unit'],
            'base_price' => $validated['base_price'],
            'specifications' => $validated['specifications'],
            'category_id' => $category->id,
            'minimum_stock' => 0,
            'current_stock' => 0
        ]);

        // Attach suppliers if any
        if (!empty($validated['suppliers'])) {
            $material->suppliers()->sync($validated['suppliers']);
        }

        // Handle image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('materials', 'public');
                $material->images()->create(['path' => $path]);
            }
        }

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
        $query = $request->get('query', '');
        
        $materials = Material::with('category')
            ->when($query, function($q) use ($query) {
                return $q->where('name', 'like', "%{$query}%")
                    ->orWhere('code', 'like', "%{$query}%");
            })
            ->limit(10)
            ->get();

        return response()->json($materials);
    }

    public function show(Material $material)
    {
        $material->load(['category', 'suppliers', 'images']);
        return view('admin.materials.show', compact('material'));
    }
}