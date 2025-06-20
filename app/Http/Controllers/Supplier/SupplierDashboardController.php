<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\SupplierRanking;
use App\Models\OrderEvaluation;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            ->where('delivery_date', '<=', DB::raw('expected_delivery_date'))
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
            ->where('delivery_date', '>', DB::raw('expected_delivery_date'))
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
        // Fetch supplier's materials
        $materials = $supplier ? $supplier->materials->load('category') : collect();
        // Fetch active quotations for this supplier
        $activeQuotations = $supplier ? $supplier->quotations->where('status', 'pending') : collect();
        // Fetch pending invitations (dummy/empty for now, unless you have a model for this)
        $pendingInvitations = collect();
        // Performance metrics (reuse from ranking method if needed)
        $ranking = null;
        $completedOrders = 0;
        $onTimeRate = null;
        $averageRating = null;
        if ($supplier) {
            $ranking = SupplierRanking::where('supplier_id', $supplier->id)->first();
            $completedOrders = PurchaseOrder::where('supplier_id', $supplier->id)->where('status', 'completed')->count();
            $totalDeliveries = PurchaseOrder::where('supplier_id', $supplier->id)->where('status', 'completed')->count();
            $onTimeDeliveries = PurchaseOrder::where('supplier_id', $supplier->id)->where('status', 'completed')->where('delivery_date', '<=', DB::raw('expected_delivery_date'))->count();
            $onTimeRate = $totalDeliveries > 0 ? round(($onTimeDeliveries / $totalDeliveries) * 100) : 0;
            $averageRating = OrderEvaluation::whereHas('order', function($query) use ($supplier) {
                $query->where('supplier_id', $supplier->id);
            })->avg('quality_rating');
        }
        return view('supplier.dashboard', compact(
            'materials',
            'activeQuotations',
            'pendingInvitations',
            'ranking',
            'completedOrders',
            'onTimeRate',
            'averageRating'
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
            'supplier_type' => 'required|string',
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
            'bank_name' => 'nullable|string|max:100',
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
}