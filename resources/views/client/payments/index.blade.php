@extends('layouts.app')

@section('content')
<div class="container py-4">
    {{-- Contract Payments Card --}}
    @if($pagedContracts->isEmpty())
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-bold">Contract Payments</span>
                <div>
                    <button class="btn btn-outline-primary btn-sm me-2">🔍 Search Contract</button>
                    <button class="btn btn-outline-info btn-sm">📋 View All Contracts</button>
                </div>
            </div>
            <div class="card-body">
                <div class="alert alert-warning mb-0">No contract payments found.</div>
            </div>
        </div>
    @else
        @foreach($pagedContracts as $item)
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <div>
                        <span class="fw-bold">Contract #{{ $item->contract->id }} - {{ $item->contract->title ?? 'Untitled Contract' }}</span><br>
                        <span><strong>Client:</strong> {{ $item->contract->client->name ?? '' }}</span><br>
                        <span><strong>Contractor:</strong> {{ $item->contract->contractor->name ?? '' }}</span><br>
                        <span><strong>Total Contract Amount:</strong> ₱{{ number_format($item->totalAmount, 2) }}</span>
                    </div>
                    <div class="text-end">
                        <div class="mb-1"><span class="badge bg-warning text-dark">Pending: ₱{{ number_format($item->pendingAmount, 2) }}</span></div>
                        <div><strong>Paid Amount:</strong> ₱{{ number_format($item->paidAmount, 2) }}</div>
                        <div><strong>Pending Amount:</strong> ₱{{ number_format($item->pendingAmount, 2) }}</div>
                        @if($item->nextDue)
                            <div><strong>Next Due:</strong> ₱{{ number_format($item->nextDue->amount, 2) }} on {{ \Carbon\Carbon::parse($item->nextDue->due_date)->format('M d, Y') }}
                                @if($item->nextDue->status === 'pending' && \Carbon\Carbon::parse($item->nextDue->due_date)->isPast())
                                    <span class="badge bg-danger ms-1">Overdue</span>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Payment Stage</th>
                                    <th>Amount</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($item->payments as $payment)
                                    <tr>
                                        <td>{{ $payment->payment_type }}</td>
                                        <td>₱{{ number_format($payment->amount, 2) }}</td>
                                        <td>{{ \Carbon\Carbon::parse($payment->due_date)->format('M d, Y') }}</td>
                                        <td>
                                            @if($payment->status === 'paid')
                                                <span class="badge bg-success">Paid</span>
                                            @elseif($payment->status === 'for_verification')
                                                <span class="badge bg-info">For Verification</span>
                                            @elseif($payment->status === 'pending')
                                                @if(\Carbon\Carbon::parse($payment->due_date)->isPast())
                                                    <span class="badge bg-danger">Overdue</span>
                                                @else
                                                    <span class="badge bg-warning text-dark">Pending</span>
                                                @endif
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst($payment->status) }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($payment->status === 'pending')
                                                <a href="{{ route('client.payments.show', $payment->id) }}" class="btn btn-sm btn-success">Pay</a>
                                            @elseif($payment->status === 'for_verification' || $payment->status === 'paid')
                                                <a href="{{ route('client.payments.show', $payment->id) }}" class="btn btn-sm btn-outline-primary">View</a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endforeach
        <div class="d-flex justify-content-center mt-3">
            {{ $pagedContracts->links() }}
        </div>
    @endif

    {{-- Purchase Order Payments Card --}}
    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <span class="fw-bold">Purchase Order Payments</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>PO Number</th>
                            <th>Supplier</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- TODO: Replace with actual PO payments data --}}
                        <tr>
                            <td colspan="5" class="text-center text-muted">No pending purchase order payments.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection 