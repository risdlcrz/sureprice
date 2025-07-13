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

@push('scripts')
<script>
function confirmDelete(projectId) {
    if (confirm('Are you sure you want to delete this project?')) {
        document.getElementById('delete-form-' + projectId).submit();
    }
}
</script>
@endpush

@push('styles')
<style>
    .projects-table-container {
        border-radius: 20px;
        box-shadow: 0 4px 24px rgba(44,62,80,0.08), 0 1.5px 6px rgba(44,62,80,0.04);
        border: none;
        margin-bottom: 2rem;
    }
    .projects-table th, .projects-table td {
        vertical-align: middle;
        text-align: center;
        padding: 0.9rem 0.5rem;
        border: none;
        background: #f8fafc;
    }
    .projects-table thead th {
        background: #e8f5e9;
        font-weight: 700;
        color: #198754;
        border-bottom: 2px solid #e3e3e3;
    }
    .projects-table tbody tr:hover {
        background: #e3f2fd44;
    }
    .projects-table .progress {
        background: #e9ecef;
        height: 16px;
        border-radius: 8px;
        box-shadow: none;
    }
    .projects-table .progress-bar {
        font-size: 0.95em;
        font-weight: 600;
    }
    .card {
        border-radius: 1.25rem;
        box-shadow: 0 4px 24px rgba(44,62,80,0.08), 0 1.5px 6px rgba(44,62,80,0.04);
        border: none;
        margin-bottom: 2rem;
    }
    .card-header {
        background: #fff;
        border-radius: 1.25rem 1.25rem 0 0;
        border-bottom: none;
        padding: 1.5rem 2rem 1rem 2rem;
        display: flex;
        justify-content: flex-start;
        align-items: center;
        gap: 1rem;
    }
    .badge {
        font-size: 0.85em;
        padding: 0.4em 0.9em;
        border-radius: 1em;
        font-weight: 600;
        letter-spacing: 0.01em;
    }
    .btn-group .btn {
        margin-right: 0.25rem;
    }
    .btn-group .btn:last-child {
        margin-right: 0;
    }
    .progress {
        background: #e9ecef;
        height: 12px;
        border-radius: 8px;
        box-shadow: none;
    }
    .progress-bar {
        font-size: 0.85em;
        font-weight: 600;
    }
</style>
@endpush
@endsection