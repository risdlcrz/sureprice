<?php

namespace App\Http\Controllers;

use App\Models\MaterialRequest;
use App\Models\Contract;
use App\Models\Material;
use App\Models\Warehouse;
use App\Models\PurchaseRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        $contracts = Contract::with('items.material')->latest()->get();
        $materials = Material::orderBy('name')->get();
        $contract_id = $request->get('contract_id');
        $selectedContract = null;
        $items = [];

        if ($contract_id) {
            $selectedContract = Contract::with('items.material.inventory')->findOrFail($contract_id);
            foreach ($selectedContract->items as $item) {
                $items[] = [
                    'material_id' => $item->material_id,
                    'name' => $item->material->name,
                    'unit' => $item->material->unit,
                    'quantity' => $item->quantity,
                    'available' => $item->material->inventory->sum('quantity') ?? 0
                ];
            }
        }

        return view('admin.material-requests.create', compact('contracts', 'materials', 'selectedContract', 'items'));
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

                $mainWarehouse = Warehouse::where('name', 'Main Warehouse')->first();
                $secondWarehouse = Warehouse::where('name', '2nd Warehouse')->first();

                // Try to fulfill from Main Warehouse first
                $mainStock = $mainWarehouse ? $mainWarehouse->stocks()->where('material_id', $material->id)->first() : null;
                $fulfilledFromMain = 0;
                if ($mainStock && $mainStock->current_stock > 0) {
                    $fromMain = min($remainingQty, $mainStock->current_stock);
                    if ($fromMain > 0) {
                    $materialRequest->items()->create([
                        'material_id' => $material->id,
                            'warehouse_id' => $mainWarehouse->id,
                            'quantity' => $fromMain,
                        'unit' => $material->unit,
                            'fulfilled_quantity' => 0, // No deduction yet
                        ]);
                        $remainingQty -= $fromMain;
                        $fulfilledFromMain = $fromMain;
                    }
                }

                // If not enough, try to fulfill from 2nd Warehouse
                $fulfilledFromSecond = 0;
                if ($remainingQty > 0 && $secondWarehouse) {
                    $secondStock = $secondWarehouse->stocks()->where('material_id', $material->id)->first();
                    if ($secondStock && $secondStock->current_stock > 0) {
                        $fromSecond = min($remainingQty, $secondStock->current_stock);
                        if ($fromSecond > 0) {
                            $materialRequest->items()->create([
                                'material_id' => $material->id,
                                'warehouse_id' => $secondWarehouse->id,
                                'quantity' => $fromSecond,
                                'unit' => $material->unit,
                                'fulfilled_quantity' => 0, // No deduction yet
                            ]);
                            $remainingQty -= $fromSecond;
                            $fulfilledFromSecond = $fromSecond;
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
}
