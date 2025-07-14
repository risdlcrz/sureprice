@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>{{ $project->name }}</h2>
            <p class="text-muted mb-0">Project #{{ $project->project_number }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('projects.edit', $project) }}" class="btn btn-primary">
                <i class="bi bi-pencil"></i> Edit Project
            </a>
            <a href="{{ route('projects.tasks.create', $project) }}" class="btn btn-success">
                <i class="bi bi-plus-lg"></i> Add Task
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
                        <label class="fw-bold">Progress</label>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar {{ $project->progress >= 100 ? 'bg-success' : 'bg-primary' }}" 
                                 role="progressbar" 
                                 @style("width: " . $project->progress . "%")
                                 aria-valuenow="{{ $project->progress }}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                                {{ $project->progress }}%
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">Contract</label>
                        <div>
                            <a href="{{ route('contracts.show', $project->contract) }}" class="text-decoration-none">
                                {{ $project->contract->contract_number }}
                            </a>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">Budget</label>
                        <div>₱{{ number_format($project->budget, 2) }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">Timeline</label>
                        <div>{{ $project->start_date->format('M d, Y') }} - {{ $project->end_date->format('M d, Y') }}</div>
                        <small class="text-muted">
                            {{ $project->start_date->diffForHumans($project->end_date, true) }} duration
                        </small>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">Project Manager</label>
                        <div>{{ $project->projectManager->name }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">Client Representative</label>
                        <div>{{ $project->clientRepresentative->name }}</div>
                    </div>

                    @if($project->description)
                    <div class="mb-3">
                        <label class="fw-bold">Description</label>
                        <div>{{ $project->description }}</div>
                    </div>
                    @endif

                    @if($project->notes)
                    <div class="mb-3">
                        <label class="fw-bold">Notes</label>
                        <div>{{ $project->notes }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Tasks List -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Tasks</h5>
                    <a href="{{ route('projects.tasks.index', $project) }}" class="btn btn-sm btn-outline-primary">
                        View All Tasks
                    </a>
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
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($project->tasks as $task)
                                <tr>
                                    <td>
                                        <a href="{{ route('projects.tasks.show', [$project, $task]) }}" class="text-decoration-none">
                                            {{ $task->name }}
                                        </a>
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
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar {{ $task->progress >= 100 ? 'bg-success' : 'bg-primary' }}" 
                                                 role="progressbar" 
                                                 @style("width: " . $task->progress . "%")
                                                 aria-valuenow="{{ $task->progress }}" 
                                                 aria-valuemin="0" 
                                                 aria-valuemax="100">
                                                {{ $task->progress }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('projects.tasks.show', [$project, $task]) }}" 
                                               class="btn btn-sm btn-outline-primary"
                                               title="View">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('projects.tasks.edit', [$project, $task]) }}" 
                                               class="btn btn-sm btn-outline-secondary"
                                               title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">No tasks found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($project->status === 'completed' && auth()->id() === $project->clientRepresentative->id)
        @php $existingFeedback = \App\Models\ProjectFeedback::where('project_id', $project->id)->where('client_id', auth()->id())->first(); @endphp
        @if(!$existingFeedback)
            <div class="alert alert-info mt-4">
                <strong>Project Completed!</strong> Please <a href="{{ route('projects.feedback', $project) }}" class="btn btn-sm btn-primary ms-2">Leave Feedback</a>
            </div>
        @else
            <div class="alert alert-success mt-4">
                <strong>Thank you for your feedback!</strong><br>
                <span>Rating: {{ $existingFeedback->rating }} / 5</span><br>
                <span>Comments: {{ $existingFeedback->comments }}</span>
            </div>
        @endif
    @endif
</div>
@endsection