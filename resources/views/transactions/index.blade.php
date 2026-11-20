@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="h3 mb-4 text-center page-title">Transactions</h1>
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
                        <x-status-badge :status="$transaction->status" />
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
@endsection 