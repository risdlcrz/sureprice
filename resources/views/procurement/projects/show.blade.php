@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>{{ $project->name }}</h2>
            <p class="text-muted mb-0">Project #{{ $project->project_number }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('procurement.projects.tasks', $project) }}" class="btn btn-outline-primary">
                <i class="bi bi-list-task"></i> Tasks
            </a>
            <a href="{{ route('procurement.projects.procurement', $project) }}" class="btn btn-outline-success">
                <i class="bi bi-cart"></i> Procurement
            </a>
            <a href="{{ route('procurement.projects.analytics', $project) }}" class="btn btn-outline-info">
                <i class="bi bi-graph-up"></i> Analytics
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Project Overview -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Project Overview</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="fw-bold">Status</label>
                        <div>
                            <span class="badge bg-{{ $project->status === 'completed' ? 'success' : 
                                ($project->status === 'active' ? 'primary' : 
                                ($project->status === 'on_hold' ? 'warning' : 
                                ($project->status === 'cancelled' ? 'danger' : 'secondary'))) }}">
                                {{ ucfirst($project->status) }}
                            </span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Project Manager</label>
                        <div>{{ $project->projectManager->name }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Client Representative</label>
                        <div>{{ $project->clientRepresentative->name }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Timeline</label>
                        <div>{{ $project->start_date->format('M d, Y') }} - {{ $project->end_date->format('M d, Y') }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Budget</label>
                        <div>₱{{ number_format($project->budget, 2) }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Progress</label>
                        <div class="progress">
                            <div class="progress-bar {{ $project->progress >= 100 ? 'bg-success' : 'bg-primary' }}" 
                                role="progressbar" 
                                style="width: {{ $project->progress }}%"
                                aria-valuenow="{{ $project->progress }}" 
                                aria-valuemin="0" 
                                aria-valuemax="100">
                                {{ $project->progress }}%
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Procurement Activities -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Recent Procurement Activities</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Reference</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($project->contract->purchaseOrders->take(5) as $po)
                                <tr>
                                    <td>Purchase Order</td>
                                    <td>{{ $po->po_number }}</td>
                                    <td>{{ $po->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $po->status === 'completed' ? 'success' : 
                                            ($po->status === 'approved' ? 'primary' : 'secondary') }}">
                                            {{ ucfirst($po->status) }}
                                        </span>
                                    </td>
                                    <td>₱{{ number_format($po->total_amount, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">No recent procurement activities.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Recent Tasks -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Recent Tasks</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Task</th>
                                    <th>Assigned To</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                    <th>Progress</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($project->tasks->take(5) as $task)
                                <tr>
                                    <td>
                                        {{ $task->name }}
                                        @if($task->priority === 'high' || $task->priority === 'urgent')
                                            <span class="badge bg-danger">{{ ucfirst($task->priority) }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $task->assignee->name }}</td>
                                    <td>
                                        {{ $task->due_date->format('M d, Y') }}
                                        @if($task->isOverdue())
                                            <span class="badge bg-danger">Overdue</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $task->status === 'completed' ? 'success' : 
                                            ($task->status === 'in_progress' ? 'primary' : 
                                            ($task->status === 'on_hold' ? 'warning' : 'secondary')) }}">
                                            {{ ucfirst($task->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="progress">
                                            <div class="progress-bar {{ $task->progress >= 100 ? 'bg-success' : 'bg-primary' }}" 
                                                role="progressbar" 
                                                style="width: {{ $task->progress }}%"
                                                aria-valuenow="{{ $task->progress }}" 
                                                aria-valuemin="0" 
                                                aria-valuemax="100">
                                                {{ $task->progress }}%
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">No recent tasks.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 

@push('styles')
    @vite(['resources/css/procurement/projects/show.css'])
@endpush 