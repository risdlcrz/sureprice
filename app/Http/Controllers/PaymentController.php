<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Contract;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with(['contract', 'purchaseOrder', 'creator'])
            ->latest()
            ->paginate(10);

        return view('payments.index', compact('payments'));
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

            $payment->approve(auth()->user());

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
        $request->validate([
            'reference_number' => 'required|string|max:255'
        ]);

        try {
            DB::beginTransaction();

            $payment->markAsPaid($request->reference_number);

            // If this is a contract payment, check if all payments are paid
            if ($payment->contract_id) {
                $contract = $payment->contract;
                $allPaymentsPaid = $contract->payments()
                    ->where('status', '!=', 'paid')
                    ->count() === 0;

                if ($allPaymentsPaid) {
                    $contract->update(['status' => 'completed']);
                }
            }

            // If this is a purchase order payment, check if all payments are paid
            if ($payment->purchase_order_id) {
                $purchaseOrder = $payment->purchaseOrder;
                $allPaymentsPaid = $purchaseOrder->payments()
                    ->where('status', '!=', 'paid')
                    ->count() === 0;

                if ($allPaymentsPaid) {
                    $purchaseOrder->update(['status' => 'completed']);
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 'Payment marked as paid successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to mark payment as paid.');
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
} 