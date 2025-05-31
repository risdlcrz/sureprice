@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Contract Details</h1>
        <div>
            <a href="{{ route('contracts.edit', $contract->id) }}" class="btn btn-primary">
                <i class="bi bi-pencil"></i> Edit Contract
            </a>
            <a href="{{ route('contracts.download', $contract->id) }}" class="btn btn-success">
                <i class="bi bi-download"></i> Download PDF
            </a>
            <a href="{{ route('contracts.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Contract Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                <div class="col-md-6">
                    <p><strong>Contract ID:</strong> {{ $contract->contract_id }}</p>
                    <p><strong>Start Date:</strong> {{ $contract->start_date->format('F d, Y') }}</p>
                    <p><strong>End Date:</strong> {{ $contract->end_date->format('F d, Y') }}</p>
                        </div>
                <div class="col-md-6">
                    <p><strong>Status:</strong> <span class="badge bg-{{ $contract->status === 'draft' ? 'warning' : 'success' }}">{{ ucfirst($contract->status) }}</span></p>
                    <p><strong>Total Amount:</strong> ₱{{ number_format($contract->total_amount, 2) }}</p>
                    <p><strong>Budget Allocation:</strong> ₱{{ number_format($contract->budget_allocation, 2) }}</p>
                </div>
                </div>
            </div>
        </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Contractor Information</h5>
                </div>
                <div class="card-body">
                    <p><strong>Name:</strong> {{ $contract->contractor->name }}</p>
                    @if($contract->contractor->company_name)
                    <p><strong>Company:</strong> {{ $contract->contractor->company_name }}</p>
                    @endif
                    <p><strong>Address:</strong><br>
                        {{ $contract->contractor->street }}
                        @if($contract->contractor->unit)
                            Unit {{ $contract->contractor->unit }},<br>
                        @endif
                        Barangay {{ $contract->contractor->barangay }},<br>
                        {{ $contract->contractor->city }},<br>
                        {{ $contract->contractor->state }} {{ $contract->contractor->postal }}
                    </p>
                    <p><strong>Email:</strong> {{ $contract->contractor->email }}</p>
                    <p><strong>Phone:</strong> {{ $contract->contractor->phone }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Client Information</h5>
                </div>
                <div class="card-body">
                    <p><strong>Name:</strong> {{ $contract->client->name }}</p>
                    @if($contract->client->company_name)
                        <p><strong>Company:</strong> {{ $contract->client->company_name }}</p>
                    @endif
                    <p><strong>Address:</strong><br>
                        {{ $contract->client->street }}
                        @if($contract->client->unit)
                            Unit {{ $contract->client->unit }},<br>
                        @endif
                        Barangay {{ $contract->client->barangay }},<br>
                        {{ $contract->client->city }},<br>
                        {{ $contract->client->state }} {{ $contract->client->postal }}
                    </p>
                    <p><strong>Email:</strong> {{ $contract->client->email }}</p>
                    <p><strong>Phone:</strong> {{ $contract->client->phone }}</p>
                </div>
                </div>
            </div>
        </div>

    <div class="card mb-4">
                <div class="card-header">
            <h5 class="mb-0">Payment Details</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Payment Method:</strong> {{ ucfirst($contract->payment_method) }}</p>
                    <p><strong>Payment Terms:</strong><br>{{ $contract->payment_terms }}</p>
                </div>
                <div class="col-md-6">
                    @if($contract->payment_method === 'bank_transfer')
                        <p><strong>Bank Name:</strong> {{ $contract->bank_name }}</p>
                        <p><strong>Account Name:</strong> {{ $contract->bank_account_name }}</p>
                        <p><strong>Account Number:</strong> {{ $contract->bank_account_number }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Scope of Work</h5>
        </div>
        <div class="card-body">
            <p><strong>Categories:</strong> {{ $contract->scope_of_work }}</p>
            <p><strong>Description:</strong><br>{{ $contract->scope_description }}</p>
            </div>
        </div>

    <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Contract Items</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Material</th>
                                    <th>Supplier</th>
                                    <th>Quantity</th>
                            <th>Unit Price</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($contract->items as $item)
                                    <tr>
                                        <td>{{ $item->material->name }}</td>
                                <td>{{ $item->supplier ? $item->supplier->name : 'N/A' }}</td>
                                <td>{{ $item->quantity }} {{ $item->material->unit }}</td>
                                <td>₱{{ number_format($item->amount, 2) }}</td>
                                <td>₱{{ number_format($item->total, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                </div>
            </div>
        </div>

    <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Signatures</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                    <h6>Contractor's Signature</h6>
                    @if($contract->contractor_signature)
                        <img src="{{ Storage::url($contract->contractor_signature) }}" 
                            alt="Contractor Signature" 
                            class="img-fluid mb-3" 
                            style="max-height: 100px; border: 1px solid #ddd; padding: 10px;">
                        <p class="text-muted">Signed by: {{ $contract->contractor->name }}</p>
                            @else
                        <p class="text-muted">Not yet signed</p>
                            @endif
                        </div>
                        <div class="col-md-6">
                    <h6>Client's Signature</h6>
                    @if($contract->client_signature)
                        <img src="{{ Storage::url($contract->client_signature) }}" 
                            alt="Client Signature" 
                            class="img-fluid mb-3" 
                            style="max-height: 100px; border: 1px solid #ddd; padding: 10px;">
                        <p class="text-muted">Signed by: {{ $contract->client->name }}</p>
                            @else
                        <p class="text-muted">Not yet signed</p>
                            @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 