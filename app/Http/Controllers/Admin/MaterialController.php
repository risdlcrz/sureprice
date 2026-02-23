<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\Category;
use App\Models\Company;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\Activity;

class MaterialController extends Controller
{
    public function index()
    {
        $this->logPageView('Viewed Admin Materials List', Material::class);
        $materials = Material::with('priceHistories')->get();
        // Calculate forecast for each material
        foreach ($materials as $material) {
            $material->forecasted_price = $this->forecastPrice($material);
        }
        return view('admin.materials.index', compact('materials'));
    }

    public function show(Material $material)
    {
        $this->logPageView('Viewed Admin Material #' . $material->id, Material::class, $material->id);
        $suppliers = \App\Models\Supplier::orderBy('company_name')->get();
        $linkedSupplierIds = $material->suppliers()->pluck('suppliers.id')->toArray();

        return view('admin.materials.show', compact('material', 'suppliers', 'linkedSupplierIds'));
    }

    public function edit(Material $material)
    {
        $this->logPageView('Viewed Edit Admin Material #' . $material->id, Material::class, $material->id);
        $categories = Category::whereRaw('LOWER(name) != ?', ['other'])->get();
        return view('admin.materials.edit', compact('material', 'categories'));
    }

    public function create()
    {
        $this->logPageView('Viewed Create Admin Material Page', Material::class);
        $suppliers = \App\Models\Supplier::all();
        $scopeTypes = \App\Models\ScopeType::orderBy('name')->get();
        $categories = Category::whereRaw('LOWER(name) != ?', ['other'])->get();
        return view('admin.materials.form', compact('suppliers', 'scopeTypes', 'categories'));
    }

    public function updateSuppliers(Request $request, Material $material)
    {
        $this->logAction('updated', 'Updated suppliers for Material #' . $material->id, Material::class, $material->id);
        $supplierIds = $request->input('suppliers', []);
        // Only allow approved suppliers
        $approvedSupplierIds = \App\Models\Supplier::whereIn('id', $supplierIds)->pluck('id')->toArray();
        if (count($approvedSupplierIds) !== count($supplierIds)) {
            return back()->with('error', 'One or more selected suppliers are not approved. Please select only approved suppliers.');
        }
        $material->suppliers()->sync($approvedSupplierIds);

        return redirect()->route('admin.materials.show', $material)
            ->with('success', 'Suppliers updated successfully.');
    }

    /**
     * Simple linear regression forecast for next price
     */
    protected function forecastPrice($material)
    {
        $history = $material->getPriceHistoryArray();
        if (count($history) < 2) {
            return null; // Not enough data
        }
        $dates = array_keys($history);
        $prices = array_values($history);
        // Convert dates to sequential numbers
        $x = range(1, count($dates));
        $y = $prices;
        $n = count($x);
        $sumX = array_sum($x);
        $sumY = array_sum($y);
        $sumXY = 0;
        $sumX2 = 0;
        for ($i = 0; $i < $n; $i++) {
            $sumXY += $x[$i] * $y[$i];
            $sumX2 += $x[$i] * $x[$i];
        }
        $slope = ($n * $sumXY - $sumX * $sumY) / ($n * $sumX2 - $sumX * $sumX);
        $intercept = ($sumY - $slope * $sumX) / $n;
        $nextX = $n + 1;
        $forecast = $slope * $nextX + $intercept;
        return round($forecast, 2);
    }

    public function priceAnalysis()
    {
        // Get all materials
        $materials = Material::orderBy('name')->get();
        // For each material, build price history from material_supplier_price_histories
        foreach ($materials as $material) {
            $history = \DB::table('material_supplier_price_histories')
                ->where('material_id', $material->id)
                ->orderBy('date')
                ->get()
                ->groupBy('date')
                ->map(function($items) {
                    // Use average price per day if multiple
                    return round(collect($items)->avg('price'), 2);
                });
            $material->price_history_for_analysis = $history;
            $material->forecasted_price = $this->forecastPriceFromArray($history->all());
        }
        // prepare JSON-friendly data for chart script to avoid inline blade logic
        $materialsData = $materials->map(function($m) {
            return [
                'id' => $m->id,
                'name' => $m->name,
                'price_history_for_analysis' => $m->price_history_for_analysis ?? (object)[],
                'forecasted_price' => $m->forecasted_price ?? null,
            ];
        });

        return view('admin.price-analysis', [
            'materials' => $materials,
            'materialsData' => $materialsData,
        ]);
    }

    /**
     * Forecast price using linear regression from an array of prices (date order)
     */
    protected function forecastPriceFromArray($prices)
    {
        if (count($prices) < 2) {
            return null;
        }
        $x = range(1, count($prices));
        $y = array_values($prices);
        $n = count($x);
        $sumX = array_sum($x);
        $sumY = array_sum($y);
        $sumXY = 0;
        $sumX2 = 0;
        for ($i = 0; $i < $n; $i++) {
            $sumXY += $x[$i] * $y[$i];
            $sumX2 += $x[$i] * $x[$i];
        }
        $slope = ($n * $sumXY - $sumX * $sumY) / ($n * $sumX2 - $sumX * $sumX);
        $intercept = ($sumY - $slope * $sumX) / $n;
        $nextX = $n + 1;
        $forecast = $slope * $nextX + $intercept;
        return round($forecast, 2);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required', // Only required, not exists
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
            'custom_category' => 'nullable|string|max:255',
        ]);

        try {
            \DB::beginTransaction();

            // Handle custom category
            if ($validated['category_id'] === 'other') {
                $customCategoryName = trim($request->input('custom_category'));
                if (!$customCategoryName) {
                    return back()->with('error', 'Please enter a custom category name.')->withInput();
                }
                $category = Category::firstOrCreate([
                    'name' => $customCategoryName
                ], [
                    'slug' => \Str::slug($customCategoryName),
                    'description' => 'Custom category created via material form',
                ]);
                $validated['category_id'] = $category->id;
            }

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

            $this->logAction('created', 'Created Material #' . $material->id, Material::class, $material->id);

            // ... existing code ...
        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->with('error', 'An error occurred while creating the material.')->withInput();
        }
    }

    private function logPageView($description, $modelType = null, $modelId = null)
    {
        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'viewed',
            'description' => $description,
            'model_type' => $modelType,
            'model_id' => $modelId
        ]);
    }

    private function logAction($action, $description, $modelType = null, $modelId = null)
    {
        Activity::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'description' => $description,
            'model_type' => $modelType,
            'model_id' => $modelId
        ]);
    }
} 