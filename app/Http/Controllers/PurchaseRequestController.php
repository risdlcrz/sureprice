<?php

namespace App\Http\Controllers;

use App\Models\PurchaseRequest;
use App\Models\Material;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\SupplierSelectionService;
use App\Models\Activity;

class PurchaseRequestController extends Controller
{
    public function index()
    {
        $purchaseRequests = PurchaseRequest::with(['contract', 'project', 'requestedBy', 'items.material', 'items.supplier'])
            ->latest()
            ->paginate(10);

        return view('admin.purchase-requests.index', compact('purchaseRequests'));
    }

    public function create(Request $request)
    {
        // Eager load only linked suppliers for each material
        $materials = Material::with(['suppliers' => function($query) {
            $query->orderBy('price');
        }])->get();
        // No need to pass all suppliers for dropdown fallback
        $suppliers = collect(); // Empty collection to prevent fallback in Blade
        $validSupplierIds = $materials->flatMap(function($material) {
            return $material->suppliers->pluck('id');
        })->unique();

        $contracts = \App\Models\Contract::with('client')->orderBy('created_at', 'desc')->get();
        $projects = \App\Models\Project::orderBy('created_at', 'desc')->get();
        
        $prefillItems = [];
        if ($request->has('contract_id')) {
            $contract = \App\Models\Contract::with('items.material.suppliers')->find($request->contract_id);
            if ($contract && $contract->items->isNotEmpty()) {
                foreach ($contract->items as $item) {
                    $material = $materials->firstWhere('id', $item->material_id);
                    $prefillItems[] = [
                        'material_id' => $item->material_id,
                        'material_name' => $item->material_name,
                        'description' => $item->description ?? $item->material_name,
                        'quantity' => $item->quantity,
                        'unit' => $item->unit,
                        'estimated_unit_price' => $item->amount,
                        'total_amount' => $item->total,
                        'notes' => 'From contract',
                        'preferred_brand' => null,
                        'preferred_supplier_id' => null,
                        'material_obj' => $material,
                        'material_name' => $material->name
                    ];
                }
            }
        } elseif ($request->has('material_id')) {
            $material = $materials->firstWhere('id', $request->material_id);
            if ($material) {
                $prefillItems[] = [
                    'material_id' => $material->id,
                    'description' => $material->description ?? $material->name,
                    'quantity' => 1,
                    'unit' => $material->unit,
                    'estimated_unit_price' => $material->base_price,
                    'total_amount' => $material->base_price,
                    'notes' => '',
                    'preferred_brand' => null,
                    'preferred_supplier_id' => null,
                    'material_obj' => $material,
                    'material_name' => $material->name
                ];
            }
        }
        
        $allSuppliers = $materials->flatMap(function($material) {
            return $material->suppliers->pluck('id');
        })->unique();
        
        $bestSuppliers = [];
        foreach ($materials as $material) {
            $best = null;
            $reason = '';
            // If there is a preferred supplier, use that
            $preferred = $material->suppliers->first(function($s) {
                return isset($s->pivot) && !empty($s->pivot->is_preferred);
            });
            if ($preferred) {
                $best = $preferred;
                $reason = 'Preferred supplier';
            } else {
                // Otherwise, use the supplier with the lowest price
                $lowest = $material->suppliers->sortBy(function($s) {
                    return $s->pivot->price ?? INF;
                })->first();
                if ($lowest) {
                    $best = $lowest;
                    $price = $lowest->pivot->price ?? null;
                    $reason = $price ? ('Best price: ₱' . number_format($price, 2)) : 'Best available supplier';
                }
            }
            if ($best && $allSuppliers->contains($best->id)) {
                $bestSuppliers[$material->id] = [
                    'id' => $best->id,
                    'reason' => $reason
                ];
            }
        }
        // Only pass $materials (with suppliers), $contracts, $projects, $prefillItems, $bestSuppliers
        return view('admin.purchase-requests.create', compact('materials', 'suppliers', 'contracts', 'projects', 'prefillItems', 'bestSuppliers'));
    }

