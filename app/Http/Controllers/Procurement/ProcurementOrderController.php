<?php

namespace App\Http\Controllers\Procurement;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\RfqResponse;
use App\Models\Material;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProcurementOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('supplier');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', '%' . $search . '%')
                  ->orWhereHas('supplier', function($sq) use ($search) {
                      $sq->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        $orders = $query->latest()->paginate(10);

        return view('procurement.orders.index', compact('orders'));
    }

    public function create()
    {
        $suppliers = Supplier::where('status', 'active')->get();
        $materials = Material::all(); // Or filter based on available materials from suppliers
        return view('procurement.orders.create', compact('suppliers', 'materials'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'due_date' => 'required|date|after_or_equal:today',
            'shipping_address' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'items' => 'required|array',
            'items.*.material_id' => 'required|exists:materials,id',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0.01',
        ]);

        DB::beginTransaction();
        try {
            $order = Order::create([
                'order_number' => 'PO-' . Str::random(8),
                'supplier_id' => $validated['supplier_id'],
                'created_by' => auth()->id(),
                'due_date' => $validated['due_date'],
                'shipping_address' => $validated['shipping_address'],
                'notes' => $validated['notes'],
                'status' => 'pending', // Initial status
                'total_amount' => 0,
            ]);

            $totalAmount = 0;
            foreach ($validated['items'] as $item) {
                $material = Material::find($item['material_id']);
                $subtotal = $item['quantity'] * $item['unit_price'];
                $totalAmount += $subtotal;

                $order->items()->create([
                    'material_id' => $item['material_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $subtotal,
                ]);
            }

            $order->update(['total_amount' => $totalAmount]);

            DB::commit();

            return redirect()->route('procurement.orders.show', $order)->with('success', 'Purchase Order created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Failed to create Purchase Order: ' . $e->getMessage()]);
        }
    }

    public function show(Order $order)
    {
        $order->load(['supplier', 'items.material', 'createdBy']);
        return view('procurement.orders.show', compact('order'));
    }

    public function edit(Order $order)
    {
        if (!in_array($order->status, ['pending', 'pending_approval'])) {
            return redirect()->route('procurement.orders.show', $order)->with('error', 'Only pending or pending approval orders can be edited.');
        }
        $suppliers = Supplier::where('status', 'active')->get();
        $materials = Material::all();
        $order->load(['items.material']);

        return view('procurement.orders.edit', compact('order', 'suppliers', 'materials'));
    }

    public function update(Request $request, Order $order)
    {
        if (!in_array($order->status, ['pending', 'pending_approval'])) {
            return redirect()->route('procurement.orders.show', $order)->with('error', 'Only pending or pending approval orders can be updated.');
        }

        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'due_date' => 'required|date|after_or_equal:today',
            'shipping_address' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'items' => 'required|array',
            'items.*.material_id' => 'required|exists:materials,id',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0.01',
        ]);

        DB::beginTransaction();
        try {
            $order->update([
                'supplier_id' => $validated['supplier_id'],
                'due_date' => $validated['due_date'],
                'shipping_address' => $validated['shipping_address'],
                'notes' => $validated['notes'],
            ]);

            $order->items()->delete(); // Remove existing items

            $totalAmount = 0;
            foreach ($validated['items'] as $item) {
                $material = Material::find($item['material_id']);
                $subtotal = $item['quantity'] * $item['unit_price'];
                $totalAmount += $subtotal;

                $order->items()->create([
                    'material_id' => $item['material_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $subtotal,
                ]);
            }

            $order->update(['total_amount' => $totalAmount]);

            DB::commit();

            return redirect()->route('procurement.orders.show', $order)->with('success', 'Purchase Order updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Failed to update Purchase Order: ' . $e->getMessage()]);
        }
    }

    public function destroy(Order $order)
    {
        if (!in_array($order->status, ['pending', 'pending_approval'])) {
            return redirect()->route('procurement.orders.show', $order)->with('error', 'Only pending or pending approval orders can be deleted.');
        }

        $order->delete();

        return redirect()->route('procurement.orders.index')->with('success', 'Purchase Order deleted successfully.');
    }
} 