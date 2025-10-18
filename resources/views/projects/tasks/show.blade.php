@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>{{ $task->name }}</h2>
            <p class="text-muted mb-0">{{ $project->name }} (#{{ $project->project_number }})</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('projects.tasks.index', $project) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Tasks
            </a>
            <a href="{{ route('projects.tasks.edit', [$project, $task]) }}" class="btn btn-primary">
                <i class="bi bi-pencil"></i> Edit Task
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Task Details</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted">Status & Progress</h6>
                            <div class="mb-3">
                                @php
                                    $statusColors = [
                                        'pending' => 'warning',
                                        'in_progress' => 'primary',
                                        'completed' => 'success',
                                        'on_hold' => 'secondary',
                                        'delayed' => 'danger'
                                    ];
                                @endphp
                                <span class="badge bg-{{ $statusColors[$task->status] ?? 'secondary' }} fs-6">
                                    {{ ucwords(str_replace('_', ' ', $task->status)) }}
                                </span>
                            </div>
                            
                            <div class="mb-3">
                                <label class="fw-bold">Progress</label>
                                <div class="d-flex align-items-center mt-1">
                                    <div class="progress me-3" style="width: 200px; height: 10px;">
                                        <div class="progress-bar" role="progressbar" 
                                             style="width: {{ $task->progress }}%"
                                             aria-valuenow="{{ $task->progress }}" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100">
                                        </div>
                                    </div>
                                    <span class="fw-bold">{{ $task->progress }}%</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <h6 class="text-muted">Priority & Assignment</h6>
                            <div class="mb-3">
                                @php
                                    $priorityColors = [
                                        'low' => 'success',
                                        'medium' => 'warning',
                                        'high' => 'danger',
                                        'urgent' => 'dark'
                                    ];
                                @endphp
                                <span class="badge bg-{{ $priorityColors[$task->priority] ?? 'secondary' }} fs-6">
                                    {{ ucfirst($task->priority) }} Priority
                                </span>
                            </div>
                            
                            <div class="mb-3">
                                <label class="fw-bold">Assigned To</label>
                                <div class="mt-1">
                                    @if($task->assignee)
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-md bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                {{ substr($task->assignee->name, 0, 2) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $task->assignee->name }}</div>
                                                <div class="text-muted small">{{ $task->assignee->email }}</div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted">Not assigned</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($task->description)
                    <div class="mb-4">
                        <h6 class="text-muted">Description</h6>
                        <div class="bg-light p-3 rounded">
                            {{ $task->description }}
                        </div>
                    </div>
                    @endif

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted">Timeline</h6>
                            <div class="mb-2">
                                <label class="fw-bold">Start Date</label>
                                <div>{{ $task->start_date->format('F j, Y') }}</div>
                            </div>
                            <div class="mb-2">
                                <label class="fw-bold">Due Date</label>
                                <div>{{ $task->due_date->format('F j, Y') }}</div>
                                @if($task->isOverdue())
                                    <div class="text-danger small">
                                        <i class="bi bi-exclamation-triangle"></i>
                                        {{ $task->days_overdue }} days overdue
                                    </div>
                                @endif
                            </div>
                            <div class="mb-2">
                                <label class="fw-bold">Duration</label>
                                <div>{{ $task->duration }} days</div>
                            </div>
                            @if($task->status !== 'completed')
                            <div class="mb-2">
                                <label class="fw-bold">Days Remaining</label>
                                <div>{{ $task->remaining_days }} days</div>
                            </div>
                            @endif
                        </div>
                    </div>

                    @if($task->notes)
                    <div class="mb-4">
                        <h6 class="text-muted">Notes</h6>
                        <div class="bg-light p-3 rounded">
                            {{ $task->notes }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">Project Information</h6>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <strong>Project:</strong> {{ $project->name }}
                    </div>
                    <div class="mb-2">
                        <strong>Project Number:</strong> {{ $project->project_number }}
                    </div>
                    <div class="mb-2">
                        <strong>Status:</strong> 
                        <span class="badge bg-{{ $project->status == 'active' ? 'success' : ($project->status == 'pending' ? 'warning' : 'secondary') }}">
                            {{ ucfirst($project->status) }}
                        </span>
                    </div>
                    <div class="mb-2">
                        <strong>Overall Progress:</strong> {{ $project->progress }}%
                    </div>
                    @if($project->projectManager)
                    <div class="mb-2">
                        <strong>Project Manager:</strong> {{ $project->projectManager->name }}
                    </div>
                    @endif
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="card-title mb-0">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('projects.tasks.edit', [$project, $task]) }}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-pencil"></i> Edit Task
                        </a>
                        @if($task->status !== 'completed')
                        <form action="{{ route('projects.tasks.update', [$project, $task]) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="name" value="{{ $task->name }}">
                            <input type="hidden" name="description" value="{{ $task->description }}">
                            <input type="hidden" name="start_date" value="{{ $task->start_date->format('Y-m-d') }}">
                            <input type="hidden" name="due_date" value="{{ $task->due_date->format('Y-m-d') }}">
                            <input type="hidden" name="priority" value="{{ $task->priority }}">
                            <input type="hidden" name="assigned_to" value="{{ $task->assigned_to }}">
                            <input type="hidden" name="notes" value="{{ $task->notes }}">
                            <input type="hidden" name="status" value="completed">
                            <input type="hidden" name="progress" value="100">
                            <button type="submit" class="btn btn-outline-success btn-sm w-100">
                                <i class="bi bi-check-circle"></i> Mark Complete
                            </button>
                        </form>
                        @endif
                        <form action="{{ route('projects.tasks.destroy', [$project, $task]) }}" 
                              method="POST" class="d-inline"
                              onsubmit="return confirm('Are you sure you want to delete this task?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                                <i class="bi bi-trash"></i> Delete Task
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-md {
    width: 40px;
    height: 40px;
    font-size: 14px;
}
</style>
@endsection