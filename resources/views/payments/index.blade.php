@extends('layouts.app')

@section('content')
@php
    // Old variables, no longer needed in this structure
    // $grouped = $payments->groupBy('contract_id');
@endphp
<div class="container">
    <h1>Payments</h1>
    @foreach($pagedContracts as $contractData)
    @php
        $contract = $contractData->contract;
        $nextDue = $contractData->nextDue;
        $contractPayments = $contractData->payments;
        $allPaid = $contractPayments->every(fn($p) => strtolower(trim($p->status)) === 'paid');
        $forVerification = $contractPayments->contains(fn($p) => strtolower(trim($p->status)) === 'for_verification');
    @endphp
    
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">{{ $contract->title ?? 'Contract #'.$contract->id }}</h5>
            @if($allPaid)
                <span class="badge bg-success">Paid</span>
            @elseif($forVerification)
                <span class="badge bg-info">For Verification</span>
            @endif
        </div>
        
        @if($nextDue)
        <div class="next-payment-info m-3 mb-0">
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
                            @php
                                $status = trim(strtolower($payment->status));
                            @endphp
                            @if($status === 'paid')
                                <span class="badge bg-success">Paid</span>
                            @elseif($status === 'for_verification')
                                <span class="badge bg-info">For Verification</span>
                            @elseif($payment->isOverdue())
                                <span class="badge bg-danger">Overdue</span>
                            @else
                                <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $status)) ?: 'Pending' }}</span>
                            @endif
                        </td>
                        <td>{{ $payment->payment_method ?? '-' }}</td>
                        <td>{{ $payment->client_reference_number ?? $payment->reference_number ?? '-' }}</td>
                        <td>
                            @php 
                                $proof_path = $payment->client_payment_proof ?? ($payment->attachment ? $payment->attachment->path : null);
                            @endphp
                            @if($proof_path)
                                <a href="{{ asset('storage/' . $proof_path) }}" target="_blank">
                                    <img src="{{ asset('storage/' . $proof_path) }}" alt="Proof" width="100">
                                </a>
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
        {{ $pagedContracts->links() }}
    </div>

    <h2>PO Payments</h2>
    <div class="card mb-4">
        <div class="card-header">Purchase Order Payments</div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>PO #</th>
                            <th>Supplier</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date Paid</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(App\Models\PurchaseOrderPayment::latest()->take(20)->get() as $poPayment)
                            <tr>
                                <td><a href="{{ route('purchase-orders.show', $poPayment->purchaseOrder) }}">{{ $poPayment->purchaseOrder->po_number }}</a></td>
                                <td>{{ $poPayment->purchaseOrder->supplier->company_name ?? '-' }}</td>
                                <td>₱{{ number_format($poPayment->amount, 2) }}</td>
                                <td><span class="badge bg-{{ $poPayment->status === 'verified' ? 'success' : ($poPayment->status === 'for_verification' ? 'info' : ($poPayment->status === 'rejected' ? 'danger' : 'secondary')) }}">{{ ucfirst($poPayment->status) }}</span></td>
                                <td>{{ $poPayment->admin_paid_date }}</td>
                                <td>
                                    <a href="{{ route('purchase-orders.show', $poPayment->purchaseOrder) }}" class="btn btn-sm btn-info">View PO</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection 