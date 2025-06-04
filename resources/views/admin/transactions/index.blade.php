@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h1>Transactions</h1>
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