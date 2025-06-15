@extends('layouts.app')

@section('content')
@php
    $grouped = $payments->groupBy('contract_id');
@endphp
<div class="container">
    <h1>Payments</h1>
    @foreach($grouped as $contractId => $contractPayments)
    @php
        $contract = $contractPayments->first()->contract;
        $nextDue = $contractPayments->where('status', '!=', 'paid')->sortBy('due_date')->first();
    @endphp
    
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">{{ $contract->title ?? 'Contract #'.$contractId }}</h5>
        </div>
        
        @if($nextDue)
        <div class="alert alert-info m-3 mb-0">
            <strong>Next Payment Due:</strong> ₱{{ number_format($nextDue->amount, 2) }} on {{ $nextDue->due_date->format('M d, Y') }}
            @if($nextDue->isOverdue())
                <span class="badge bg-danger ms-2">Overdue</span>
            @endif
        </div>
        @endif
        
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Payment #</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Due Date</th>
                        <th>Status</th>
                        <th>Payment Method</th>
                        <th>Reference #</th>
                        <th>Proof</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($contractPayments as $payment)
                    <tr>
                        <td>{{ $payment->payment_number }}</td>
                        <td>{{ ucfirst($payment->payment_type) }}</td>
                        <td>₱{{ number_format($payment->amount, 2) }}</td>
                        <td>{{ $payment->due_date ? $payment->due_date->format('Y-m-d') : '' }}</td>
                        <td>
                            @if($payment->status === 'paid')
                                <span class="badge bg-success">Paid</span>
                            @elseif($payment->isOverdue())
                                <span class="badge bg-danger">Overdue</span>
                            @elseif($payment->status === 'for_verification')
                                <span class="badge bg-info">For Verification</span>
                            @else
                                <span class="badge bg-warning">Pending</span>
                            @endif
                        </td>
                        <td>{{ $payment->payment_method ?? 'bank_transfer' }}</td>
                        <td>{{ $payment->reference_number ?? '-' }}</td>
                        <td>
                            @if($payment->attachment)
                                <a href="{{ asset('storage/' . $payment->attachment->path) }}" class="btn btn-sm btn-link">View Proof</a>
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if($payment->status === 'for_verification' && auth()->user()->user_type === 'admin')
                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#adminVerifyModal{{ $payment->id }}">Verify</button>
                                @include('payments.partials.admin_verify_modal', ['payment' => $payment])
                            @elseif($payment->status !== 'paid' && auth()->user()->user_type !== 'admin')
                                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#clientPayModal{{ $payment->id }}">Pay</button>
                                @include('payments.partials.client_pay_modal', ['payment' => $payment])
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endforeach

    <!-- Pagination Links -->
    <div class="d-flex justify-content-center">
        {{ $payments->links() }}
    </div>
</div>
@endsection 