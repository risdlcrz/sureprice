<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\Auth;

class PurchaseOrderController extends Controller
{
    public function index()
    {
        $supplierId = Auth::user()->supplier?->id;
        $query = PurchaseOrder::with(['payments' => function($q) {
            $q->latest();
        }, 'items.material', 'contract'])
            ->where('supplier_id', $supplierId)
            ->where('total_amount', '>', 0);

        // Add filters for status, sort, and per page
        if (request('status')) {
            $query->where('status', request('status'));
        }
        $sort = request('sort', 'created_at');
        $direction = request('direction', 'desc');
        $query->orderBy($sort, $direction);
        $perPage = request('perPage', 10);
        $purchaseOrders = $query->paginate($perPage);
        return view('supplier.purchase-orders.index', compact('purchaseOrders'));
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['payments' => function($q) {
            $q->latest();
        }, 'items.material']);
        // Only allow access if this supplier owns the PO
        if ($purchaseOrder->supplier_id !== Auth::user()->supplier?->id) {
            abort(403);
        }
        return view('supplier.purchase-orders.show', compact('purchaseOrder'));
    }
} 