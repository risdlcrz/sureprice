@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="h3 mb-4 text-center" style="font-weight:700;color:#198754;letter-spacing:0.01em;">Transactions</h1>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(isset($warning))
        <div class="alert alert-warning">{{ $warning }}</div>
    @endif
    <div class="table-responsive rounded shadow-sm bg-white p-3">
        <table class="table table-bordered table-hover align-middle mb-0">
            <thead class="table-success">
                <tr>
                    <th>Date</th>
                    <th>Payment #</th>
                    <th>Contract</th>
                    <th>Description</th>
                    <th>Amount</th>
                    <th>Reference #</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
            @forelse($transactions as $transaction)
                <tr>
                    <td>{{ $transaction->date->format('Y-m-d') }}</td>
                    <td>{{ $transaction->payment->payment_number ?? '-' }}</td>
                    <td>{{ optional($transaction->contract)->contract_number ?? 'N/A' }}</td>
                    <td>{{ $transaction->description }}</td>
                    <td>₱{{ number_format($transaction->amount, 2) }}</td>
                    <td>{{ $transaction->reference_number }}</td>
                    <td>
                        <span class="badge bg-{{ $transaction->status === 'completed' ? 'success' : 'warning' }}">
                            {{ ucfirst($transaction->status) }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center py-5">
                        <div class="d-flex flex-column align-items-center justify-content-center">
                            <i class="fas fa-file-invoice-dollar fa-3x mb-3 text-muted"></i>
                            <span class="fw-semibold text-muted" style="font-size:1.2em;">No transactions found.</span>
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-center mt-4">
        @if(method_exists($transactions, 'links'))
            {{ $transactions->links() }}
        @endif
    </div>
</div>
@push('styles')
<style>
body {
    background: linear-gradient(135deg, #f8fafc 0%, #e9ecef 100%) !important;
}
.table-responsive {
    border-radius: 1.1rem;
    overflow-x: auto;
    box-shadow: 0 4px 24px rgba(44,62,80,0.08), 0 1.5px 6px rgba(44,62,80,0.04);
    background: #fff;
    max-width: 100%;
}
.table {
    margin-bottom: 0;
    background: #fff;
    border-radius: 1.1rem;
    overflow: hidden;
    font-size: 0.97rem;
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