<?php

namespace App\Http\Controllers\Procurement;

use App\Http\Controllers\Controller;
use App\Models\Rfq;
use App\Models\Order;
use App\Models\Supplier;
use Illuminate\Http\Request;

class ProcurementDashboardController extends Controller
{
    public function index()
    {
        $data = [
            'activeRfqs' => Rfq::where('status', 'active')->count(),
            'pendingApprovals' => Order::where('status', 'pending_approval')->count(),
            'activeOrders' => Order::whereIn('status', ['approved', 'processing'])->count(),
            'totalSuppliers' => Supplier::count(),
            'recentRfqs' => Rfq::withCount('responses')
                ->latest()
                ->take(5)
                ->get(),
            'recentOrders' => Order::with('supplier')
                ->latest()
                ->take(5)
                ->get(),
        ];

        return view('procurement.dashboard', $data);
    }
} 