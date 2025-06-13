@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="mb-4">
        <h1 class="display-5 fw-bold">Welcome, {{ $company->contact_person }}</h1>
        <p class="text-muted">Project & Procurement Dashboard</p>
    </div>

    <!-- Stats Overview -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <h3 class="card-title h5">Total Contracts</h3>
                    <p class="display-6 text-primary mb-0">{{ $contracts->count() }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <h3 class="card-title h5">Active Projects</h3>
                    <p class="display-6 text-success mb-0">
                        {{ $contracts->whereIn('status', ['approved', 'in_progress'])->count() }}
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <h3 class="card-title h5">Pending Approvals</h3>
                    <p class="display-6 text-warning mb-0">
                        {{ $contracts->whereIn('status', ['draft', 'pending'])->count() }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mb-4">
        <h2 class="h4 mb-3">Quick Actions</h2>
        <div class="row">
            <div class="col-md-6 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <h3 class="card-title h5">New Contract</h3>
                        <p class="card-text text-muted">Create a new contract for your project</p>
                        <a href="{{ route('contracts.create') }}" class="btn btn-primary">Create Contract</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <h3 class="card-title h5">Payment History</h3>
                        <p class="card-text text-muted">View your payment history and status</p>
                        <a href="{{ route('client.payments') }}" class="btn btn-primary">View History</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Contracts -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="h4 mb-0">Your Recent Contracts</h2>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Contract ID</th>
                        <th>Title</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($contracts->take(5) as $contract)
                    <tr>
                        <td>{{ $contract->contract_number }}</td>
                        <td>{{ $contract->title }}</td>
                        <td>
                            <span class="badge {{ $contract->status === 'approved' || $contract->status === 'in_progress' ? 'bg-success' : 
                                   ($contract->status === 'draft' || $contract->status === 'pending' ? 'bg-warning' : 'bg-secondary') }}">
                                {{ ucfirst(str_replace('_', ' ', $contract->status)) }}
                            </span>
                        </td>
                        <td>{{ $contract->created_at->format('M d, Y') }}</td>
                        <td>
                            <a href="{{ route('contracts.show', $contract) }}" class="btn btn-sm btn-primary">
                                View Details
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">
                            No contracts found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($contracts->count() > 5)
        <div class="card-footer">
            <a href="{{ route('contracts.index') }}" class="btn btn-link">View all contracts →</a>
        </div>
        @endif
    </div>
</div>
@endsection 