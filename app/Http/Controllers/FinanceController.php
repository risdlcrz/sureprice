<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Contract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class FinanceController extends Controller
{
    public function verifyPayments(Request $request)
    {
        // Get payments pending verification
        $pendingVerifications = Payment::with(['contract', 'contract.client', 'contract.contractor'])
            ->where('status', 'for_verification')
            ->orderBy('updated_at', 'desc')
            ->get();

        // Get statistics
        $verifiedToday = Payment::where('status', 'paid')
            ->whereDate('approved_at', Carbon::today())
            ->count();

        $rejectedToday = Payment::where('status', 'pending')
            ->whereNotNull('rejected_at')
            ->whereDate('rejected_at', Carbon::today())
            ->count();

        $totalPendingAmount = $pendingVerifications->sum('amount');

        // Apply filters if provided
        if ($request->filled('payment_method')) {
            $pendingVerifications = $pendingVerifications->filter(function($payment) use ($request) {
                return $payment->client_payment_method === $request->payment_method;
            });
        }

        if ($request->filled('date_range')) {
            $pendingVerifications = $pendingVerifications->filter(function($payment) use ($request) {
                $date = $payment->updated_at;
                switch ($request->date_range) {
                    case 'today':
                        return $date->isToday();
                    case 'yesterday':
                        return $date->isYesterday();
                    case 'this_week':
                        return $date->isCurrentWeek();
                    case 'last_week':
                        return $date->isLastWeek();
                    case 'this_month':
                        return $date->isCurrentMonth();
                    default:
                        return true;
                }
            });
        }

        if ($request->filled('amount_range')) {
            $pendingVerifications = $pendingVerifications->filter(function($payment) use ($request) {
                $amount = $payment->amount;
                switch ($request->amount_range) {
                    case '0-10000':
                        return $amount >= 0 && $amount <= 10000;
                    case '10000-50000':
                        return $amount > 10000 && $amount <= 50000;
                    case '50000-100000':
                        return $amount > 50000 && $amount <= 100000;
                    case '100000+':
                        return $amount > 100000;
                    default:
                        return true;
                }
            });
        }

        return view('finance.verify-payments', compact(
            'pendingVerifications',
            'verifiedToday',
            'rejectedToday',
            'totalPendingAmount'
        ));
    }

    public function dashboard()
    {
        // Get finance dashboard statistics
        $totalPayments = Payment::count();
        $pendingPayments = Payment::where('status', 'pending')->count();
        $forVerification = Payment::where('status', 'for_verification')->count();
        $paidPayments = Payment::where('status', 'paid')->count();

        $totalAmount = Payment::sum('amount');
        $pendingAmount = Payment::where('status', 'pending')->sum('amount');
        $verificationAmount = Payment::where('status', 'for_verification')->sum('amount');
        $paidAmount = Payment::where('status', 'paid')->sum('amount');

        // Get recent payments
        $recentPayments = Payment::with(['contract', 'contract.client'])
            ->latest()
            ->take(10)
            ->get();

        // Get contracts with payments
        $contractsWithPayments = Contract::with(['client', 'contractor', 'payments'])
            ->whereHas('payments')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('finance.dashboard', compact(
            'totalPayments',
            'pendingPayments',
            'forVerification',
            'paidPayments',
            'totalAmount',
            'pendingAmount',
            'verificationAmount',
            'paidAmount',
            'recentPayments',
            'contractsWithPayments'
        ));
    }

    public function payments()
    {
        // Get pending purchase order payments
        $pendingPOs = \App\Models\PurchaseOrder::where('status', 'pending_payment')->with('supplier')->get();
        
        // Get all contracts with payments for search functionality
        $allContracts = Contract::with(['client', 'contractor', 'payments'])
            ->whereHas('payments')
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Get contract payments that need finance attention
        $contractPayments = Payment::with(['contract', 'contract.client', 'contract.contractor'])
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
        
        return view('finance.payments', compact(
            'pendingPOs',
            'allContracts',
            'contractsWithPayments'
        ));
    }
} 