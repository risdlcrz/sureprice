<?php

namespace App\Http\Controllers\Procurement;

use App\Http\Controllers\Controller;
use App\Models\PurchaseRequest;
use App\Models\Material;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Activity;
use App\Models\Notification;

class PurchaseRequestController extends Controller
{
    public function index()
    {
        $purchaseRequests = PurchaseRequest::with(['contract', 'project', 'requestedBy', 'items.material', 'items.supplier'])
            ->where('requested_by', auth()->id())
            ->latest()
            ->paginate(10);

        return view('procurement.purchase-requests.index', compact('purchaseRequests'));
    }

    public function create(Request $request)
    {
        // Eager load only linked suppliers for each material, including pivot price
        $materials = Material::with(['suppliers' => function($query) {
            $query->orderBy('price');
        }])->get();
        
        // Make sure each supplier has pivot->price for JS
        $materials->each(function($material) {
            $material->suppliers->each(function($supplier) {
                $supplier->price = $supplier->pivot->price ?? null;
            });
        });
        
        $suppliers = collect(); // Empty collection to prevent fallback in Blade
        $validSupplierIds = $materials->flatMap(function($material) {
            return $material->suppliers->pluck('id');
        })->unique();

        $contracts = \App\Models\Contract::with('client')->orderBy('created_at', 'desc')->get();
        $projects = \App\Models\Project::orderBy('created_at', 'desc')->get();
        
        $prefillItems = [];
        if ($request->has('contract_id')) {
            $contract = \App\Models\Contract::find($request->contract_id);
            if ($contract) {
                $prefillItems = $contract->items->map(function($item) {
                    return [
                        'material_id' => $item->material_id,
                        'description' => $item->material->name,
                        'quantity' => $item->quantity,
                        'unit' => $item->material->unit,
                        'estimated_unit_price' => $item->material->base_price,
                        'total_amount' => $item->quantity * $item->material->base_price
                    ];
                })->toArray();
            }
        }

        return view('procurement.purchase-requests.create', compact(
            'materials', 
            'suppliers', 
            'validSupplierIds', 
            'contracts', 
            'projects', 
            'prefillItems'
        ));
    }

