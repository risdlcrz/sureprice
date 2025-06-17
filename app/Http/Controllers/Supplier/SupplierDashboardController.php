<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\SupplierRanking;
use App\Models\OrderEvaluation;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class SupplierDashboardController extends Controller
{
    public function ranking()
    {
        $supplier = auth()->user()->supplier;
        
        // Get supplier ranking
        $ranking = SupplierRanking::where('supplier_id', $supplier->id)
            ->with(['supplier'])
            ->first();

        // Get completed orders count
        $completedOrders = Order::where('supplier_id', $supplier->id)
            ->where('status', 'completed')
            ->count();

        // Calculate on-time delivery rate
        $totalDeliveries = Order::where('supplier_id', $supplier->id)
            ->where('status', 'completed')
            ->count();
        $onTimeDeliveries = Order::where('supplier_id', $supplier->id)
            ->where('status', 'completed')
            ->where('delivery_date', '<=', DB::raw('expected_delivery_date'))
            ->count();
        $onTimeRate = $totalDeliveries > 0 ? round(($onTimeDeliveries / $totalDeliveries) * 100) : 0;

        // Calculate average delivery time
        $averageDeliveryTime = Order::where('supplier_id', $supplier->id)
            ->where('status', 'completed')
            ->whereNotNull('delivery_date')
            ->avg(DB::raw('DATEDIFF(delivery_date, created_at)'));

        // Get late deliveries count
        $lateDeliveries = Order::where('supplier_id', $supplier->id)
            ->where('status', 'completed')
            ->where('delivery_date', '>', DB::raw('expected_delivery_date'))
            ->count();

        // Get quality metrics
        $qualityRating = OrderEvaluation::whereHas('order', function($query) use ($supplier) {
            $query->where('supplier_id', $supplier->id);
        })->avg('quality_rating');

        $returnRate = Order::where('supplier_id', $supplier->id)
            ->where('status', 'completed')
            ->where('has_returns', true)
            ->count();
        $returnRate = $totalDeliveries > 0 ? round(($returnRate / $totalDeliveries) * 100) : 0;

        $qualityComplaints = OrderEvaluation::whereHas('order', function($query) use ($supplier) {
            $query->where('supplier_id', $supplier->id);
        })->where('has_complaints', true)->count();

        // Get recent evaluations
        $recentEvaluations = OrderEvaluation::whereHas('order', function($query) use ($supplier) {
            $query->where('supplier_id', $supplier->id);
        })
        ->with(['order'])
        ->latest()
        ->take(5)
        ->get();

        return view('supplier.ranking', compact(
            'ranking',
            'completedOrders',
            'onTimeRate',
            'averageDeliveryTime',
            'lateDeliveries',
            'qualityRating',
            'returnRate',
            'qualityComplaints',
            'recentEvaluations'
        ));
    }
} 