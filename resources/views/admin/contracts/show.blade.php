@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Contract Details</h1>
        <div>
            <a href="{{ route('contracts.edit', $contract->id) }}" class="btn btn-primary">
                <i class="bi bi-pencil"></i> Edit Contract
            </a>
            <a href="{{ route('contracts.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Contract Information -->
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Contract Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <p class="mb-1"><strong>Contract ID:</strong></p>
                            <p>{{ $contract->contract_id }}</p>
                        </div>
                        <div class="col-md-3">
                            <p class="mb-1"><strong>Status:</strong></p>
                            <p>
                                <span class="badge bg-{{ $contract->status === 'draft' ? 'warning' : ($contract->status === 'approved' ? 'success' : 'secondary') }}">
                                    {{ ucfirst($contract->status) }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-3">
                            <p class="mb-1"><strong>Start Date:</strong></p>
                            <p>{{ $contract->start_date->format('M d, Y') }}</p>
                        </div>
                        <div class="col-md-3">
                            <p class="mb-1"><strong>End Date:</strong></p>
                            <p>{{ $contract->end_date->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contractor Information -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Contractor Information</h5>
                </div>
                <div class="card-body">
                    <p><strong>Name:</strong> {{ $contract->contractor->name }}</p>
                    <p><strong>Company:</strong> {{ $contract->contractor->company_name }}</p>
                    <p><strong>Email:</strong> {{ $contract->contractor->email }}</p>
                    <p><strong>Phone:</strong> {{ $contract->contractor->phone }}</p>
                    <p><strong>Address:</strong><br>
                        {{ $contract->contractor->street }}<br>
                        {{ $contract->contractor->city }}, {{ $contract->contractor->state }} {{ $contract->contractor->postal }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Client Information -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Client Information</h5>
                </div>
                <div class="card-body">
                    <p><strong>Name:</strong> {{ $contract->client->name }}</p>
                    @if($contract->client->company_name)
                        <p><strong>Company:</strong> {{ $contract->client->company_name }}</p>
                    @endif
                    <p><strong>Email:</strong> {{ $contract->client->email }}</p>
                    <p><strong>Phone:</strong> {{ $contract->client->phone }}</p>
                    <p><strong>Address:</strong><br>
                        {{ $contract->client->street }}<br>
                        {{ $contract->client->city }}, {{ $contract->client->state }} {{ $contract->client->postal }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Property Information -->
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Property Information</h5>
                </div>
                <div class="card-body">
                    <p><strong>Address:</strong><br>
                        {{ $contract->property->street }}<br>
                        {{ $contract->property->city }}, {{ $contract->property->state }} {{ $contract->property->postal }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Contract Items -->
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Contract Items</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Material</th>
                                    <th>Supplier</th>
                                    <th>Quantity</th>
                                    <th>Amount</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($contract->items as $item)
                                    <tr>
                                        <td>{{ $item->material->name }}</td>
                                        <td>
                                            @if($item->supplier)
                                                {{ $item->supplier->name }}
                                                <br>
                                                <small class="text-muted">{{ $item->supplier->company_name }}</small>
                                            @else
                                                <span class="text-muted">No preferred supplier</span>
                                            @endif
                                        </td>
                                        <td>{{ number_format($item->quantity, 2) }}</td>
                                        <td>${{ number_format($item->amount, 2) }}</td>
                                        <td>${{ number_format($item->total, 2) }}</td>
                                    </tr>
                                @endforeach
                                <tr class="table-secondary">
                                    <td colspan="4" class="text-end"><strong>Total Amount:</strong></td>
                                    <td><strong>${{ number_format($contract->total_amount, 2) }}</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Scope of Work -->
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Scope of Work</h5>
                </div>
                <div class="card-body">
                    <p><strong>Work Types:</strong> {{ $contract->scope_of_work }}</p>
                    <p><strong>Description:</strong></p>
                    <p>{{ $contract->scope_description }}</p>
                </div>
            </div>
        </div>

        <!-- Contract Terms -->
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Contract Terms</h5>
                </div>
                <div class="card-body">
                    <p><strong>Jurisdiction:</strong> {{ $contract->jurisdiction }}</p>
                    <div class="mt-3">
                        {!! nl2br(e($contract->contract_terms)) !!}
                    </div>
                </div>
            </div>
        </div>

        <!-- Signatures -->
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Signatures</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Client Signature</h6>
                            @if($contract->client_signature)
                                <img src="{{ $contract->client_signature }}" alt="Client Signature" class="img-fluid mb-2" style="max-height: 100px;">
                                <p class="mb-0"><small>Signed by: {{ $contract->client->name }}</small></p>
                            @else
                                <p class="text-muted">Not signed yet</p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <h6>Contractor Signature</h6>
                            @if($contract->contractor_signature)
                                <img src="{{ $contract->contractor_signature }}" alt="Contractor Signature" class="img-fluid mb-2" style="max-height: 100px;">
                                <p class="mb-0"><small>Signed by: {{ $contract->contractor->name }}</small></p>
                            @else
                                <p class="text-muted">Not signed yet</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 