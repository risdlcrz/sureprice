<?php

namespace App\Http\Controllers;

use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\QuotationAttachment;
use App\Models\QuotationResponseAttachment;
use App\Models\Supplier;

class MaterialController extends Controller
{
    public function index(Request $request)
    {
        $query = Material::with(['category', 'suppliers', 'scopeTypes']);

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
        $sort = $request->input('sort', 'created_at');
        $allowedSorts = ['name', 'code', 'base_price', 'created_at'];
        if (!in_array($sort, $allowedSorts)) {
            $sort = 'created_at';
        }
        $query->orderBy($sort, $sort === 'created_at' ? 'asc' : 'asc');

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
        $suppliers = \App\Models\Supplier::all();
        $scopeTypes = \App\Models\ScopeType::orderBy('name')->get();
        $categories = \App\Models\Category::orderBy('name')->get();
        return view('admin.materials.form', compact('suppliers', 'scopeTypes', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'unit' => 'required|string',
            'base_price' => 'required|numeric|min:0',
            'srp_price' => 'required|numeric|min:0',
            'specifications' => 'nullable|string',
            'suppliers' => 'nullable|array',
            'suppliers.*' => 'exists:suppliers,id',
            'scope_types' => 'nullable|array',
            'scope_types.*' => 'exists:scope_types,id',
            'images.*' => 'nullable|image|max:2048',
            'is_per_area' => 'sometimes|boolean',
            'is_wall_material' => 'sometimes|boolean',
            'coverage_rate' => 'nullable|numeric|min:0.01|required_if:is_per_area,1',
            'minimum_quantity' => 'nullable|integer|min:0|required_if:is_per_area,0',
            'warranty_period' => 'nullable|integer|min:0',
        ]);

        try {
            DB::beginTransaction();

            $material = Material::create([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'category_id' => $validated['category_id'],
                'unit' => $validated['unit'],
                'base_price' => $validated['base_price'],
                'srp_price' => $validated['srp_price'],
                'specifications' => $validated['specifications'],
                'custom_category' => $request->input('custom_category'),
                'warranty_period' => $validated['warranty_period'] ?? null,
                'is_per_area' => $request->boolean('is_per_area'),
                'is_wall_material' => $request->boolean('is_wall_material'),
                'coverage_rate' => $validated['coverage_rate'] ?? null,
                'minimum_quantity' => $validated['minimum_quantity'] ?? null,
            ]);

            // Handle image uploads
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('materials', 'public');
                    $material->images()->create(['path' => $path]);
                }
            }

            // Attach suppliers if any
            if (!empty($validated['suppliers'])) {
                $material->suppliers()->attach($validated['suppliers']);
            }

            // Attach scope types if any
            if (!empty($validated['scope_types'])) {
                $material->scopeTypes()->attach($validated['scope_types']);
            }

            DB::commit();

