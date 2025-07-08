<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Material;
use App\Models\Supplier;
use App\Models\ScopeType;
use App\Services\SupplierSelectionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\User;

class ClientQuotationController extends Controller
{
    public function create(Request $request)
    {
        $category = $request->query('category');
        
        // Get scope types organized by category
        $scopeTypesQuery = ScopeType::with(['materials', 'tasks']);
        
        if ($category) {
            if (is_array($category)) {
                $scopeTypesQuery->whereIn('category', $category);
            } else {
                $scopeTypesQuery->where('category', $category);
            }
        }
        
        $scopeTypes = $scopeTypesQuery->get();
        
        // Group scope types by category, and ensure all material fields are present
        $scopeTypesByCode = $scopeTypes->mapWithKeys(function ($scope) {
            $scopeArr = $scope->toArray();
            $scopeArr['materials'] = collect($scope->materials)->map(function ($material) {
                return [
                    'id' => $material->id,
                    'name' => $material->name,
                    'unit' => $material->unit,
                    'base_price' => $material->base_price,
                    'is_per_area' => $material->is_per_area,
                    'is_wall_material' => $material->is_wall_material,
                    'coverage_rate' => $material->coverage_rate,
                    'waste_factor' => $material->waste_factor,
                    'minimum_quantity' => $material->minimum_quantity,
                ];
            })->toArray();
            return [$scope->id => $scopeArr];
        })->toArray();
        
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
        ]);

        // Debug: Log the validated data
        \Log::info('Validated Quotation Request:', $validated);

        // Generate request number
        $lastRequest = \App\Models\QuotationRequest::orderByDesc('id')->first();
        if ($lastRequest && preg_match('/QR-(\\d+)/i', $lastRequest->request_number, $matches)) {
            $nextNumber = intval($matches[1]) + 1;
        } else {
            $nextNumber = 1;
        }
        $requestNumber = 'QR-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        // Create quotation request
        $quotationRequest = \App\Models\QuotationRequest::create([
            'user_id' => auth()->id(),
            'request_number' => $requestNumber,
            'status' => 'pending'
        ]);

        // Save rooms and scopes
        foreach ($validated['rooms'] as $roomData) {
            $room = $quotationRequest->rooms()->create([
                'name' => $roomData['name'],
                'length' => $roomData['length'],
                'width' => $roomData['width'],
                'height' => $roomData['height']
            ]);

            // Save scopes for this room (scope is an array of IDs)
            foreach ($roomData['scope'] as $scopeId) {
                $scopeType = \App\Models\ScopeType::where('id', $scopeId)->first();
                if ($scopeType) {
                    $room->scopes()->create([
                        'scope_type_id' => $scopeType->id,
                        'scope_name' => $scopeType->name,
                        'scope_category' => $scopeType->category,
                        'selected_materials' => [] // No materials
                    ]);
                }
            }
        }

        // Notify all admins
        $admins = User::role('admin')->get();
        foreach ($admins as $admin) {
            \App\Models\Notification::create([
                'user_id' => $admin->id,
                'type' => 'client_quotation_submitted',
                'notifiable_type' => \App\Models\QuotationRequest::class,
                'notifiable_id' => $quotationRequest->id,
                'data' => [
                    'title' => 'New Client Quotation Submitted',
                    'message' => 'A new client quotation request (Request #' . $quotationRequest->request_number . ') has been submitted and needs review.',
                    'link' => route('admin.quotation.review', ['id' => $quotationRequest->id]),
                ],
                'for_role' => 'admin',
            ]);
        }

        Session::put('client_quotation_data', $validated);
        Session::put('quotation_request_id', $quotationRequest->id);
        return redirect()->route('client.quotation.view', ['id' => $quotationRequest->id]);
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
            // Only get suppliers that actually offer this material (via pivot)
            $suppliers = $material->suppliers()->with('metrics')->get();
            $supplierData = [];
            foreach ($suppliers as $supplier) {
                // Defensive: Only include if the pivot price exists (i.e., real offer)
                if (!isset($supplier->pivot) || $supplier->pivot->price === null) continue;
                $supplierData[] = [
                    'id' => $supplier->id,
                    'name' => $supplier->company_name,
                    'price' => $supplier->pivot->price,
                    'base_price' => $material->base_price,
                    'on_time_delivery_rate' => $supplier->metrics->on_time_delivery_rate ?? 0,
                    'average_defect_rate' => $supplier->metrics->average_defect_rate ?? 0,
                    'average_cost_variance' => $supplier->metrics->average_cost_variance ?? 0,
                ];
            }
            // Badges
            if (count($supplierData) > 0) {
                $minPrice = min(array_column($supplierData, 'price'));
                $maxDelivery = max(array_column($supplierData, 'on_time_delivery_rate'));
                $minDefect = min(array_column($supplierData, 'average_defect_rate'));
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

    public function index()
    {
        // Get all quotation requests for the logged-in client
        $quotationRequests = \App\Models\QuotationRequest::where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->with('rooms')
            ->get();
        return view('client.quotation.index', compact('quotationRequests'));
    }

    public function view(Request $request)
    {
        $quotationRequestId = $request->query('id') ?? $request->id ?? session('quotation_request_id');
        if ($quotationRequestId) {
            $quotationRequest = \App\Models\QuotationRequest::with(['rooms.scopes.scopeType.materials'])->where('id', $quotationRequestId)->where('user_id', auth()->id())->first();
        } else {
            $quotationRequest = null;
        }
        $sessionData = session('client_quotation_data');

        // Fetch all RFQs (Quotations) generated for this QuotationRequest
        $rfqs = [];
        $materialSupplierResponses = [];
        $selectedSuppliers = [];
        if ($quotationRequest) {
            $rfqs = \App\Models\Quotation::where('notes', 'like', '%client quotation request #'. $quotationRequest->request_number .'%')->with(['suppliers', 'materials', 'responses.items', 'responses.supplier.metrics'])->get();
            // For each material, gather all supplier responses and the selected supplier
            $materialIds = collect();
            foreach ($quotationRequest->rooms as $room) {
                foreach ($room->scopes as $scope) {
                    if ($scope->scopeType && $scope->scopeType->materials) {
                        $materialIds = $materialIds->merge($scope->scopeType->materials->pluck('id'));
                    }
                }
            }
            $materialIds = $materialIds->unique()->values();
            foreach ($materialIds as $materialId) {
                $materialSupplierResponses[$materialId] = [];
                foreach ($rfqs as $rfq) {
                    foreach ($rfq->responses as $response) {
                        foreach ($response->items as $item) {
                            if ($item->material_id == $materialId) {
                                $materialSupplierResponses[$materialId][] = [
                                    'supplier_id' => $response->supplier_id,
                                    'supplier_name' => $response->supplier->company_name,
                                    'unit_price' => $item->unit_price,
                                    'badges' => [], // Add badge logic if needed
                                    'metrics' => $response->supplier->metrics,
                                ];
                            }
                        }
                    }
                }
                // Get selected supplier from the pivot
                foreach ($rfqs as $rfq) {
                    $material = $rfq->materials->firstWhere('id', $materialId);
                    if ($material && $material->pivot && $material->pivot->selected_supplier_id) {
                        $selectedSuppliers[$materialId] = $material->pivot->selected_supplier_id;
                        break;
                    }
                }
            }
        }
        return view('client.quotation.view', compact('quotationRequest', 'sessionData', 'materialSupplierResponses', 'selectedSuppliers'));
    }

    public function finalizeSelection(Request $request, $id)
    {
        $quotationRequest = \App\Models\QuotationRequest::with(['rooms.scopes.scopeType.materials'])->where('id', $id)->where('user_id', auth()->id())->firstOrFail();
        $selectedSuppliers = $request->input('selected_suppliers', []);

        // Find all RFQs (Quotations) generated for this QuotationRequest
        $rfqs = \App\Models\Quotation::where('notes', 'like', '%client quotation request #'. $quotationRequest->request_number .'%')->with(['materials'])->get();

        // For each material, update the selected_supplier_id in material_quotation
        foreach ($selectedSuppliers as $materialId => $supplierId) {
            foreach ($rfqs as $rfq) {
                $rfq->materials()->updateExistingPivot($materialId, ['selected_supplier_id' => $supplierId]);
            }
        }

        // Optionally, notify the admin
        $admins = \App\Models\User::role('admin')->get();
        foreach ($admins as $admin) {
            \App\Models\Notification::create([
                'user_id' => $admin->id,
                'type' => 'client_finalized_selection',
                'notifiable_type' => \App\Models\QuotationRequest::class,
                'notifiable_id' => $quotationRequest->id,
                'data' => [
                    'title' => 'Client Finalized Supplier Selection',
                    'message' => 'The client has finalized their supplier selections for Quotation Request #' . $quotationRequest->request_number . '.',
                    'link' => route('admin.quotation.review', ['id' => $quotationRequest->id]),
                ],
                'for_role' => 'admin',
            ]);
        }

        return redirect()->route('client.quotation.view', ['id' => $quotationRequest->id])
            ->with('success', 'Your supplier selections have been saved!');
    }
} 