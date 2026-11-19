<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Inventory;
use App\Models\Material;
use App\Models\Notification;
use App\Models\PurchaseOrder;
use App\Models\PurchaseRequest;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Purchase order lifecycle:
 *   pending_payment -> for_verification -> paid  (payments: PurchaseOrderPaymentController / finance)
 *   paid -> shipping (supplier ships) -> delivered (warehouse receives) -> completed (admin quality check + stock-in)
 * Manually drafted orders may also go draft -> pending -> approved/rejected via updateStatus().
 */
class PurchaseOrderController extends Controller
{
    private const STAFF_ROLES = ['admin', 'manager', 'finance', 'procurement'];

    public function index(Request $request)
    {
        $this->authorizeStaff();
        $this->logPageView('Viewed Purchase Order List', PurchaseOrder::class);

        $purchaseOrders = PurchaseOrder::with(['purchaseRequest', 'contract', 'supplier'])
            ->when($request->query('status'), fn ($q, $status) => $q->where('status', $status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.purchase-orders.index', compact('purchaseOrders'));
    }

    public function create(Request $request)
    {
        $this->authorizeStaff();
        $this->logPageView('Viewed Create Purchase Order Page', PurchaseOrder::class);

        return view('admin.purchase-orders.form', $this->formData() + [
            'selectedPurchaseRequestId' => $request->query('purchase_request_id'),
        ]);
    }

    public function store(Request $request)
    {
        $this->authorizeStaff();
        $data = $request->validate($this->rules() + [
            'purchase_request_id' => 'required|exists:purchase_requests,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'delivery_date' => 'required|date|after:today',
        ]);
        $purchaseRequest = PurchaseRequest::findOrFail($data['purchase_request_id']);

        $purchaseOrder = DB::transaction(function () use ($data, $purchaseRequest) {
            $po = PurchaseOrder::create([
                'po_number' => $this->nextPoNumber(),
                'purchase_request_id' => $purchaseRequest->id,
                'contract_id' => $purchaseRequest->contract_id,
                'supplier_id' => $data['supplier_id'],
                'delivery_date' => $data['delivery_date'],
                'payment_terms' => $data['payment_terms'],
                'shipping_terms' => $data['shipping_terms'],
                'notes' => $data['notes'] ?? null,
                'status' => 'pending_payment',
                'total_amount' => 0,
            ]);
            $this->syncItems($po, $data['items']);

            return $po;
        });

        $this->logActivity('created', $purchaseOrder);

        return redirect()->route('purchase-orders.show', $purchaseOrder)->with('success', 'Purchase Order created successfully.');
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $this->authorizeStaff();
        $this->logPageView('Viewed Purchase Order #' . $purchaseOrder->po_number, PurchaseOrder::class, $purchaseOrder->id);
        $purchaseOrder->load(['purchaseRequest', 'contract', 'supplier', 'items.material', 'payments']);

        return view('admin.purchase-orders.show', compact('purchaseOrder'));
    }

    public function edit(PurchaseOrder $purchaseOrder)
    {
        $this->authorizeStaff();
        if (!$purchaseOrder->isEditable()) {
            return back()->with('error', 'This Purchase Order can no longer be edited.');
        }
        $this->logPageView('Viewed Edit Purchase Order #' . $purchaseOrder->po_number, PurchaseOrder::class, $purchaseOrder->id);
        $purchaseOrder->load(['purchaseRequest', 'contract', 'supplier', 'items.material']);

        return view('admin.purchase-orders.form', $this->formData($purchaseOrder) + compact('purchaseOrder'));
    }

    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        $this->authorizeStaff();
        if (!$purchaseOrder->isEditable()) {
            return back()->with('error', 'This Purchase Order can no longer be updated.');
        }
        $data = $request->validate($this->rules() + ['delivery_date' => 'required|date']);

        DB::transaction(function () use ($purchaseOrder, $data) {
            $purchaseOrder->update([
                'delivery_date' => $data['delivery_date'],
                'payment_terms' => $data['payment_terms'],
                'shipping_terms' => $data['shipping_terms'],
                'notes' => $data['notes'] ?? null,
            ]);
            $purchaseOrder->items()->delete();
            $this->syncItems($purchaseOrder, $data['items']);
        });

        $this->logActivity('updated', $purchaseOrder);

        return redirect()->route('purchase-orders.show', $purchaseOrder)->with('success', 'Purchase Order updated successfully.');
    }

    public function destroy(PurchaseOrder $purchaseOrder)
    {
        $this->authorizeStaff();
        if (!$purchaseOrder->isEditable()) {
            return back()->with('error', 'Only unpaid, unshipped Purchase Orders can be deleted.');
        }

        DB::transaction(function () use ($purchaseOrder) {
            $purchaseOrder->items()->delete();
            $purchaseOrder->delete();
        });
        $this->logActivity('deleted', $purchaseOrder);

        return redirect()->route('purchase-orders.index')->with('success', 'Purchase Order deleted successfully.');
    }

    /** Admin approval of manually drafted orders (route is admin-only). */
    public function updateStatus(Request $request, PurchaseOrder $purchaseOrder)
    {
        $allowed = ['draft' => ['pending'], 'pending' => ['approved', 'rejected']][$purchaseOrder->status] ?? [];
        $data = $request->validate(['status' => ['required', 'in:' . implode(',', $allowed ?: ['-'])]]);

        $purchaseOrder->update(['status' => $data['status']]);
        $this->logActivity($data['status'], $purchaseOrder, 'Set status to ' . $data['status'] . ' for');

        return $request->expectsJson()
            ? response()->json(['success' => true, 'message' => 'Purchase Order status updated successfully.'])
            : redirect()->route('purchase-orders.show', $purchaseOrder)->with('success', 'Purchase Order status updated successfully.');
    }

    /** Purchase order details as JSON (contract prefill). */
    public function showJson($id)
    {
        $this->authorizeStaff();

        return response()->json(PurchaseOrder::with(['supplier', 'items.material'])->findOrFail($id));
    }

    /** Record delivery quality, update supplier metrics, and stock the items in (route is admin-only). */
    public function complete(Request $request, PurchaseOrder $purchaseOrder)
    {
        if (!$purchaseOrder->canComplete()) {
            return response()->json(['error' => 'Only received (or approved) purchase orders can be completed.'], 422);
        }
        $data = $request->validate([
            'delivery_date' => 'required|date',
            'is_on_time' => 'required|boolean',
            'total_units' => 'required|integer|min:0',
            'defective_units' => 'required|integer|min:0|lte:total_units',
            'quality_notes' => 'nullable|string',
            'estimated_cost' => 'nullable|numeric|min:0',
            'actual_cost' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::transaction(function () use ($purchaseOrder, $data) {
                $purchaseOrder->load('items.material', 'supplier');
                $estimated = $data['estimated_cost'] ?? $purchaseOrder->calculateEstimatedCost();
                $actual = $data['actual_cost'] ?? $purchaseOrder->calculateActualCost();

                $purchaseOrder->forceFill([
                    'delivery_date' => $data['delivery_date'],
                    'is_delivered' => true,
                    'is_on_time' => $data['is_on_time'],
                    'total_units' => $data['total_units'],
                    'defective_units' => $data['defective_units'],
                    'quality_notes' => $data['quality_notes'] ?? null,
                    'estimated_cost' => $estimated,
                    'actual_cost' => $actual,
                    'is_completed' => true,
                    'status' => PurchaseOrder::STATUS_COMPLETED,
                ])->save();

                $this->updateSupplierMetrics($purchaseOrder, $data, $estimated, $actual);
                $this->stockIn($purchaseOrder);
            });
        } catch (\Throwable $e) {
            Log::error('Error completing purchase order', ['purchase_order_id' => $purchaseOrder->id, 'error' => $e->getMessage()]);

            return response()->json(['error' => 'Failed to complete purchase order: ' . $e->getMessage()], 500);
        }
        $this->logActivity('completed', $purchaseOrder);

        return response()->json(['message' => 'Purchase order completed successfully.', 'purchaseOrder' => $purchaseOrder->fresh()]);
    }

    /** Supplier ships a paid order. */
    public function markAsShipped(Request $request, PurchaseOrder $purchaseOrder)
    {
        abort_unless(Auth::user()->isSupplierCompany() && $purchaseOrder->supplier?->user_id === Auth::id(), 403);
        $data = $request->validate(['tracking_number' => 'required|string|max:255', 'shipping_note' => 'nullable|string|max:1000']);

        try {
            $purchaseOrder->markAsShippedOut($data['tracking_number'], $data['shipping_note'] ?? null);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        $this->notifyUsers(User::whereIn('role', ['admin', 'manager', 'finance', 'warehousing'])->get(), $purchaseOrder, 'Purchase Order Shipped',
            "Purchase Order #{$purchaseOrder->po_number} has been shipped by the supplier. Tracking #: {$purchaseOrder->tracking_number}");

        return back()->with('success', 'Purchase Order marked as shipped out.');
    }

    /** Warehouse (or admin) confirms receipt of a shipped order. */
    public function markAsReceived(PurchaseOrder $purchaseOrder)
    {
        abort_unless(Auth::user()->hasRole('warehousing') || Auth::user()->isAdmin(), 403);

        try {
            $purchaseOrder->markAsReceived();
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        $recipients = User::where('role', 'admin')->get()->push($purchaseOrder->supplier?->user)->filter();
        $this->notifyUsers($recipients, $purchaseOrder, 'Purchase Order Delivered', "Purchase Order #{$purchaseOrder->po_number} has been received by the warehouse.");

        return back()->with('success', 'Purchase Order marked as received.');
    }

    /** Procurement asks admins to approve an order. */
    public function requestApproval(Request $request)
    {
        $this->authorizeStaff();
        $purchaseOrder = PurchaseOrder::findOrFail($request->validate(['purchase_order_id' => 'required|exists:purchase_orders,id'])['purchase_order_id']);
        if ($purchaseOrder->status === 'draft') {
            $purchaseOrder->update(['status' => 'pending']);
        }

        $this->notifyUsers(User::where('role', 'admin')->orWhere('user_type', 'admin')->get(), $purchaseOrder, 'Approval Requested for Purchase Order',
            Auth::user()->name . " requested approval for Purchase Order #{$purchaseOrder->po_number}.", 'approval_request');

        return back()->with('success', 'Approval request sent to admin.');
    }

    // ---- helpers -------------------------------------------------------------------------

    private function authorizeStaff(): void
    {
        $user = Auth::user();
        abort_unless($user->isAdmin() || collect(self::STAFF_ROLES)->contains(fn ($r) => $user->hasRole($r)), 403);
    }

    private function rules(): array
    {
        return [
            'payment_terms' => 'required|string|max:255',
            'shipping_terms' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.material_id' => 'required|exists:materials,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0.01',
            'items.*.unit' => 'nullable|string|max:50',
            'items.*.specifications' => 'nullable|string',
            'items.*.notes' => 'nullable|string',
        ];
    }

    private function formData(?PurchaseOrder $purchaseOrder = null): array
    {
        $purchaseRequests = $purchaseOrder
            ? PurchaseRequest::whereKey($purchaseOrder->purchase_request_id)->get()
            : PurchaseRequest::where('status', 'approved')->whereDoesntHave('purchaseOrder')->with(['contract', 'items.supplier', 'items.material'])->get();

        return [
            'purchaseRequests' => $purchaseRequests,
            'suppliers' => Supplier::orderBy('company_name')->get(),
            'materials' => Material::orderBy('name')->get(),
        ];
    }

    /** Create the order's items and set its total. */
    private function syncItems(PurchaseOrder $po, array $items): void
    {
        $units = Material::whereIn('id', array_column($items, 'material_id'))->pluck('unit', 'id');
        $total = 0;
        foreach ($items as $item) {
            $line = $item['quantity'] * $item['unit_price'];
            $total += $line;
            $po->items()->create([
                'material_id' => $item['material_id'],
                'quantity' => $item['quantity'],
                'unit' => $item['unit'] ?? $units[$item['material_id']] ?? 'pcs',
                'unit_price' => $item['unit_price'],
                'total_amount' => $line,
                // items table has no specifications column; keep them with the notes
                'notes' => trim(implode("\n", array_filter([$item['specifications'] ?? null, $item['notes'] ?? null]))) ?: null,
            ]);
        }
        $po->update(['total_amount' => $total]);
    }

    private function nextPoNumber(): string
    {
        $prefix = 'PO' . date('Y');
        $last = PurchaseOrder::where('po_number', 'like', "$prefix%")->orderByDesc('po_number')->value('po_number');

        return $prefix . str_pad((int) substr((string) $last, strlen($prefix)) + 1, 4, '0', STR_PAD_LEFT);
    }

    private function updateSupplierMetrics(PurchaseOrder $po, array $data, float $estimated, float $actual): void
    {
        if (!$po->supplier) return;
        $m = $po->supplier->metrics()->firstOrCreate([]);
        $n = ++$m->total_deliveries;
        $m->ontime_deliveries += $data['is_on_time'] ? 1 : 0;
        $m->total_units += $data['total_units'];
        $m->defective_units += $data['defective_units'];
        $m->estimated_cost += $estimated;
        $m->actual_cost += $actual;
        if ($data['total_units'] > 0) {
            $rate = $data['defective_units'] / $data['total_units'] * 100;
            $m->average_defect_rate = (($m->average_defect_rate * ($n - 1)) + $rate) / $n;
        }
        if ($estimated > 0) {
            $variance = ($actual - $estimated) / $estimated * 100;
            $m->average_cost_variance = (($m->average_cost_variance * ($n - 1)) + $variance) / $n;
        }
        $m->save();
    }

    /** Add the delivered items to inventory and the main warehouse's stock, logging each movement. */
    private function stockIn(PurchaseOrder $po): void
    {
        $warehouseId = Warehouse::orderBy('id')->value('id');
        foreach ($po->items as $item) {
            Inventory::firstOrCreate(['material_id' => $item->material_id], [
                'quantity' => 0, 'unit' => $item->unit ?? $item->material?->unit, 'status' => 'active', 'minimum_threshold' => 0,
            ])->updateStock((float) $item->quantity, 'add');

            if (!$warehouseId) continue;
            $stock = Stock::firstOrCreate(['warehouse_id' => $warehouseId, 'material_id' => $item->material_id], ['current_stock' => 0, 'threshold' => 0]);
            $previous = $stock->current_stock;
            $stock->increment('current_stock', $item->quantity);
            StockMovement::create([
                'material_id' => $item->material_id,
                'warehouse_id' => $warehouseId,
                'type' => 'in',
                'quantity' => $item->quantity,
                'previous_stock' => $previous,
                'new_stock' => $stock->current_stock,
                'notes' => 'PO completed',
                'reference_number' => $po->po_number,
            ]);
        }
    }

    private function notifyUsers($users, PurchaseOrder $po, string $title, string $message, ?string $type = null): void
    {
        foreach ($users->unique('id') as $user) {
            Notification::create([
                'user_id' => $user->id,
                'type' => $type ?? $title,
                'data' => ['title' => $title, 'message' => $message, 'purchase_order_id' => $po->id,
                    // suppliers can't open the staff page; send them to their own view of the order
                    'link' => $user->isSupplierCompany() ? route('supplier.purchase-orders.show', $po) : route('purchase-orders.show', $po)],
            ]);
        }
    }

    private function logActivity(string $action, PurchaseOrder $po, ?string $verb = null): void
    {
        Activity::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'description' => ($verb ?? ucfirst($action)) . ' Purchase Order #' . $po->po_number,
            'model_type' => PurchaseOrder::class,
            'model_id' => $po->id,
        ]);
    }
}
