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
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Notifications\PaymentStatusNotification;

class PaymentController extends Controller
{
    public function index()
    {
        // Get payments based on user role
        if (auth()->user()->role === 'client') {
            // For clients, only show their contract payments
            $client = auth()->user()->party;
            
            // Check if client has a party record
            if (!$client) {
                return view('payments.index', [
                    'pagedContracts' => collect([]),
                    'error' => 'No client profile found. Please contact the administrator.'
                ]);
            }
            
            $allPayments = Payment::with(['contract', 'attachment'])
                ->whereHas('contract', function($query) use ($client) {
                    $query->where('client_id', $client->id);
                })
                ->orderBy('due_date')
                ->get();
        } else {
            // For admin/finance, show all payments
            $allPayments = Payment::with(['contract', 'attachment'])->orderBy('due_date')->get();
        }

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

        // Check if there are any contracts with payments
        if ($contractsWithPayments->isEmpty()) {
            return view('payments.index', [
                'pagedContracts' => collect([]),
                'message' => 'No payments found.'
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
                // Create a transaction record for this contract payment
                \App\Models\Transaction::create([
                    'payment_id' => $payment->id,
                    'contract_id' => $payment->contract_id,
                    'date' => now(),
                    'amount' => $payment->amount,
                    'type' => 'contract_payment',
                    'reference_number' => $payment->reference_number,
                    'description' => 'Payment for Contract #' . ($payment->contract ? $payment->contract->contract_number : $payment->contract_id),
                    'status' => 'completed',
                    'created_by' => auth()->id(),
                ]);
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

    /**
     * Serve payment proof file (client or admin proof) for viewing/download.
     */
    public function showProof(Request $request, Payment $payment)
    {
        $proofPath = $payment->client_payment_proof
            ?? ($payment->attachment ? $payment->attachment->path : null)
            ?? $payment->admin_payment_proof;

        if (!$proofPath) {
            abort(404, 'Payment proof not found.');
        }

        if (!Storage::disk('public')->exists($proofPath)) {
            abort(404, 'Payment proof file not found.');
        }

        $path = Storage::disk('public')->path($proofPath);
        $mime = Storage::disk('public')->mimeType($proofPath);
        $name = basename($proofPath);
        $disposition = $request->boolean('download') ? 'attachment' : 'inline';

        return response()->file($path, [
            'Content-Type' => $mime,
            'Content-Disposition' => $disposition . '; filename="' . $name . '"',
        ]);
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
            'client_payment_method' => 'required|string|max:255',
            'client_reference_number' => 'required|string|max:255',
            'client_paid_amount' => 'required|numeric|min:0',
            'client_paid_date' => 'required|date',
            'client_payment_proof' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'client_notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            // Update payment with client submission data
            $payment->update([
                'client_payment_method' => $request->client_payment_method,
                'client_reference_number' => $request->client_reference_number,
                'client_paid_amount' => $request->client_paid_amount,
                'client_paid_date' => $request->client_paid_date,
                'client_notes' => $request->client_notes,
                'status' => 'for_verification',
            ]);

            // Handle file upload
            if ($request->hasFile('client_payment_proof')) {
                $file = $request->file('client_payment_proof');
                $path = $file->store('payment_proofs', 'public');
                
                // Create or update attachment
                $payment->attachment()->updateOrCreate(
                    ['attachable_id' => $payment->id, 'attachable_type' => Payment::class],
                    [
                        'path' => $path,
                        'original_name' => $file->getClientOriginalName(),
                        'mime_type' => $file->getMimeType(),
                        'size' => $file->getSize(),
                    ]
                );
            }

            DB::commit();

            return redirect()->back()->with('success', 'Payment proof submitted successfully! Your payment is now under verification.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to submit client proof', [
                'payment_id' => $payment->id,
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Failed to submit payment proof: ' . $e->getMessage());
        }
    }

    public function submitAdminProof(Request $request, Payment $payment)
    {
        $request->validate([
            'admin_payment_proof' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'admin_reference_number' => 'required|string',
            'admin_received_amount' => 'required|numeric',
            'admin_received_date' => 'required|date',
            'admin_notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Use client's payment method automatically
            $adminPaymentMethod = $payment->client_payment_method;

            if ($request->admin_reference_number !== $payment->client_reference_number) {
                return redirect()->back()->with('error', 'Reference number does not match. Expected: ' . $payment->client_reference_number . ', Received: ' . $request->admin_reference_number);
            }

            if (abs($request->admin_received_amount - $payment->client_paid_amount) > 0.01) {
                return redirect()->back()->with('error', 'Amount does not match. Expected: ₱' . number_format($payment->client_paid_amount, 2) . ', Received: ₱' . number_format($request->admin_received_amount, 2));
            }

            $data = [
                'admin_payment_method' => $adminPaymentMethod,
                'admin_reference_number' => $request->admin_reference_number,
                'admin_received_amount' => $request->admin_received_amount,
                'admin_received_date' => $request->admin_received_date,
                'admin_notes' => $request->admin_notes,
            ];

            if ($request->hasFile('admin_payment_proof')) {
                $file = $request->file('admin_payment_proof');
                $path = $file->store('payment_proofs', 'public');
                $data['admin_payment_proof'] = $path;
            }

            $payment->update($data);

            // Mark as paid and create transaction
            $payment->status = 'paid';
            $payment->paid_date = now();
            $payment->reference_number = $payment->admin_reference_number;
            $payment->marked_paid_by = Auth::id();
            $payment->approved_by = Auth::id();
            $payment->approved_at = now();
            $payment->save();

            // Create transaction record
            $transaction = Transaction::create([
                'payment_id' => $payment->id,
                'contract_id' => $payment->contract_id,
                'date' => now(),
                'amount' => $payment->amount,
                'type' => 'contract_payment',
                'reference_number' => $payment->admin_reference_number,
                'description' => 'Payment for ' . $payment->payment_type . ' - Contract #' . ($payment->contract ? $payment->contract->contract_number : $payment->contract_id),
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
            return redirect()->back()->with('success', 'Payment verified successfully!');
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

    public function verify(Request $request, Payment $payment)
    {
        $request->validate([
            'admin_reference_number' => 'required|string',
            'admin_received_amount' => 'required|numeric',
            'admin_received_date' => 'required|date',
            'admin_notes' => 'nullable|string',
            'admin_payment_proof' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        try {
            DB::beginTransaction();

            // Use client's payment method automatically
            $adminPaymentMethod = $payment->client_payment_method;

            // Check if payment details match client submission
            if ($request->admin_reference_number !== $payment->client_reference_number) {
                return redirect()->back()->with('error', 'Reference number does not match client submission.');
            }

            if (abs($request->admin_received_amount - $payment->client_paid_amount) > 0.01) {
                return redirect()->back()->with('error', 'Amount does not match client submission.');
            }

            $data = [
                'admin_payment_method' => $adminPaymentMethod,
                'admin_reference_number' => $request->admin_reference_number,
                'admin_received_amount' => $request->admin_received_amount,
                'admin_received_date' => $request->admin_received_date,
                'admin_notes' => $request->admin_notes,
            ];

            if ($request->hasFile('admin_payment_proof')) {
                $file = $request->file('admin_payment_proof');
                $path = $file->store('payment_proofs', 'public');
                $data['admin_payment_proof'] = $path;
            }

            $payment->update($data);

            // Mark as paid and create transaction
            $payment->status = 'paid';
            $payment->paid_date = now();
            $payment->reference_number = $payment->admin_reference_number;
            $payment->marked_paid_by = Auth::id();
            $payment->approved_by = Auth::id();
            $payment->approved_at = now();
            $payment->save();

            // Create transaction record
            $transaction = Transaction::create([
                'payment_id' => $payment->id,
                'contract_id' => $payment->contract_id,
                'date' => now(),
                'amount' => $payment->amount,
                'type' => 'contract_payment',
                'reference_number' => $payment->admin_reference_number,
                'description' => 'Payment for ' . $payment->payment_type . ' - Contract #' . ($payment->contract ? $payment->contract->contract_number : $payment->contract_id),
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

            // Send notification to client
            if ($payment->contract && $payment->contract->client) {
                $client = $payment->contract->client;
                if ($client->user) {
                    $client->user->notify(new PaymentStatusNotification(
                        $payment, 
                        'verified', 
                        'Your payment has been verified successfully. Thank you for your payment.'
                    ));
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 'Payment verified successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to verify payment', [
                'payment_id' => $payment->id,
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Failed to verify payment: ' . $e->getMessage());
        }
    }

    public function rejectPayment(Request $request, Payment $payment)
    {
        $request->validate([
            'rejection_reason' => 'required|string',
            'rejection_details' => 'required|string',
            'action_required' => 'required|string',
            'finance_notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Update payment status back to pending
            $payment->update([
                'status' => 'pending',
                'rejection_reason' => $request->rejection_reason,
                'rejection_details' => $request->rejection_details,
                'action_required' => $request->action_required,
                'finance_notes' => $request->finance_notes,
                'rejected_by' => Auth::id(),
                'rejected_at' => now(),
            ]);

            // Clear client submission data
            $payment->update([
                'client_payment_proof' => null,
                'client_payment_method' => null,
                'client_reference_number' => null,
                'client_paid_amount' => null,
                'client_paid_date' => null,
                'client_notes' => null,
            ]);

            // Send notification to client
            if ($payment->contract && $payment->contract->client) {
                $client = $payment->contract->client;
                if ($client->user) {
                    $message = "Your payment was rejected. Reason: " . $request->rejection_details . 
                              "\nAction Required: " . $request->action_required;
                    $client->user->notify(new PaymentStatusNotification(
                        $payment, 
                        'rejected', 
                        $message
                    ));
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 'Payment rejected. Client will be notified to resubmit.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to reject payment', [
                'payment_id' => $payment->id,
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Failed to reject payment: ' . $e->getMessage());
        }
    }

    public function requestMoreInfo(Request $request, Payment $payment)
    {
        $request->validate([
            'info_request_type' => 'required|string',
            'specific_request' => 'required|string',
            'response_deadline' => 'required|date',
            'priority_level' => 'required|string',
            'finance_notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Update payment with information request
            $payment->update([
                'info_request_type' => $request->info_request_type,
                'specific_request' => $request->specific_request,
                'response_deadline' => $request->response_deadline,
                'priority_level' => $request->priority_level,
                'finance_notes' => $request->finance_notes,
                'info_requested_by' => Auth::id(),
                'info_requested_at' => now(),
            ]);

            // Send notification to client
            if ($payment->contract && $payment->contract->client) {
                $client = $payment->contract->client;
                if ($client->user) {
                    $message = "We need additional information about your payment.\n\n" .
                              "Request: " . $request->specific_request . "\n" .
                              "Deadline: " . $request->response_deadline . "\n" .
                              "Priority: " . ucfirst($request->priority_level);
                    $client->user->notify(new PaymentStatusNotification(
                        $payment, 
                        'info_requested', 
                        $message
                    ));
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 'Information request sent to client.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to request more info', [
                'payment_id' => $payment->id,
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Failed to request more information: ' . $e->getMessage());
        }
    }
} 