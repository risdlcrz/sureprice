<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Contract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;

class ClientPaymentController extends Controller
{
    public function index()
    {
        $client = Auth::user()->party;
        
        if (!$client) {
            return view('client.payments.index', [
                'pagedContracts' => collect([]),
                'error' => 'No client profile found. Please contact the administrator.'
            ]);
        }

        // Get all payments for this client's contracts
        $allPayments = Payment::with(['contract', 'attachment'])
            ->whereHas('contract', function($query) use ($client) {
                $query->where('client_id', $client->id);
            })
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
                'message' => 'No payments found for your contracts.'
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
        $client = Auth::user()->party;
        
        if (!$client) {
            return view('client.payments.dashboard', [
                'error' => 'No client profile found. Please contact the administrator.'
            ]);
        }
        
        // Get client's payments
        $payments = Payment::whereHas('contract', function ($query) use ($client) {
            $query->where('client_id', $client->id);
        })
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
            'client',
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
        $client = Auth::user()->party;
        
        // Check if this payment belongs to the client
        if (!$payment->contract || $payment->contract->client_id !== $client->id) {
            abort(403, 'You are not authorized to view this payment.');
        }

        $payment->load(['contract', 'contract.client', 'contract.contractor']);
        return view('client.payments.show', compact('payment'));
    }
} 