    public function store(Request $request)
    {
        \Log::info('PurchaseRequestController@store called', ['request' => $request->all()]);
        // Normalize empty preferred_supplier_id to null before validation
        if ($request->has('items')) {
            $items = collect($request->input('items'))->map(function ($item) {
                if (isset($item['preferred_supplier_id']) && ($item['preferred_supplier_id'] === '' || $item['preferred_supplier_id'] === 0 || $item['preferred_supplier_id'] === '0')) {
                    $item['preferred_supplier_id'] = null;
                }
                return $item;
            })->toArray();
            $request->merge(['items' => $items]);
        }
        $validated = $request->validate([
            'is_project_related' => 'required|boolean',
            'contract_id' => 'nullable|exists:contracts,id',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.material_id' => 'required|exists:materials,id',
            'items.*.supplier_id' => 'nullable|exists:suppliers,id',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0',
            'items.*.unit' => 'required|string',
            'items.*.estimated_unit_price' => 'required|numeric|min:0',
            'items.*.notes' => 'nullable|string',
            'items.*.preferred_brand' => 'nullable|string',
            'items.*.preferred_supplier_id' => 'nullable|exists:suppliers,id',
        ]);

        // Custom validation: preferred_supplier_id must be a supplier for the selected material
        foreach ($validated['items'] as $idx => $item) {
            if (!empty($item['preferred_supplier_id'])) {
                $material = \App\Models\Material::with('suppliers')->find($item['material_id']);
                
                // Ensure supplier is linked to material in material_supplier table
                if ($material && !$material->suppliers->pluck('id')->map(fn($id) => (string)$id)->contains((string)$item['preferred_supplier_id'])) {
                    $material->suppliers()->attach($item['preferred_supplier_id']);
                    $material->load('suppliers'); // Refresh the relationship
                }

                $supplierIds = $material ? $material->suppliers->pluck('id')->map(fn($id) => (string)$id) : collect();
                if (!$material || !$supplierIds->contains((string)$item['preferred_supplier_id'])) {
                    return back()->withErrors(["items.$idx.preferred_supplier_id" => 'The selected supplier is not valid for the chosen material.'])->withInput();
                }
            }
        }

        // Custom validation: require contract_id if is_project_related
        if ($validated['is_project_related'] && empty($validated['contract_id'])) {
            return back()->withErrors(['contract_id' => 'Contract must be selected for project-related requests.'])->withInput();
        }

        try {
            \Log::info('Validation passed', ['validated' => $validated]);

            DB::beginTransaction();
            $purchaseRequest = new PurchaseRequest([
                'request_number' => 'PR-' . str_pad(PurchaseRequest::count() + 1, 6, '0', STR_PAD_LEFT),
                'contract_id' => $validated['is_project_related'] ? $validated['contract_id'] : null,
                'requested_by' => auth()->id(),
                'status' => 'pending',
                'is_project_related' => $validated['is_project_related'],
                'notes' => $validated['notes']
            ]);
            $purchaseRequest->save();
            \Log::info('PurchaseRequest instance created', ['purchaseRequest' => $purchaseRequest]);
            $totalAmount = 0;
            foreach ($validated['items'] as $item) {
                $purchaseRequest->items()->create([
                    'material_id' => $item['material_id'],
                    'supplier_id' => $item['preferred_supplier_id'] ?? null,
                    'preferred_supplier_id' => $item['preferred_supplier_id'] ?? null,
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit' => $item['unit'],
                    'estimated_unit_price' => $item['estimated_unit_price'],
                    'total_amount' => $item['quantity'] * $item['estimated_unit_price'],
                    'notes' => $item['notes'] ?? null,
                    'preferred_brand' => $item['preferred_brand'] ?? null
                ]);
                $totalAmount += $item['quantity'] * $item['estimated_unit_price'];
            }
            $purchaseRequest->total_amount = $totalAmount;
            $purchaseRequest->save();
            DB::commit();
            \Log::info('PurchaseRequest saved and committed', ['purchaseRequest' => $purchaseRequest]);
            // Log activity
            Activity::create([
                'user_id' => auth()->id(),
                'action' => 'created',
                'description' => 'Created Purchase Request #' . $purchaseRequest->request_number,
                'model_type' => PurchaseRequest::class,
                'model_id' => $purchaseRequest->id
            ]);
            return redirect()->route('purchase-requests.show', $purchaseRequest)
                ->with('success', 'Purchase request created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating purchase request', ['exception' => $e, 'request' => $request->all()]);
            return back()->with('error', 'Error creating purchase request: ' . $e->getMessage());
        }
    }

