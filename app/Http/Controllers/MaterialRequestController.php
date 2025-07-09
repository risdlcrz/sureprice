<?php

namespace App\Http\Controllers;

use App\Models\MaterialRequest;
use App\Models\Contract;
use App\Models\Material;
use App\Models\Warehouse;
use App\Models\PurchaseRequest;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\SupplierSelectionService;

class MaterialRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $materialRequests = MaterialRequest::with(['contract', 'user'])->latest()->paginate(10);
        return view('admin.material-requests.index', compact('materialRequests'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $quotation_id = $request->get('quotation_id');
        \Log::info('MaterialRequestController@create', ['quotation_id' => $quotation_id, 'request' => $request->all()]);
        $contracts = Contract::with('items.material')->latest()->get();
        $materials = Material::orderBy('name')->get();
        $contract_id = $request->get('contract_id');
        $selectedContract = null;
        $items = [];

        if ($quotation_id) {
            $quotation = \App\Models\QuotationRequest::with(['rooms.scopes'])->find($quotation_id);
            \Log::info('QuotationRequest loaded', ['quotation' => $quotation]);
            if ($quotation) {
                $materialQuantities = [];
                $materialDetails = [];
                foreach ($quotation->rooms as $room) {
                    foreach ($room->scopes as $scope) {
                        \Log::info('Scope selected_materials', ['selected_materials' => $scope->selected_materials]);
                        if (is_array($scope->selected_materials)) {
                            foreach ($scope->selected_materials as $mat) {
                                $materialId = $mat['material_id'] ?? $mat['id'] ?? null;
                                if ($materialId) {
                                    $qty = $mat['quantity'] ?? 1;
                                    $materialQuantities[$materialId] = ($materialQuantities[$materialId] ?? 0) + $qty;
                                    // Store extra info for each material
                                    if (!isset($materialDetails[$materialId])) {
                                        $materialDetails[$materialId] = [
                                            'description' => $mat['description'] ?? ($mat['specifications'] ?? ''),
                                            'notes' => $mat['notes'] ?? '',
                                        ];
                                    }
                                }
                            }
                        }
                    }
                }
                $materialsList = Material::whereIn('id', array_keys($materialQuantities))->get();
                foreach ($materialsList as $mat) {
                    $id = $mat->id;
                    // Fetch actual stock for this material (sum across all warehouses)
                    $actualStock = \App\Models\Stock::where('material_id', $id)->sum('current_stock');
                    $items[] = [
                        'material_id' => $id,
                        'name' => $mat->name,
                        'unit' => $mat->unit,
                        'quantity' => $materialQuantities[$id],
                        'description' => $materialDetails[$id]['description'] ?? $mat->description,
                        'notes' => $materialDetails[$id]['notes'] ?? '',
                        'warehouse_name' => 'N/A',
                        'available' => $actualStock
                    ];
                }
            }
            $selectedContract = null;
        } elseif ($contract_id) {
            // Admin/contract flow (existing logic)
            $selectedContract = Contract::with('items.material.inventory')->findOrFail($contract_id);
            $warehouses = Warehouse::orderByRaw("name = 'Warehouse A' DESC, name ASC")->get();
            foreach ($selectedContract->items as $item) {
                // Fetch actual stock for this material (sum across all warehouses)
                $actualStock = \App\Models\Stock::where('material_id', $item->material_id)->sum('current_stock');
                $warehouseName = 'N/A';
                foreach ($warehouses as $warehouse) {
                    $stock = $warehouse->stocks()->where('material_id', $item->material_id)->first();
                    if ($stock && $stock->current_stock > 0) {
                        $warehouseName = $warehouse->name;
                        break;
                    }
                }
                $items[] = [
                    'material_id' => $item->material_id,
                    'name' => $item->material->name,
                    'unit' => $item->material->unit,
                    'quantity' => $item->quantity,
                    'warehouse_name' => $warehouseName,
                    'available' => $actualStock
                ];
            }
        }

        return view('admin.material-requests.create', compact('contracts', 'materials', 'selectedContract', 'items', 'quotation_id'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'contract_id' => 'required|exists:contracts,id',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.material_id' => 'required|exists:materials,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'create_purchase_request' => 'nullable|boolean',
        ]);

        DB::beginTransaction();
        try {
            $materialRequest = MaterialRequest::create([
                'contract_id' => $validated['contract_id'],
                'requested_by' => auth()->id(),
                'status' => 'pending',
                'notes' => $validated['notes'],
            ]);

            $purchaseRequestItems = [];

            foreach ($validated['items'] as $itemData) {
                $material = Material::find($itemData['material_id']);
                $requestedQty = $itemData['quantity'];
                $remainingQty = $requestedQty;

                // Try to fulfill from the requested warehouse first
                $requestedWarehouseId = $request->input('warehouse_id'); // You may need to adjust this if multiple warehouses can be requested
                $requestedWarehouse = $requestedWarehouseId ? Warehouse::find($requestedWarehouseId) : null;
                $fulfilledFromRequested = 0;
                if ($requestedWarehouse) {
                    $requestedStock = $requestedWarehouse->stocks()->where('material_id', $material->id)->first();
                    if ($requestedStock && $requestedStock->current_stock > 0) {
                        $fromRequested = min($remainingQty, $requestedStock->current_stock);
                        if ($fromRequested > 0) {
                            $materialRequest->items()->create([
                                'material_id' => $material->id,
                                'warehouse_id' => $requestedWarehouse->id,
                                'quantity' => $fromRequested,
                                'unit' => $material->unit,
                                'fulfilled_quantity' => 0,
                            ]);
                            $remainingQty -= $fromRequested;
                            $fulfilledFromRequested = $fromRequested;
                        }
                    }
                }

                // If not enough, try to fulfill from other warehouses
                $fulfilledFromOther = 0;
                if ($remainingQty > 0) {
                    $otherWarehouses = Warehouse::where('id', '!=', $requestedWarehouseId)->get();
                    foreach ($otherWarehouses as $otherWarehouse) {
                        $otherStock = $otherWarehouse->stocks()->where('material_id', $material->id)->first();
                        if ($otherStock && $otherStock->current_stock >= $remainingQty) {
                            $materialRequest->items()->create([
                                'material_id' => $material->id,
                                'warehouse_id' => $otherWarehouse->id,
                                'quantity' => $remainingQty,
                                'unit' => $material->unit,
                                'fulfilled_quantity' => 0,
                            ]);
                            $fulfilledFromOther = $remainingQty;
                            $remainingQty = 0;
                            break;
                        }
                    }
                }

                // If still not enough and user wants to create purchase request, add to purchase request
                if ($remainingQty > 0 && $request->boolean('create_purchase_request')) {
                    $materialRequest->items()->create([
                        'material_id' => $material->id,
                        'warehouse_id' => null,
                        'quantity' => $remainingQty,
                        'unit' => $material->unit,
                        'fulfilled_quantity' => 0,
                    ]);
                    $purchaseRequestItems[] = [
                        'material_id' => $material->id,
                        'description' => $material->name,
                        'quantity' => $remainingQty,
                        'unit' => $material->unit,
                        'estimated_unit_price' => $material->base_price,
                        'total_amount' => $remainingQty * $material->base_price,
                    ];
                } elseif ($remainingQty > 0) {
                    // If user doesn't want purchase request, still create material request item but mark as unfulfilled
                    $materialRequest->items()->create([
                        'material_id' => $material->id,
                        'warehouse_id' => null,
                        'quantity' => $remainingQty,
                        'unit' => $material->unit,
                        'fulfilled_quantity' => 0,
                    ]);
                }
            }
            
            // If there are items that need purchasing and user opted to create purchase request, create a Purchase Request
            if (!empty($purchaseRequestItems) && $request->boolean('create_purchase_request')) {
                $contract = Contract::find($validated['contract_id']);
                $purchaseRequest = PurchaseRequest::create([
                    'request_number' => 'PR-' . str_pad(PurchaseRequest::count() + 1, 6, '0', STR_PAD_LEFT),
                    'material_request_id' => $materialRequest->id,
                    'contract_id' => $validated['contract_id'],
                    'requested_by' => auth()->id(),
                    'status' => 'pending',
                    'is_project_related' => true,
                    'notes' => 'Auto-generated from Material Request #' . $materialRequest->id . ' for contract ' . $contract->contract_number,
                ]);

                $totalAmount = 0;
                foreach ($purchaseRequestItems as $prItem) {
                    $purchaseRequest->items()->create($prItem);
                    $totalAmount += $prItem['total_amount'];
                }
                $purchaseRequest->total_amount = $totalAmount;
                $purchaseRequest->save();
            }

            $materialRequest->save();

            \App\Models\Notification::create([
                'notifiable_id' => auth()->id(),
                'notifiable_type' => \App\Models\User::class,
                'type' => 'Material Request',
                'data' => ['message' => 'Your material request #' . $materialRequest->id . ' has been created.'],
            ]);

            DB::commit();
            return redirect()->route('material-requests.show', $materialRequest)->with('success', 'Material request created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating material request: ' . $e->getMessage());
            return back()->with('error', 'There was an error creating the material request. Please try again.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(MaterialRequest $materialRequest)
    {
        $materialRequest->load(['contract.client', 'user', 'items.material.inventory', 'items.warehouse']);
        $purchaseRequest = PurchaseRequest::where('material_request_id', $materialRequest->id)->first();
        return view('admin.material-requests.show', compact('materialRequest', 'purchaseRequest'));
    }

    public function recommendSuppliersForMaterial(Request $request)
    {
        $materialId = $request->input('material_id');
        $budget = $request->input('budget', 100000);
        $mode = $request->input('mode', 'best_score');
        $projectFeatures = [
            'on_time_delivery_rate' => $request->input('on_time_delivery_rate', 90),
            'average_defect_rate' => $request->input('average_defect_rate', 2),
            'average_cost_variance' => $request->input('average_cost_variance', 0),
        ];

        $suppliers = Supplier::with(['metrics', 'materials'])->get()->map(function($supplier) {
            return [
                'id' => $supplier->id,
                'name' => $supplier->company_name,
                'material_ids' => $supplier->materials->pluck('id')->toArray(),
                'on_time_delivery_rate' => $supplier->metrics ? $supplier->metrics->on_time_delivery_rate : 0,
                'average_defect_rate' => $supplier->metrics->average_defect_rate ?? 0,
                'average_cost_variance' => $supplier->metrics->average_cost_variance ?? 0,
                'score' => $supplier->metrics->score ?? 0,
                'delivery' => $supplier->metrics->delivery ?? 0,
                'quality' => $supplier->metrics->quality ?? 0,
                'cost' => $supplier->metrics->cost ?? 0,
                'performance' => $supplier->metrics->performance ?? 0,
                'engagement' => $supplier->metrics->engagement ?? 0,
                'sustainability' => $supplier->metrics->sustainability ?? 0,
            ];
        })->toArray();

        $service = new SupplierSelectionService();
        $filteredSuppliers = $service->filterByMaterial($suppliers, $materialId);
        // Support different modes if needed (for now, use recommend/optimize as before)
        $recommended = $service->recommend($filteredSuppliers, $projectFeatures, 5);
        $optimal = $service->optimize($recommended, $budget);

        return response()->json([
            'html' => view('admin.suppliers.partials.recommendation-tables', [
                'recommended' => $recommended,
                'optimal' => $optimal,
            ])->render()
        ]);
    }
}
