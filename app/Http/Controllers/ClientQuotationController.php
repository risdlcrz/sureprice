<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Material;
use App\Models\Supplier;
use App\Models\ScopeType;
use App\Services\SupplierSelectionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ClientQuotationController extends Controller
{
    public function create(Request $request)
    {
        $category = $request->query('category');
        
        // Get scope types organized by category
        $scopeTypesQuery = ScopeType::with(['materials', 'tasks']);
        
        if ($category) {
            $scopeTypesQuery->where('category', $category);
        }
        
        $scopeTypes = $scopeTypesQuery->get();
        
        // Group scope types by category
        $scopeTypesByCode = $scopeTypes->keyBy('id')->toArray();
        
        // Get session data if any
        $sessionData = session('client_quotation_data', []);
        
        // Get all unique materials for this category
        $allMaterials = $scopeTypes->flatMap->materials->unique('id');
        
        // Get supplier/badge data for each material
        $materialSuppliers = $this->getMaterialSuppliersWithBadges($allMaterials);
        
        return view('client.quotation.create', compact('scopeTypesByCode', 'sessionData', 'category', 'materialSuppliers'));
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'rooms' => 'required|array|min:1',
            'rooms.*.name' => 'required|string|max:255',
            'rooms.*.length' => 'required|numeric|min:0.01',
            'rooms.*.width' => 'required|numeric|min:0.01',
            'rooms.*.height' => 'required|numeric|min:0.01',
            'rooms.*.scope' => 'required|array|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);
        
        // Store in session for now (in a real app, you'd save to database)
        Session::put('client_quotation_data', $validated);
        
        return redirect()->route('client.quotation.suppliers')
            ->with('success', 'Quotation request created successfully. Please select suppliers.');
    }
    
    public function suppliers()
    {
        $quotationData = Session::get('client_quotation_data');
        
        if (!$quotationData) {
            return redirect()->route('client.quotation.create')
                ->with('error', 'Please create a quotation request first.');
        }
        
        // Get all materials from the selected scopes
        $materialIds = collect($quotationData['rooms'])->flatMap(function($room) {
            return $room['scope'];
        })->unique();
        
        $scopeTypes = ScopeType::with(['materials'])->whereIn('id', $materialIds)->get();
        $materials = $scopeTypes->flatMap->materials->unique('id');
        
        // Get suppliers for these materials
        $suppliers = Supplier::with(['materials', 'metrics'])
            ->whereHas('materials', function($query) use ($materials) {
                $query->whereIn('materials.id', $materials->pluck('id'));
            })
            ->get();
        
        return view('client.quotation.suppliers', compact('quotationData', 'materials', 'suppliers'));
    }
    
    public function recommendSuppliers(Request $request)
    {
        $materialId = $request->input('material_id');
        $budget = $request->input('budget', 100000);
        $projectFeatures = [
            'on_time_delivery_rate' => $request->input('on_time_delivery_rate', 90),
            'average_defect_rate' => $request->input('average_defect_rate', 2),
            'average_cost_variance' => $request->input('average_cost_variance', 0),
        ];

        $suppliers = Supplier::with(['metrics', 'materials'])->get()->map(function($supplier) use ($materialId) {
            return [
                'id' => $supplier->id,
                'name' => $supplier->company_name,
                'material_ids' => $supplier->materials->pluck('id')->toArray(),
                'on_time_delivery_rate' => $supplier->metrics ? $supplier->metrics->on_time_delivery_rate : 0,
                'average_defect_rate' => $supplier->metrics->average_defect_rate ?? 0,
                'average_cost_variance' => $supplier->metrics->average_cost_variance ?? 0,
                'cost' => $supplier->metrics->average_cost_variance ?? 0,
                'price' => $supplier->materials->where('id', $materialId)->first()?->pivot?->price ?? null,
            ];
        })->toArray();

        $service = new SupplierSelectionService();
        $filteredSuppliers = $service->filterByMaterial($suppliers, $materialId);

        // KNN Modes
        $knn = [];
        // Best Overall (KNN)
        $knn['best_overall'] = [];
        $knnResults = $service->recommend($filteredSuppliers, $projectFeatures, 5);
        foreach ($knnResults as $i => $item) {
            $knn['best_overall'][] = [
                'supplier' => $item['supplier'],
                'reason' => 'Best Overall',
                'score' => isset($item['distance']) ? (100 - $item['distance']) : null,
                'is_cheapest' => false,
                'recommended' => $i === 0,
            ];
        }
        // Cheapest
        $cheapest = array_filter($filteredSuppliers, fn($s) => $s['price'] !== null);
        usort($cheapest, fn($a, $b) => $a['price'] <=> $b['price']);
        $knn['cheapest'] = [];
        foreach ($cheapest as $i => $supplier) {
            $knn['cheapest'][] = [
                'supplier' => $supplier,
                'reason' => 'Cheapest',
                'is_cheapest' => $i === 0,
                'recommended' => $i === 0,
            ];
        }
        // Best Delivery
        $delivery = $filteredSuppliers;
        usort($delivery, fn($a, $b) => $b['on_time_delivery_rate'] <=> $a['on_time_delivery_rate']);
        $knn['delivery'] = [];
        foreach ($delivery as $i => $supplier) {
            $knn['delivery'][] = [
                'supplier' => $supplier,
                'reason' => 'Best On-Time Delivery',
                'is_cheapest' => false,
                'recommended' => $i === 0,
            ];
        }
        // Least Defects
        $defects = $filteredSuppliers;
        usort($defects, fn($a, $b) => $a['average_defect_rate'] <=> $b['average_defect_rate']);
        $knn['defects'] = [];
        foreach ($defects as $i => $supplier) {
            $knn['defects'][] = [
                'supplier' => $supplier,
                'reason' => 'Lowest Defect Rate',
                'is_cheapest' => false,
                'recommended' => $i === 0,
            ];
        }

        // Simple LP: pick the cheapest supplier for the material if within budget
        $lp = [];
        if (count($cheapest) > 0) {
            $best = $cheapest[0];
            $lp['suppliers'] = [[
                'supplier' => $best,
                'reason' => 'Cheapest (LP)',
                'recommended' => true,
            ]];
            $lp['total'] = $best['price'] ?? 0;
            $lp['fits_budget'] = $lp['total'] <= $budget;
        } else {
            $lp['suppliers'] = [];
            $lp['total'] = 0;
            $lp['fits_budget'] = false;
        }

        return response()->json([
            'html' => view('client.quotation.partials.supplier-recommendations', [
                'knn' => $knn,
                'lp' => $lp,
            ])->render()
        ]);
    }
    
    public function submit(Request $request)
    {
        $validated = $request->validate([
            'selected_suppliers' => 'required|array',
            'selected_suppliers.*' => 'required|exists:suppliers,id',
            'notes' => 'nullable|string',
        ]);
        
        // Here you would typically:
        // 1. Create a quotation request record
        // 2. Send notifications to selected suppliers
        // 3. Store the quotation data
        
        // For now, just clear the session and redirect
        Session::forget('client_quotation_data');
        
        return redirect()->route('landing.catalogue')
            ->with('success', 'Quotation request submitted successfully. Suppliers will be notified.');
    }

    /**
     * Get suppliers for each material with price, metrics, and badges
     */
    private function getMaterialSuppliersWithBadges($materials, $projectFeatures = [])
    {
        $result = [];
        foreach ($materials as $material) {
            $suppliers = $material->suppliers()->with('metrics')->get();
            $supplierData = [];
            foreach ($suppliers as $supplier) {
                $supplierData[] = [
                    'id' => $supplier->id,
                    'name' => $supplier->company_name,
                    'price' => $supplier->pivot->price ?? null,
                    'base_price' => $material->base_price,
                    'on_time_delivery_rate' => $supplier->metrics->on_time_delivery_rate ?? 0,
                    'average_defect_rate' => $supplier->metrics->average_defect_rate ?? 0,
                    'average_cost_variance' => $supplier->metrics->average_cost_variance ?? 0,
                ];
            }
            // Badges
            $badges = [];
            if (count($supplierData) > 0) {
                // Cheapest
                $minPrice = min(array_column($supplierData, 'price'));
                // Best Delivery
                $maxDelivery = max(array_column($supplierData, 'on_time_delivery_rate'));
                // Least Defects
                $minDefect = min(array_column($supplierData, 'average_defect_rate'));
                // Overall Best (KNN)
                $service = new \App\Services\SupplierSelectionService();
                $knn = $service->recommend($supplierData, $projectFeatures, 1);
                $bestOverallId = $knn[0]['supplier']['id'] ?? null;
                foreach ($supplierData as &$s) {
                    $s['badges'] = [];
                    if ($s['price'] == $minPrice) $s['badges'][] = 'Cheapest';
                    if ($s['on_time_delivery_rate'] == $maxDelivery) $s['badges'][] = 'Best Delivery';
                    if ($s['average_defect_rate'] == $minDefect) $s['badges'][] = 'Least Defects';
                    if ($s['id'] == $bestOverallId) $s['badges'][] = 'Overall Best';
                }
            }
            $result[$material->id] = $supplierData;
        }
        return $result;
    }
} 