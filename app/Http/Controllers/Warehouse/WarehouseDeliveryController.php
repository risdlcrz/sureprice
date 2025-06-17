<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\Delivery;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class WarehouseDeliveryController extends Controller
{
    public function index(Request $request)
    {
        $query = Delivery::with(['items', 'items.material']);

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
        $delivery->load(['items.material', 'items.material.category']);
        return view('warehouse.deliveries.show', compact('delivery'));
    }

    public function process(Request $request, Delivery $delivery)
    {
        // Ensure only warehouse role can process deliveries
        if (!auth()->user()->hasRole('warehouse')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'status' => 'required|in:completed,partial,cancelled',
            'notes' => 'nullable|string',
            'received_quantities' => 'required|array'
        ]);

        DB::transaction(function() use ($request, $delivery) {
            // Update delivery status
            $delivery->status = $request->status;
            $delivery->processed_at = now();
            $delivery->processed_by_id = auth()->id();
            $delivery->notes = $request->notes;
            $delivery->save();

            // Process each item
            foreach ($request->received_quantities as $itemId => $quantity) {
                $item = $delivery->items()->findOrFail($itemId);
                $receivedQuantity = min($quantity, $item->quantity);

                if ($receivedQuantity > 0) {
                    // Create stock movement
                    StockMovement::create([
                        'material_id' => $item->material_id,
                        'type' => ($delivery->type === 'incoming' ? 'in' : 'out'),
                        'quantity' => $receivedQuantity,
                        'previous_stock' => $item->material->stock,
                        'new_stock' => $delivery->type === 'incoming' 
                            ? $item->material->stock + $receivedQuantity 
                            : $item->material->stock - $receivedQuantity,
                        'reference_number' => $delivery->delivery_number,
                        'notes' => "Processed from delivery #" . $delivery->delivery_number
                    ]);

                    // Update material stock
                    if ($delivery->type === 'incoming') {
                        $item->material->increment('stock', $receivedQuantity);
                    } else {
                        $item->material->decrement('stock', $receivedQuantity);
                    }
                }
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Delivery processed successfully',
 