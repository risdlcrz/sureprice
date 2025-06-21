<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Contract;
use App\Models\PurchaseOrder;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;

class PaymentController extends Controller
{
    public function index()
    {
        // Get all payments, eager load contract relationship
        $allPayments = Payment::with(['contract', 'attachment'])->orderBy('due_date')->get();

        // Group all payments by contract for accurate 'next due' calculation
        $groupedAllPayments = $allPayments->groupBy('contract_id');

        // Prepare data for the view, calculating next due payment for each contract
        $contractsWithPayments = collect();
        foreach ($groupedAllPayments as $contractId => $paymentsForContract) {
            $contract = $paymentsForContract->first()->contract; // Get the contract model
            if (!$contract) continue;

            $nextDue = $paymentsForContract->where('status', '!=', 'paid')->sortBy('due_date')->first();
            
            // Re-evaluate contract status based on its payments
            if ($paymentsForContract->every('status', '==', 'paid')) {
                $contract->status = 'completed';
            } elseif ($paymentsForContract->contains('status', 'for_verification')) {
                $contract->status = 'for_verification';
            } else {
                $contract->status = 'ongoing'; // Or whatever default status is appropriate
            }
            $contract->save();

            $contractsWithPayments->push((object)[
                'contract' => $contract,
                'payments' => $paymentsForContract->sortBy('due_date'), // Ensure payments are sorted for display
                'nextDue' => $nextDue,
            ]);
        }

        // Manually paginate the contractsWithPayments collection
        $perPage = 15;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $contractsWithPayments->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $pagedContracts = new LengthAwarePaginator($currentItems, $contractsWithPayments->count(), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath()
        ]);

