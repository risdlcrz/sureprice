@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>Project Analytics</h2>
            <p class="text-muted mb-0">{{ $project->name }} (#{{ $project->project_number }})</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('procurement.projects.show', $project) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Project
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Budget Overview -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Budget Overview</h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="border rounded p-3">
                                <h6 class="text-muted">Total Budget</h6>
                                <h3>₱{{ number_format($totalBudget, 2) }}</h3>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded p-3">
                                <h6 class="text-muted">Total Spent</h6>
                                <h3>₱{{ number_format($totalSpent, 2) }}</h3>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded p-3">
                                <h6 class="text-muted">Remaining Budget</h6>
                                <h3>₱{{ number_format($remainingBudget, 2) }}</h3>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded p-3">
                                <h6 class="text-muted">Budget Utilization</h6>
                                <h3>{{ number_format($budgetUtilization, 1) }}%</h3>
                                <div class="progress mt-2" style="height: 10px;">
                                    <div class="progress-bar {{ $budgetUtilization > 90 ? 'bg-danger' : 
                                        ($budgetUtilization > 70 ? 'bg-warning' : 'bg-success') }}" 
                                         role="progressbar" 
                                         @style(['width' => $budgetUtilization.'%'])></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Task Progress -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Task Progress</h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="border rounded p-3">
                                <h6 class="text-muted">Total Tasks</h6>
                                <h3>{{ $totalTasks }}</h3>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded p-3">
                                <h6 class="text-muted">Completed Tasks</h6>
                                <h3>{{ $completedTasks }}</h3>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded p-3">
                                <h6 class="text-muted">In Progress Tasks</h6>
                                <h3>{{ $inProgressTasks }}</h3>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded p-3">
                                <h6 class="text-muted">Task Completion</h6>
                                <h3>{{ number_format($taskCompletion, 1) }}%</h3>
                                <div class="progress mt-2" style="height: 10px;">
                                    <div class="progress-bar {{ $taskCompletion >= 100 ? 'bg-success' : 'bg-primary' }}" 
                                         role="progressbar" 
                                         @style(['width' => $taskCompletion.'%'])></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Purchase Requests Overview -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Purchase Requests Overview</h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="border rounded p-3">
                                <h6 class="text-muted">Total PRs</h6>
                                <h3>{{ $totalPurchaseRequests }}</h3>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded p-3">
                                <h6 class="text-muted">Pending PRs</h6>
                                <h3>{{ $pendingPurchaseRequests }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Purchase Orders Overview -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Purchase Orders Overview</h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="border rounded p-3">
                                <h6 class="text-muted">Total POs</h6>
                                <h3>{{ $totalPurchaseOrders }}</h3>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded p-3">
                                <h6 class="text-muted">Pending POs</h6>
                                <h3>{{ $pendingPurchaseOrders }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 