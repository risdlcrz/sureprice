@extends('layouts.app')

@section('content')
@php
    $grouped = $payments->groupBy('contract_id');
@endphp
<div class="container">
    <h1>Payments</h1>
    <table class="table">
        <thead>
            <tr>
                <th>Payment #</th>
                <th>Type</th>
                <th>Amount</th>
                <th>Due Date</th>
                <th>Status</th>
                <th>Reference #</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        @foreach($grouped as $contractId => $contractPayments)
            @php $contract = $contractPayments->first()->contract; @endphp
            <tr style="background:#f8f9fa; font-weight:bold;">
                <td colspan="7">
                    @if($contract)
                        <a href="{{ route('contracts.show', $contract->id) }}">
                            {{ $contract->title ?? 'Contract #'.$contract->id }}
                        </a>
                    @else
                        Contract #{{ $contractId }}
                    @endif
                </td>
            </tr>
            @foreach($contractPayments as $payment)
                <tr>
                    <td>{{ $payment->payment_number }}</td>
                    <td>{{ $payment->payment_type }}</td>
                    <td>{{ number_format($payment->amount, 2) }}</td>
                    <td>{{ $payment->due_date ? $payment->due_date->format('Y-m-d') : '' }}</td>
                    <td>{{ ucfirst($payment->status) }}</td>
                    <td>
                        @if($payment->status === 'paid')
                            {{ $payment->reference_number }}
                            @if($payment->attachment)
                                <br><a href="{{ asset('storage/' . $payment->attachment->path) }}" target="_blank">View Proof</a>
                            @endif
                        @else
                            <span class="text-muted">Pending</span>
                            @if($payment->attachment)
                                <br><a href="{{ asset('storage/' . $payment->attachment->path) }}" target="_blank">View Proof</a>
                            @else
                                @if(auth()->user()->role === 'client')
                                    <form method="POST" action="{{ route('payments.uploadProof', $payment) }}" enctype="multipart/form-data" style="display:inline; max-width: 250px;">
                                        @csrf
                                        <input type="file" name="payment_proof" class="form-control mb-1" accept="image/*,application/pdf" required>
                                        <button type="submit" class="btn btn-primary btn-sm mt-1 w-100">Upload Proof</button>
                                    </form>
                                @endif
                            @endif
                        @endif
                    </td>
                    <td>
                        @if(auth()->user()->role === 'admin' && $payment->status === 'pending')
                            <form method="POST" action="{{ route('payments.markAsPaid', $payment) }}" style="display:inline; max-width: 200px;">
                                @csrf
                                <input type="text" name="reference_number" class="form-control mb-1" placeholder="Reference #" required>
                                <button type="submit" class="btn btn-success btn-sm mt-1 w-100">Mark as Paid</button>
                            </form>
                        @elseif($payment->status === 'paid')
                            <span class="badge bg-success">Paid</span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        @endforeach
        </tbody>
    </table>
    {{ $payments->links() }}
</div>
@endsection 