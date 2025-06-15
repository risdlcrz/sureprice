@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Transactions</h1>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead>
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
                <tr><td colspan="7" class="text-center">No transactions found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-center mt-4">
        {{ $transactions->links() }}
    </div>
</div>
@endsection 