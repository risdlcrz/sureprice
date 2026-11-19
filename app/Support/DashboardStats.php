<?php

namespace App\Support;

use App\Models\Contract;
use App\Models\MaterialRequest;
use App\Models\PurchaseOrder;
use App\Models\PurchaseRequest;

/** Data for the "Project & Procurement" dashboard shared by admin/manager and procurement. */
class DashboardStats
{
    public static function projectProcurement(): array
    {
        return [
            'totalBudget' => Contract::sum('total_amount'),
            // money committed to suppliers (purchase orders that weren't cancelled/rejected)
            'totalSpent' => PurchaseOrder::whereNotIn('status', ['cancelled', 'rejected'])->sum('total_amount'),
            'contractCount' => Contract::count(),
            'purchaseOrderCount' => PurchaseOrder::count(),
            'pendingMaterialRequests' => MaterialRequest::where('status', 'pending')->count(),
            'recentContracts' => Contract::latest()->take(5)->get(),
            'recentPurchaseOrders' => PurchaseOrder::latest()->take(5)->get(),
            'recentPurchaseRequests' => PurchaseRequest::latest()->take(5)->get(),
        ];
    }
}
