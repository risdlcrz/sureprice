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
    <div class="d-flex justify-content-end align-items-center mb-3">
        @if(isset($userParty) && $userParty->banned)
            <button class="btn btn-primary" disabled title="You are banned and cannot create new projects."><i class="bi bi-plus-lg"></i> New Project</button>
        @else
            <a href="{{ route('projects.create') }}" class="btn btn-primary rounded-pill px-4 py-2 fs-5 shadow-sm">
                <i class="bi bi-plus-lg"></i> New Project
            </a>
        @endif
    </div>
    <form method="GET" action="{{ route('projects.index') }}" class="mb-4">
        <div class="input-group search-bar-container custom-search-bar" style="max-width: 600px; margin: 0 auto;">
            <span class="input-group-text bg-white border-0 ps-3" id="search-icon" style="border-radius: 32px 0 0 32px;">
                <i class="bi bi-search text-primary"></i>
            </span>
            <input type="text" name="q" class="form-control border-0 px-4 py-3 bg-white" placeholder="Search projects..." value="{{ request('q') }}" aria-label="Search projects" aria-describedby="search-icon" style="border-radius: 0 32px 32px 0; font-size: 1.15rem;">
            <button class="btn btn-primary px-4 py-2 ms-2 rounded-pill shadow-sm" type="submit" style="font-size: 1.1rem; font-weight: 500;">
                Search
            </button>
        </div>
    </form>
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
        <div class="d-flex align-items-center flex-wrap gap-2">
            <span class="fw-semibold me-2">Projects by Status:</span>
            <span class="badge bg-secondary">Proposed: {{ $projects->where('status', 'proposed')->count() }}</span>
            <span class="badge bg-info">Planning: {{ $projects->where('status', 'planning')->count() }}</span>
            <span class="badge bg-success">Approved: {{ $projects->where('status', 'approved')->count() }}</span>
            <span class="badge bg-primary">In Progress: {{ $projects->where('status', 'in_progress')->count() }}</span>
            <span class="badge bg-warning">On Hold: {{ $projects->where('status', 'on_hold')->count() }}</span>
            <span class="badge bg-success">Completed: {{ $projects->where('status', 'completed')->count() }}</span>
            <span class="badge bg-dark">Closed: {{ $projects->where('status', 'closed')->count() }}</span>
            <span class="badge bg-danger">Cancelled: {{ $projects->where('status', 'cancelled')->count() }}</span>
        </div>
    </div>
    @php $recentProjects = $projects->sortByDesc('created_at')->take(5); @endphp
    @if($recentProjects->count())
        <div class="card mb-3">
            <div class="card-header bg-white fw-semibold">Recent Projects</div>
            <div class="card-body p-2">
                <ul class="list-group list-group-flush">
                    @foreach($recentProjects as $project)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>{{ $project->name }}</span>
                            <span class="text-muted small">{{ $project->created_at->diffForHumans() }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif
    <div class="card projects-table-container">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table projects-table table-hover">
                    <thead>
                        <tr>
                            <th>Project #</th>
                            <th>Name</th>
                            <th>Contract</th>
                            <th>Project Manager</th>
                            <th>Client Rep.</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Status</th>
                            <th>Progress</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($projects as $project)
                        <tr>
                            <td>{{ $project->project_number }}</td>
                            <td>
                                <a href="{{ route('projects.show', $project) }}" class="text-decoration-none">
                                    {{ $project->name }}
                                </a>
                            </td>
                            <td>
                                <a href="{{ route('contracts.show', $project->contract) }}" class="text-decoration-none">
                                    {{ $project->contract->contract_number }}
                                </a>
                            </td>
                            <td>{{ $project->projectManager->name }}</td>
                            <td>{{ $project->clientRepresentative->name }}</td>
                            <td>{{ $project->start_date->format('M d, Y') }}</td>
                            <td>{{ $project->end_date->format('M d, Y') }}</td>
                            <td>
                                <span class="badge
                                    @if($project->status === 'proposed') bg-secondary
                                    @elseif($project->status === 'planning') bg-info
                                    @elseif($project->status === 'approved') bg-success
                                    @elseif($project->status === 'in_progress') bg-primary
                                    @elseif($project->status === 'on_hold') bg-warning
                                    @elseif($project->status === 'completed') bg-success
                                    @elseif($project->status === 'closed') bg-dark
                                    @elseif($project->status === 'cancelled') bg-danger
                                    @else bg-secondary @endif">
                                    {{ ucwords(str_replace('_', ' ', $project->status)) }}
                                </span>
                            </td>
                            <td>
                                <div class="progress rounded-pill" style="height: 16px;">
                                    <div class="progress-bar {{ $project->progress >= 100 ? 'bg-success' : 'bg-primary' }}" 
                                         role="progressbar" 
                                         @style("width: " . $project->progress . "%")
                                         aria-valuenow="{{ $project->progress }}" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                        {{ $project->progress }}%
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('projects.show', $project) }}" 
                                       class="btn btn-outline-primary rounded-3" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('projects.edit', $project) }}" 
                                       class="btn btn-outline-secondary rounded-3" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn btn-outline-danger rounded-3" 
                                            onclick="confirmDelete('{{ $project->id }}')"
                                            title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                                <form id="delete-form-{{ $project->id }}" 
                                      action="{{ route('projects.destroy', $project) }}" 
                                      method="POST" 
                                      style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center">No projects found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-end mt-3">
                {{ $projects->links() }}
            </div>
        </div>
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
        overflow: hidden;
        border: 1px solid #e3e8ee;
        background: #fff;
        box-shadow: 0 4px 24px rgba(56,189,248,0.07), 0 1.5px 6px rgba(0,0,0,0.03);
        transition: box-shadow 0.25s;
        padding: 8px 0;
        margin-bottom: 32px;
    }
    .projects-table {
        border-radius: 20px;
        overflow: hidden;
        background: #fff;
    }
    .projects-table th, .projects-table td {
        vertical-align: middle;
        padding: 18px 24px;
        font-size: 1.08rem;
        border: none;
        transition: background 0.22s, color 0.18s;
    }
    .projects-table th {
        background: #f7fafc;
        font-weight: 700;
        color: #1a7f4e;
        border-bottom: 2px solid #e3e8ee;
        letter-spacing: 0.01em;
    }
    .projects-table tbody tr:nth-child(even) {
        background: #f4f7fa;
    }
    .projects-table tbody tr:hover {
        background: #e0f2fe;
        box-shadow: 0 2px 8px rgba(56,189,248,0.10);
        z-index: 1;
        position: relative;
    }
    .projects-table .btn-group .btn {
        padding: 0.25rem 0.6rem;
        font-size: 1rem;
        border-radius: 8px;
        margin-right: 4px;
        transition: background 0.18s, color 0.18s;
    }
    .projects-table .btn-group .btn:last-child {
        margin-right: 0;
    }
    .projects-table .btn-outline-primary {
        border: 1px solid #38bdf8;
        color: #38bdf8;
        background: #e0f2fe;
    }
    .projects-table .btn-outline-primary:hover {
        background: #38bdf8;
        color: #fff;
    }
    .projects-table .btn-outline-secondary {
        border: 1px solid #a0aec0;
        color: #4a5568;
        background: #f7fafc;
    }
    .projects-table .btn-outline-secondary:hover {
        background: #a0aec0;
        color: #fff;
    }
    .projects-table .btn-outline-danger {
        border: 1px solid #ef4444;
        color: #ef4444;
        background: #fee2e2;
    }
    .projects-table .btn-outline-danger:hover {
        background: #ef4444;
        color: #fff;
    }
    .projects-table .badge {
        font-size: 0.95rem;
        border-radius: 8px;
        padding: 0.4em 0.8em;
    }
    .projects-table .progress {
        border-radius: 8px;
        height: 16px;
    }
    .projects-table .progress-bar {
        font-size: 0.95rem;
        font-weight: 600;
    }
    .projects-table .text-center {
        color: #a0aec0;
        font-size: 1.1rem;
        padding: 32px 0;
    }
    .custom-search-bar {
        box-shadow: 0 2px 12px rgba(56,189,248,0.10);
        background: #fff;
        border-radius: 32px;
        margin-bottom: 24px;
        align-items: center;
        padding: 0.25rem 0.5rem;
    }
    .custom-search-bar .form-control {
        border: none;
        box-shadow: none;
        background: #fff;
        font-size: 1.15rem;
        border-radius: 0 32px 32px 0;
    }
    .custom-search-bar .form-control:focus {
        box-shadow: none;
        background: #f7fafc;
    }
    .custom-search-bar .input-group-text {
        background: #fff;
        border: none;
        font-size: 1.3rem;
        border-radius: 32px 0 0 32px;
        padding-right: 0.5rem;
    }
    .custom-search-bar .btn-primary {
        border-radius: 32px;
        font-size: 1.1rem;
        font-weight: 500;
        box-shadow: 0 2px 8px rgba(56,189,248,0.13);
        transition: background 0.18s, color 0.18s;
    }
    .custom-search-bar .btn-primary:hover {
        background: #2563eb;
        color: #fff;
    }
</style>
@endpush
@endsection