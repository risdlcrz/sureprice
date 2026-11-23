<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Contract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Support\SafeErrors;

class ClientPaymentController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $company = $user->company;
        
        if (!$company) {
            return view('client.payments.index', [
                'pagedContracts' => collect([]),
                'error' => 'No company associated with this account. Please contact the administrator.'
            ]);
        }

        // The client's approved contracts (a new client has none yet: the page shows an empty state, not an error)
        $contracts = Contract::visibleTo($user)
            ->where('status', 'approved')
            ->with(['payments', 'client', 'contractor'])
            ->get();

        // Check and generate payments for contracts that have payment schedules but no payment records
        foreach ($contracts as $contract) {
            if ($contract->payment_schedule && $contract->payments()->count() === 0) {
                try {
                    $contract->generatePayments();
                } catch (\Exception $e) {
                    SafeErrors::log($e, "Error generating payments for contract: " . $contract->id);
                }
            }
        }

        // Refresh contracts to get the newly generated payments
        $contracts->load(['payments', 'client', 'contractor']);

        $allPayments = Payment::with(['contract', 'attachment'])
            ->whereHas('contract', fn ($query) => $query->visibleTo($user))
            ->orderBy('due_date')
            ->get();

        // Group payments by contract
        $groupedPayments = $allPayments->groupBy('contract_id');

        // Prepare data for the view
        $contractsWithPayments = collect();
        foreach ($groupedPayments as $contractId => $paymentsForContract) {
            $contract = $paymentsForContract->first()->contract;
            if (!$contract) continue;

            $nextDue = $paymentsForContract->where('status', '!=', 'paid')->sortBy('due_date')->first();
            
            $contractsWithPayments->push((object)[
                'contract' => $contract,
                'payments' => $paymentsForContract->sortBy('due_date'),
                'nextDue' => $nextDue,
                'totalAmount' => $paymentsForContract->sum('amount'),
                'paidAmount' => $paymentsForContract->where('status', 'paid')->sum('amount'),
                'pendingAmount' => $paymentsForContract->where('status', 'pending')->sum('amount'),
                'verificationAmount' => $paymentsForContract->where('status', 'for_verification')->sum('amount'),
            ]);
        }

        // Check if there are any contracts with payments
        if ($contractsWithPayments->isEmpty()) {
            return view('client.payments.index', [
                'pagedContracts' => collect([]),
                'hasContracts' => $contracts->isNotEmpty(),
            ]);
        }

        // Manually paginate the contractsWithPayments collection
        $perPage = 10;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $contractsWithPayments->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $pagedContracts = new LengthAwarePaginator($currentItems, $contractsWithPayments->count(), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath()
        ]);

        return view('client.payments.index', [
            'pagedContracts' => $pagedContracts,
        ]);
    }

    public function dashboard()
    {
        $user = Auth::user();
        $company = $user->company;
        
        if (!$company) {
            return view('client.payments.dashboard', [
                'error' => 'No company associated with this account. Please contact the administrator.'
            ]);
        }

        // The client's approved contracts; make sure their payment schedules have payment records
        $contracts = Contract::visibleTo($user)
            ->where('status', 'approved')
            ->with(['payments', 'client', 'contractor'])
            ->get();

        // Check and generate payments for contracts that have payment schedules but no payment records
        foreach ($contracts as $contract) {
            if ($contract->payment_schedule && $contract->payments()->count() === 0) {
                try {
                    $contract->generatePayments();
                } catch (\Exception $e) {
                    SafeErrors::log($e, "Error generating payments for contract: " . $contract->id);
                }
            }
        }

        // Refresh contracts to get the newly generated payments
        $contracts->load(['payments', 'client', 'contractor']);
        
        $payments = Payment::whereHas('contract', fn ($query) => $query->visibleTo($user))
        ->with(['contract'])
        ->latest()
        ->get();

        // Calculate payment statistics
        $totalPayments = $payments->count();
        $pendingPayments = $payments->where('status', 'pending')->count();
        $paidPayments = $payments->where('status', 'paid')->count();
        $totalAmount = $payments->sum('amount');
        $paidAmount = $payments->where('status', 'paid')->sum('amount');
        $pendingAmount = $payments->where('status', 'pending')->sum('amount');

        return view('client.payments.dashboard', compact(
            'company',
            'payments',
            'totalPayments',
            'pendingPayments',
            'paidPayments',
            'totalAmount',
            'paidAmount',
            'pendingAmount'
        ));
    }

    public function show(Payment $payment)
    {
        $user = Auth::user();
        $company = $user->company;
        
        if (!$company) {
            abort(403, 'No company associated with this account.');
        }

        if (!$payment->contract || !$payment->contract->isVisibleTo($user)) {
            abort(403, 'You are not authorized to view this payment.');
        }

        $payment->load(['contract', 'contract.client', 'contract.contractor']);
        return view('client.payments.show', compact('payment'));
    }
} 