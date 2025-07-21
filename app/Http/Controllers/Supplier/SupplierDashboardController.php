<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\SupplierRanking;
use App\Models\OrderEvaluation;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Notification;

class SupplierDashboardController extends Controller
{
    public function ranking()
    {
        $supplier = Auth::user()->supplier;

        // Get supplier ranking
        $ranking = SupplierRanking::with('supplier')
            ->where('supplier_id', $supplier->id)
            ->first();

        // Get completed orders count
        $completedOrders = PurchaseOrder::where('supplier_id', $supplier->id)
            ->where('status', 'completed')
            ->count();

        // Calculate on-time delivery rate
        $totalDeliveries = PurchaseOrder::where('supplier_id', $supplier->id)
            ->where('status', 'completed')
            ->count();
        $onTimeDeliveries = PurchaseOrder::where('supplier_id', $supplier->id)
            ->where('status', 'completed')
            ->where('delivery_date', '<=', DB::raw('delivery_date'))
            ->count();
        $onTimeRate = $totalDeliveries > 0 ? round(($onTimeDeliveries / $totalDeliveries) * 100) : 0;

        // Calculate average delivery time
        $averageDeliveryTime = PurchaseOrder::where('supplier_id', $supplier->id)
            ->where('status', 'completed')
            ->whereNotNull('delivery_date')
            ->avg(DB::raw('DATEDIFF(delivery_date, created_at)'));

        // Get late deliveries count
        $lateDeliveries = PurchaseOrder::where('supplier_id', $supplier->id)
            ->where('status', 'completed')
            ->where('delivery_date', '>', DB::raw('delivery_date'))
            ->count();

        // Get quality metrics
        $qualityRating = OrderEvaluation::whereHas('order', function($query) use ($supplier) {
            $query->where('supplier_id', $supplier->id);
        })->avg('quality_rating');

        $returnCount = PurchaseOrder::where('supplier_id', $supplier->id)
            ->where('status', 'completed')
            ->where('has_returns', true)
            ->count();
        $returnRate = $totalDeliveries > 0 ? round(($returnCount / $totalDeliveries) * 100) : 0;

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

    public function index()
    {
        $supplier = Auth::user()->supplier;
        if (!$supplier) {
            // Show a friendly error or redirect
            return redirect()->route('landing.catalogue')->with('error', 'Your account is not linked to a supplier profile.');
        }
        
        // Fetch supplier's materials with inventory and category
        $materials = $supplier ? $supplier->materials()->with(['inventory', 'category'])->get() : collect();
        
        // Set stock and price for each material
        $materials->each(function ($material) {
            $material->stock = (float) $material->inventory->sum('quantity');
            $material->price = $material->pivot->price ?? 0;
        });
        
        // Fetch active quotations for this supplier
        $activeStatuses = ['pending', 'sent', 'in_progress', 'responded', 'approved', 'draft'];
        $activeQuotations = $supplier ? $supplier->quotations->whereIn('status', $activeStatuses) : collect();
        
        // Fetch pending invitations (dummy/empty for now, unless you have a model for this)
        $pendingInvitations = collect();
        
        // Performance metrics (reuse from ranking method if needed)
        $ranking = null;
        $completedOrders = 0;
        $onTimeRate = null;
        $averageRating = null;
        
        // Sales data calculations
        $totalSales = 0;
        $monthlySales = [];
        $salesTrend = [];
        $topSellingMaterials = [];
        
        if ($supplier) {
            $ranking = SupplierRanking::where('supplier_id', $supplier->id)->first();
            $completedOrders = PurchaseOrder::where('supplier_id', $supplier->id)->where('status', 'completed')->count();
            $totalDeliveries = PurchaseOrder::where('supplier_id', $supplier->id)->where('status', 'completed')->count();
            $onTimeDeliveries = PurchaseOrder::where('supplier_id', $supplier->id)->where('status', 'completed')->where('delivery_date', '<=', DB::raw('delivery_date'))->count();
            $onTimeRate = $totalDeliveries > 0 ? round(($onTimeDeliveries / $totalDeliveries) * 100) : 0;
            $averageRating = OrderEvaluation::whereHas('order', function($query) use ($supplier) {
                $query->where('supplier_id', $supplier->id);
            })->avg('quality_rating');
            
            // Calculate total sales (sum of all completed purchase orders)
            $totalSales = PurchaseOrder::where('supplier_id', $supplier->id)
                ->where('status', 'completed')
                ->sum('total_amount');
            
            // Calculate monthly sales for the last 12 months
            $monthlySales = PurchaseOrder::where('supplier_id', $supplier->id)
                ->where('status', 'completed')
                ->where('created_at', '>=', Carbon::now()->subMonths(12))
                ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(total_amount) as total')
                ->groupBy('year', 'month')
                ->orderBy('year')
                ->orderBy('month')
                ->get()
                ->mapWithKeys(function ($item) {
                    $date = Carbon::createFromDate($item->year, $item->month, 1);
                    return [$date->format('M Y') => (float) $item->total];
                });
            
            // Calculate sales trend (last 6 months vs previous 6 months)
            $currentPeriodSales = PurchaseOrder::where('supplier_id', $supplier->id)
                ->where('status', 'completed')
                ->where('created_at', '>=', Carbon::now()->subMonths(6))
                ->sum('total_amount');
            
            $previousPeriodSales = PurchaseOrder::where('supplier_id', $supplier->id)
                ->where('status', 'completed')
                ->whereBetween('created_at', [
                    Carbon::now()->subMonths(12),
                    Carbon::now()->subMonths(6)
                ])
                ->sum('total_amount');
            
            $salesTrend = [
                'current_period' => $currentPeriodSales,
                'previous_period' => $previousPeriodSales,
                'percentage_change' => $previousPeriodSales > 0 ? 
                    round((($currentPeriodSales - $previousPeriodSales) / $previousPeriodSales) * 100, 2) : 0
            ];
            
            // Get top selling materials
            $topSellingMaterials = DB::table('purchase_order_items')
                ->join('purchase_orders', 'purchase_order_items.purchase_order_id', '=', 'purchase_orders.id')
                ->join('materials', 'purchase_order_items.material_id', '=', 'materials.id')
                ->where('purchase_orders.supplier_id', $supplier->id)
                ->where('purchase_orders.status', 'completed')
                ->selectRaw('materials.name, SUM(purchase_order_items.quantity * purchase_order_items.unit_price) as total_sales, COUNT(*) as order_count')
                ->groupBy('materials.id', 'materials.name')
                ->orderByDesc('total_sales')
                ->limit(5)
                ->get();
        }
        
        return view('supplier.dashboard', compact(
            'materials',
            'activeQuotations',
            'pendingInvitations',
            'ranking',
            'completedOrders',
            'onTimeRate',
            'averageRating',
            'totalSales',
            'monthlySales',
            'salesTrend',
            'topSellingMaterials'
        ));
    }

    public function editProfile()
    {
        $supplier = Auth::user()->company;
        $bankDetails = $supplier->bankDetails;
        $documents = $supplier->documents->keyBy('type');
        return view('supplier.edit-profile', compact('supplier', 'bankDetails', 'documents'));
    }

    public function updateProfile(Request $request)
    {
        $supplier = Auth::user()->company;
        $validated = $request->validate([
            'username' => 'required|string|max:50',
            'company_name' => 'required|string|max:255',
            'supplier_type' => 'required|in:Construction & Engineering,Architecture & Design,Real Estate & Property Development,Manufacturing,Wholesale & Distribution,Retail & E-Commerce,Information Technology & Software,Telecommunications,Healthcare & Medical,Logistics & Transportation,Energy & Utilities,Financial Services,Legal & Compliance,Education & Training,Marketing & Advertising,Hospitality & Tourism,Government & Public Sector,Nonprofit / NGO,Other',
            'other_supplier_type' => 'nullable|string|max:100',
            'designation' => 'required|string',
            'business_reg_no' => 'nullable|string|max:100',
            'contact_person' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'mobile_number' => 'required|string|max:20',
            'telephone_number' => 'nullable|string|max:20',
            'street' => 'required|string|max:255',
            'barangay' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal' => 'nullable|string|max:10',
            'years_operation' => 'nullable|numeric|min:0',
            'primary_products_services' => 'nullable|string',
            'service_areas' => 'nullable|string',
            'business_size' => 'nullable|string',
            'payment_terms' => 'nullable|string',
            'vat_registered' => 'required|in:0,1',
            'use_sureprice' => 'required|in:0,1',
            'bank_name' => 'nullable|in:BDO,BPI,MetroBank,PNB,Security Bank,Union Bank,RCBC,China Bank',
            'bank_account_name' => 'nullable|string|max:100',
            'bank_account_number' => 'nullable|string|max:50',
        ]);
        // Directly update the company record
        $supplier->update($validated);
        // Save or update bank details
        $supplier->bankDetails()->updateOrCreate([], [
            'bank_name' => $request->input('bank_name'),
            'account_name' => $request->input('bank_account_name'),
            'account_number' => $request->input('bank_account_number'),
        ]);
        // (Optional) Trigger admin notification here
        return redirect()->route('supplier.dashboard')->with('success', 'Profile updated successfully.');
    }

    public function notificationCenter()
    {
        $user = Auth::user();
        $notifications = Notification::where('user_id', $user->id)
            ->latest()
            ->take(50)
            ->get();
        return view('supplier.notification-center', compact('notifications'));
    }

    public function markAllNotificationsAsRead()
    {
        $user = auth()->user();
        \App\Models\Notification::where('user_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }
        return back()->with('success', 'All notifications marked as read.');
    }

    public function clearReadNotifications()
    {
        $user = auth()->user();
        \App\Models\Notification::where('user_id', $user->id)
            ->whereNotNull('read_at')
            ->delete();
        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }
        return back()->with('success', 'Read notifications cleared.');
    }
}