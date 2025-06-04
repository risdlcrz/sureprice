<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\PurchaseRequest;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
    public function index()
    {
        $purchaseOrders = PurchaseOrder::with(['purchaseRequest', 'contract', 'supplier'])
            ->when(request('status'), function($query, $status) {
                return $query->where('status', $status);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.purchase-orders.index', compact('purchaseOrders'));
    }

    public function create()
    {
        $purchaseRequests = PurchaseRequest::where('status', 'approved')
            ->whereDoesntHave('purchaseOrder')
            ->with(['contract', 'items.supplier', 'materials'])
            ->get();

        $suppliers = Supplier::all();

        return view('admin.purchase-orders.create', compact('purchaseRequests', 'suppliers'));
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
                $purchaseOrder->items()->create([
                    'material_id' => $item['material_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['quantity'] * $item['unit_price'],
                    'specifications' => $item['specifications'] ?? null,
                    'notes' => $item['notes'] ?? null
                ]);
            }

            DB::commit();

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
        $purchaseOrder->load(['purchaseRequest', 'contract', 'supplier', 'items.material']);
        return view('admin.purchase-orders.show', compact('purchaseOrder'));
    }

    public function edit(PurchaseOrder $purchaseOrder)
    {
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
                    'total_price' => $item['quantity'] * $item['unit_price'],
                    'specifications' => $item['specifications'] ?? null,
                    'notes' => $item['notes'] ?? null
                ]);
            }

            DB::commit();

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
            $purchaseOrder->items()->delete();
            $purchaseOrder->delete();
            DB::commit();

            return redirect()->route('purchase-orders.index')
                ->with('success', 'Purchase Order deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error deleting Purchase Order: ' . $e->getMessage());
        }
    }

    public function updateStatus(Request $request, PurchaseOrder $purchaseOrder)
    {
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
} 