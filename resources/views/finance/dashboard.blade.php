@extends('layouts.app')

@section('content')
<div class="container py-4">
<h1 class="h3 mb-4 text-gray-800 text-center" style="font-weight:700;color:#198754;letter-spacing:0.01em;">Finance Dashboard</h1>
    <div class="row g-4 mb-4">
        <!-- Pending PO Payments Card -->
        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body d-flex flex-column align-items-center justify-content-center">
                    <i class="fas fa-file-invoice-dollar fa-3x mb-3 text-success"></i>
                    <h5 class="card-title">Pending PO Payments</h5>
                    <p class="card-text display-6 fw-bold text-success">
                        {{ \App\Models\PurchaseOrder::where('status', 'pending_payment')->count() }}
                    </p>
                    <a href="{{ route('finance.payments') }}" class="btn btn-success mt-2">View Payments</a>
                </div>
            </div>
        </div>
        <!-- Recent Transactions Card -->
        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body d-flex flex-column align-items-center justify-content-center">
                    <i class="fas fa-money-check-alt fa-3x mb-3 text-info"></i>
                    <h5 class="card-title">Recent Transactions</h5>
                    <p class="card-text display-6 fw-bold text-info">
                        {{ \App\Models\Transaction::count() }}
                    </p>
                    <a href="{{ route('finance.transactions') }}" class="btn btn-info mt-2">View Transactions</a>
                </div>
            </div>
        </div>
        <!-- Contract Payments for Verification Card -->
        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body d-flex flex-column align-items-center justify-content-center">
                    <i class="fas fa-user-check fa-3x mb-3 text-warning"></i>
                    <h5 class="card-title">Contract Payments for Verification</h5>
                    <p class="card-text display-6 fw-bold text-warning">
                        {{ \App\Models\Payment::where('status', 'for_verification')->count() }}
                    </p>
                    <a href="{{ route('payments.index') }}" class="btn btn-warning mt-2 text-white">Verify Payments</a>
                </div>
            </div>
        </div>
    </div>
    <!-- Recent PO Payments Table -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white fw-semibold">Recent PO Payments</div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-success">
                        <tr>
                            <th>PO #</th>
                            <th>Supplier</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date Paid</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $recentPOs = \App\Models\PurchaseOrderPayment::latest()->take(5)->get(); @endphp
                        @forelse($recentPOs as $poPayment)
                            <tr>
                                <td>{{ $poPayment->purchaseOrder->po_number ?? '-' }}</td>
                                <td>{{ $poPayment->purchaseOrder->supplier->company_name ?? '-' }}</td>
                                <td>₱{{ number_format($poPayment->amount, 2) }}</td>
                                <td><span class="badge bg-{{ $poPayment->status === 'verified' ? 'success' : ($poPayment->status === 'for_verification' ? 'info' : ($poPayment->status === 'rejected' ? 'danger' : 'secondary')) }}">{{ ucfirst($poPayment->status) }}</span></td>
                                <td>{{ $poPayment->admin_paid_date }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No recent PO payments found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@push('styles')
<style>
.card {
    border-radius: 1.25rem;
    box-shadow: 0 4px 24px rgba(44,62,80,0.08), 0 1.5px 6px rgba(44,62,80,0.04);
    border: none;
    margin-bottom: 2rem;
}
.card-header {
    background: #fff;
    border-radius: 1.25rem 1.25rem 0 0;
    border-bottom: none;
    padding: 1.5rem 2rem 1rem 2rem;
    display: flex;
    justify-content: flex-start;
    align-items: center;
    gap: 1rem;
}
.table-responsive {
    border-radius: 1.1rem;
    overflow-x: auto;
    box-shadow: 0 4px 24px rgba(44,62,80,0.08), 0 1.5px 6px rgba(44,62,80,0.04);
    background: #fff;
    max-width: 100%;
}
.table th, .table td {
    vertical-align: middle;
    padding: 0.7rem 0.5rem;
    border: none;
    background: #f8fafc;
    text-align: center;
}
.table thead th {
    background: #e8f5e9;
    font-weight: 700;
    color: #198754;
    border-bottom: 2px solid #e3e3e3;
    text-align: center;
}
.table-hover tbody tr:hover {
    background: #e3f2fd44;
}
</style>
@endpush
@endsection 