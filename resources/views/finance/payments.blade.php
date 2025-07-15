@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Pending Purchase Order Payments</h1>
    <table class="table table-bordered mt-4">
        <thead>
            <tr>
                <th>PO Number</th>
                <th>Supplier</th>
                <th>Total Amount</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pendingPOs as $po)
                <tr>
                    <td>{{ $po->po_number }}</td>
                    <td>{{ $po->supplier->company_name ?? 'N/A' }}</td>
                    <td>₱{{ number_format($po->total_amount, 2) }}</td>
                    <td>{{ ucfirst($po->status) }}</td>
                    <td>
                        @if(auth()->user()->role === 'finance')
                        <!-- Pay Button triggers modal -->
                        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#payModal-{{ $po->id }}">
                            Pay
                        </button>
                        <!-- Payment Modal -->
                        <div class="modal fade" id="payModal-{{ $po->id }}" tabindex="-1" aria-labelledby="payModalLabel-{{ $po->id }}" aria-hidden="true">
                          <div class="modal-dialog">
                            <div class="modal-content">
                              <form action="{{ route('purchase-order-payments.store', $po->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="modal-header">
                                  <h5 class="modal-title" id="payModalLabel-{{ $po->id }}">Pay Purchase Order #{{ $po->po_number }}</h5>
                                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                  <div class="mb-3">
                                    <label for="amount-{{ $po->id }}" class="form-label">Amount</label>
                                    <input type="number" step="0.01" min="0" class="form-control" id="amount-{{ $po->id }}" name="amount" value="{{ $po->total_amount }}" required>
                                  </div>
                                  <div class="mb-3">
                                    <label for="payment_method-{{ $po->id }}" class="form-label">Payment Method</label>
                                    <select class="form-select" id="payment_method-{{ $po->id }}" name="payment_method" required>
                                      <option value="">Select method</option>
                                      <option value="bank_transfer">Bank Transfer</option>
                                      <option value="check">Check</option>
                                      <option value="cash">Cash</option>
                                    </select>
                                  </div>
                                  <div class="mb-3">
                                    <label for="admin_reference_number-{{ $po->id }}" class="form-label">Reference Number</label>
                                    <input type="text" class="form-control" id="admin_reference_number-{{ $po->id }}" name="admin_reference_number" required>
                                  </div>
                                  <div class="mb-3">
                                    <label for="admin_paid_date-{{ $po->id }}" class="form-label">Payment Date</label>
                                    <input type="date" class="form-control" id="admin_paid_date-{{ $po->id }}" name="admin_paid_date" required>
                                  </div>
                                  <div class="mb-3">
                                    <label for="admin_proof-{{ $po->id }}" class="form-label">Upload Proof</label>
                                    <input type="file" class="form-control" id="admin_proof-{{ $po->id }}" name="admin_proof" accept=".jpg,.jpeg,.png,.pdf" required>
                                  </div>
                                  <div class="mb-3">
                                    <label for="admin_notes-{{ $po->id }}" class="form-label">Notes</label>
                                    <textarea class="form-control" id="admin_notes-{{ $po->id }}" name="admin_notes" rows="2"></textarea>
                                  </div>
                                </div>
                                <div class="modal-footer">
                                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                  <button type="submit" class="btn btn-success">Submit Payment</button>
                                </div>
                              </form>
                            </div>
                          </div>
                        </div>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">No pending payments.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection 