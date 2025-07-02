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
    <form method="GET" action="{{ route('projects.index') }}" class="mb-4">
        <div class="input-group" style="max-width: 600px;">
            <input type="text" name="q" class="form-control" placeholder="Search projects..." value="{{ request('q') }}">
            <button class="btn btn-outline-primary" type="submit"><i class="bi bi-search"></i> Search</button>
        </div>
    </form>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="fw-bold" style="letter-spacing:1px;">Projects Dashboard</h1>
        @if(isset($userParty) && $userParty->banned)
            <button class="btn btn-primary" disabled title="You are banned and cannot create new projects."><i class="bi bi-plus-lg"></i> New Project</button>
        @else
            <a href="{{ route('projects.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> New Project
            </a>
        @endif
    </div>
    <div class="row mb-3 g-3">
        <div class="col-md-4 col-6">
            <div class="card text-center shadow-sm border-0 p-3">
                <div class="fw-bold fs-4 text-primary">{{ $projects->total() }}</div>
                <div class="text-muted">Total Projects</div>
            </div>
        </div>
        <div class="col-md-4 col-6">
            <div class="card text-center shadow-sm border-0 p-3">
                <div class="fw-bold fs-4 text-info">{{ $projects->where('status', 'in_progress')->count() }}</div>
                <div class="text-muted">In Progress</div>
            </div>
        </div>
        <div class="col-md-4 col-6">
            <div class="card text-center shadow-sm border-0 p-3">
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
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
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
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('projects.show', $project) }}" 
                                       class="btn btn-sm btn-outline-primary"
                                       title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('projects.edit', $project) }}" 
                                       class="btn btn-sm btn-outline-secondary"
                                       title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-danger" 
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
@endsection