            return redirect()->route('materials.index')
                ->with('success', 'Material created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating material: ' . $e->getMessage());
            return back()->with('error', 'Error creating material: ' . $e->getMessage())->withInput();
        }
    }

    public function edit(Material $material)
    {
        $suppliers = \App\Models\Supplier::all();
        $scopeTypes = \App\Models\ScopeType::orderBy('name')->get();
        $categories = \App\Models\Category::orderBy('name')->get();
        return view('admin.materials.form', compact('material', 'suppliers', 'scopeTypes', 'categories'));
    }

    public function update(Request $request, Material $material)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'sometimes|string|max:50|unique:materials,code,' . $material->id,
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'unit' => 'required|string',
            'base_price' => 'required|numeric|min:0',
            'srp_price' => 'required|numeric|min:0',
            'specifications' => 'nullable|string',
            'suppliers' => 'nullable|array',
            'suppliers.*' => 'exists:suppliers,id',
            'scope_types' => 'nullable|array',
            'scope_types.*' => 'exists:scope_types,id',
            'images.*' => 'nullable|image|max:2048',
            'is_per_area' => 'sometimes|boolean',
            'is_wall_material' => 'sometimes|boolean',
            'coverage_rate' => 'nullable|numeric|min:0.01|required_if:is_per_area,1',
            'minimum_quantity' => 'nullable|integer|min:0|required_if:is_per_area,0',
            'warranty_period' => 'nullable|integer|min:0',
        ]);

        try {
            DB::beginTransaction();

            $material->update([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'category_id' => $validated['category_id'],
                'unit' => $validated['unit'],
                'base_price' => $validated['base_price'],
                'srp_price' => $validated['srp_price'],
                'specifications' => $validated['specifications'],
                'custom_category' => $request->input('custom_category'),
                'warranty_period' => $validated['warranty_period'] ?? null,
                'is_per_area' => $request->boolean('is_per_area'),
                'is_wall_material' => $request->boolean('is_wall_material'),
                'coverage_rate' => $validated['coverage_rate'] ?? null,
                'minimum_quantity' => $validated['minimum_quantity'] ?? null,
            ]);

            // Handle image uploads
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('materials', 'public');
                    $material->images()->create(['path' => $path]);
                }
            }

            // Sync suppliers
            $material->suppliers()->sync($validated['suppliers'] ?? []);

            // Sync scope types
            $material->scopeTypes()->sync($validated['scope_types'] ?? []);

            DB::commit();

            return redirect()->route('materials.index')
                ->with('success', 'Material updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating material: ' . $e->getMessage());
            return back()->with('error', 'Failed to update material: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Material $material)
    {
        try {
            DB::beginTransaction();
            
            // Delete associated images
            foreach ($material->images as $image) {
                // Delete the file from storage
                if (Storage::disk('public')->exists($image->path)) {
                    Storage::disk('public')->delete($image->path);
                }
                $image->delete();
            }
            
            // Delete the material
            $material->delete();
            
            DB::commit();
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Material deleted successfully.'
                ]);
            }
            
            return redirect()->route('materials.index')
                ->with('success', 'Material deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting material: ' . $e->getMessage());
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete material: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->route('materials.index')
                ->with('error', 'Failed to delete material: ' . $e->getMessage());
        }
    }

    public function search(Request $request)
    {
        try {
            $query = $request->get('query', '');
            
            $materials = Material::with(['category', 'suppliers' => function($query) {
                $query->select('suppliers.*')
                      ->withPivot('price', 'lead_time');
            }])
            ->select('materials.id', 'materials.name', 'materials.code', 'materials.description', 'materials.unit', 'materials.base_price', 'materials.category_id')
            ->when($query !== 'all' && !empty($query), function($q) use ($query) {
                return $q->where('materials.name', 'like', "%{$query}%")
                        ->orWhere('materials.code', 'like', "%{$query}%");
            })
            ->orderBy('materials.name')
            ->limit(20)
            ->get();

            return response()->json($materials);
        } catch (\Exception $e) {
            Log::error('Material search error: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while searching materials'], 500);
        }
    }

    public function show(Material $material)
    {
        $material->load(['category', 'suppliers', 'images']);
        return view('admin.materials.show', compact('material'));
    }

    public function apiSearch(Request $request)
    {
        $query = $request->get('query', '');
        
        $materials = Material::with('category')
            ->select('id', 'name', 'code', 'description', 'unit', 'base_price', 'category_id')
            ->when(!empty($query), function($q) use ($query) {
                return $q->where('name', 'like', "%{$query}%")
                    ->orWhere('code', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%");
            })
            ->orderBy('name')
            ->limit(10)
            ->get();

        return response()->json($materials);
    }

    public function updateSrpPrices(Request $request)
    {
        try {
            $validated = $request->validate([
                'materials' => 'required|array',
                'materials.*.id' => 'required|exists:materials,id',
                'materials.*.srp_price' => 'required|numeric|min:0'
            ]);

            DB::beginTransaction();

            foreach ($validated['materials'] as $material) {
                $materialModel = Material::findOrFail($material['id']);
                $materialModel->update([
                    'srp_price' => $material['srp_price']
                ]);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'SRP prices updated successfully'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating SRP prices: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update SRP prices: ' . $e->getMessage()
            ], 500);
        }
    }

    public function suppliers(Material $material)
    {
        $material->load(['suppliers' => function($query) {
            $query->select('suppliers.id', 'suppliers.company_name')
                  ->withPivot(['price', 'lead_time', 'updated_at']);
        }]);

        return response()->json([
            'base_price' => $material->base_price,
            'suppliers' => $material->suppliers
        ]);
    }

    public function getAllMaterials()
    {
        return response()->json(Material::all());
    }

    public function getSuppliersForMaterial($id)
    {
        $material = Material::with(['suppliers'])->findOrFail($id);
        $suppliers = $material->suppliers->map(function($supplier) {
            return [
                'id' => $supplier->id,
                'company_name' => $supplier->company_name,
                'price' => $supplier->pivot->price ?? null,
                'lead_time' => $supplier->pivot->lead_time ?? null,
                'last_updated' => $supplier->pivot->updated_at ?? null,
            ];
        });
        return response()->json($suppliers);
    }

    public function getMaterialById($id)
    {
        $material = Material::with(['category'])->findOrFail($id);
        return response()->json([
            'id' => $material->id,
            'name' => $material->name,
            'code' => $material->code,
            'description' => $material->description,
            'unit' => $material->unit,
            'base_price' => $material->base_price,
            'srp_price' => $material->srp_price,
            'specifications' => $material->specifications,
            'category_name' => $material->category->name ?? 'N/A',
            'custom_category' => $material->custom_category,
        ]);
    }

    public function ajaxStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:materials,code',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'unit' => 'required|string',
            'base_price' => 'required|numeric|min:0',
            'srp_price' => 'required|numeric|min:0',
            'specifications' => 'nullable|string',
            'scope_types' => 'nullable|array',
            'scope_types.*' => 'exists:scope_types,id',
            'images.*' => 'nullable|image|max:2048'
        ]);

        try {
            DB::beginTransaction();

            // Create a temporary material record
            $material = Material::create([
                'name' => $validated['name'],
                'code' => $validated['code'],
                'description' => $validated['description'],
                'category_id' => $validated['category_id'],
                'unit' => $validated['unit'],
                'base_price' => $validated['base_price'],
                'srp_price' => $validated['srp_price'],
                'specifications' => $validated['specifications'],
                'custom_category' => $request->input('custom_category'),
            ]);

            // Handle image uploads
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('materials', 'public');
                    $material->images()->create(['path' => $path]);
                }
            }

            // Sync scope types
            if (!empty($validated['scope_types'])) {
                $material->scopeTypes()->sync($validated['scope_types']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'material' => [
                    'id' => $material->id,
                    'name' => $material->name,
                    'code' => $material->code,
                    'unit' => $material->unit,
                    'base_price' => $material->base_price,
                    'category_name' => $material->category->name
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating material: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create material: ' . $e->getMessage()
            ], 500);
        }
    }

    public function checkCode(Request $request)
    {
        $code = $request->query('code');
        $exists = Material::where('code', $code)->exists();
        
        return response()->json(['exists' => $exists]);
    }

    public function procurementIndex(Request $request)
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
        $sort = $request->input('sort', 'created_at');
        $allowedSorts = ['name', 'code', 'base_price', 'created_at'];
        if (!in_array($sort, $allowedSorts)) {
            $sort = 'created_at';
        }
        $query->orderBy($sort, $sort === 'created_at' ? 'asc' : 'asc');

        // Per page
        $perPage = (int) $request->input('per_page', 10);
        if (!in_array($perPage, [10, 25, 50, 100])) {
            $perPage = 10;
        }

        $materials = $query->paginate($perPage)->appends($request->all());

        return view('procurement.inventory-dashboard', compact('materials'));
    }

    public function transactions($id)
    {
        $items = \App\Models\PurchaseRequestItem::with(['supplier'])
            ->where('material_id', $id)
            ->orderByDesc('created_at')
            ->limit(20)
            ->get()
            ->map(function($item) {
                return [
                    'date' => $item->created_at->format('Y-m-d'),
                    'supplier' => $item->supplier ? $item->supplier->company_name : null,
                    'price' => $item->estimated_unit_price,
                    'quantity' => $item->quantity,
                    'total' => $item->total_amount,
                    'notes' => $item->notes,
                ];
            });
        return response()->json($items);
    }

    public function searchBySupplier(Request $request)
    {
        $query = $request->input('query');
        $supplierIds = explode(',', $request->input('suppliers'));

        $materials = Material::where(function ($q) use ($query) {
                $q->where('name', 'like', "%$query%")
                  ->orWhere('code', 'like', "%$query%");
            })
            ->whereHas('suppliers', function ($q) use ($supplierIds) {
                $q->whereIn('suppliers.id', $supplierIds);
            })
            ->with('category') // Eager load category for material details
            ->get();

        return response()->json($materials);
    }
}