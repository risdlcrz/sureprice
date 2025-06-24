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
                $material = Material::with('inventory')->find($itemData['material_id']);
                $requestedQty = $itemData['quantity'];
                $availableQty = $material->inventory->sum('quantity') ?? 0;

                $fulfilledQty = min($requestedQty, $availableQty);
                $lackingQty = $requestedQty - $fulfilledQty;

                // Create Material Request Item for what can be fulfilled
                if ($fulfilledQty > 0) {
                    $materialRequest->items()->create([
                        'material_id' => $material->id,
                        'quantity' => $fulfilledQty,
                        'unit' => $material->unit,
                        'fulfilled_quantity' => $fulfilledQty,
                        // You might need to specify which warehouse it's from if you have multiple
                    ]);

                    // Deduct from inventory
                    $inventory = $material->inventory->first(); // Assuming one inventory record per material for simplicity
                    if ($inventory) {
                        $inventory->quantity -= $fulfilledQty;
                        $inventory->save();
                    }
                }

                // If not enough stock, add to a list to create a Purchase Request
                if ($lackingQty > 0) {
                     $materialRequest->items()->create([
                        'material_id' => $material->id,
                        'quantity' => $lackingQty,
                        'unit' => $material->unit,
                        'fulfilled_quantity' => 0,
                    ]);
                    $purchaseRequestItems[] = [
                        'material_id' => $material->id,
                        'description' => $material->name,
                        'quantity' => $lackingQty,
                        'unit' => $material->unit,
                        'estimated_unit_price' => $material->base_price,
                        'total_amount' => $lackingQty * $material->base_price,
                    ];
                }
            }
            
            // If there are items that need purchasing, create a Purchase Request
            if (!empty($purchaseRequestItems)) {
                $contract = Contract::find($validated['contract_id']);
                $purchaseRequest = PurchaseRequest::create([
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
