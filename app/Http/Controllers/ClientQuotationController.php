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
        // Clear any old session data
        \Session::forget('client_quotation_data');
        \Session::forget('quotation_request_id');
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
            'status' => 'pending',
            'total_hours' => 0 // will update after calculation
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
                $scopeType = \App\Models\ScopeType::with('materials')->where('id', $scopeId)->first();
                $selectedMaterials = [];
                if ($scopeType) {
                    foreach ($scopeType->materials as $material) {
                        $area = $material->is_wall_material ? ($roomData['length'] * 2 + $roomData['width'] * 2) * $roomData['height'] : $roomData['length'] * $roomData['width'];
                        $quantity = 1;
                        if ($material->is_per_area || $material->isPerArea) {
                            $coverage = floatval($material->coverage_rate ?? 1) ?: 1;
                            $quantity = $area > 0 && $coverage > 0 ? ceil($area / $coverage) : 0;
                        } else {
                            $quantity = $area > 0 ? ceil($area) : 1;
                        }
                        if ($quantity > 0) {
                            $wasteFactor = floatval($material->waste_factor ?? 1.1) ?: 1.1;
                            $quantity = ceil($quantity * $wasteFactor);
                        }
                        $quantity = max(1, floatval($quantity));
                        // Coverage explanation
                        $coverageInfo = '';
                        if ($material->coverage_rate && $material->unit) {
                            $coverageInfo = " (1 {$material->unit} covers {$material->coverage_rate} sqm)";
                        } elseif ($material->unit) {
                            $coverageInfo = " (1 {$material->unit} covers 1 sqm)";
                        }
                        $selectedMaterials[] = [
                            'material_id' => $material->id,
                            'quantity' => $quantity,
                            'unit' => $material->unit,
                            'coverage_info' => $coverageInfo,
                        ];
                    }
                    $room->scopes()->create([
                        'scope_type_id' => $scopeType->id,
                        'scope_name' => $scopeType->name,
                        'scope_category' => $scopeType->category,
                        'selected_materials' => $selectedMaterials
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
        // Notify all managers
        $managers = User::role('manager')->get();
        foreach ($managers as $manager) {
            \App\Models\Notification::create([
                'user_id' => $manager->id,
                'type' => 'client_quotation_submitted',
                'notifiable_type' => \App\Models\QuotationRequest::class,
                'notifiable_id' => $quotationRequest->id,
                'data' => [
                    'title' => 'New Client Quotation Submitted',
                    'message' => 'A new client quotation request (Request #' . $quotationRequest->request_number . ') has been submitted and needs review.',
                    'link' => route('manager.quotation-requests.view', ['id' => $quotationRequest->id]),
                ],
                'for_role' => 'manager',
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
        $quotationRequestId = $request->query('id') ?? $request->id ?? session('quotation_request_id');
        $category = $request->query('category', 'overall_best');
        $budget = $request->query('budget');
        $recommendations = [];
        if ($quotationRequestId) {
            $quotationRequest = \App\Models\QuotationRequest::with(['rooms.scopes.scopeType.materials'])->find($quotationRequestId);
            $rfqs = \App\Models\Quotation::where('notes', 'like', '%client quotation request #'. $quotationRequest->request_number .'%')->with(['suppliers', 'materials', 'responses.items', 'responses.supplier.metrics'])->get();
            $materialIds = collect();
            foreach ($quotationRequest->rooms as $room) {
                foreach ($room->scopes as $scope) {
                    if ($scope->scopeType && $scope->scopeType->materials) {
                        $materialIds = $materialIds->merge($scope->scopeType->materials->pluck('id'));
                    }
                }
            }
            $materialIds = $materialIds->unique()->values();
            $totalCost = 0;
            foreach ($materialIds as $materialId) {
                $offers = [];
                foreach ($rfqs as $rfq) {
                    foreach ($rfq->responses as $response) {
                        foreach ($response->items as $item) {
                            if ($item->material_id == $materialId) {
                                $offers[] = [
                                    'supplier_id' => $response->supplier_id,
                                    'unit_price' => $item->unit_price,
                                    'metrics' => $response->supplier->metrics,
                                ];
                            }
                        }
                    }
                }
                if (count($offers) > 0) {
                    // LP/Greedy: select best supplier per category
                    if ($category === 'cheapest') {
                        usort($offers, fn($a, $b) => $a['unit_price'] <=> $b['unit_price']);
                    } elseif ($category === 'fastest_delivery') {
                        usort($offers, fn($a, $b) => ($b['metrics']->on_time_delivery_rate ?? 0) <=> ($a['metrics']->on_time_delivery_rate ?? 0));
                    } elseif ($category === 'least_defects') {
                        usort($offers, fn($a, $b) => ($a['metrics']->average_defect_rate ?? 0) <=> ($b['metrics']->average_defect_rate ?? 0));
                    } else { // overall_best
                        // Composite score: higher is better
                        $minPrice = min(array_column($offers, 'unit_price'));
                        $maxDelivery = max(array_map(function($o) { return $o['metrics']->on_time_delivery_rate ?? 0; }, $offers));
                        $minDefect = min(array_map(function($o) { return $o['metrics']->average_defect_rate ?? 0; }, $offers));
                        $scores = [];
                        foreach ($offers as $ix => $o) {
                            $priceScore = $minPrice / max($o['unit_price'], 1);
                            $deliveryScore = ($o['metrics']->on_time_delivery_rate ?? 0) / max($maxDelivery, 1);
                            $defectScore = $minDefect / max($o['metrics']->average_defect_rate ?? 1, 1);
                            $scores[$ix] = $priceScore + $deliveryScore + $defectScore;
                        }
                        array_multisort($scores, SORT_DESC, $offers);
                    }
                    // Pick the top offer
                    $selected = $offers[0];
                    $recommendations[$materialId] = $selected['supplier_id'];
                    $totalCost += $selected['unit_price'];
                }
            }
            // If budget is set, check if totalCost fits
            if ($budget && $totalCost > $budget) {
                // Optionally, try to fit within budget by picking next best offers (not implemented here)
            }
        }
        return response()->json(['recommendations' => $recommendations]);
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

    public function cancel($id)
    {
        $quotationRequest = \App\Models\QuotationRequest::findOrFail($id);
        // Optionally, check if the user is authorized to cancel
        $quotationRequest->status = 'cancelled';
        $quotationRequest->save();

        // Clear session data
        \Session::forget('client_quotation_data');
        \Session::forget('quotation_request_id');

        return redirect()->route('client.quotation.index')->with('success', 'Quotation cancelled.');
    }

    public function showContractForm($id)
    {
        $quotationRequest = \App\Models\QuotationRequest::with(['rooms.scopes.scopeType'])->findOrFail($id);
        // Calculate timeline
        $crewSize = 8;
        $hoursPerDay = 8;
        $totalEstimatedDays = 0;
        foreach ($quotationRequest->rooms as $room) {
            $roomEstimatedDays = 0;
            foreach ($room->scopes as $scope) {
                $scopeType = $scope->scopeType;
                $isWallWork = $scopeType && $scopeType->is_wall_work;
                $area = $isWallWork
                    ? 2 * ($room->length + $room->width) * $room->height
                    : $room->length * $room->width;
                $laborHoursPerSqm = $scopeType && $scopeType->labor_hours_per_sqm ? $scopeType->labor_hours_per_sqm : 1;
                $totalLaborHours = $area * $laborHoursPerSqm;
                $days = $totalLaborHours / ($crewSize * $hoursPerDay);
                $days = ceil($days * 2) / 2;
                $days = max(0.5, $days);
                $roomEstimatedDays += $days;
            }
            $totalEstimatedDays = max($totalEstimatedDays, $roomEstimatedDays);
        }
        $timelineStartDate = now()->format('Y-m-d');
        $timelineEndDate = now()->copy()->addDays(ceil($totalEstimatedDays))->format('Y-m-d');
        $timelineEstimatedDays = $totalEstimatedDays;
        \Log::info('Timeline', [
            'start' => $timelineStartDate,
            'end' => $timelineEndDate,
            'days' => $timelineEstimatedDays
        ]);
        return view('client.quotation.contract', compact('quotationRequest', 'timelineStartDate', 'timelineEndDate', 'timelineEstimatedDays'));
    }

    // Removed proceed method, status is now set in finalizeSelection

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
                $offers = [];
                foreach ($rfqs as $rfq) {
                    foreach ($rfq->responses as $response) {
                        foreach ($response->items as $item) {
                            if ($item->material_id == $materialId) {
                                $offers[] = [
                                    'supplier_id' => $response->supplier_id,
                                    'supplier_name' => $response->supplier->company_name,
                                    'unit_price' => $item->unit_price,
                                    'metrics' => $response->supplier->metrics,
                                ];
                            }
                        }
                    }
                }
                // KNN-style ranking and badges
                if (count($offers) > 0) {
                    $minPrice = min(array_column($offers, 'unit_price'));
                    $maxDelivery = max(array_map(function($o) { return $o['metrics']->on_time_delivery_rate ?? 0; }, $offers));
                    $minDefect = min(array_map(function($o) { return $o['metrics']->average_defect_rate ?? 0; }, $offers));
                    // Composite score for best overall (normalize and sum)
                    $scores = [];
                    foreach ($offers as $ix => $o) {
                        $priceScore = $minPrice / max($o['unit_price'], 1);
                        $deliveryScore = ($o['metrics']->on_time_delivery_rate ?? 0) / max($maxDelivery, 1);
                        $defectScore = $minDefect / max($o['metrics']->average_defect_rate ?? 1, 1);
                        $scores[$ix] = $priceScore + $deliveryScore + $defectScore;
                    }
                    $bestOverallIx = array_keys($scores, max($scores));
                    foreach ($offers as $ix => &$offer) {
                        $offer['badges'] = [];
                        if ($offer['unit_price'] == $minPrice) $offer['badges'][] = 'Cheapest';
                        if (($offer['metrics']->on_time_delivery_rate ?? 0) == $maxDelivery) $offer['badges'][] = 'Best Delivery';
                        if (($offer['metrics']->average_defect_rate ?? 0) == $minDefect) $offer['badges'][] = 'Least Defects';
                        if (in_array($ix, $bestOverallIx)) $offer['badges'][] = 'Overall Best';
                    }
                }
                $materialSupplierResponses[$materialId] = $offers;
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
        \Log::info('finalizeSelection called', ['id' => $id, 'request' => $request->all()]);
        $quotationRequest = \App\Models\QuotationRequest::with(['rooms.scopes.scopeType.materials'])->where('id', $id)->where('user_id', auth()->id())->firstOrFail();
        $selectedSuppliers = $request->input('selected_suppliers', []);

        // Find all RFQs (Quotations) generated for this QuotationRequest
        $rfqs = \App\Models\Quotation::where('notes', 'like', '%client quotation request #'. $quotationRequest->request_number .'%')->with(['materials', 'responses.items'])->get();

        // For each material, update the selected_supplier_id and unit_price in material_quotation
        foreach ($selectedSuppliers as $materialId => $supplierId) {
            $found = false;
            foreach ($rfqs as $rfq) {
                $quotedPrice = null;
                foreach ($rfq->responses as $response) {
                    if ($response->supplier_id == $supplierId) {
                        foreach ($response->items as $item) {
                            if ($item->material_id == $materialId) {
                                $quotedPrice = $item->unit_price;
                                // Only update the pivot for this RFQ and break out of both loops
                                \Log::info('Pivot update', [
                                    'material_id' => $materialId,
                                    'supplier_id' => $supplierId,
                                    'quoted_price' => $quotedPrice,
                                    'rfq_id' => $rfq->id,
                                ]);
                                $rfq->materials()->updateExistingPivot($materialId, [
                                    'selected_supplier_id' => $supplierId,
                                    'unit_price' => $quotedPrice,
                                ]);
                                $found = true;
                                break 3;
                            }
                        }
                    }
                }
            }
            // Optionally, log if no quoted price was found for this material/supplier
            if (!$found) {
                \Log::warning('No quoted price found for material/supplier', [
                    'material_id' => $materialId,
                    'supplier_id' => $supplierId,
                ]);
            }
        }

        // Set status to proceeded only after client finalizes selection
        $quotationRequest->status = 'proceeded';
        $quotationRequest->save();

        // Store selected_suppliers in session for contract calculation
        session(['selected_suppliers' => $selectedSuppliers]);

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
        // Notify all managers
        $managers = \App\Models\User::role('manager')->get();
        foreach ($managers as $manager) {
            \App\Models\Notification::create([
                'user_id' => $manager->id,
                'type' => 'client_finalized_selection',
                'notifiable_type' => \App\Models\QuotationRequest::class,
                'notifiable_id' => $quotationRequest->id,
                'data' => [
                    'title' => 'Client Finalized Supplier Selection',
                    'message' => 'The client has finalized their supplier selections for Quotation Request #' . $quotationRequest->request_number . '.',
                    'link' => route('manager.quotation-requests.view', ['id' => $quotationRequest->id]),
                ],
                'for_role' => 'manager',
            ]);
        }

        return redirect()->route('client.quotation.view', ['id' => $quotationRequest->id])
            ->with('success', 'Your supplier selections have been saved!');
    }

    public function saveSupplierSelection(Request $request)
    {
        $request->validate([
            'quotation_request_id' => 'required|integer|exists:quotation_requests,id',
            'material_id' => 'required|integer',
            'supplier_id' => 'nullable|integer',
        ]);
        $quotationRequest = \App\Models\QuotationRequest::findOrFail($request->quotation_request_id);
        // Save or update the selected supplier for this material
        $selected = $quotationRequest->selected_suppliers ?? [];
        $selected[$request->material_id] = $request->supplier_id;
        $quotationRequest->selected_suppliers = $selected;
        $quotationRequest->save();

        // Update the pivot table for all related RFQs
        $rfqs = \App\Models\Quotation::where('notes', 'like', '%client quotation request #' . $quotationRequest->request_number . '%')->get();
        foreach ($rfqs as $rfq) {
            $rfq->materials()->updateExistingPivot($request->material_id, ['selected_supplier_id' => $request->supplier_id]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * API: Get Quotation Request details for contract editor
     */
    public function apiShow($id)
    {
        $quotation = \App\Models\QuotationRequest::with(['user.company', 'rooms.scopes.scopeType.materials'])->findOrFail($id);
        $company = $quotation->user->company;
        $clientAddress = $company
            ? trim("{$company->street}, {$company->barangay}, {$company->city}, {$company->state}, {$company->postal}", ', ')
            : '';
        return response()->json([
            'client' => [
                'name' => $company->company_name ?? $quotation->user->name,
                'street' => $company->street ?? '',
                'barangay' => $company->barangay ?? '',
                'city' => $company->city ?? '',
                'state' => $company->state ?? '',
                'postal' => $company->postal ?? '',
                'address' => $clientAddress,
                'email' => $quotation->user->email,
                'phone' => $company->mobile_number ?? '',
            ],
            // Add more fields as needed for your contract editor (property, items, etc.)
        ]);
    }
} 