    public function show(PurchaseRequest $purchaseRequest)
    {
        $purchaseRequest->load(['contract', 'requestedBy', 'items.material', 'items.supplier', 'items.preferredSupplier']);
        return view('admin.purchase-requests.show', compact('purchaseRequest'));
    }

    public function edit(PurchaseRequest $purchaseRequest)
    {
        if ($purchaseRequest->status !== 'pending') {
            return back()->with('error', 'Cannot edit a purchase request that is not pending.');
        }

        $purchaseRequest->load(['items.material', 'items.supplier']);
        $materials = Material::with(['suppliers' => function($query) {
            $query->orderBy('price');
        }])->get();
        
        $suppliers = Supplier::orderBy('company_name')->get();
        $contracts = \App\Models\Contract::with('client')->orderBy('created_at', 'desc')->get();
        $projects = \App\Models\Project::orderBy('created_at', 'desc')->get();
        \Log::info('Projects variable in PurchaseRequestController@edit:', ['projects' => $projects]);

        return view('admin.purchase-requests.edit', compact('purchaseRequest', 'materials', 'suppliers', 'contracts', 'projects'));
    }

    public function update(Request $request, PurchaseRequest $purchaseRequest)
    {
        if ($purchaseRequest->status !== 'pending') {
            return back()->with('error', 'Cannot update a purchase request that is not pending.');
        }

        // Normalize empty preferred_supplier_id to null before validation
        if ($request->has('items')) {
            $items = collect($request->input('items'))->map(function ($item) {
                if (isset($item['preferred_supplier_id']) && ($item['preferred_supplier_id'] === '' || $item['preferred_supplier_id'] === 0 || $item['preferred_supplier_id'] === '0')) {
                    $item['preferred_supplier_id'] = null;
                }
                return $item;
            })->toArray();
            $request->merge(['items' => $items]);
        }
        $validated = $request->validate([
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.material_id' => 'required|exists:materials,id',
            'items.*.supplier_id' => 'nullable|exists:suppliers,id',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0',
            'items.*.unit' => 'required|string',
            'items.*.estimated_unit_price' => 'required|numeric|min:0',
            'items.*.notes' => 'nullable|string',
            'items.*.preferred_brand' => 'nullable|string',
            'items.*.preferred_supplier_id' => 'nullable|exists:suppliers,id'
        ]);

        DB::beginTransaction();
        try {
        $purchaseRequest->update([
                'notes' => $validated['notes']
            ]);

            // Delete existing items
        $purchaseRequest->items()->delete();

            $totalAmount = 0;

            // Create new items
        foreach ($validated['items'] as $item) {
            $purchaseRequest->items()->create([
                'material_id' => $item['material_id'],
                    'supplier_id' => $item['preferred_supplier_id'],
                'preferred_supplier_id' => $item['preferred_supplier_id'],
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit' => $item['unit'],
                'estimated_unit_price' => $item['estimated_unit_price'],
                    'total_amount' => $item['quantity'] * $item['estimated_unit_price'],
                    'notes' => $item['notes'] ?? null,
                    'preferred_brand' => $item['preferred_brand'] ?? null
                ]);

                $totalAmount += $item['quantity'] * $item['estimated_unit_price'];
            }

            $purchaseRequest->update(['total_amount' => $totalAmount]);

            DB::commit();
            // Log activity
            Activity::create([
                'user_id' => auth()->id(),
                'action' => 'updated',
                'description' => 'Updated Purchase Request #' . $purchaseRequest->request_number,
                'model_type' => PurchaseRequest::class,
                'model_id' => $purchaseRequest->id
            ]);
            return redirect()->route('purchase-requests.show', $purchaseRequest)
                ->with('success', 'Purchase request updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error updating purchase request: ' . $e->getMessage());
        }
    }

