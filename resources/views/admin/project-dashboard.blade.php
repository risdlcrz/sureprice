@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Project Dashboard</h2>
        <div class="d-flex gap-2">
            <a href="{{ route('projects.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> New Project
            </a>
            <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-list"></i> View All Projects
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Projects</h5>
                    <h2 class="mb-0">{{ $totalProjects }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Active Projects</h5>
                    <h2 class="mb-0">{{ $activeProjects }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5 class="card-title">On Hold</h5>
                    <h2 class="mb-0">{{ $onHoldProjects }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Completed</h5>
                    <h2 class="mb-0">{{ $completedProjects }}</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Project Status Distribution -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Projects by Status</h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        @foreach($projectsByStatus as $status => $count)
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            {{ ucfirst($status) }}
                            <span class="badge bg-{{ $status === 'completed' ? 'success' : 
                                ($status === 'active' ? 'primary' : 
                                ($status === 'on_hold' ? 'warning' : 
                                ($status === 'cancelled' ? 'danger' : 'secondary'))) }} rounded-pill">
                                {{ $count }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Projects -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Recent Projects</h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        @forelse($recentProjects as $project)
                        <a href="{{ route('projects.show', $project) }}" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">{{ $project->name }}</h6>
                                <small class="text-muted">{{ $project->created_at->diffForHumans() }}</small>
                            </div>
                            <p class="mb-1">Contract: {{ $project->contract->contract_number }}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">PM: {{ $project->projectManager->name }}</small>
                                <span class="badge bg-{{ $project->status === 'completed' ? 'success' : 
                                    ($project->status === 'active' ? 'primary' : 
                                    ($project->status === 'on_hold' ? 'warning' : 
                                    ($project->status === 'cancelled' ? 'danger' : 'secondary'))) }}">
                                    {{ ucfirst($project->status) }}
                                </span>
                            </div>
                        </a>
                        @empty
                        <div class="list-group-item">
                            <p class="mb-0">No recent projects found.</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
