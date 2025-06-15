<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\PurchaseOrder;
use App\Services\InventoryAutomationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeliveryController extends Controller
{
    protected $inventoryService;

    public function __construct(InventoryAutomationService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    public function index()
    {
        $deliveries = Delivery::with(['purchaseOrder.supplier', 'receivedBy'])
            ->latest()
            ->paginate(10);

        return view('admin.deliveries.index', compact('deliveries'));
    }

    public function create()
    {
        $purchaseOrders = PurchaseOrder::where('status', 'approved')
            ->whereDoesntHave('deliveries', function($query) {
                $query->where('status', 'received');
            })
            ->with(['supplier', 'items.material'])
            ->get();

        return view('admin.deliveries.create', compact('purchaseOrders'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'purchase_order_id' => 'required|exists:purchase_orders,id',
            'delivery_date' => 'required|date',
            'received_by' => 'required|exists:users,id',
            'total_units' => 'required|numeric|min:0',
            'defective_units' => 'required|numeric|min:0',
            'wastage_units' => 'required|numeric|min:0',
            'quality_check_notes' => 'nullable|string',
            'actual_cost' => 'required|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.material_id' => 'required|exists:materials,id',
            'items.*.quantity' => 'required|numeric|min:0',
            'items.*.defective_quantity' => 'required|numeric|min:0',
            'items.*.wastage_quantity' => 'required|numeric|min:0'
        ]);

        try {
            DB::beginTransaction();

            $purchaseOrder = PurchaseOrder::findOrFail($validated['purchase_order_id']);
            
            // Generate delivery number
            $deliveryNumber = 'DEL' . date('Y') . str_pad(Delivery::count() + 1, 4, '0', STR_PAD_LEFT);

            // Check if delivery is on time
            $isOnTime = $validated['delivery_date'] <= $purchaseOrder->expected_delivery_date;

            $delivery = Delivery::create([
                'delivery_number' => $deliveryNumber,
                'purchase_order_id' => $validated['purchase_order_id'],
                'delivery_date' => $validated['delivery_date'],
                'received_by' => $validated['received_by'],
                'status' => 'received',
                'total_units' => $validated['total_units'],
                'defective_units' => $validated['defective_units'],
                'wastage_units' => $validated['wastage_units'],
                'quality_check_notes' => $validated['quality_check_notes'],
                'is_on_time' => $isOnTime,
                'actual_cost' => $validated['actual_cost'],
                'estimated_cost' => $purchaseOrder->total_amount
            ]);

            // Create delivery items
            foreach ($validated['items'] as $item) {
                $delivery->items()->create([
                    'material_id' => $item['material_id'],
                    'quantity' => $item['quantity'],
                    'defective_quantity' => $item['defective_quantity'],
                    'wastage_quantity' => $item['wastage_quantity']
                ]);
            }

            // Process delivery using inventory service
            $this->inventoryService->processDelivery($delivery);

            DB::commit();

            return redirect()->route('deliveries.show', $delivery)
                ->with('success', 'Delivery processed successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error processing delivery: ' . $e->getMessage());
        }
    }

    public function show(Delivery $delivery)
    {
        $delivery->load(['purchaseOrder.supplier', 'receivedBy', 'items.material', 'supplierEvaluation']);
        return view('admin.deliveries.show', compact('delivery'));
    }

    public function edit(Delivery $delivery)
    {
        if ($delivery->status !== 'pending') {
            return back()->with('error', 'Cannot edit a delivery that is not pending.');
        }

        $delivery->load(['purchaseOrder.supplier', 'items.material']);
        return view('admin.deliveries.edit', compact('delivery'));
    }

    public function update(Request $request, Delivery $delivery)
    {
        if ($delivery->status !== 'pending') {
            return back()->with('error', 'Cannot update a delivery that is not pending.');
        }

        $validated = $request->validate([
            'delivery_date' => 'required|date',
            'received_by' => 'required|exists:users,id',
            'total_units' => 'required|numeric|min:0',
            'defective_units' => 'required|numeric|min:0',
            'wastage_units' => 'required|numeric|min:0',
            'quality_check_notes' => 'nullable|string',
            'actual_cost' => 'required|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.material_id' => 'required|exists:materials,id',
            'items.*.quantity' => 'required|numeric|min:0',
            'items.*.defective_quantity' => 'required|numeric|min:0',
            'items.*.wastage_quantity' => 'required|numeric|min:0'
        ]);

        try {
            DB::beginTransaction();

            $purchaseOrder = $delivery->purchaseOrder;
            $isOnTime = $validated['delivery_date'] <= $purchaseOrder->expected_delivery_date;

            $delivery->update([
                'delivery_date' => $validated['delivery_date'],
                'received_by' => $validated['received_by'],
                'total_units' => $validated['total_units'],
                'defective_units' => $validated['defective_units'],
                'wastage_units' => $validated['wastage_units'],
                'quality_check_notes' => $validated['quality_check_notes'],
                'is_on_time' => $isOnTime,
                'actual_cost' => $validated['actual_cost']
            ]);

            // Update delivery items
            $delivery->items()->delete();
            foreach ($validated['items'] as $item) {
                $delivery->items()->create([
                    'material_id' => $item['material_id'],
                    'quantity' => $item['quantity'],
                    'defective_quantity' => $item['defective_quantity'],
                    'wastage_quantity' => $item['wastage_quantity']
                ]);
            }

            DB::commit();

            return redirect()->route('deliveries.show', $delivery)
                ->with('success', 'Delivery updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error updating delivery: ' . $e->getMessage());
        }
    }

    public function destroy(Delivery $delivery)
    {
        if ($delivery->status !== 'pending') {
            return back()->with('error', 'Cannot delete a delivery that is not pending.');
        }

        try {
            $delivery->delete();
            return redirect()->route('deliveries.index')
                ->with('success', 'Delivery deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting delivery: ' . $e->getMessage());
        }
    }
} 