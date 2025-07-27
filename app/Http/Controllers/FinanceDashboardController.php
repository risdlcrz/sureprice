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
        // Get pending purchase order payments
        $pendingPOs = \App\Models\PurchaseOrder::where('status', 'pending_payment')->with('supplier')->get();
        
        // Get all contracts with payments for search functionality
        $allContracts = \App\Models\Contract::with(['client', 'contractor', 'payments'])
            ->whereHas('payments')
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Get contract payments that need finance attention
        $contractPayments = \App\Models\Payment::with(['contract', 'contract.client', 'contract.contractor'])
            ->where('contract_id', '!=', null)
            ->whereIn('status', ['pending', 'for_verification'])
            ->orderBy('due_date')
            ->get();
        
        // Group contract payments by contract
        $contractsWithPayments = $contractPayments->groupBy('contract_id')->map(function($payments, $contractId) {
            $contract = $payments->first()->contract;
            $nextDue = $payments->where('status', 'pending')->sortBy('due_date')->first();
            
            return (object)[
                'contract' => $contract,
                'payments' => $payments->sortBy('due_date'),
                'nextDue' => $nextDue,
                'totalAmount' => $payments->sum('amount'),
                'paidAmount' => $payments->where('status', 'paid')->sum('amount'),
                'pendingAmount' => $payments->where('status', 'pending')->sum('amount'),
                'verificationAmount' => $payments->where('status', 'for_verification')->sum('amount'),
            ];
        });
        
        return view('finance.payments', compact('pendingPOs', 'contractsWithPayments', 'allContracts'));
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