@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>Project Tasks</h2>
            <p class="text-muted mb-0">{{ $project->name }} (#{{ $project->project_number }})</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('procurement.projects.show', $project) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Project
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Task</th>
                            <th>Assigned To</th>
                            <th>Timeline</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Progress</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tasks as $task)
                        <tr>
                            <td>{{ $task->name }}</td>
                            <td>{{ $task->assignee->name }}</td>
                            <td>
                                {{ $task->start_date->format('M d, Y') }} - 
                                {{ $task->due_date->format('M d, Y') }}
                                @if($task->isOverdue())
                                    <br><span class="badge bg-danger">Overdue by {{ $task->daysOverdue }} days</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $task->priority === 'high' ? 'danger' : 
                                    ($task->priority === 'medium' ? 'warning' : 'success') }}">
                                    {{ ucfirst($task->priority) }}
                                </span>
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
                            <td>{{ $task->notes }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">No tasks found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-4">
                {{ $tasks->links() }}
            </div>
        </div>
    </div>
</div>
@endsection 

@push('styles')
    @vite(['resources/css/procurement/projects/tasks.css'])
@endpush 