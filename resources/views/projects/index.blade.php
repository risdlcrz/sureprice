@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Projects</h2>
        <a href="{{ route('projects.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> New Project
        </a>
    </div>

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
                                <span class="badge bg-{{ $project->status === 'completed' ? 'success' : 
                                    ($project->status === 'active' ? 'primary' : 
                                    ($project->status === 'on_hold' ? 'warning' : 
                                    ($project->status === 'cancelled' ? 'danger' : 'secondary'))) }}">
                                    {{ ucfirst($project->status) }}
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