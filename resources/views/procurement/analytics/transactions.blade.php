@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Transaction Analytics</h1>
        {{-- Add any filter or export buttons here --}}
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Purchase Order Transactions</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>PO Number</th>
                            <th>Supplier</th>
                            <th>Description</th>
                            <th class="text-end">Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transactions as $transaction)
                            <tr>
                                <td>{{ $transaction->date->format('M d, Y') }}</td>
                                <td>
                                    @if($transaction->payment && $transaction->payment->purchaseOrder)
                                    <a href="{{ route('purchase-orders.show', $transaction->payment->purchaseOrder->id) }}">
                                        {{ $transaction->payment->purchaseOrder->po_number }}
                                    </a>
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>{{ $transaction->payment->purchaseOrder->supplier->name ?? 'N/A' }}</td>
                                <td>{{ $transaction->description }}</td>
                                <td class="text-end">₱{{ number_format($transaction->amount, 2) }}</td>
                                <td>
                                    <span class="badge bg-success">{{ ucfirst($transaction->status) }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No transactions found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $transactions->links() }}
        </div>
    </div>
</div>
@endsection 