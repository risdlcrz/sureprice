<?php

namespace App\Http\Controllers;

use App\Models\PurchaseRequest;
use App\Models\Material;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $materials = Material::with(['suppliers' => function($query) {
            $query->orderBy('price');
        }])->get();
        
        $suppliers = Supplier::orderBy('company_name')->get();
        $contracts = \App\Models\Contract::with('client')->orderBy('created_at', 'desc')->get();
        $projects = \App\Models\Project::orderBy('created_at', 'desc')->get();
        
        $prefillItems = [];
        if ($request->has('contract_id')) {
            $contract = \App\Models\Contract::with('items')->find($request->contract_id);
            if ($contract) {
                foreach ($contract->items as $item) {
                    $prefillItems[] = [
                        'material_id' => $item->material_id,
                        'description' => $item->material_name ?? $item->description,
                        'quantity' => $item->quantity,
                        'unit' => $item->unit,
                        'estimated_unit_price' => $item->amount,
                        'total_amount' => $item->total,
                        'notes' => 'From contract',
                        'preferred_brand' => null,
                        'preferred_supplier_id' => null
                    ];
                }
            }
        }
        
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
            if ($best) {
                $bestSuppliers[$material->id] = [
                    'id' => $best->id,
                    'reason' => $reason
                ];
            }
        }
        return view('admin.purchase-requests.create', compact('materials', 'suppliers', 'contracts', 'projects', 'prefillItems', 'bestSuppliers'));
    }

    public function store(Request $request)
    {
        \Log::info('PurchaseRequestController@store called', ['request' => $request->all()]);
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
            'items.*.preferred_supplier_id' => 'required|exists:suppliers,id',
        ]);

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
            $purchaseRequest->total_amount = $totalAmount;
            $purchaseRequest->save();
            DB::commit();
            \Log::info('PurchaseRequest saved and committed', ['purchaseRequest' => $purchaseRequest]);
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
            'items.*.preferred_supplier_id' => 'required|exists:suppliers,id'
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
        $purchaseRequest->delete();
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

        $purchaseRequest->update(['status' => 'approved']);

        return redirect()->route('purchase-requests.show', $purchaseRequest)
            ->with('success', 'Purchase request approved successfully.');
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
} 