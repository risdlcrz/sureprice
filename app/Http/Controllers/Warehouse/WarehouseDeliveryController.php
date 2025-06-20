<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\Delivery;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Stock;
use Illuminate\Support\Facades\Auth;

class WarehouseDeliveryController extends Controller
{
    public function index(Request $request)
    {
        $query = Delivery::with(['items', 'items.material', 'warehouse']);

        // Apply filters
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_range')) {
            $dates = explode(' - ', $request->date_range);
            $query->whereBetween('expected_date', [
                Carbon::parse($dates[0])->startOfDay(),
                Carbon::parse($dates[1])->endOfDay()
            ]);
        }

        $deliveries = $query->latest()->paginate(10);

        return view('warehouse.deliveries.index', compact('deliveries'));
    }

    public function show(Delivery $delivery)
    {
        $delivery->load(['items.material', 'items.material.category', 'warehouse']);
        return view('warehouse.deliveries.show', compact('delivery'));
    }

    public function process(Request $request, Delivery $delivery)
    {
        $user = Auth::user();
        if (!$user || !($user->role === 'warehousing')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'status' => 'required|in:completed,partial,cancelled',
            'notes' => 'nullable|string',
            'received_quantities' => 'required|array'
        ]);

        $warehouseId = $delivery->warehouse_id;

        DB::transaction(function() use ($request, $delivery, $warehouseId) {
            // Update delivery status
            $delivery->status = $request->status;
            $delivery->processed_at = now();
            $delivery->processed_by_id = Auth::id();
            $delivery->notes = $request->notes;
            $delivery->save();

            // Process each item
            foreach ($request->received_quantities as $itemId => $quantity) {
                $item = $delivery->items()->findOrFail($itemId);
                $receivedQuantity = min($quantity, $item->quantity);

                if ($receivedQuantity > 0) {
                    // Get or create stock for this material in this warehouse
                    $stock = Stock::firstOrCreate([
                        'warehouse_id' => $warehouseId,
                        'material_id' => $item->material_id,
                    ], [
                        'current_stock' => 0,
                        'minimum_stock' => 0,
                    ]);
                    $oldStock = $stock->current_stock;
                    // Update stock based on delivery type
                    if ($delivery->type === 'incoming') {
                        $stock->current_stock += $receivedQuantity;
                    } else {
                        $stock->current_stock -= $receivedQuantity;
                    }
                    $stock->save();
                    // Create stock movement for the correct warehouse
                    StockMovement::create([
                        'material_id' => $item->material_id,
                        'type' => ($delivery->type === 'incoming' ? 'in' : 'out'),
                        'quantity' => $receivedQuantity,
                        'previous_stock' => $oldStock,
                        'new_stock' => $stock->current_stock,
                        'reference_number' => $delivery->delivery_number,
                        'notes' => "Processed from delivery #" . $delivery->delivery_number,
                        'warehouse_id' => $warehouseId,
                    ]);
                }
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Delivery processed successfully',
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            // ... other fields ...
            'warehouse_id' => 'required|exists:warehouses,id',
        ]);
        $delivery = Delivery::create([
            // ... other fields ...
            'warehouse_id' => $request->warehouse_id,
        ]);
        // ... rest of logic ...
    }
}
 