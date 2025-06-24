@extends('layouts.app')
@section('content')
<div class="container">
    <h1>My Purchase Orders</h1>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>PO Number</th>
                <th>Total Amount</th>
                <th>Status</th>
                <th>Payment Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($purchaseOrders as $po)
                @php $payment = $po->payments->first(); @endphp
                <tr>
                    <td>{{ $po->po_number }}</td>
                    <td>₱{{ number_format($po->total_amount, 2) }}</td>
                    <td><span class="badge bg-{{ $po->status_color }}">{{ ucfirst($po->status) }}</span></td>
                    <td>
                        @if($payment)
                            <span class="badge bg-{{ $payment->status === 'verified' ? 'success' : ($payment->status === 'for_verification' ? 'info' : ($payment->status === 'rejected' ? 'danger' : 'secondary')) }}">
                                {{ ucfirst($payment->status) }}
                            </span>
                        @else
                            <span class="badge bg-secondary">Unpaid</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('supplier.purchase-orders.show', $po) }}" class="btn btn-sm btn-info">View</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center">No purchase orders found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection 