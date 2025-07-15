<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\PurchaseRequest;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Activity;

class PurchaseOrderController extends Controller
{
    private function logPageView($description, $modelType = null, $modelId = null)
    {
        Activity::create([
            'user_id' => Auth::id(),
            'action' => 'viewed',
            'description' => $description,
            'model_type' => $modelType,
            'model_id' => $modelId
        ]);
    }

    public function index()
    {
        $user = Auth::user();
        if (!($user->hasRole('admin') || $user->hasRole('finance') || $user->hasRole('manager'))) {
            abort(403, 'Unauthorized action.');
        }
        $this->logPageView('Viewed Purchase Order List', PurchaseOrder::class);
        $purchaseOrders = PurchaseOrder::with(['purchaseRequest', 'contract', 'supplier'])
            ->when(request('status'), function($query, $status) {
                return $query->where('status', $status);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.purchase-orders.index', compact('purchaseOrders'));
    }

    public function create(Request $request)
    {
        $this->logPageView('Viewed Create Purchase Order Page', PurchaseOrder::class);
        $selectedPurchaseRequestId = $request->query('purchase_request_id');
        $purchaseRequests = PurchaseRequest::where('status', 'approved')
            ->whereDoesntHave('purchaseOrder')
            ->with(['contract', 'items.supplier', 'materials'])
            ->get();

        // Get all suppliers referenced by PR items
        $referencedSupplierIds = $purchaseRequests->flatMap(function ($pr) {
            return $pr->items->pluck('supplier_id')->filter();
        })->unique()->values();

        $referencedSuppliers = Supplier::whereIn('id', $referencedSupplierIds)->get();
        $allSuppliers = Supplier::all();
        // Merge and deduplicate
        $suppliers = $allSuppliers->merge($referencedSuppliers)->unique('id')->values();

        return view('admin.purchase-orders.create', compact('purchaseRequests', 'suppliers', 'selectedPurchaseRequestId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'purchase_request_id' => 'required|exists:purchase_requests,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'delivery_date' => 'required|date|after:today',
            'payment_terms' => 'required|string',
            'shipping_terms' => 'required|string',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.material_id' => 'required|exists:materials,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0.01',
            'items.*.specifications' => 'nullable|string',
            'items.*.notes' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            $purchaseRequest = PurchaseRequest::findOrFail($request->purchase_request_id);
            
            // Generate PO number (you might want to customize this format)
            $poNumber = 'PO' . date('Y') . str_pad(PurchaseOrder::count() + 1, 4, '0', STR_PAD_LEFT);

            $totalAmount = collect($request->items)->sum(function ($item) {
                return $item['quantity'] * $item['unit_price'];
            });

            $purchaseOrder = PurchaseOrder::create([
                'po_number' => $poNumber,
                'purchase_request_id' => $purchaseRequest->id,
                'contract_id' => $purchaseRequest->contract_id,
                'supplier_id' => $request->supplier_id,
                'total_amount' => $totalAmount,
                'delivery_date' => $request->delivery_date,
                'payment_terms' => $request->payment_terms,
                'shipping_terms' => $request->shipping_terms,
                'notes' => $request->notes,
                'status' => 'draft'
            ]);

            foreach ($request->items as $item) {
                $material = \App\Models\Material::find($item['material_id']);
                $unit = $item['unit'] ?? ($material ? $material->unit : 'pcs');
                $purchaseOrder->items()->create([
                    'material_id' => $item['material_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'unit' => $unit,
                    'total_amount' => $item['quantity'] * $item['unit_price'],
                    'specifications' => $item['specifications'] ?? null,
                    'notes' => $item['notes'] ?? null
                ]);
            }

            DB::commit();
            // Log activity
            Activity::create([
                'user_id' => Auth::id(),
                'action' => 'created',
                'description' => 'Created Purchase Order #' . $purchaseOrder->po_number,
                'model_type' => PurchaseOrder::class,
                'model_id' => $purchaseOrder->id
            ]);
            return redirect()->route('purchase-orders.show', $purchaseOrder)
                ->with('success', 'Purchase Order created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Error creating Purchase Order: ' . $e->getMessage());
        }
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $user = Auth::user();
        if (!($user->hasRole('admin') || $user->hasRole('finance') || $user->hasRole('manager'))) {
            abort(403, 'Unauthorized action.');
        }
        $this->logPageView('Viewed Purchase Order #' . $purchaseOrder->po_number, PurchaseOrder::class, $purchaseOrder->id);
        $purchaseOrder->load(['purchaseRequest', 'contract', 'supplier', 'items.material']);
        return view('admin.purchase-orders.show', compact('purchaseOrder'));
    }

    public function edit(PurchaseOrder $purchaseOrder)
    {
        $this->logPageView('Viewed Edit Purchase Order #' . $purchaseOrder->po_number, PurchaseOrder::class, $purchaseOrder->id);
        if (!in_array($purchaseOrder->status, ['draft', 'pending'])) {
            return back()->with('error', 'This Purchase Order cannot be edited');
        }

        $purchaseOrder->load(['purchaseRequest', 'contract', 'supplier', 'items.material']);
        $materials = \App\Models\Material::orderBy('name')->get();
        $suppliers = \App\Models\Supplier::all();
        $purchaseRequests = \App\Models\PurchaseRequest::where('status', 'approved')->get();
        return view('admin.purchase-orders.edit', compact('purchaseOrder', 'materials', 'suppliers', 'purchaseRequests'));
    }

    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        if (!in_array($purchaseOrder->status, ['draft', 'pending'])) {
            return back()->with('error', 'This Purchase Order cannot be updated');
        }

        $validated = $request->validate([
            'delivery_date' => 'required|date',
            'payment_terms' => 'required|string',
            'shipping_terms' => 'required|string',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.material_id' => 'required|exists:materials,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0.01',
            'items.*.specifications' => 'nullable|string',
            'items.*.notes' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            $totalAmount = collect($request->items)->sum(function ($item) {
                return $item['quantity'] * $item['unit_price'];
            });

            $purchaseOrder->update([
                'total_amount' => $totalAmount,
                'delivery_date' => $request->delivery_date,
                'payment_terms' => $request->payment_terms,
                'shipping_terms' => $request->shipping_terms,
                'notes' => $request->notes
            ]);

            // Delete existing items and create new ones
            $purchaseOrder->items()->delete();
            foreach ($request->items as $item) {
                $purchaseOrder->items()->create([
                    'material_id' => $item['material_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_amount' => $item['quantity'] * $item['unit_price'],
                    'specifications' => $item['specifications'] ?? null,
                    'notes' => $item['notes'] ?? null
                ]);
            }

            DB::commit();
            // Log activity
            Activity::create([
                'user_id' => Auth::id(),
                'action' => 'updated',
                'description' => 'Updated Purchase Order #' . $purchaseOrder->po_number,
                'model_type' => PurchaseOrder::class,
                'model_id' => $purchaseOrder->id
            ]);
            return redirect()->route('purchase-orders.show', $purchaseOrder)
                ->with('success', 'Purchase Order updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Error updating Purchase Order: ' . $e->getMessage());
        }
    }

    public function destroy(PurchaseOrder $purchaseOrder)
    {
        if (!in_array($purchaseOrder->status, ['draft'])) {
            return back()->with('error', 'Only draft Purchase Orders can be deleted');
        }

        try {
            DB::beginTransaction();
            $poNumber = $purchaseOrder->po_number;
            $purchaseOrder->items()->delete();
            $purchaseOrder->delete();
            DB::commit();
            // Log activity
            Activity::create([
                'user_id' => Auth::id(),
                'action' => 'deleted',
                'description' => 'Deleted Purchase Order #' . $poNumber,
                'model_type' => PurchaseOrder::class,
                'model_id' => $purchaseOrder->id
            ]);
            return redirect()->route('purchase-orders.index')
                ->with('success', 'Purchase Order deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error deleting Purchase Order: ' . $e->getMessage());
        }
    }

    public function updateStatus(Request $request, PurchaseOrder $purchaseOrder)
    {
        // Ensure only admin can update status
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,approved,rejected'
        ]);

        $purchaseOrder->update(['status' => $request->status]);

        // If AJAX, return JSON. Otherwise, redirect with flash message.
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Purchase Order status updated successfully'
            ]);
        }

        return redirect()->route('purchase-orders.show', $purchaseOrder)
            ->with('success', 'Purchase Order status updated successfully');
    }

