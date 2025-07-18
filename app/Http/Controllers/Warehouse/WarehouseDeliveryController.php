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
use App\Models\Activity;
use App\Models\DeliveryFeedback;
use App\Models\User;

class WarehouseDeliveryController extends Controller
{
    private function logPageView($description, $modelType = null, $modelId = null)
    {
        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'viewed',
            'description' => $description,
            'model_type' => $modelType,
            'model_id' => $modelId
        ]);
    }

    public function index(Request $request)
    {
        $this->logPageView('Viewed Warehouse Deliveries Index');
        $query = Delivery::with(['warehouse'])
            ->withCount('items')
            ->latest();

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

        $deliveries = $query->paginate(15);

        return view('warehouse.deliveries.index', compact('deliveries'));
    }

    public function show(Delivery $delivery)
    {
        $this->logPageView('Viewed Warehouse Delivery #' . $delivery->id, Delivery::class, $delivery->id);
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

    public function create()
    {
        $this->logPageView('Viewed Create Warehouse Delivery Page');
        // ... existing code ...
    }

    public function edit($id)
    {
        $this->logPageView('Viewed Edit Warehouse Delivery #' . $id, Delivery::class, $id);
        // ... existing code ...
    }

    public function feedbackForm(Delivery $delivery)
    {
        $user = Auth::user();
        $userWarehouseId = property_exists($user, 'warehouse_id') ? $user->warehouse_id : null;
        $hasWarehouseRole = method_exists($user, 'hasRole') ? $user->hasRole('warehouse') : false;
        if ($delivery->status !== 'completed' || !$hasWarehouseRole || $delivery->warehouse_id !== $userWarehouseId) {
            abort(403, 'Feedback only allowed for completed deliveries by the assigned warehouse.');
        }
        $existing = DeliveryFeedback::where('delivery_id', $delivery->id)->where('warehouse_id', $userWarehouseId)->first();
        return view('warehouse.deliveries.feedback', compact('delivery', 'existing'));
    }

    public function submitFeedback(Request $request, Delivery $delivery)
    {
        $user = Auth::user();
        $userWarehouseId = property_exists($user, 'warehouse_id') ? $user->warehouse_id : null;
        $hasWarehouseRole = method_exists($user, 'hasRole') ? $user->hasRole('warehouse') : false;
        if ($delivery->status !== 'completed' || !$hasWarehouseRole || $delivery->warehouse_id !== $userWarehouseId) {
            abort(403, 'Feedback only allowed for completed deliveries by the assigned warehouse.');
        }
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comments' => 'nullable|string',
        ]);
        DeliveryFeedback::updateOrCreate(
            [
                'delivery_id' => $delivery->id,
                'warehouse_id' => $userWarehouseId,
            ],
            [
                'supplier_id' => $delivery->supplier_id ?? null,
                'rating' => $request->rating,
                'comments' => $request->comments,
            ]
        );
        // Update supplier evaluation/ranking
        if ($delivery->supplier_id) {
            $supplierId = $delivery->supplier_id;
            // Aggregate all feedback ratings for this supplier
            $avgRating = \App\Models\DeliveryFeedback::where('supplier_id', $supplierId)->avg('rating');
            $feedbackCount = \App\Models\DeliveryFeedback::where('supplier_id', $supplierId)->count();
            \App\Models\SupplierEvaluation::updateOrCreate(
                [
                    'supplier_id' => $supplierId,
                    'evaluation_date' => now()->startOfMonth(),
                ],
                [
                    'quality_score' => $avgRating,
                    'performance_score' => $avgRating,
                    'final_score' => $avgRating,
                    'engagement_score' => $feedbackCount,
                ]
            );
        }
        return redirect()->route('warehouse.deliveries.show', $delivery)->with('success', 'Feedback submitted!');
    }
}
 