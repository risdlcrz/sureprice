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

    public function show(Material $material)
    {
        $this->logPageView('Viewed Admin Material #' . $material->id, Material::class, $material->id);
        $suppliers = \App\Models\Supplier::orderBy('company_name')->get();
        $linkedSupplierIds = $material->suppliers()->pluck('suppliers.id')->toArray();

        return view('admin.materials.show', compact('material', 'suppliers', 'linkedSupplierIds'));
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