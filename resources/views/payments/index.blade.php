@extends('layouts.app')

@section('content')
@php
    // Old variables, no longer needed in this structure
    // $grouped = $payments->groupBy('contract_id');
@endphp
<div class="container">
<h1 class="h3 mb-4 text-gray-800 text-center" style="font-weight:700;color:#198754;letter-spacing:0.01em;">Payments</h1>

@if(isset($error))
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle"></i>
        {{ $error }}
    </div>
@endif

@if(isset($message))
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i>
        {{ $message }}
    </div>
@endif
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
                            @if($payment->status === 'for_verification' && (auth()->user()->user_type === 'admin' || auth()->user()->role === 'finance'))
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
    @if($pagedContracts instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="d-flex justify-content-center">
            {{ $pagedContracts->links() }}
        </div>
    @endif

    <div class="card mb-4 shadow-sm rounded">
        <div class="card-header bg-white fw-semibold">Purchase Order Payments</div>
        <div class="card-body">
            <div class="table-responsive rounded bg-white p-2">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-success">
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
                        @php $poPayments = App\Models\PurchaseOrderPayment::latest()->take(20)->get(); @endphp
                        @forelse($poPayments as $poPayment)
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
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <i class="fas fa-money-check-alt fa-3x mb-3 text-muted"></i>
                                        <span class="fw-semibold text-muted" style="font-size:1.2em;">No purchase order payments found.</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
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
    background: #e8f5e9;
    font-weight: 700;
    color: #198754;
    border-bottom: 2px solid #e3e3e3;
    text-align: center;
}
.table-hover tbody tr:hover {
    background: #e3f2fd44;
}
</style>
@endpush
@endsection 