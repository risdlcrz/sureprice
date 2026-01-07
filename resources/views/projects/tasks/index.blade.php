@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>Project Tasks</h2>
            <p class="text-muted mb-0">{{ $project->name }} (#{{ $project->project_number }})</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('projects.show', $project) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Project
            </a>
            <a href="{{ route('projects.tasks.create', $project) }}" class="btn btn-success">
                <i class="bi bi-plus-lg"></i> Add Task
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            @if($tasks->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Task Name</th>
                                <th>Assigned To</th>
                                <th>Timeline</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Progress</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tasks as $task)
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ $task->name }}</div>
                                        @if($task->description)
                                            <div class="text-muted small">{{ Str::limit($task->description, 60) }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        @if($task->assignee)
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                    {{ substr($task->assignee->name, 0, 1) }}
                                                </div>
                                                <div>{{ $task->assignee->name }}</div>
                                            </div>
                                        @else
                                            <span class="text-muted">Unassigned</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="small">
                                            <div><strong>Start:</strong> {{ $task->start_date->format('M d, Y') }}</div>
                                            <div><strong>Due:</strong> {{ $task->due_date->format('M d, Y') }}</div>
                                            @if($task->isOverdue())
                                                <div class="text-danger"><small>{{ $task->days_overdue }} days overdue</small></div>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $priorityColors = [
                                                'low' => 'success',
                                                'medium' => 'warning',
                                                'high' => 'danger',
                                                'urgent' => 'dark'
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $priorityColors[$task->priority] ?? 'secondary' }}">
                                            {{ ucfirst($task->priority) }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'pending' => 'warning',
                                                'in_progress' => 'primary',
                                                'completed' => 'success',
                                                'on_hold' => 'secondary',
                                                'delayed' => 'danger'
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $statusColors[$task->status] ?? 'secondary' }}">
                                            {{ ucwords(str_replace('_', ' ', $task->status)) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress me-2" style="width: 60px; height: 8px;">
                                                <div class="progress-bar" role="progressbar" 
                                                     style="width: {{ $task->progress }}%"
                                                     aria-valuenow="{{ $task->progress }}" 
                                                     aria-valuemin="0" 
                                                     aria-valuemax="100">
                                                </div>
                                            </div>
                                            <span class="small">{{ $task->progress }}%</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('projects.tasks.show', [$project, $task]) }}" 
                                               class="btn btn-outline-primary" title="View">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('projects.tasks.edit', [$project, $task]) }}" 
                                               class="btn btn-outline-secondary" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ route('projects.tasks.destroy', [$project, $task]) }}" 
                                                  method="POST" class="d-inline"
                                                  onsubmit="return confirm('Are you sure you want to delete this task?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger" title="Delete">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $tasks->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-list-task display-1 text-muted"></i>
                    <h4 class="mt-3">No tasks found</h4>
                    <p class="text-muted">There are no tasks for this project yet.</p>
                    <a href="{{ route('projects.tasks.create', $project) }}" class="btn btn-primary">
                        <i class="bi bi-plus-lg"></i> Create First Task
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.avatar-sm {
    width: 32px;
    height: 32px;
    font-size: 14px;
}
</style>
@endsection