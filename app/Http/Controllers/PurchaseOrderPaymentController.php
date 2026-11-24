<?php
namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Support\Uploads;

class PurchaseOrderPaymentController extends Controller
{
    // Admin submits payment
    public function store(Request $request, $poId)
    {
        // Only the purchasing/finance staff record payments to suppliers.
        abort_unless(collect(['admin', 'manager', 'finance', 'procurement'])->contains(fn ($role) => Auth::user()->hasRole($role)), 403);
        $po = PurchaseOrder::findOrFail($poId);
        $data = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string',
            ...Uploads::file('admin_proof', 'documents', required: true, label: 'The proof of payment')->rules,
            'admin_reference_number' => 'required|string',
            'admin_paid_date' => 'required|date',
            'admin_notes' => 'nullable|string',
        ], Uploads::file('admin_proof', 'documents', required: true, label: 'The proof of payment')->messages);
        $data['admin_proof'] = $request->file('admin_proof')->store('po_payments', 'public');
        $data['status'] = 'for_verification';
        $payment = $po->payments()->create($data);
        // Update PO status to for_verification
        $po->status = 'for_verification';
        $po->save();
        return back()->with('success', 'Payment submitted for supplier verification.');
    }

    // Supplier verifies or rejects payment
    public function verify(Request $request, $paymentId)
    {
        $payment = PurchaseOrderPayment::with('purchaseOrder.supplier')->findOrFail($paymentId);
        // Only the supplier the order was placed with (or an admin) may verify or reject its payment.
        $user = Auth::user();
        abort_unless($user->isAdmin() || ($payment->purchaseOrder?->supplier && $payment->purchaseOrder->supplier->user_id === $user->id), 403);
        $data = $request->validate([
            'supplier_reference_number' => 'required|string',
            'supplier_notes' => 'nullable|string',
            'action' => 'required|in:verify,reject',
        ]);
        if ($data['action'] === 'verify') {
            if ($data['supplier_reference_number'] !== $payment->admin_reference_number) {
                return back()->withErrors(['supplier_reference_number' => 'Reference number does not match admin reference number.']);
            }
            $payment->update([
                'supplier_verified' => true,
                'supplier_verified_at' => now(),
                'supplier_notes' => $data['supplier_notes'],
                'status' => 'verified',
            ]);
            // Create a transaction record for this PO payment
            \App\Models\Transaction::create([
                'payment_id' => null,
                'purchase_order_id' => $payment->purchase_order_id,
                'date' => now(),
                'amount' => $payment->amount,
                'type' => 'po_payment',
                'reference_number' => $payment->admin_reference_number,
                'description' => 'Payment for PO #' . ($payment->purchaseOrder ? $payment->purchaseOrder->po_number : $payment->purchase_order_id),
                'status' => 'completed',
                'created_by' => auth()->id(),
            ]);
            // Update the purchase order status to 'paid'
            $po = $payment->purchaseOrder;
            if ($po) {
                $po->status = 'paid';
                $po->save();
            }
        } else {
            $payment->update([
                'supplier_verified' => false,
                'supplier_verified_at' => now(),
                'supplier_notes' => $data['supplier_notes'],
                'status' => 'rejected',
            ]);
        }
        return back()->with('success', 'Payment verification updated.');
    }
} 