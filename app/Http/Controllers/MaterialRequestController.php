<?php

namespace App\Http\Controllers;

use App\Models\MaterialRequest;
use App\Models\Material;
use App\Models\Warehouse;
use App\Models\PurchaseRequest;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MaterialRequestController extends Controller
{
    public function index()
    {
        $materialRequests = MaterialRequest::with(['quotationRequest', 'user'])->latest()->paginate(10);
        return view('admin.material-requests.index', compact('materialRequests'));
    }

    public function create(Request $request)
    {
        $quotation_id = $request->get('quotation_id');
        $contract_id = $request->get('contract_id');
        
        if ($contract_id) {
            $contract = \App\Models\Contract::findOrFail($contract_id);
            if ($contract->status !== 'approved') {
                abort(403, 'You cannot request materials until the contract is approved by the administrator.');
            }
            
            // Handle contract-based material request
            $materials = Material::orderBy('name')->get();
            $items = [];
            $anyShort = false;
            
            // Get selected suppliers from the original quotation request
            $selectedSuppliers = [];
            if ($contract->quotationRequest) {
                $selectedSuppliers = $contract->quotationRequest->selected_suppliers ?? [];
            }
            
            \Log::info('Contract-based Material Request Creation - Selected Suppliers', [
                'contract_id' => $contract->id,
                'quotation_request_id' => $contract->quotationRequest?->id,
                'selected_suppliers' => $selectedSuppliers,
                'selected_suppliers_keys' => array_keys($selectedSuppliers ?? [])
            ]);
            
            // Get materials from contract items
            $contractItems = $contract->items;
            foreach ($contractItems as $item) {
                $material = $item->material;
                $actualStock = Stock::where('material_id', $material->id)->sum('current_stock');
                $needed = $item->quantity;
                
                if ($actualStock < $needed) {
                    $anyShort = true;
                }
                
                // Get the client's selected supplier for this material
                $selectedSupplierId = $selectedSuppliers[$material->id] ?? $selectedSuppliers[(string)$material->id] ?? null;
                
                \Log::info('Processing contract material for material request', [
                    'material_id' => $material->id,
                    'material_name' => $material->name,
                    'selected_supplier_id' => $selectedSupplierId,
                    'selected_suppliers_keys' => array_keys($selectedSuppliers ?? [])
                ]);
                
                $items[] = [
                    'material_id' => $material->id,
                    'name' => $material->name,
                    'unit' => $material->unit,
                    'quantity' => $needed,
                    'available' => $actualStock,
                    'selected_supplier_id' => $selectedSupplierId
                ];
            }
            
            return view('admin.material-requests.create', compact('materials', 'items', 'contract_id', 'anyShort', 'selectedSuppliers'));
        }
        
        if (!$quotation_id) {
            abort(404, 'Quotation request or contract ID required.');
        }
        
        // Handle quotation-based material request (existing logic)
        $quotation = \App\Models\QuotationRequest::with(['rooms.scopes'])->findOrFail($quotation_id);
        $materials = Material::orderBy('name')->get();
        $items = [];
        $anyShort = false;

        // Get client's selected suppliers from the quotation request
        $selectedSuppliers = $quotation->selected_suppliers ?? [];
        
        \Log::info('Material Request Creation - Selected Suppliers', [
            'quotation_id' => $quotation->id,
            'selected_suppliers' => $selectedSuppliers,
            'quotation_selected_suppliers' => $quotation->selected_suppliers,
            'selected_suppliers_keys' => array_keys($selectedSuppliers ?? []),
            'selected_suppliers_types' => array_map(function($key) { return gettype($key); }, array_keys($selectedSuppliers ?? []))
        ]);

        // Gather all materials and quantities from the quotation
        $materialQuantities = [];
        foreach ($quotation->rooms as $room) {
            foreach ($room->scopes as $scope) {
                if (is_array($scope->selected_materials)) {
                    foreach ($scope->selected_materials as $mat) {
                        $materialId = $mat['material_id'] ?? $mat['id'] ?? null;
                        if ($materialId) {
                            $qty = $mat['quantity'] ?? 1;
                            $materialQuantities[$materialId] = ($materialQuantities[$materialId] ?? 0) + $qty;
                        }
                    }
                }
            }
        }
        $materialsList = Material::whereIn('id', array_keys($materialQuantities))->get();
        foreach ($materialsList as $mat) {
            $id = $mat->id;
            $actualStock = Stock::where('material_id', $id)->sum('current_stock');
            $needed = $materialQuantities[$id];
            if ($actualStock < $needed) {
                $anyShort = true;
            }
            
            // Get the client's selected supplier for this material
            // Try both string and integer keys since JSON might store keys as strings
            $selectedSupplierId = $selectedSuppliers[$id] ?? $selectedSuppliers[(string)$id] ?? null;
            
            // If not found in selected_suppliers, try to get from material_quotation pivot
            if (!$selectedSupplierId) {
                $rfqs = \App\Models\Quotation::where('notes', 'like', '%client quotation request #'. $quotation->request_number .'%')->with(['materials'])->get();
                foreach ($rfqs as $rfq) {
                    $matPivot = $rfq->materials->firstWhere('id', $id);
                    if ($matPivot && $matPivot->pivot && $matPivot->pivot->selected_supplier_id) {
                        $selectedSupplierId = $matPivot->pivot->selected_supplier_id;
                        break;
                    }
                }
            }
            
            \Log::info('Processing material for material request', [
                'material_id' => $id,
                'material_id_type' => gettype($id),
                'material_name' => $mat->name,
                'selected_supplier_id' => $selectedSupplierId,
                'selected_suppliers_keys' => array_keys($selectedSuppliers ?? []),
                'trying_key_int' => $id,
                'trying_key_string' => (string)$id,
                'available_suppliers' => $mat->suppliers->pluck('id')->toArray()
            ]);
            
            $items[] = [
                'material_id' => $id,
                'name' => $mat->name,
                'unit' => $mat->unit,
                'quantity' => $needed,
                'available' => $actualStock,
                'selected_supplier_id' => $selectedSupplierId
            ];
        }
        
        \Log::info('MaterialRequestController@create - Final items for view', [
            'items' => $items,
            'selectedSuppliers' => $selectedSuppliers,
        ]);
        
        return view('admin.material-requests.create', compact('materials', 'items', 'quotation_id', 'anyShort', 'selectedSuppliers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'quotation_request_id' => 'nullable|exists:quotation_requests,id',
            'contract_id' => 'nullable|exists:contracts,id',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.material_id' => 'required|exists:materials,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit' => 'nullable|string',
            'items.*.preferred_supplier_id' => 'nullable|exists:suppliers,id',
        ]);

        // Ensure either quotation_request_id or contract_id is provided
        if (empty($validated['quotation_request_id'] ?? null) && empty($validated['contract_id'] ?? null)) {
            return back()->withErrors(['error' => 'Either quotation request ID or contract ID is required.']);
        }

        DB::beginTransaction();
        try {
            \Log::info('Creating material request', [
                'validated' => $validated,
                'user_id' => auth()->id()
            ]);
            $materialRequest = MaterialRequest::create([
                'quotation_request_id' => $validated['quotation_request_id'] ?? null,
                'contract_id' => $validated['contract_id'] ?? null,
                'requested_by' => auth()->id(),
                'status' => 'pending',
                'notes' => $validated['notes'] ?? null,
            ]);

            $purchaseRequestItems = [];
            \Log::info('Starting material request item processing', [
                'items_count' => count($validated['items']),
                'items' => $validated['items']
            ]);
            
            foreach ($validated['items'] as $itemData) {
                $material = Material::find($itemData['material_id']);
                $requestedQty = $itemData['quantity'];
                $remainingQty = $requestedQty;
                $unit = $itemData['unit'] ?? ($material ? $material->unit : null);
                if (empty($unit)) {
                    throw new \RuntimeException('Material "' . ($material ? $material->name : 'Unknown') . '" (ID: ' . ($material ? $material->id : 'N/A') . ') is missing a unit. Please check your materials data.');
                }

                // Deduct from stock across all warehouses
                $totalStock = Stock::where('material_id', $material->id)->sum('current_stock');
                \Log::info('Stock availability check', [
                    'material_id' => $material->id,
                    'material_name' => $material->name,
                    'total_stock' => $totalStock,
                    'requested_quantity' => $remainingQty,
                    'stock_sufficient' => $totalStock >= $remainingQty
                ]);
                
                if ($totalStock >= $remainingQty) {
                    // Stock is sufficient, create a single item for the total requested quantity
                    $materialRequest->items()->create([
                        'material_id' => $material->id,
                        'warehouse_id' => null, // No specific warehouse since it's fulfilled from stock
                        'quantity' => $requestedQty,
                        'unit' => $unit,
                        'fulfilled_quantity' => $requestedQty,
                    ]);
                    
                    // Deduct from stock across warehouses (for inventory tracking)
                    $warehouses = Warehouse::all();
                    $remainingToDeduct = $requestedQty;
                    foreach ($warehouses as $warehouse) {
                        if ($remainingToDeduct <= 0) break;
                        
                        $stock = $warehouse->stocks()->where('material_id', $material->id)->first();
                        if ($stock && $stock->current_stock > 0) {
                            $deduct = min($stock->current_stock, $remainingToDeduct);
                            $stock->current_stock -= $deduct;
                            $stock->save();
                            $remainingToDeduct -= $deduct;
                        }
                    }
                } else {
                    \Log::info('Insufficient stock detected', [
                        'material_id' => $material->id,
                        'material_name' => $material->name,
                        'total_stock' => $totalStock,
                        'requested_quantity' => $requestedQty,
                        'remaining_quantity' => $remainingQty
                    ]);
                    
                    // Fulfill as much as possible from stock, rest goes to purchase request
                    $fulfilledFromStock = 0;
                    $warehouses = Warehouse::all();
                    foreach ($warehouses as $warehouse) {
                        $stock = $warehouse->stocks()->where('material_id', $material->id)->first();
                        if ($stock && $stock->current_stock > 0 && $remainingQty > 0) {
                            $deduct = min($stock->current_stock, $remainingQty);
                            $stock->current_stock -= $deduct;
                            $stock->save();
                            $fulfilledFromStock += $deduct;
                            $remainingQty -= $deduct;
                        }
                    }
                    
                    // Create a single material request item for the total requested quantity
                    $materialRequest->items()->create([
                        'material_id' => $material->id,
                        'warehouse_id' => null,
                        'quantity' => $requestedQty,
                        'unit' => $unit,
                        'fulfilled_quantity' => $fulfilledFromStock,
                    ]);
                    // Add shortfall to purchase request
                    \Log::info('Stock calculation for material', [
                        'material_id' => $material->id,
                        'material_name' => $material->name,
                        'requested_quantity' => $itemData['quantity'],
                        'remaining_quantity' => $remainingQty,
                        'will_create_purchase_request' => $remainingQty > 0
                    ]);
                    
                    if ($remainingQty > 0) {
                        // Get supplier from form submission or from quotation request
                        $preferredSupplierId = $itemData['preferred_supplier_id'] ?? null;
                        $estimatedUnitPrice = $material->base_price;
                        
                        // If no supplier selected in form, try to get from quotation request
                        if (!$preferredSupplierId && $materialRequest->quotationRequest) {
                            $quotationRequest = $materialRequest->quotationRequest;
                            $selectedSuppliers = $quotationRequest->selected_suppliers ?? [];
                            $preferredSupplierId = $selectedSuppliers[$material->id] ?? null;
                            
                            // If still no supplier, try to get from material_quotation pivot
                            if (!$preferredSupplierId) {
                                $rfqs = \App\Models\Quotation::where('notes', 'like', '%client quotation request #'. $quotationRequest->request_number .'%')->with(['materials'])->get();
                                foreach ($rfqs as $rfq) {
                                    $mat = $rfq->materials->firstWhere('id', $material->id);
                                    if ($mat && $mat->pivot && $mat->pivot->selected_supplier_id) {
                                        $preferredSupplierId = $mat->pivot->selected_supplier_id;
                                        $estimatedUnitPrice = $mat->pivot->unit_price ?? $estimatedUnitPrice;
                                        break;
                                    }
                                }
                            }
                        }
                        
                        $materialRequest->items()->create([
                            'material_id' => $material->id,
                            'warehouse_id' => null,
                            'quantity' => $remainingQty,
                            'unit' => $unit,
                            'fulfilled_quantity' => 0,
                            'supplier_id' => $preferredSupplierId,
                        ]);
                        $purchaseRequestItem = [
                            'material_id' => $material->id,
                            'description' => $material->name,
                            'quantity' => $remainingQty,
                            'unit' => $unit,
                            'preferred_supplier_id' => $preferredSupplierId,
                            'supplier_id' => $preferredSupplierId,
                            'estimated_unit_price' => $estimatedUnitPrice,
                            'total_amount' => $remainingQty * $estimatedUnitPrice,
                        ];
                        
                        $purchaseRequestItems[] = $purchaseRequestItem;
                        
                        \Log::info('Added item to purchase request', [
                            'material_id' => $material->id,
                            'material_name' => $material->name,
                            'quantity' => $remainingQty,
                            'supplier_id' => $preferredSupplierId,
                            'purchase_request_items_count' => count($purchaseRequestItems),
                            'purchase_request_item' => $purchaseRequestItem
                        ]);
                    }
                }
            }

            // If any item was short, create a purchase request
            $purchaseRequestCreated = false;
            \Log::info('Purchase request items check', [
                'purchase_request_items_count' => count($purchaseRequestItems),
                'purchase_request_items' => $purchaseRequestItems,
                'material_request_id' => $materialRequest->id
            ]);
            
            if (!empty($purchaseRequestItems)) {
                \Log::info('Creating purchase request for insufficient stock', [
                    'purchase_request_items' => $purchaseRequestItems,
                    'material_request_id' => $materialRequest->id
                ]);
                
                // Always create purchase request if there are items that need purchasing
                // regardless of the checkbox value, since stock is insufficient
                try {
                    // Get the next request number
                    $nextNumber = PurchaseRequest::count() + 1;
                    $requestNumber = 'PR-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
                    
                    $purchaseRequestData = [
                        'request_number' => $requestNumber,
                        'material_request_id' => $materialRequest->id,
                        'requested_by' => auth()->id(),
                        'status' => 'pending_admin_approval',
                        'is_project_related' => false,
                        'notes' => 'Auto-generated from Material Request #' . $materialRequest->id . ' - Stock replenishment needed',
                        'total_amount' => 0,
                    ];
                    
                    \Log::info('Attempting to create purchase request with data', [
                        'purchase_request_data' => $purchaseRequestData,
                        'user_id' => auth()->id(),
                        'material_request_id' => $materialRequest->id
                    ]);
                    
                    // Create the purchase request
                    $purchaseRequest = PurchaseRequest::create($purchaseRequestData);
                    \Log::info('Purchase request created successfully', ['purchase_request_id' => $purchaseRequest->id]);
                    
                    // Create purchase request items
                    $totalAmount = 0;
                    foreach ($purchaseRequestItems as $prItem) {
                        \Log::info('Creating purchase request item', ['item' => $prItem]);
                        $purchaseRequest->items()->create($prItem);
                        $totalAmount += $prItem['total_amount'];
                    }
                    
                    // Update total amount
                    $purchaseRequest->total_amount = $totalAmount;
                    $purchaseRequest->save();
                    $purchaseRequestCreated = true;
                    
                    \Log::info('Purchase request completed successfully', [
                        'purchase_request_id' => $purchaseRequest->id,
                        'total_amount' => $totalAmount,
                        'items_count' => count($purchaseRequestItems)
                    ]);
                    
                    // Notify admins for approval
                    $adminUsers = \App\Models\User::role('admin')->get();
                    foreach ($adminUsers as $admin) {
                        \App\Models\Notification::create([
                            'notifiable_id' => $admin->id,
                            'notifiable_type' => \App\Models\User::class,
                            'type' => 'Purchase Request Approval Needed',
                            'data' => [
                                'title' => 'Purchase Request Approval Required',
                                'message' => 'A new purchase request #' . $purchaseRequest->request_number . ' requires your approval.',
                                'link' => route('purchase-requests.show', $purchaseRequest->id),
                                'purchase_request_id' => $purchaseRequest->id,
                                'request_number' => $purchaseRequest->request_number
                            ],
                        ]);
                    }
                } catch (\Exception $e) {
                    \Log::error('Error creating purchase request: ' . $e->getMessage(), [
                        'material_request_id' => $materialRequest->id,
                        'purchase_request_items' => $purchaseRequestItems,
                        'exception' => $e,
                        'trace' => $e->getTraceAsString()
                    ]);
                    // Continue with material request creation even if purchase request fails
                }
            }

            $materialRequest->save();
            DB::commit();
            
            // Prepare success message
            $successMessage = 'Material request created successfully.';
            if ($purchaseRequestCreated) {
                $successMessage .= ' A purchase request has been automatically created for items that exceeded available stock.';
            }
            
            return redirect()->route('material-requests.show', $materialRequest)->with('success', $successMessage);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating material request: ' . $e->getMessage(), ['exception' => $e]);
            return back()->with('error', 'There was an error creating the material request. Please try again.');
        }
    }

    public function show($id)
    {
        $materialRequest = \App\Models\MaterialRequest::with(['items.material', 'items.supplier', 'user'])->findOrFail($id);
        $purchaseRequest = \App\Models\PurchaseRequest::where('material_request_id', $materialRequest->id)->first();
        return view('admin.material-requests.show', compact('materialRequest', 'purchaseRequest'));
    }

    public function getMaterialSuppliers($materialId)
    {
        $material = Material::with('suppliers')->findOrFail($materialId);
        return response()->json([
            'suppliers' => $material->suppliers->map(function($supplier) {
                return [
                    'id' => $supplier->id,
                    'company_name' => $supplier->company_name
                ];
            })
        ]);
    }

    public function getMaterialStock($materialId)
    {
        $material = Material::findOrFail($materialId);
        $totalStock = Stock::where('material_id', $materialId)->sum('current_stock');
        
        return response()->json([
            'total_stock' => $totalStock,
            'material_name' => $material->name,
            'unit' => $material->unit
        ]);
    }
}
