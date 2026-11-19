@extends('layouts.app')

@section('title', $project->name)

@section('content')
<div class="container-fluid">
    <x-page-header :title="$project->name">
        <span class="text-muted me-2">Project #{{ $project->project_number }}</span>
        <a href="{{ route('projects.edit', $project) }}" class="btn btn-primary btn-sm"><i class="bi bi-pencil"></i> Edit Project</a>
        <a href="{{ route('projects.tasks.create', $project) }}" class="btn btn-success btn-sm"><i class="bi bi-plus-lg"></i> Add Task</a>
    </x-page-header>

    <div class="row">
        <div class="col-md-4">@include('projects.partials.overview')</div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Tasks</span>
                    <a href="{{ route('projects.tasks.index', $project) }}" class="btn btn-sm btn-outline-primary">View All Tasks</a>
                </div>
                <div class="card-body">
                    @include('projects.partials.tasks-table', ['tasks' => $project->tasks, 'actions' => true, 'empty' => 'No tasks found.'])
                </div>
            </div>
        </div>
    </div>

    @if($project->status === 'completed' && auth()->id() === $project->clientRepresentative?->id)
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
