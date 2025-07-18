@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="h3 mb-4 text-gray-800 text-center" style="font-weight:700;color:#198754;letter-spacing:0.01em;">Pending Purchase Order Payments</h1>
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

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
<style>
body, .container {
    background: linear-gradient(135deg, #f8fafc 0%, #e9ecef 100%) !important;
    font-family: 'Inter', 'Segoe UI', Arial, sans-serif;
}
h1, .h3 {
    font-family: 'Inter', Arial, sans-serif;
    font-weight: 700;
    letter-spacing: 0.01em;
    color: #198754;
    font-size: 2.2rem;
    margin-bottom: 2rem;
}
.table {
    border-radius: 1.25rem;
    box-shadow: 0 8px 32px 0 rgba(44,62,80,0.10), 0 1.5px 6px rgba(44,62,80,0.04);
    background: rgba(255,255,255,0.92);
    backdrop-filter: blur(8px);
    overflow: hidden;
    margin-bottom: 2rem;
    font-size: 1.05em;
}
.table th {
    font-weight: 600;
    color: #495057;
    background: #f8fafc;
    border-top: none;
    text-align: center;
}
.table-hover tbody tr:hover {
    background: #f4faff;
    transition: background 0.2s;
}
.table td, .table th {
    vertical-align: middle;
    text-align: center;
}
.btn-success, .btn-primary {
    background: linear-gradient(90deg, #43e97b 0%, #38f9d7 100%) !important;
    color: #fff !important;
    border: none;
    font-weight: 600;
    border-radius: 2rem;
    padding: 0.5em 1.5em;
    font-size: 1.08em;
    letter-spacing: 0.01em;
    box-shadow: 0 2px 8px #43e97b22;
    transition: background 0.2s, color 0.2s, box-shadow 0.2s;
}
.btn-success:hover, .btn-primary:hover {
    background: linear-gradient(90deg, #38f9d7 0%, #43e97b 100%) !important;
    color: #fff;
    box-shadow: 0 4px 16px #43e97b44;
}
.btn-secondary {
    border-radius: 2rem;
    font-weight: 600;
    padding: 0.5em 1.5em;
    font-size: 1.08em;
}
.form-select, .form-control {
    padding: 0.7rem 1.2rem;
    border-radius: 1.1rem;
    border: 1.5px solid #ced4da;
    font-size: 1.08em;
    background: #f8fafc;
    transition: border 0.2s, box-shadow 0.2s;
}
.form-select:focus, .form-control:focus {
    border-color: #198754;
    box-shadow: 0 0 0 0.2rem #19875422;
}
.badge {
    font-size: 0.95em;
    padding: 0.5em 1em;
    border-radius: 0.7em;
    font-weight: 600;
    letter-spacing: 0.01em;
    box-shadow: 0 1px 4px #38b6ff22;
}
</style>
@endpush 