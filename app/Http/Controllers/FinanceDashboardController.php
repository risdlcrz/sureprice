<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FinanceDashboardController extends Controller
{
    public function index(Request $request)
    {
        // You can add logic here to fetch finance-related data
        return view('finance.dashboard');
    }

    public function payments(Request $request)
    {
        $pendingPOs = \App\Models\PurchaseOrder::where('status', 'pending_payment')->with('supplier')->get();
        return view('finance.payments', compact('pendingPOs'));
    }

    public function pay(Request $request, \App\Models\PurchaseOrder $purchaseOrder)
    {
        // Only allow if still pending payment
        if ($purchaseOrder->status !== 'pending_payment') {
            return redirect()->back()->with('error', 'This purchase order is not pending payment.');
        }
        $purchaseOrder->status = 'paid';
        $purchaseOrder->paid_at = now();
        $purchaseOrder->save();
        return redirect()->back()->with('success', 'Purchase order marked as paid.');
    }
} 