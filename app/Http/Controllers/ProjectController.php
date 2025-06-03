<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Inquiry;
use App\Models\Quotation;
use App\Models\Activity;
use App\Models\Transaction;
use App\Models\PurchaseOrder;
use App\Models\PurchaseRequest;
use App\Models\SupplierInvitation;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function dashboard()
    {
        // Get recent contracts with budget information
        $recentContracts = Contract::with('client')
            ->latest()
            ->take(5)
            ->get();

        // Calculate total budget from all contracts
        $totalBudget = Contract::sum('budget_allocation');

        // Calculate total spent from transactions
        $totalSpent = Transaction::sum('amount');

        // Get procurement-related data
        $recentPurchaseOrders = PurchaseOrder::with(['contract', 'supplier'])
            ->latest()
            ->take(5)
            ->get();

        $recentPurchaseRequests = PurchaseRequest::with('contract')
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

        $recentActivities = Activity::latest()
            ->take(5)
            ->get();

        return view('admin.project-dashboard', compact(
            'recentContracts',
            'recentPurchaseOrders',
            'recentPurchaseRequests',
            'recentInquiries',
            'recentQuotations',
            'recentActivities',
            'totalBudget',
            'totalSpent'
        ));
    }
} 