    /**
     * Return purchase order details as JSON for contract prefill
     */
    public function showJson($id)
    {
        $po = \App\Models\PurchaseOrder::with(['supplier', 'items.material'])->findOrFail($id);
        return response()->json($po);
    }

    public function complete(Request $request, PurchaseOrder $purchaseOrder)
    {
        // Ensure only admin can complete
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }

        if ($purchaseOrder->status !== 'approved') {
            return response()->json(['error' => 'Only approved purchase orders can be completed'], 422);
        }

        $validated = $request->validate([
            'delivery_date' => 'required|date',
            'is_on_time' => 'required|boolean',
            'total_units' => 'required|integer|min:0',
            'defective_units' => 'required|integer|min:0',
            'quality_notes' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            // Calculate costs based on SRP and quoted prices
            $estimatedCost = $purchaseOrder->calculateEstimatedCost();
            $actualCost = $purchaseOrder->calculateActualCost();

            $purchaseOrder->update([
                'delivery_date' => $validated['delivery_date'],
                'is_delivered' => true,
                'is_on_time' => $validated['is_on_time'],
                'total_units' => $validated['total_units'],
                'defective_units' => $validated['defective_units'],
                'quality_notes' => $validated['quality_notes'],
                'estimated_cost' => $estimatedCost,
                'actual_cost' => $actualCost,
                'is_completed' => true,
                'status' => 'completed'
            ]);

            // Update supplier metrics
            $supplier = $purchaseOrder->supplier;
            $metrics = $supplier->metrics()->firstOrCreate([]);
            
            // Update delivery metrics
            $metrics->total_deliveries++;
            if ($validated['is_on_time']) {
                $metrics->ontime_deliveries++;
            }

            // Update quality metrics
            if ($validated['total_units'] > 0) {
                $defectRate = ($validated['defective_units'] / $validated['total_units']) * 100;
                $metrics->average_defect_rate = (($metrics->average_defect_rate * ($metrics->total_deliveries - 1)) + $defectRate) / $metrics->total_deliveries;
            }

            // Update cost variance metrics
            if ($estimatedCost > 0) {
                $costVariance = (($actualCost - $estimatedCost) / $estimatedCost) * 100;
                $metrics->average_cost_variance = (($metrics->average_cost_variance * ($metrics->total_deliveries - 1)) + $costVariance) / $metrics->total_deliveries;
            }

            $metrics->save();

            // Update inventory for each material in the PO items
            foreach ($purchaseOrder->items as $item) {
                $inventory = \App\Models\Inventory::firstOrCreate([
                    'material_id' => $item->material_id
                ], [
                    'quantity' => 0,
                    'unit' => $item->material->unit ?? null,
                    'status' => 'active',
                ]);
                $inventory->updateStock($item->quantity, 'add');

                // --- Add to warehouse stock as well ---
                $warehouseId = $purchaseOrder->warehouse_id ?? 1; // Default to Main Warehouse if not set
                $stock = \App\Models\Stock::firstOrCreate([
                    'warehouse_id' => $warehouseId,
                    'material_id' => $item->material_id,
                ], [
                    'current_stock' => 0,
                    'threshold' => 0,
                ]);
                $oldStock = $stock->current_stock;
                $stock->current_stock += $item->quantity;
                $stock->save();
                // Log the movement
                \App\Models\StockMovement::create([
                    'material_id' => $item->material_id,
                    'warehouse_id' => $warehouseId,
                    'type' => 'in',
                    'quantity' => $item->quantity,
                    'previous_stock' => $oldStock,
                    'new_stock' => $stock->current_stock,
                    'notes' => 'PO completed',
                    'reference_number' => 'PO-' . $purchaseOrder->id,
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Purchase order completed successfully',
                'purchaseOrder' => $purchaseOrder->fresh()
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error completing purchase order: ' . $e->getMessage(), [
                'purchase_order_id' => $purchaseOrder->id,
                'error' => $e
            ]);
            return response()->json(['error' => 'Failed to complete purchase order: ' . $e->getMessage()], 500);
        }
    }

    public function validateClientPayment(Request $request, PurchaseOrder $purchaseOrder)
    {
        try {
            $purchaseOrder->validateClientPayment();
            
            return response()->json([
                'success' => true,
                'message' => 'Payment validated by client successfully.',
                'is_fully_validated' => $purchaseOrder->isPaymentValidated()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 403);
        }
    }

    public function validateSupplierPayment(Request $request, PurchaseOrder $purchaseOrder)
    {
        try {
            $purchaseOrder->validateSupplierPayment();
            
            return response()->json([
                'success' => true,
                'message' => 'Payment validated by supplier successfully.',
                'is_fully_validated' => $purchaseOrder->isPaymentValidated()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 403);
        }
    }

    public function confirmDelivery(Request $request, PurchaseOrder $purchaseOrder)
    {
        $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id'
        ]);

        try {
            $purchaseOrder->warehouse_id = $request->warehouse_id;
            $purchaseOrder->confirmDelivery();
            
            return response()->json([
                'success' => true,
                'message' => 'Delivery confirmed and warehouse stock updated successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 403);
        }
    }

    // --- New Delivery Workflow Methods ---
    public function markAsShipped(Request $request, $id)
    {
        $po = PurchaseOrder::findOrFail($id);
        if (auth()->user()->isSupplier() && $po->status === PurchaseOrder::STATUS_CONFIRMED) {
            $po->status = PurchaseOrder::STATUS_SHIPPING;
            $po->shipped_at = now();
            $po->shipping_note = $request->input('shipping_note');
            $po->save();
            // Notify warehouse and admin users
            $warehouseUsers = \App\Models\User::where('role', 'warehouse')->get();
            $adminUsers = \App\Models\User::where('role', 'admin')->get();
            foreach ($warehouseUsers->merge($adminUsers) as $user) {
                $user->notify(new \App\Notifications\PurchaseOrderShipped($po));
            }
            return back()->with('success', 'Purchase Order marked as shipped.');
        }
        abort(403);
    }

    public function markAsDelivered(Request $request, $id)
    {
        $po = PurchaseOrder::findOrFail($id);
        if ((auth()->user()->isWarehouse() || auth()->user()->isAdmin()) && $po->status === PurchaseOrder::STATUS_SHIPPING) {
            $po->status = PurchaseOrder::STATUS_DELIVERED;
            $po->delivered_at = now();
            $po->save();
            // Notify supplier and admin users
            $supplierUser = $po->supplier ? $po->supplier->user : null;
            $adminUsers = \App\Models\User::where('role', 'admin')->get();
            if ($supplierUser) {
                $supplierUser->notify(new \App\Notifications\PurchaseOrderDelivered($po));
            }
            foreach ($adminUsers as $user) {
                $user->notify(new \App\Notifications\PurchaseOrderDelivered($po));
            }
            return back()->with('success', 'Purchase Order marked as delivered.');
        }
        abort(403);
    }

    public function getStatus(PurchaseOrder $purchaseOrder)
    {
        return response()->json([
            'success' => true,
            'data' => [
                'client_payment_validated' => $purchaseOrder->client_payment_validated,
                'client_payment_validated_at' => $purchaseOrder->client_payment_validated_at,
                'supplier_payment_validated' => $purchaseOrder->supplier_payment_validated,
                'supplier_payment_validated_at' => $purchaseOrder->supplier_payment_validated_at,
                'is_payment_validated' => $purchaseOrder->isPaymentValidated(),
                'delivery_confirmed' => $purchaseOrder->delivery_confirmed,
                'delivery_confirmed_at' => $purchaseOrder->delivery_confirmed_at,
                'status' => $purchaseOrder->status,
                'warehouse' => $purchaseOrder->warehouse ? [
                    'id' => $purchaseOrder->warehouse->id,
                    'name' => $purchaseOrder->warehouse->name
                ] : null
            ]
        ]);
    }

    /**
     * Request approval for a purchase order (Procurement user action)
     */
    public function requestApproval(Request $request)
    {
        $request->validate([
            'purchase_order_id' => 'required|exists:purchase_orders,id',
        ]);
        $purchaseOrder = \App\Models\PurchaseOrder::findOrFail($request->purchase_order_id);
        // Create notification for admin
        \App\Models\Notification::create([
            'user_id' => auth()->id(),
            'type' => 'approval_request',
            'notifiable_type' => PurchaseOrder::class,
            'notifiable_id' => $purchaseOrder->id,
            'data' => [
                'title' => 'Approval Requested for Purchase Order',
                'message' => 'A procurement user has requested approval for Purchase Order #' . $purchaseOrder->po_number,
                'link' => route('purchase-orders.show', $purchaseOrder->id),
            ],
            'for_role' => 'admin',
        ]);
        return back()->with('success', 'Approval request sent to admin.');
    }
} 