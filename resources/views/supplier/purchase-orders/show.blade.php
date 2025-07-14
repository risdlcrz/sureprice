@extends('layouts.app')
@section('content')
<div class="container">
    <h1>Purchase Order: {{ $purchaseOrder->po_number }}</h1>
    <div class="mb-3">
        <strong>Status:</strong> <span class="badge bg-{{ $purchaseOrder->status_color }}">{{ ucfirst($purchaseOrder->status) }}</span><br>
        <strong>Total Amount:</strong> ₱{{ number_format($purchaseOrder->total_amount, 2) }}<br>
        <strong>Delivery Date:</strong> {{ $purchaseOrder->delivery_date }}<br>
        <strong>Payment Terms:</strong> {{ $purchaseOrder->payment_terms }}<br>
        <strong>Shipping Terms:</strong> {{ $purchaseOrder->shipping_terms }}<br>
    </div>
    <h4>Items</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Material</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Total Price</th>
            </tr>
        </thead>
        <tbody>
            @foreach($purchaseOrder->items as $item)
                <tr>
                    <td>{{ $item->material->name ?? '-' }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>₱{{ number_format($item->unit_price, 2) }}</td>
                    <td>₱{{ number_format($item->total_price, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <h4>Payment</h4>
    @php $payment = $purchaseOrder->payments->first(); @endphp
    @if($payment)
        <div class="mb-3">
            <strong>Status:</strong> <span class="badge bg-{{ $payment->status === 'verified' ? 'success' : ($payment->status === 'for_verification' ? 'info' : ($payment->status === 'rejected' ? 'danger' : 'secondary')) }}">{{ ucfirst($payment->status) }}</span><br>
            <strong>Amount:</strong> ₱{{ number_format($payment->amount, 2) }}<br>
            <strong>Method:</strong> {{ ucfirst($payment->payment_method) }}<br>
            <strong>Reference #:</strong> {{ $payment->admin_reference_number }}<br>
            <strong>Date Paid:</strong> {{ $payment->admin_paid_date }}<br>
            <strong>Proof:</strong> <a href="{{ asset('storage/' . $payment->admin_proof) }}" target="_blank">View</a><br>
            <strong>Admin Notes:</strong> {{ $payment->admin_notes }}<br>
            @if($payment->supplier_notes)
                <strong>Supplier Notes:</strong> {{ $payment->supplier_notes }}<br>
            @endif
        </div>
        @if($payment->status === 'for_verification')
            <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#verifyPaymentModal">Verify Admin Payment</button>
            <!-- Supplier Verify Payment Modal -->
            <div class="modal fade" id="verifyPaymentModal" tabindex="-1">
              <div class="modal-dialog">
                <div class="modal-content">
                  <form method="POST" action="{{ route('purchase-order-payments.verify', $payment) }}">
                    @csrf
                    <div class="modal-header">
                      <h5 class="modal-title">Verify Admin Payment</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                      <p><strong>Amount:</strong> ₱{{ number_format($payment->amount, 2) }}</p>
                      <p><strong>Method:</strong> {{ ucfirst($payment->payment_method) }}</p>
                      <p><strong>Reference #:</strong> {{ $payment->admin_reference_number }}</p>
                      <p><strong>Date Paid:</strong> {{ $payment->admin_paid_date }}</p>
                      <p><strong>Proof:</strong> <a href="{{ asset('storage/' . $payment->admin_proof) }}" target="_blank">View</a></p>
                      <div class="mb-3">
                        <label for="supplier_reference_number">Reference Number (required)</label>
                        <input type="text" name="supplier_reference_number" id="supplier_reference_number" class="form-control" required>
                      </div>
                      <div class="mb-3">
                        <label>Notes (optional)</label>
                        <textarea name="supplier_notes" class="form-control"></textarea>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button name="action" value="verify" class="btn btn-success">Verify</button>
                      <button name="action" value="reject" class="btn btn-danger">Reject</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
        @endif
    @else
        <div class="alert alert-secondary">No payment submitted yet.</div>
    @endif
    {{-- Supplier Shipped Out Button --}}
    @if(auth()->user()->isSupplier() && $purchaseOrder->status === 'confirmed' && $payment && $payment->status === 'verified')
        <form method="POST" action="{{ route('purchase-orders.ship', $purchaseOrder->id) }}">
            @csrf
            <div class="mb-2">
                <textarea name="shipping_note" class="form-control" placeholder="Shipping note (optional)"></textarea>
            </div>
            <button type="submit" class="btn btn-info">Mark as Shipped Out</button>
        </form>
    @endif
    <a href="{{ route('supplier.purchase-orders.index') }}" class="btn btn-secondary mt-3">Back to List</a>
</div>
@endsection 