    public function destroy(PurchaseRequest $purchaseRequest)
    {
        if ($purchaseRequest->status !== 'pending') {
            return back()->with('error', 'Cannot delete a purchase request that is not pending.');
        }

        try {
            $requestNumber = $purchaseRequest->request_number;
            $purchaseRequest->items()->delete();
            $purchaseRequest->delete();
            DB::commit();
            // Log activity
            Activity::create([
                'user_id' => auth()->id(),
                'action' => 'deleted',
                'description' => 'Deleted Purchase Request #' . $requestNumber,
                'model_type' => PurchaseRequest::class,
                'model_id' => $purchaseRequest->id
            ]);
            return redirect()->route('purchase-requests.index')
                ->with('success', 'Purchase request deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting purchase request: ' . $e->getMessage());
        }
    }

    public function approve(PurchaseRequest $purchaseRequest)
    {
        // Ensure only admin can approve/reject
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }

        if ($purchaseRequest->status !== 'pending') {
            return back()->with('error', 'Only pending purchase requests can be approved.');
        }

        try {
            $purchaseRequest->approveByAdmin();
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        $successMessage = 'Purchase request approved by admin.';
        if ($purchaseRequest->isFullyApproved()) {
            $successMessage .= ' Purchase request fully approved. You may now create a Purchase Order.';
        } else {
            $successMessage .= ' Awaiting supplier approval.';
        }

        return redirect()->route('purchase-requests.show', $purchaseRequest)
            ->with('success', $successMessage);
    }

    public function reject(PurchaseRequest $purchaseRequest)
    {
        // Ensure only admin can approve/reject
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }

        if ($purchaseRequest->status !== 'pending') {
            return back()->with('error', 'Only pending purchase requests can be rejected.');
        }

        $purchaseRequest->update(['status' => 'rejected']);

