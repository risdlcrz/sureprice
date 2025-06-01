@extends('layouts.app')

@section('content')
    <div class="sidebar">
    @include('include.header_project')
    </div>

    <div class="content">
    <h1 class="text-center my-4">Project Dashboard</h1>

    <div class="container-fluid">
        <!-- Budget Overview Section -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">Budget Overview</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="budget-stat">
                                    <h6>Total Budget Allocated</h6>
                                    <h3>₱{{ number_format($totalBudget ?? 0, 2) }}</h3>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="budget-stat">
                                    <h6>Total Spent</h6>
                                    <h3>₱{{ number_format($totalSpent ?? 0, 2) }}</h3>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="budget-stat">
                                    <h6>Remaining Budget</h6>
                                    <h3>₱{{ number_format(($totalBudget ?? 0) - ($totalSpent ?? 0), 2) }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                @php
                                    $percentUsed = $totalBudget > 0 ? ($totalSpent / $totalBudget) * 100 : 0;
                                    $statusClass = $percentUsed > 90 ? 'danger' : ($percentUsed > 70 ? 'warning' : 'success');
                                @endphp
                                <div class="progress" style="height: 25px;">
                                    <div class="progress-bar bg-{{ $statusClass }}" 
                                         role="progressbar" 
                                         style="width: {{ min($percentUsed, 100) }}%"
                                         aria-valuenow="{{ $percentUsed }}" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                        {{ number_format($percentUsed, 1) }}% Used
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row row-cols-1 row-cols-md-3 g-4">
            <!-- Create Contract Card -->
            <div class="col">
                <div class="card h-100">
                    <img src="{{ asset('images/new-contract.jpg') }}" class="card-img-top" alt="Create Contract">
                    <div class="card-body">
                        <h5 class="card-title">Create Contract</h5>
                        <p class="card-text">Start a new contract and set up initial terms and conditions.</p>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('contracts.create') }}" class="btn btn-primary w-100">Create New Contract</a>
                    </div>
                </div>
            </div>

            <!-- View Contracts Card -->
            <div class="col">
                <div class="card h-100">
                    <img src="{{ asset('images/view-contracts.jpg') }}" class="card-img-top" alt="View Contracts">
                    <div class="card-body">
                        <h5 class="card-title">View Contracts</h5>
                        <p class="card-text">Access and manage existing contracts, track status and approvals.</p>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('contracts.index') }}" class="btn btn-secondary w-100">View All Contracts</a>
                    </div>
                </div>
            </div>

            <!-- Budget Allocation Card -->
            <div class="col">
                <div class="card h-100">
                    <img src="{{ asset('images/budget-allocation.jpg') }}" class="card-img-top" alt="Budget Allocation">
                    <div class="card-body">
                        <h5 class="card-title">Budget Allocation</h5>
                        <p class="card-text">View and manage detailed budget allocation and expenditures.</p>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('admin.budget-allocation') }}" class="btn btn-info w-100">View Budget Details</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Recent Contracts</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            @forelse($recentContracts ?? [] as $contract)
                            <a href="{{ route('contracts.show', $contract->id) }}" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">{{ $contract->project_name }}</h6>
                                    <small>{{ $contract->created_at->diffForHumans() }}</small>
                                </div>
                                <p class="mb-1">{{ $contract->client_name }}</p>
                                <small class="text-muted">Budget: ₱{{ number_format($contract->budget_allocation, 2) }}</small>
                            </a>
                            @empty
                            <div class="list-group-item">
                                <p class="mb-0">No recent contracts</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

@push('styles')
<style>
.budget-stat {
    text-align: center;
    padding: 1rem;
    background-color: #f8f9fa;
    border-radius: 0.5rem;
}

.budget-stat h6 {
    color: #6c757d;
    margin-bottom: 0.5rem;
}

.budget-stat h3 {
    color: #2c3e50;
    margin: 0;
}

.progress {
    border-radius: 0.5rem;
}

.progress-bar {
    font-size: 0.9rem;
    font-weight: 500;
}
</style>
@endpush
@endsection
