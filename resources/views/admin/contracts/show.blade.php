@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="text-center mb-4">
        <h1>Contract Agreement</h1>
        <h4 class="text-muted">Contract ID: {{ $contract->contract_id }}</h4>
    </div>

    <div class="mb-3">
        <div class="d-flex gap-2 justify-content-center">
            <a href="{{ route('contracts.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Contracts
            </a>
            <a href="{{ route('contracts.create') }}" class="btn btn-primary">
                Create New
            </a>
            <a href="{{ route('contracts.edit', $contract->id) }}" class="btn btn-warning">
                Edit
            </a>
            <div class="dropdown d-inline">
                <button class="btn btn-secondary dropdown-toggle" type="button" id="statusDropdown" data-bs-toggle="dropdown">
                    Update Status
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#">Draft</a></li>
                    <li><a class="dropdown-item" href="#">Pending</a></li>
                    <li><a class="dropdown-item" href="#">Approved</a></li>
                    <li><a class="dropdown-item" href="#">Rejected</a></li>
                </ul>
            </div>
            <a href="#" class="btn btn-success">
                <i class="bi bi-download"></i> Download PDF
            </a>
            <form action="{{ route('contracts.destroy', $contract->id) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this contract?')">
                    Delete
                </button>
            </form>
        </div>
        <div class="text-center mt-2">
            <span class="badge bg-secondary">Status: {{ ucfirst($contract->status) }}</span>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Contractor Information</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Name:</strong> {{ $contract->contractor->name }}</p>
                    <p><strong>Company:</strong> {{ $contract->contractor->company_name }}</p>
                    <p><strong>Email:</strong> {{ $contract->contractor->email }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Address:</strong><br>
                        {{ $contract->contractor->street }}<br>
                        {{ $contract->contractor->city }}, {{ $contract->contractor->state }} {{ $contract->contractor->postal }}
                    </p>
                    <p><strong>Phone:</strong> {{ $contract->contractor->phone }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
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

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Scope of Work</h5>
        </div>
        <div class="card-body">
            <p><strong>Work Types:</strong> {{ $contract->scope_of_work }}</p>
            <p><strong>Description:</strong><br>{{ $contract->scope_description }}</p>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Project Period</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Start Date:</strong> {{ $contract->start_date->format('m/d/Y') }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>End Date:</strong> {{ $contract->end_date->format('m/d/Y') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Contract Terms</h5>
        </div>
        <div class="card-body">
            <p><strong>Total Amount:</strong> ${{ number_format($contract->total_amount, 2) }}</p>
            <p><strong>Terms and Conditions:</strong><br>{{ $contract->contract_terms }}</p>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Signatures</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Client Signature:</strong><br>
                        @if($contract->client_signature)
                            <span class="text-success">Signed</span>
                        @else
                            <span class="text-danger">Not Signed</span>
                        @endif
                    </p>
                </div>
                <div class="col-md-6">
                    <p><strong>Contractor Signature:</strong><br>
                        @if($contract->contractor_signature)
                            <span class="text-success">Signed</span>
                        @else
                            <span class="text-danger">Not Signed</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .badge {
        font-size: 1rem;
    }
</style>
@endpush
@endsection 