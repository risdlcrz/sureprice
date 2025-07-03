@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="h3 mb-4 text-gray-800 text-center" style="font-weight:700;color:#198754;letter-spacing:0.01em;">Transactions</h1>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <div class="mb-3">
        <a href="{{ route('transactions.create') }}" class="btn btn-primary">Add Transaction</a>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Contract</th>
                    <th>Date</th>
                    <th>Description</th>
                    <th>Amount</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Payment Method</th>
                    <th>Reference #</th>
                    <th>Notes</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $transaction)
                    <tr>
                        <td>{{ $transaction->id }}</td>
                        <td>{{ optional($transaction->contract)->contract_id ?? 'N/A' }}</td>
                        <td>{{ $transaction->date->format('Y-m-d') }}</td>
                        <td>{{ $transaction->description }}</td>
                        <td>₱{{ number_format($transaction->amount, 2) }}</td>
                        <td>{{ ucfirst($transaction->type) }}</td>
                        <td>{{ ucfirst($transaction->status) }}</td>
                        <td>{{ $transaction->payment_method }}</td>
                        <td>{{ $transaction->reference_number }}</td>
                        <td>{{ $transaction->notes }}</td>
                        <td>
                            <a href="{{ route('transactions.edit', $transaction) }}" class="btn btn-sm btn-warning">Edit</a>
                            <form action="{{ route('transactions.destroy', $transaction) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this transaction?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="11" class="text-center">No transactions found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div>
        {{ $transactions->links() }}
    </div>
</div>
@endsection

@push('styles')
<style>
body {
    background: linear-gradient(135deg, #f8fafc 0%, #e9ecef 100%) !important;
}
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
.btn-primary {
    border-radius: 2rem;
    font-weight: 600;
    font-size: 1.1rem;
    background: linear-gradient(90deg, #38b6ff 0%, #2563eb 100%);
    border: none;
    box-shadow: 0 2px 8px #38b6ff33;
    transition: background 0.2s, color 0.2s, box-shadow 0.2s;
}
.btn-primary:hover {
    filter: brightness(1.08);
    box-shadow: 0 4px 16px #38b6ff33;
}
.btn-success {
    border-radius: 2rem;
    font-weight: 600;
    font-size: 1.1rem;
    background: linear-gradient(90deg, #43e97b 0%, #38f9d7 100%);
    border: none;
    box-shadow: 0 2px 8px #43e97b33;
    transition: background 0.2s, color 0.2s, box-shadow 0.2s;
}
.btn-success:hover {
    filter: brightness(1.08);
    box-shadow: 0 4px 16px #43e97b33;
}
.btn-default {
    border-radius: 2rem;
    font-weight: 600;
    font-size: 1.1rem;
    background: #e9ecef;
    color: #495057;
    border: none;
    margin-left: 0.5rem;
    transition: background 0.2s, color 0.2s;
}
.btn-default:hover {
    background: #d1d5db;
    color: #222;
}
.form-control, .form-select {
    border-radius: 1.2rem;
    border: 1px solid #d1d5db;
    background: #f8fafc;
    font-size: 1.08rem;
    padding: 0.85rem 1.1rem;
    transition: border-color 0.2s, box-shadow 0.2s;
}
.form-control:focus, .form-select:focus {
    border-color: #38b6ff;
    box-shadow: 0 0 0 2px #38b6ff33;
    background: #fff;
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
    background: #f1f5f9;
    font-weight: 700;
    color: #198754;
    border-bottom: 2px solid #e3e3e3;
    text-align: center;
}
.table-hover tbody tr:hover {
    background: #e3f2fd44;
}
textarea.form-control {
    min-height: 100px;
}
@media (max-width: 991.98px) {
    .card-header {
        padding: 1rem 0.5rem 0.5rem 0.5rem;
    }
    .card {
        padding: 0.5rem;
    }
    .table th, .table td {
        padding: 0.4rem 0.2rem;
        font-size: 0.93rem;
    }
}
</style>
@endpush 