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
        $query = Supplier::with(['materials.category']);

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
        $allowedSorts = ['company_name', 'contact_person', 'materials_count', 'created_at'];
        if (!in_array($sort, $allowedSorts)) {
            $sort = 'company_name';
        }
        if ($sort === 'materials_count') {
            $query->withCount('materials')->orderBy('materials_count', 'desc');
        } else {
            $query->withCount('materials')->orderBy($sort);
        }

        // Per page
        $perPage = (int) $request->input('per_page', 10);
        if (!in_array($perPage, [10, 25, 50, 100])) {
            $perPage = 10;
        }

        $suppliers = $query->paginate($perPage)->appends($request->all());

        return view('admin.suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        $scopeTypes = \App\Models\ScopeType::orderBy('name')->get();
        return view('admin.suppliers.form', compact('scopeTypes'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'company_name' => 'required|string|max:255',
                'contact_person' => 'required|string|max:255',
                'email' => 'required|email|unique:suppliers,email',
                'phone' => 'required|string|max:20',
                'address' => 'required|string',
                'tax_number' => 'nullable|string|max:50',
                'registration_number' => 'nullable|string|max:50',
                'status' => 'required|in:active,inactive,pending',
                'materials' => 'nullable|array',
                'materials.*.price' => 'required|numeric|min:0',
                'materials.*.lead_time' => 'required|integer|min:0',
                'new_materials' => 'nullable|array',
                'new_materials.*.name' => 'required|string|max:255',
                'new_materials.*.code' => 'required|string|max:50',
                'new_materials.*.description' => 'nullable|string',
                'new_materials.*.category' => 'required|string',
                'new_materials.*.unit' => 'required|string',
                'new_materials.*.base_price' => 'required|numeric|min:0',
                'new_materials.*.srp_price' => 'required|numeric|min:0',
                'new_materials.*.specifications' => 'nullable|string',
                'new_materials.*.scope_types' => 'nullable|array',
                'new_materials.*.scope_types.*' => 'exists:scope_types,id',
                'new_materials.*.images' => 'nullable|array',
            ]);

            DB::beginTransaction();

            $supplier = Supplier::create([
                'company_name' => $validated['company_name'],
                'contact_person' => $validated['contact_person'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'address' => $validated['address'],
                'tax_number' => $validated['tax_number'],
                'registration_number' => $validated['registration_number'],
                'status' => $validated['status']
            ]);

            // Handle existing materials
            if (!empty($validated['materials'])) {
                foreach ($validated['materials'] as $materialId => $data) {
                    $supplier->materials()->attach($materialId, [
                        'price' => $data['price'],
                        'lead_time' => $data['lead_time']
                    ]);
                }
            }

            // Handle new materials
            if (!empty($validated['new_materials'])) {
                foreach ($validated['new_materials'] as $tempId => $data) {
                    // Get or create the category
                    $category = \App\Models\Category::firstOrCreate(
                        ['slug' => $data['category']],
                        ['name' => ucfirst($data['category'])]
                    );

                    $material = Material::create([
                        'name' => $data['name'],
                        'code' => $data['code'],
                        'description' => $data['description'],
                        'category_id' => $category->id,
                        'unit' => $data['unit'],
                        'base_price' => $data['base_price'],
                        'srp_price' => $data['srp_price'],
                        'specifications' => $data['specifications']
                    ]);

                    // Handle image uploads
                    $newMaterialImages = $request->file("new_materials.{$tempId}.images");
                    $existingMaterialImagesData = $data['images'] ?? [];

                    if (!empty($newMaterialImages)) {
                        // Process newly uploaded files
                        foreach ($newMaterialImages as $image) {
                            $path = $image->store('materials', 'public');
                            $material->images()->create(['path' => $path]);
                        }
                    } elseif (!empty($existingMaterialImagesData)) {
                        // Process images that were temporarily stored from a previous failed validation
                        foreach ($existingMaterialImagesData as $image) {
                            if (is_array($image) && isset($image['path'])) {
                                $finalPath = str_replace('temp/', '', $image['path']);
                                if (Storage::disk('public')->exists($image['path'])) {
                                    Storage::disk('public')->move($image['path'], $finalPath);
                                    $material->images()->create(['path' => $finalPath]);
                                } else {
                                    \Log::warning('Missing temporary image file during re-submission: ' . $image['path']);
                                }
                            }
                        }
                    }

                    // Attach scope types if any
                    if (!empty($data['scope_types'])) {
                        $material->scopeTypes()->attach($data['scope_types']);
                    }

                    // Attach to supplier
                    $supplier->materials()->attach($material->id, [
                        'price' => $data['price'] ?? $data['base_price'],
                        'lead_time' => $data['lead_time'] ?? 0
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('suppliers.index')
                ->with('success', 'Supplier created successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Flash the materials data to the session
            if ($request->has('materials')) {
                session()->flash('materials', $request->input('materials'));
            }
            if ($request->has('new_materials')) {
                $newMaterials = $request->input('new_materials');
                
                // Handle image files for new materials
                foreach ($newMaterials as $tempId => &$data) {
                    if ($request->hasFile("new_materials.{$tempId}.images")) {
                        $images = [];
                        foreach ($request->file("new_materials.{$tempId}.images") as $image) {
                            // Store the image temporarily
                            $path = $image->store('temp/materials', 'public');
                            $images[] = [
                                'path' => $path,
                                'original_name' => $image->getClientOriginalName(),
                                'mime_type' => $image->getMimeType(),
                                'size' => $image->getSize()
                            ];
                        }
                        $data['images'] = $images;
                    }
                }
                
                session()->flash('new_materials', $newMaterials);
            }
            
            // Log request data for debugging purposes
            \Log::info('Validation Exception - Request Input:', $request->all());
            \Log::info('Validation Exception - Request Files:', $request->file());

            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating supplier: ' . $e->getMessage());
            return back()->with('error', 'Error creating supplier: ' . $e->getMessage())->withInput();
        }
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