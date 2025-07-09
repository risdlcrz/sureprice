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
        if (!$quotation_id) {
            abort(404, 'Quotation request required.');
        }
        $quotation = \App\Models\QuotationRequest::with(['rooms.scopes'])->findOrFail($quotation_id);
        $materials = Material::orderBy('name')->get();
        $items = [];
        $anyShort = false;

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
            $items[] = [
                'material_id' => $id,
                'name' => $mat->name,
                'unit' => $mat->unit,
                'quantity' => $needed,
                'available' => $actualStock
            ];
        }
        return view('admin.material-requests.create', compact('materials', 'items', 'quotation_id', 'anyShort'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'quotation_request_id' => 'required|exists:quotation_requests,id',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.material_id' => 'required|exists:materials,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
        ]);

        DB::beginTransaction();
        try {
            $materialRequest = MaterialRequest::create([
                'quotation_request_id' => $validated['quotation_request_id'],
                'requested_by' => auth()->id(),
                'status' => 'pending',
                'notes' => $validated['notes'],
            ]);

            $purchaseRequestItems = [];
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
                if ($totalStock >= $remainingQty) {
                    // Fulfill from stock (across warehouses)
                    $warehouses = Warehouse::all();
                    foreach ($warehouses as $warehouse) {
                        $stock = $warehouse->stocks()->where('material_id', $material->id)->first();
                        if ($stock && $stock->current_stock > 0 && $remainingQty > 0) {
                            $deduct = min($stock->current_stock, $remainingQty);
                            $stock->current_stock -= $deduct;
                            $stock->save();
                            $materialRequest->items()->create([
                                'material_id' => $material->id,
                                'warehouse_id' => $warehouse->id,
                                'quantity' => $deduct,
                                'unit' => $unit,
                                'fulfilled_quantity' => $deduct,
                            ]);
                            $remainingQty -= $deduct;
                        }
                    }
                } else {
                    // Fulfill as much as possible from stock, rest goes to purchase request
                    $warehouses = Warehouse::all();
                    foreach ($warehouses as $warehouse) {
                        $stock = $warehouse->stocks()->where('material_id', $material->id)->first();
                        if ($stock && $stock->current_stock > 0 && $remainingQty > 0) {
                            $deduct = min($stock->current_stock, $remainingQty);
                            $stock->current_stock -= $deduct;
                            $stock->save();
                            $materialRequest->items()->create([
                                'material_id' => $material->id,
                                'warehouse_id' => $warehouse->id,
                                'quantity' => $deduct,
                                'unit' => $unit,
                                'fulfilled_quantity' => $deduct,
                            ]);
                            $remainingQty -= $deduct;
                        }
                    }
                    // Add shortfall to purchase request
                    if ($remainingQty > 0) {
                        $materialRequest->items()->create([
                            'material_id' => $material->id,
                            'warehouse_id' => null,
                            'quantity' => $remainingQty,
                            'unit' => $unit,
                            'fulfilled_quantity' => 0,
                        ]);
                        // Fetch supplier and price from material_quotation
                        $preferredSupplierId = null;
                        $estimatedUnitPrice = $material->base_price;
                        $quotationRequest = $materialRequest->quotationRequest;
                        if ($quotationRequest) {
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
                        $purchaseRequestItems[] = [
                            'material_id' => $material->id,
                            'description' => $material->name,
                            'quantity' => $remainingQty,
                            'unit' => $unit,
                            'preferred_supplier_id' => $preferredSupplierId,
                            'estimated_unit_price' => $estimatedUnitPrice,
                            'total_amount' => $remainingQty * $estimatedUnitPrice,
                        ];
                    }
                }
            }

            // If any item was short, create a purchase request
            if (!empty($purchaseRequestItems)) {
                $purchaseRequest = PurchaseRequest::create([
                    'request_number' => 'PR-' . str_pad(PurchaseRequest::count() + 1, 6, '0', STR_PAD_LEFT),
                    'material_request_id' => $materialRequest->id,
                    'requested_by' => auth()->id(),
                    'status' => 'pending',
                    'is_project_related' => false,
                    'notes' => 'Auto-generated from Material Request #' . $materialRequest->id,
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
            DB::commit();
            return redirect()->route('material-requests.show', $materialRequest)->with('success', 'Material request created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating material request: ' . $e->getMessage());
            return back()->with('error', 'There was an error creating the material request. Please try again.');
        }
    }

    public function show($id)
    {
        $materialRequest = \App\Models\MaterialRequest::with(['items.material', 'user'])->findOrFail($id);
        $purchaseRequest = \App\Models\PurchaseRequest::where('material_request_id', $materialRequest->id)->first();
        return view('admin.material-requests.show', compact('materialRequest', 'purchaseRequest'));
    }
}