        return view('payments.index', [
            'pagedContracts' => $pagedContracts, // Pass the paginated contracts with their payments and nextDue
        ]);
    }

    public function show(Payment $payment)
    {
        $payment->load(['contract', 'purchaseOrder', 'creator', 'approver']);
        return view('payments.show', compact('payment'));
    }

    public function approve(Payment $payment)
    {
        try {
            DB::beginTransaction();

            $payment->approve(Auth::user());

            // If this is a contract payment, check if all payments are approved
            if ($payment->contract_id) {
                $contract = $payment->contract;
                $allPaymentsApproved = $contract->payments()
                    ->where('status', '!=', 'approved')
                    ->where('status', '!=', 'paid')
                    ->count() === 0;

                if ($allPaymentsApproved) {
                    $contract->update(['status' => 'approved']);
                }
            }

            // If this is a purchase order payment, check if all payments are approved
            if ($payment->purchase_order_id) {
                $purchaseOrder = $payment->purchaseOrder;
                $allPaymentsApproved = $purchaseOrder->payments()
                    ->where('status', '!=', 'approved')
                    ->where('status', '!=', 'paid')
                    ->count() === 0;

                if ($allPaymentsApproved) {
                    $purchaseOrder->update(['status' => 'approved']);
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 'Payment approved successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to approve payment.');
        }
    }

    public function markAsPaid(Request $request, Payment $payment)
    {
        try {
            DB::beginTransaction();

            // Load the contract relationship
            $payment->load('contract');

            // Check if payment is already paid
            if ($payment->status === 'paid') {
                return redirect()->back()->with('error', 'Payment is already marked as paid.');
            }

            // Generate reference number if not provided
            $referenceNumber = $payment->reference_number ?? 'REF-' . date('Ymd') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);

            // Log the payment details before update
            Log::info('Attempting to mark payment as paid', [
                'payment_id' => $payment->id,
                'payment_number' => $payment->payment_number,
                'current_status' => $payment->status,
                'reference_number' => $referenceNumber,
                'user_id' => Auth::id()
            ]);

            // Update payment status
            $payment->status = 'paid';
            $payment->paid_date = now();
            $payment->reference_number = $referenceNumber;
            $payment->marked_paid_by = Auth::id();

            // Log before saving payment
            Log::info('About to save payment', [
                'payment_data' => $payment->getDirty()
            ]);

            $payment->save();

            // Log after saving payment
            Log::info('Payment saved successfully', [
                'payment_id' => $payment->id,
                'new_status' => $payment->status
            ]);

            try {
                // Create transaction record
                $transaction = Transaction::create([
                    'payment_id' => $payment->id,
                    'contract_id' => $payment->contract_id,
                    'date' => now(),
                    'amount' => $payment->amount,
                    'type' => 'payment',
                    'reference_number' => $referenceNumber,
                    'description' => 'Payment for Contract #' . ($payment->contract ? $payment->contract->contract_number : 'N/A') . ' - ' . 
                                   ($payment->description ?? 'Payment #' . $payment->payment_number),
                    'status' => 'completed',
                    'created_by' => Auth::id()
                ]);

                // Log transaction creation
                Log::info('Transaction created successfully', [
                    'transaction_id' => $transaction->id,
                    'payment_id' => $payment->id,
                    'transaction_data' => $transaction->toArray()
                ]);

                // Check if all contract payments are paid
                if ($payment->contract) {
                    $unpaidPayments = $payment->contract->payments()
                        ->where('status', '!=', 'paid')
                        ->count();

                    if ($unpaidPayments === 0) {
                        $payment->contract->update(['status' => 'completed']);
                        Log::info('Contract marked as completed', [
                            'contract_id' => $payment->contract_id
                        ]);
                    }
                }

                DB::commit();

                return redirect()->route('payments.index')
                               ->with('success', 'Payment #' . $payment->payment_number . ' has been marked as paid successfully.');
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Failed to create transaction', [
                    'payment_id' => $payment->id,
                    'error_message' => $e->getMessage(),
                    'error_trace' => $e->getTraceAsString()
                ]);

                return redirect()->back()
                               ->with('error', 'Failed to create transaction: ' . $e->getMessage());
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to mark payment as paid', [
                'payment_id' => $payment->id,
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                           ->with('error', 'Failed to mark payment as paid: ' . $e->getMessage());
        }
    }

    public function reject(Payment $payment)
    {
        try {
            DB::beginTransaction();

            $payment->reject();

            DB::commit();
            return redirect()->back()->with('success', 'Payment rejected successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to reject payment.');
        }
    }

    public function duePayments()
    {
        $duePayments = Payment::with(['contract', 'purchaseOrder', 'creator'])
            ->where('status', 'pending')
            ->where('due_date', '<=', now()->addDays(7))
            ->orderBy('due_date')
            ->paginate(10);

        return view('payments.due', compact('duePayments'));
    }

    public function overduePayments()
    {
        $overduePayments = Payment::with(['contract', 'purchaseOrder', 'creator'])
            ->where('status', 'pending')
            ->where('due_date', '<', now())
            ->orderBy('due_date')
            ->paginate(10);

        return view('payments.overdue', compact('overduePayments'));
    }

    /**
     * Show the client payment dashboard
     *
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        $client = Auth::user()->party;
        
        // Get client's payments
        $payments = Payment::whereHas('contract', function ($query) use ($client) {
            $query->where('client_id', $client->id);
        })
        ->with(['contract', 'status'])
        ->latest()
        ->get();

        // Calculate payment statistics
        $totalPayments = $payments->count();
        $pendingPayments = $payments->where('status', 'pending')->count();
        $paidPayments = $payments->where('status', 'paid')->count();
        $totalAmount = $payments->sum('amount');
        $paidAmount = $payments->where('status', 'paid')->sum('amount');
        $pendingAmount = $payments->where('status', 'pending')->sum('amount');

        return view('payments.client-dashboard', compact(
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

    public function uploadProof(Request $request, Payment $payment)
    {
        $request->validate([
            'reference_number' => 'required|string|max:255',
            'payment_proof' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $payment->update([
            'reference_number' => $request->reference_number,
        ]);

        if ($request->hasFile('payment_proof')) {
            $file = $request->file('payment_proof');
            $path = $file->store('payment_proofs', 'public');
            $payment->attachment()->create([
                'path' => $path,
                'original_name' => $file->getClientOriginalName(),
            ]);
        }

        return redirect()->back()->with('success', 'Payment proof uploaded successfully.');
    }

    public function submitClientProof(Request $request, Payment $payment)
    {
        $request->validate([
            'client_payment_proof' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'client_payment_method' => 'required|string',
            'client_reference_number' => 'required|string',
            'client_paid_amount' => 'required|numeric',
            'client_paid_date' => 'required|date',
            'client_notes' => 'nullable|string',
        ]);

        $data = $request->only([
            'client_payment_method',
            'client_reference_number',
            'client_paid_amount',
            'client_paid_date',
            'client_notes',
        ]);

        if ($request->hasFile('client_payment_proof')) {
            $file = $request->file('client_payment_proof');
            $path = $file->store('payment_proofs', 'public');
            $data['client_payment_proof'] = $path;
        }

        $payment->update($data);
        $payment->markForVerification();

        return redirect()->back()->with('success', 'Payment proof submitted for verification.');
    }

    public function submitAdminProof(Request $request, Payment $payment)
    {
        $request->validate([
            'admin_payment_proof' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'admin_payment_method' => 'required|string',
            'admin_reference_number' => 'required|string',
            'admin_received_amount' => 'required|numeric',
            'admin_received_date' => 'required|date',
            'admin_notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $data = $request->only([
                'admin_payment_method',
                'admin_reference_number',
                'admin_received_amount',
                'admin_received_date',
                'admin_notes',
            ]);

            if ($request->hasFile('admin_payment_proof')) {
                $file = $request->file('admin_payment_proof');
                $path = $file->store('payment_proofs', 'public');
                $data['admin_payment_proof'] = $path;
            }

            $payment->update($data);

            // If all details match, mark as paid and create transaction
            if ($payment->canBeMarkedPaid()) {
                // Update payment status
                $payment->status = 'paid';
                $payment->paid_date = now();
                $payment->reference_number = $payment->admin_reference_number;
                $payment->marked_paid_by = Auth::id();
                $payment->save();

                // Create transaction record
                $transaction = Transaction::create([
                    'payment_id' => $payment->id,
                    'contract_id' => $payment->contract_id,
                    'date' => now(),
                    'amount' => $payment->amount,
                    'type' => 'payment',
                    'reference_number' => $payment->admin_reference_number,
                    'description' => 'Payment for Contract #' . ($payment->contract ? $payment->contract->contract_number : 'N/A') . ' - ' . 
                                   ($payment->description ?? 'Payment #' . $payment->payment_number),
                    'status' => 'completed',
                    'created_by' => Auth::id()
                ]);

                // Check if all contract payments are paid
                if ($payment->contract) {
                    $unpaidPayments = $payment->contract->payments()
                        ->where('status', '!=', 'paid')
                        ->count();

                    if ($unpaidPayments === 0) {
                        $payment->contract->update(['status' => 'completed']);
                    }
                }

                DB::commit();
                return redirect()->back()->with('success', 'Payment validated and marked as paid.');
            }

            DB::commit();
            return redirect()->back()->with('info', 'Admin proof submitted. Waiting for details to match for validation.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to process admin proof', [
                'payment_id' => $payment->id,
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Failed to process admin proof: ' . $e->getMessage());
        }
    }
} 