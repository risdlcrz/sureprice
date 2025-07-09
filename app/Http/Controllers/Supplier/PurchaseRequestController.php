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
        $purchaseRequest->load(['contract', 'requestedBy', 'items.material', 'items.supplier']);

        // Check if the preferred_supplier_id column exists
        $hasPreferredSupplierColumn = \Schema::hasColumn('purchase_request_items', 'preferred_supplier_id');
        
        if ($hasPreferredSupplierColumn) {
            // Load preferred supplier relationship if column exists
            $purchaseRequest->load('items.preferredSupplier');
            
            // Check if the currently authenticated supplier is the preferred supplier for any item.
            $isAssignedSupplier = $purchaseRequest->items->contains(function ($item) use ($supplierId) {
                return $item->preferred_supplier_id == $supplierId;
            });
        } else {
            // If column doesn't exist, allow access for now
            // You can implement more specific logic here if needed
            $isAssignedSupplier = true;
        }

        // If not the assigned supplier, deny access.
        if (!$isAssignedSupplier) {
            abort(403, 'You are not authorized to view this purchase request.');
        }

        return view('supplier.purchase-requests.show', compact('purchaseRequest'));
    }

    public function index()
    {
        $supplierId = auth()->user()->supplier?->id;
        
        // Check if the preferred_supplier_id column exists in the purchase_request_items table
        $hasPreferredSupplierColumn = \Schema::hasColumn('purchase_request_items', 'preferred_supplier_id');
        
        if ($hasPreferredSupplierColumn) {
            // Use the preferred_supplier_id column if it exists
            $purchaseRequests = \App\Models\PurchaseRequest::whereHas('items', function($q) use ($supplierId) {
                $q->where('preferred_supplier_id', $supplierId);
            })->latest()->get();
        } else {
            // Fallback: Get all purchase requests and filter in PHP
            // This is a temporary solution until the migration is run
            $purchaseRequests = \App\Models\PurchaseRequest::with(['items.material', 'contract', 'requestedBy'])
                ->latest()
                ->get()
                ->filter(function ($purchaseRequest) use ($supplierId) {
                    // For now, show all purchase requests to suppliers
                    // You can implement more specific logic here if needed
                    return true;
                });
        }

        return view('supplier.purchase-requests.index', compact('purchaseRequests'));
    }
} 