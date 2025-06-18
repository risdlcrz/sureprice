<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SupplierInvitation;
use App\Models\Inquiry;
use App\Models\Quotation;
use App\Models\PurchaseOrder;
use App\Models\PurchaseRequest;
use App\Models\Contract;
use App\Models\Inventory;
use App\Models\Project;
use App\Models\Notification;

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

        return view('procurement.dashboard', compact(
            'recentInvitations',
            'recentInquiries',
            'recentQuotations',
            'recentPurchaseOrders',
            'recentPurchaseRequests'
        ));
    }

    public function projectDashboard()
    {
        $projects = Project::with(['contract', 'client'])
            ->latest()
            ->get();

        return view('procurement.project-dashboard', compact('projects'));
    }

    public function inventoryDashboard()
    {
        $inventories = Inventory::with(['material.category'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $lowStockItems = Inventory::lowStock()->count();
        $expiringItems = Inventory::expiring()->count();
        $totalItems = Inventory::count();

        return view('procurement.inventory-dashboard', compact('inventories', 'lowStockItems', 'expiringItems', 'totalItems'));
    }

    public function projectHistory()
    {
        $projects = Project::with(['contract', 'client'])
            ->where('status', 'completed')
            ->latest()
            ->get();

        return view('procurement.project-history', compact('projects'));
    }

    public function analyticsDashboard()
    {
        // Get analytics data
        $totalProjects = Project::count();
        $activeProjects = Project::where('status', 'active')->count();
        $completedProjects = Project::where('status', 'completed')->count();
        
        $totalPurchaseOrders = PurchaseOrder::count();
        $pendingPurchaseOrders = PurchaseOrder::where('status', 'pending')->count();
        $approvedPurchaseOrders = PurchaseOrder::where('status', 'approved')->count();

        return view('procurement.analytics-dashboard', compact(
            'totalProjects',
            'activeProjects',
            'completedProjects',
            'totalPurchaseOrders',
            'pendingPurchaseOrders',
            'approvedPurchaseOrders'
        ));
    }

    public function notificationHub()
    {
        // You can fetch procurement-specific notifications here
        $notifications = Notification::where('for_role', 'procurement')
                                     ->orWhere('for_user_id', auth()->id())
                                     ->latest()->get();

        return view('procurement.notification-hub', compact('notifications'));
    }

    public function inventoryCreate()
    {
        // Return a view for creating a new inventory item
        return view('procurement.inventory-create');
    }

    public function inventoryLowStock()
    {
        // Return a view or data for low stock items
        return view('procurement.inventory-low-stock');
    }

    public function inventoryExpiring()
    {
        // Return a view or data for expiring items
        return view('procurement.inventory-expiring');
    }
} 