    public function store(Request $request)
    {
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
            'contract_id' => 'nullable|exists:contracts,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'purpose' => 'nullable|string',
            'required_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.material_id' => 'required|exists:materials,id',
            'items.*.supplier_id' => 'nullable|exists:suppliers,id',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit' => 'required|string',
            'items.*.estimated_unit_price' => 'required|numeric|min:0',
            'items.*.notes' => 'nullable|string',
            'items.*.preferred_brand' => 'nullable|string',
            'items.*.preferred_supplier_id' => 'nullable|exists:suppliers,id',
        ]);

        DB::beginTransaction();
        try {
            $purchaseRequest = PurchaseRequest::create([
                'request_number' => 'PR-' . str_pad(PurchaseRequest::count() + 1, 6, '0', STR_PAD_LEFT),
                'contract_id' => $validated['contract_id'] ?? null,
                'supplier_id' => $validated['supplier_id'] ?? null,
                'requested_by' => auth()->id(),
                'status' => 'pending_admin_approval',
                'notes' => $validated['notes'] ?? null,
                'is_project_related' => !empty($validated['contract_id']),
                'total_amount' => 0,
            ]);

            $totalAmount = 0;
            foreach ($validated['items'] as $item) {
                $purchaseRequest->items()->create([
                    'material_id' => $item['material_id'],
                    'supplier_id' => $item['supplier_id'] ?? null,
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
            
            // Log activity
            Activity::create([
                'user_id' => auth()->id(),
                'action' => 'created',
                'description' => 'Created Purchase Request #' . $purchaseRequest->request_number,
                'model_type' => PurchaseRequest::class,
                'model_id' => $purchaseRequest->id
            ]);
            
            // Create notification for requester
            Notification::create([
                'notifiable_id' => auth()->id(),
                'notifiable_type' => \App\Models\User::class,
                'type' => 'Purchase Request',
                'data' => ['message' => 'Your purchase request #' . $purchaseRequest->request_number . ' has been created and is pending admin approval.'],
            ]);
            
            // Notify admins for approval
            $adminUsers = \App\Models\User::role('admin')->get();
            foreach ($adminUsers as $admin) {
                Notification::create([
                    'notifiable_id' => $admin->id,
                    'notifiable_type' => \App\Models\User::class,
                    'type' => 'Purchase Request Approval Needed',
                    'data' => [
                        'title' => 'Purchase Request Approval Required',
                        'message' => 'A new purchase request #' . $purchaseRequest->request_number . ' from procurement requires your approval.',
                        'link' => route('purchase-requests.show', $purchaseRequest->id),
                        'purchase_request_id' => $purchaseRequest->id,
                        'request_number' => $purchaseRequest->request_number
                    ],
                ]);
            }
            
            return redirect()->route('procurement.purchase-requests.show', $purchaseRequest)
                ->with('success', 'Purchase request created successfully and is pending admin approval.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating purchase request', ['exception' => $e, 'request' => $request->all()]);
            return back()->with('error', 'Error creating purchase request: ' . $e->getMessage());
        }
    }

    public function show(PurchaseRequest $purchaseRequest)
    {
        // Ensure procurement user can only view their own requests
        if ($purchaseRequest->requested_by !== auth()->id()) {
            abort(403, 'Unauthorized access.');
        }
        
        $purchaseRequest->load(['contract', 'requestedBy', 'items.material', 'items.supplier', 'items.preferredSupplier']);
        return view('procurement.purchase-requests.show', compact('purchaseRequest'));
    }

    public function edit(PurchaseRequest $purchaseRequest)
    {
        // Ensure procurement user can only edit their own pending requests
        if ($purchaseRequest->requested_by !== auth()->id()) {
            abort(403, 'Unauthorized access.');
        }
        
        if ($purchaseRequest->status !== 'pending_admin_approval') {
            return back()->with('error', 'Cannot edit a purchase request that is not pending admin approval.');
        }

        $purchaseRequest->load(['items.material', 'items.supplier']);
        $materials = Material::with(['suppliers' => function($query) {
            $query->orderBy('price');
        }])->get();
        
        $suppliers = Supplier::orderBy('company_name')->get();
        $contracts = \App\Models\Contract::with('client')->orderBy('created_at', 'desc')->get();
        $projects = \App\Models\Project::orderBy('created_at', 'desc')->get();

        return view('procurement.purchase-requests.edit', compact('purchaseRequest', 'materials', 'suppliers', 'contracts', 'projects'));
    }

    public function update(Request $request, PurchaseRequest $purchaseRequest)
    {
        // Ensure procurement user can only update their own pending requests
        if ($purchaseRequest->requested_by !== auth()->id()) {
            abort(403, 'Unauthorized access.');
        }
        
        if ($purchaseRequest->status !== 'pending_admin_approval') {
            return back()->with('error', 'Cannot update a purchase request that is not pending admin approval.');
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
            'contract_id' => 'nullable|exists:contracts,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'purpose' => 'nullable|string',
            'required_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.material_id' => 'required|exists:materials,id',
            'items.*.supplier_id' => 'nullable|exists:suppliers,id',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit' => 'required|string',
            'items.*.estimated_unit_price' => 'required|numeric|min:0',
            'items.*.notes' => 'nullable|string',
            'items.*.preferred_brand' => 'nullable|string',
            'items.*.preferred_supplier_id' => 'nullable|exists:suppliers,id',
        ]);

        DB::beginTransaction();
        try {
            $purchaseRequest->update([
                'contract_id' => $validated['contract_id'] ?? null,
                'supplier_id' => $validated['supplier_id'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'is_project_related' => !empty($validated['contract_id']),
                'purpose' => $validated['purpose'] ?? 'Material procurement',
                'required_date' => $validated['required_date'] ?? now()->addDays(7)
            ]);

            // Delete existing items and recreate
            $purchaseRequest->items()->delete();
            
            $totalAmount = 0;
            foreach ($validated['items'] as $item) {
                $purchaseRequest->items()->create([
                    'material_id' => $item['material_id'],
                    'supplier_id' => $item['supplier_id'] ?? null,
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
            
            // Log activity
            Activity::create([
                'user_id' => auth()->id(),
                'action' => 'updated',
                'description' => 'Updated Purchase Request #' . $purchaseRequest->request_number,
                'model_type' => PurchaseRequest::class,
                'model_id' => $purchaseRequest->id
            ]);
            
            return redirect()->route('procurement.purchase-requests.show', $purchaseRequest)
                ->with('success', 'Purchase request updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating purchase request', ['exception' => $e, 'request' => $request->all()]);
            return back()->with('error', 'Error updating purchase request: ' . $e->getMessage());
        }
    }

    public function destroy(PurchaseRequest $purchaseRequest)
    {
        // Ensure procurement user can only delete their own pending requests
        if ($purchaseRequest->requested_by !== auth()->id()) {
            abort(403, 'Unauthorized access.');
        }
        
        if ($purchaseRequest->status !== 'pending_admin_approval') {
            return back()->with('error', 'Cannot delete a purchase request that is not pending admin approval.');
        }

        try {
            $purchaseRequest->delete();
            
            // Log activity
            Activity::create([
                'user_id' => auth()->id(),
                'action' => 'deleted',
                'description' => 'Deleted Purchase Request #' . $purchaseRequest->request_number,
                'model_type' => PurchaseRequest::class,
                'model_id' => $purchaseRequest->id
            ]);
            
            return redirect()->route('procurement.purchase-requests.index')
                ->with('success', 'Purchase request deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting purchase request: ' . $e->getMessage());
        }
    }

    public function recommendSuppliersForMaterial(Request $request)
    {
        $materialId = $request->input('material_id') ?? $request->query('material_id');
        $contractId = $request->input('contract_id');
        $mode = $request->input('mode', 'overall_best');
        $budget = $request->input('budget');
        $offers = [];

        // Fetch relevant RFQs/Quotations
        if ($contractId) {
            $contract = \App\Models\Contract::find($contractId);
            if ($contract) {
                $rfqs = \App\Models\Quotation::where('contract_id', $contractId)
                    ->with(['suppliers', 'materials', 'responses.items', 'responses.supplier.metrics'])
                    ->get();
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
            }
        } else {
            // Standalone: consider all suppliers who have quoted for this material in any RFQ
            $rfqs = \App\Models\Quotation::whereHas('materials', function($q) use ($materialId) {
                $q->where('materials.id', $materialId);
            })->with(['suppliers', 'materials', 'responses.items', 'responses.supplier.metrics'])->get();
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
        }

        // Recommendation logic (copied from ClientQuotationController)
        if (count($offers) > 0) {
            if ($mode === 'cheapest') {
                usort($offers, fn($a, $b) => $a['unit_price'] <=> $b['unit_price']);
            } elseif ($mode === 'fastest_delivery') {
                usort($offers, fn($a, $b) => ($b['metrics']->on_time_delivery_rate ?? 0) <=> ($a['metrics']->on_time_delivery_rate ?? 0));
            } elseif ($mode === 'least_defects') {
                usort($offers, fn($a, $b) => ($a['metrics']->average_defect_rate ?? 0) <=> ($b['metrics']->average_defect_rate ?? 0));
            } else { // overall_best
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
        }

        // Prepare response (top 5 offers)
        $recommended = array_slice($offers, 0, 5);
        return response()->json([
            'recommended' => $recommended,
        ]);
    }
} 