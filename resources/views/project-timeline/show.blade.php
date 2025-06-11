@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Task Details</h3>
                    <div class="card-tools">
                        <a href="{{ route('project-timeline.index') }}" class="btn btn-default">
                            <i class="fas fa-arrow-left"></i> Back to Timeline
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h4>{{ $task->title }}</h4>
                            <p class="text-muted">
                                Contract: {{ $task->contract->contract_number }} - {{ $task->contract->title }}
                            </p>
                            
                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <h5>Task Information</h5>
                                    <table class="table table-bordered">
                                        <tr>
                                            <th style="width: 200px;">Status</th>
                                            <td>
                                                <span class="badge badge-{{ $task->status === 'completed' ? 'success' : ($task->status === 'in_progress' ? 'info' : ($task->status === 'delayed' ? 'danger' : 'secondary')) }}">
                                                    {{ ucfirst($task->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Priority</th>
                                            <td>
                                                <span class="badge badge-{{ $task->priority === 'high' ? 'danger' : ($task->priority === 'medium' ? 'warning' : 'success') }}">
                                                    {{ ucfirst($task->priority) }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Progress</th>
                                            <td>
                                                <div class="progress">
                                                    <div class="progress-bar" role="progressbar" style="width: {{ $task->progress }}%">
                                                        {{ $task->progress }}%
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Start Date</th>
                                            <td>{{ $task->start_date->format('M d, Y') }}</td>
                                        </tr>
                                        <tr>
                                            <th>End Date</th>
                                            <td>{{ $task->end_date->format('M d, Y') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Duration</th>
                                            <td>{{ $task->duration }} days</td>
                                        </tr>
                                        <tr>
                                            <th>Remaining Days</th>
                                            <td>
                                                @if($task->status === 'completed')
                                                    Completed
                                                @else
                                                    {{ $task->remaining_days }} days
                                                    @if($task->isOverdue())
                                                        <span class="text-danger">(Overdue by {{ $task->days_overdue }} days)</span>
                                                    @endif
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>

                                <div class="col-md-6">
                                    <h5>Assignment Details</h5>
                                    <table class="table table-bordered">
                                        <tr>
                                            <th style="width: 200px;">Room</th>
                                            <td>{{ $task->room ? $task->room->name : 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Scope Type</th>
                                            <td>{{ $task->scopeType ? $task->scopeType->name : 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Assigned To</th>
                                            <td>{{ $task->assignee ? $task->assignee->name : 'Unassigned' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Created By</th>
                                            <td>{{ $task->creator->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Created At</th>
                                            <td>{{ $task->created_at->format('M d, Y H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Last Updated</th>
                                            <td>{{ $task->updated_at->format('M d, Y H:i') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <h5>Description</h5>
                                    <div class="card">
                                        <div class="card-body">
                                            {{ $task->description ?: 'No description provided.' }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if($task->notes)
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <h5>Notes</h5>
                                    <div class="card">
                                        <div class="card-body">
                                            {{ $task->notes }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>

                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Actions</h5>
                                </div>
                                <div class="card-body">
                                    <a href="{{ route('project-timeline.edit', $task) }}" class="btn btn-primary btn-block mb-2">
                                        <i class="fas fa-edit"></i> Edit Task
                                    </a>

                                    @if($task->status !== 'completed')
                                    <form action="{{ route('project-timeline.update-progress', $task) }}" method="POST" class="mb-2">
                                        @csrf
                                        @method('PUT')
                                        <div class="form-group">
                                            <label>Update Progress</label>
                                            <div class="input-group">
                                                <input type="number" name="progress" class="form-control" min="0" max="100" value="{{ $task->progress }}">
                                                <div class="input-group-append">
                                                    <button type="submit" class="btn btn-success">Update</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                    @endif

                                    @if($task->status !== 'delayed' && $task->status !== 'completed')
                                    <form action="{{ route('project-timeline.mark-delayed', $task) }}" method="POST" class="mb-2">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-warning btn-block">
                                            <i class="fas fa-exclamation-triangle"></i> Mark as Delayed
                                        </button>
                                    </form>
                                    @endif

                                    <form action="{{ route('project-timeline.destroy', $task) }}" method="POST" class="mb-2" onsubmit="return confirm('Are you sure you want to delete this task?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-block">
                                            <i class="fas fa-trash"></i> Delete Task
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 