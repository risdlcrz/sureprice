<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Models\PurchaseRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PurchaseRequestController extends Controller
{
    /**
     * Display the specified purchase request to the assigned supplier.
     *
     * @param  \App\Models\PurchaseRequest  $purchaseRequest
     * @return \Illuminate\View\View
     */
    public function show(PurchaseRequest $purchaseRequest)
    {
        $supplierId = Auth::user()->supplier?->id;

        // Eager load relationships for the view
        $purchaseRequest->load(['contract', 'requestedBy', 'items.material', 'items.supplier', 'items.preferredSupplier']);

        // Check if the currently authenticated supplier is the preferred supplier for any item.
        $isAssignedSupplier = $purchaseRequest->items->contains(function ($item) use ($supplierId) {
            return $item->preferred_supplier_id == $supplierId;
        });

        // If not the assigned supplier, deny access.
        if (!$isAssignedSupplier) {
            abort(403, 'You are not authorized to view this purchase request.');
        }

        return view('supplier.purchase-requests.show', compact('purchaseRequest'));
    }

    public function index()
    {
        $supplierId = auth()->user()->supplier?->id;
        $purchaseRequests = \App\Models\PurchaseRequest::whereHas('items', function($q) use ($supplierId) {
            $q->where('preferred_supplier_id', $supplierId);
        })->latest()->get();

        return view('supplier.purchase-requests.index', compact('purchaseRequests'));
    }
} 