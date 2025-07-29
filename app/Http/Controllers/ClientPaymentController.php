<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Contract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;

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

        // Find the client party record for this user
        $clientParty = \App\Models\Party::where('user_id', $user->id)
            ->where('entity_type', 'client')
            ->first();

        // If no client party found, try to find by email with user_id set (prioritize linked parties)
        if (!$clientParty) {
            $clientParty = \App\Models\Party::where('email', $user->email)
                ->where('user_id', $user->id)
                ->first();
        }

        // If still no client party found, try to find by email with entity_type client
        if (!$clientParty) {
            $clientParty = \App\Models\Party::where('email', $user->email)
                ->where('entity_type', 'client')
                ->first();
        }

        // If still no client party found, try to find by company
        if (!$clientParty && $company) {
            $clientParty = \App\Models\Party::where('company_name', $company->company_name)
                ->where('entity_type', 'client')
                ->first();
        }

        // If still no client party found, try to find any party with the same email
        if (!$clientParty) {
            $clientParty = \App\Models\Party::where('email', $user->email)->first();
        }

        if (!$clientParty) {
            return view('client.payments.index', [
                'pagedContracts' => collect([]),
                'error' => 'No client profile found. Please contact the administrator.'
            ]);
        }

        // Get all contracts for this client
        $contracts = Contract::where('client_id', $clientParty->id)
            ->where('status', 'approved')
            ->with(['payments', 'client', 'contractor'])
            ->get();

        // Check and generate payments for contracts that have payment schedules but no payment records
        foreach ($contracts as $contract) {
            if ($contract->payment_schedule && $contract->payments()->count() === 0) {
                try {
                    $contract->generatePayments();
                    Log::info("Generated payments for contract: " . $contract->id);
                } catch (\Exception $e) {
                    Log::error("Error generating payments for contract: " . $contract->id . " - " . $e->getMessage());
                }
            }
        }

        // Refresh contracts to get the newly generated payments
        $contracts->load(['payments', 'client', 'contractor']);

        // Get all payments for this client's contracts using the party relationship
        $allPayments = Payment::with(['contract', 'attachment'])
            ->whereHas('contract', function($query) use ($clientParty) {
                $query->where('client_id', $clientParty->id);
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
        $user = Auth::user();
        $company = $user->company;
        
        if (!$company) {
            return view('client.payments.dashboard', [
                'error' => 'No company associated with this account. Please contact the administrator.'
            ]);
        }

        // Find the client party record for this user
        $clientParty = \App\Models\Party::where('user_id', $user->id)
            ->where('entity_type', 'client')
            ->first();

        // If no client party found, try to find by email with user_id set (prioritize linked parties)
        if (!$clientParty) {
            $clientParty = \App\Models\Party::where('email', $user->email)
                ->where('user_id', $user->id)
                ->first();
        }

        // If still no client party found, try to find by email with entity_type client
        if (!$clientParty) {
            $clientParty = \App\Models\Party::where('email', $user->email)
                ->where('entity_type', 'client')
                ->first();
        }

        // If still no client party found, try to find by company
        if (!$clientParty && $company) {
            $clientParty = \App\Models\Party::where('company_name', $company->company_name)
                ->where('entity_type', 'client')
                ->first();
        }

        // If still no client party found, try to find any party with the same email
        if (!$clientParty) {
            $clientParty = \App\Models\Party::where('email', $user->email)->first();
        }

        if (!$clientParty) {
            return view('client.payments.dashboard', [
                'error' => 'No client profile found. Please contact the administrator.'
            ]);
        }

        // Get all contracts for this client and ensure payments are generated
        $contracts = Contract::where('client_id', $clientParty->id)
            ->where('status', 'approved')
            ->with(['payments', 'client', 'contractor'])
            ->get();

        // Check and generate payments for contracts that have payment schedules but no payment records
        foreach ($contracts as $contract) {
            if ($contract->payment_schedule && $contract->payments()->count() === 0) {
                try {
                    $contract->generatePayments();
                    Log::info("Generated payments for contract: " . $contract->id);
                } catch (\Exception $e) {
                    Log::error("Error generating payments for contract: " . $contract->id . " - " . $e->getMessage());
                }
            }
        }

        // Refresh contracts to get the newly generated payments
        $contracts->load(['payments', 'client', 'contractor']);
        
        // Get client's payments using the party relationship
        $payments = Payment::whereHas('contract', function ($query) use ($clientParty) {
            $query->where('client_id', $clientParty->id);
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

        // Find the client party record for this user
        $clientParty = \App\Models\Party::where('user_id', $user->id)
            ->where('entity_type', 'client')
            ->first();

        // If no client party found, try to find by email
        if (!$clientParty) {
            $clientParty = \App\Models\Party::where('email', $user->email)
                ->where('entity_type', 'client')
                ->first();
        }

        // If still no client party found, try to find any party with the same email
        if (!$clientParty) {
            $clientParty = \App\Models\Party::where('email', $user->email)->first();
        }

        if (!$clientParty) {
            abort(403, 'No client profile found.');
        }
        
        // Check if this payment belongs to the client using the party relationship
        if (!$payment->contract || $payment->contract->client_id !== $clientParty->id) {
            abort(403, 'You are not authorized to view this payment.');
        }

        $payment->load(['contract', 'contract.client', 'contract.contractor']);
        return view('client.payments.show', compact('payment'));
    }
} 