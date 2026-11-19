@extends('layouts.app')

@section('content')
<div class="container py-4">
    <x-page-header title="Transaction Analytics">
        {{-- Add any filter or export buttons here --}}
    </x-page-header>

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
                            <x-empty-row cols="6">No transactions found.</x-empty-row>
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