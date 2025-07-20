@extends('layouts.app')

@section('content')
<div class="container-fluid">
    @if(isset($userParty) && $userParty->banned)
        <div class="alert alert-danger mb-4">
            <strong>You have been banned from the system.</strong>
            @if($userParty->ban_reason)
                <br>Reason: {{ $userParty->ban_reason }}
            @endif
            <br>Please contact support for more information.
        </div>
    @endif
    <h1 class="h3 mb-4 text-gray-800 text-center" style="font-weight:700;color:#198754;letter-spacing:0.01em;">Projects Dashboard</h1>
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <form method="GET" action="{{ route('projects.index') }}" class="flex-grow-1 me-3" style="max-width: 600px;">
            <div class="input-group search-bar-container custom-search-bar">
                <span class="input-group-text bg-white border-0 ps-3" id="search-icon" style="border-radius: 32px 0 0 32px;">
                    <i class="bi bi-search text-primary"></i>
                </span>
                <input type="text" name="q" class="form-control border-0 px-4 py-3 bg-white" placeholder="Search projects..." value="{{ request('q') }}" aria-label="Search projects" aria-describedby="search-icon" style="border-radius: 0 32px 32px 0; font-size: 1.15rem;">
                <button class="btn btn-primary px-4 py-2 ms-2 rounded-pill shadow-sm" type="submit" style="font-size: 1.1rem; font-weight: 500;">
                    Search
                </button>
            </div>
        </form>
        @if(isset($userParty) && $userParty->banned)
            <button class="btn btn-primary" disabled title="You are banned and cannot create new projects."><i class="bi bi-plus-lg"></i> New Project</button>
        @else
            <a href="{{ route('projects.create') }}" class="btn btn-primary rounded-pill px-4 py-2 fs-5 shadow-sm">
                <i class="bi bi-plus-lg"></i> New Project
            </a>
        @endif
    </div>
    <div class="row mb-3 g-3">
        <div class="col-md-4 col-6">
            <div class="card text-center shadow-sm border-0 p-3 rounded-4">
                <div class="fw-bold fs-4 text-primary">{{ $projects->total() }}</div>
                <div class="text-muted">Total Projects</div>
            </div>
        </div>
        <div class="col-md-4 col-6">
            <div class="card text-center shadow-sm border-0 p-3 rounded-4">
                <div class="fw-bold fs-4 text-info">{{ $projects->where('status', 'in_progress')->count() }}</div>
                <div class="text-muted">In Progress</div>
            </div>
        </div>
        <div class="col-md-4 col-6">
            <div class="card text-center shadow-sm border-0 p-3 rounded-4">
                <div class="fw-bold fs-4 text-success">{{ $projects->where('status', 'completed')->count() }}</div>
                <div class="text-muted">Completed</div>
            </div>
        </div>
    </div>
    <div class="mb-3">
        <!-- Status summary bar removed -->
    </div>
    <div class="row g-4">
        @forelse($projects as $project)
            <div class="col-md-4 col-lg-3">
                <div class="card shadow-sm border-0 rounded-4 h-100">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center mb-2">
                                <span class="fw-bold text-primary me-2">#{{ $project->project_number }}</span>
                                <span class="badge ms-auto @if($project->status === 'proposed') bg-secondary
                                    @elseif($project->status === 'planning') bg-info
                                    @elseif($project->status === 'approved') bg-success
                                    @elseif($project->status === 'in_progress') bg-primary
                                    @elseif($project->status === 'on_hold') bg-warning
                                    @elseif($project->status === 'completed') bg-success
                                    @elseif($project->status === 'closed') bg-dark
                                    @elseif($project->status === 'cancelled') bg-danger
                                    @else bg-secondary @endif" style="font-size:0.85em;">{{ ucwords(str_replace('_', ' ', $project->status)) }}</span>
                            </div>
                            <div class="mb-1">
                                <span class="fw-semibold">Client:</span> {{ $project->contract->client->company_name ?? $project->contract->client->name ?? 'N/A' }}
                            </div>
                            <div class="mb-1">
                                <span class="fw-semibold">Name:</span> {{ $project->name }}
                            </div>
                            <div class="mb-1">
                                <span class="fw-semibold">Start:</span> {{ $project->start_date->format('M d, Y') }}
                            </div>
                            <div class="mb-1">
                                <span class="fw-semibold">End:</span> {{ $project->end_date->format('M d, Y') }}
                            </div>
                        </div>
                        <div class="mt-3 d-flex justify-content-between align-items-center">
                            <a href="{{ route('projects.show', $project) }}" class="btn btn-outline-primary rounded-pill px-3 py-1">View</a>
                            <div class="progress rounded-pill flex-grow-1 ms-2" style="height: 12px; min-width: 80px;">
                                <div class="progress-bar {{ $project->progress >= 100 ? 'bg-success' : 'bg-primary' }}" role="progressbar" style="width: {{ $project->progress }}%" aria-valuenow="{{ $project->progress }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="d-flex flex-column align-items-center justify-content-center py-5">
                    <i class="fas fa-folder-open fa-3x mb-3 text-muted"></i>
                    <span class="fw-semibold text-muted" style="font-size:1.2em;">No projects found.</span>
                </div>
            </div>
        @endforelse
    </div>
    <div class="d-flex justify-content-end mt-3">
        {{ $projects->links() }}
    </div>
</div>

@push('styles')
{{-- Extracted to resources/css/projects-index.css --}}
@vite('resources/css/projects-index.css')
@endpush
@push('scripts')
{{-- Extracted to resources/js/projects-index.js --}}
@vite('resources/js/projects-index.js')
@endpush
@endsection