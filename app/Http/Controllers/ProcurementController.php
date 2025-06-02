<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SupplierInvitation;
use App\Models\Inquiry;
use App\Models\Quotation;
use App\Models\PurchaseOrder;
use App\Models\PurchaseRequest;

class ProcurementController extends Controller
{
    public function index()
    {
        $recentInvitations = SupplierInvitation::with(['contract', 'materials'])
            ->latest()
            ->take(5)
            ->get();

        $recentInquiries = Inquiry::with('contract')
            ->latest()
            ->take(5)
            ->get();

        $recentQuotations = Quotation::with('contract')
            ->latest()
            ->take(5)
            ->get();

        $recentPurchaseOrders = PurchaseOrder::with(['contract', 'supplier'])
            ->latest()
            ->take(5)
            ->get();

        $recentPurchaseRequests = PurchaseRequest::with('contract')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.procurement-dashboard', compact(
            'recentInvitations',
            'recentInquiries',
            'recentQuotations',
            'recentPurchaseOrders',
            'recentPurchaseRequests'
        ));
    }
} 