        return redirect()->route('purchase-requests.show', $purchaseRequest)
            ->with('success', 'Purchase request rejected.');
    }

    public function supplierApprove(Request $request, PurchaseRequest $purchaseRequest)
    {
        $supplierId = auth()->user()->supplier?->id;
        $isAssigned = $purchaseRequest->items()->where('preferred_supplier_id', $supplierId)->exists();
        if (!$isAssigned) {
            abort(403, 'You are not authorized to approve this request.');
        }

        try {
            $purchaseRequest->approveBySupplier();
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        $successMessage = 'Purchase request approved by supplier.';
        if ($purchaseRequest->isFullyApproved()) {
            $successMessage .= ' Purchase request fully approved. You may now create a Purchase Order.';
        }

        return redirect()->back()->with('success', $successMessage);
    }

    public function generateFromContract(Request $request)
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'contract_id' => 'required|exists:contracts,id',
                'items' => 'required|array|min:1',
                'items.*.name' => 'required|string',
                'items.*.unit' => 'required|string',
                'items.*.unitCost' => 'required|numeric|min:0',
                'items.*.quantity' => 'required|numeric|min:0',
                'items.*.totalCost' => 'required|numeric|min:0'
            ]);

            \Log::info('Items received in generateFromContract:', ['items' => $validated['items']]);

            // Find the contract
            $contract = \App\Models\Contract::findOrFail($validated['contract_id']);

            // Generate a unique PR number
            $date = now()->format('Ymd');
            $lastPR = PurchaseRequest::where('request_number', 'like', "PR-{$date}-%")
                ->orderBy('request_number', 'desc')
                ->first();
            
            $sequence = '0001';
            if ($lastPR) {
                $lastSequence = intval(substr($lastPR->request_number, -4));
                $sequence = str_pad($lastSequence + 1, 4, '0', STR_PAD_LEFT);
            }
            
            $prNumber = "PR-{$date}-{$sequence}";

            // Start database transaction
            DB::beginTransaction();

            try {
                // Create the purchase request
                $purchaseRequest = PurchaseRequest::create([
                    'request_number' => $prNumber,
                    'contract_id' => $contract->id,
                    'requested_by' => auth()->id(),
                    'status' => 'pending',
                    'is_project_related' => true,
                    'notes' => 'Automatically generated from contract ' . $contract->contract_number,
                    'total_amount' => collect($validated['items'])->sum('totalCost')
                ]);

                // Create purchase request items
                foreach ($validated['items'] as $item) {
                    // Find or create the material based on its name
                    $material = Material::firstOrCreate(
                        ['name' => $item['name']],
                        [
                            'unit' => $item['unit'] ?? 'pcs',
                            'base_price' => $item['unitCost'] ?? 0, // Use unitCost from contract if available
                            'category_id' => 1, // Default category, adjust as needed
                            'code' => 'MAT' . str_pad(rand(1, 99999), 6, '0', STR_PAD_LEFT) // Generate a random code
                        ]
                    );

                    // Determine the estimated unit price: prioritize contract unitCost, then material base_price
                    $estimatedUnitPrice = $item['unitCost'] > 0 ? $item['unitCost'] : ($material->srp_price > 0 ? $material->srp_price : $material->base_price);

                    $purchaseRequest->items()->create([
                        'material_id' => $material->id,
                        'description' => $item['name'],
                        'quantity' => $item['quantity'],
                        'unit' => $item['unit'],
                        'estimated_unit_price' => $estimatedUnitPrice,
                        'total_amount' => $estimatedUnitPrice * $item['quantity'],
                        'notes' => 'Generated from contract'
                    ]);
                }

                // Commit the transaction
                DB::commit();

                return response()->json([
                    'success' => true,
                    'pr_number' => $prNumber,
                    'contract_number' => $contract->contract_number,
                    'pr_id' => $purchaseRequest->id,
                    'message' => 'Purchase request generated successfully'
                ]);

            } catch (\Exception $e) {
                // Rollback the transaction
                DB::rollBack();
                throw $e;
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error: ' . $e->getMessage(),
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Error generating purchase request: ' . $e->getMessage(), [
                'contract_id' => $request->input('contract_id'),
                'items_count' => count($request->input('items', [])),
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error generating purchase request: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getItems(PurchaseRequest $purchaseRequest)
    {
        $items = $purchaseRequest->items()->with(['material', 'supplier'])->get();
        return response()->json($items);
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
                'cost' => $supplier->metrics->average_cost_variance ?? 0,
            ];
        })->toArray();

        $service = new SupplierSelectionService();
        $filteredSuppliers = $service->filterByMaterial($suppliers, $materialId);
        $recommended = $service->recommend($filteredSuppliers, $projectFeatures, 5);
        $optimal = $service->optimize($recommended, $budget);

        return response()->json([
            'html' => view('admin.suppliers.partials.recommendation-tables', [
                'recommended' => $recommended,
                'optimal' => $optimal,
            ])->render()
        ]);
    }

    /**
     * Request approval for a purchase request (Procurement user action)
     */
    public function requestApproval(Request $request)
    {
        $request->validate([
            'purchase_request_id' => 'required|exists:purchase_requests,id',
        ]);
        $purchaseRequest = \App\Models\PurchaseRequest::findOrFail($request->purchase_request_id);
        // Create notification for admin
        \App\Models\Notification::create([
            'user_id' => auth()->id(),
            'type' => 'approval_request',
            'notifiable_type' => PurchaseRequest::class,
            'notifiable_id' => $purchaseRequest->id,
            'data' => [
                'title' => 'Approval Requested for Purchase Request',
                'message' => 'A procurement user has requested approval for Purchase Request #' . $purchaseRequest->request_number,
                'link' => route('purchase-requests.show', $purchaseRequest->id),
            ],
            'for_role' => 'admin',
        ]);
        return back()->with('success', 'Approval request sent to admin.');
    }
} 