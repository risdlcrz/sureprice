@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Material Requests</h3>
                    <div class="card-tools">
                        <a href="{{ route('material-requests.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> New Material Request
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3 mb-3">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3>{{ $materialRequests->where('status', 'pending')->count() }}</h3>
                                    <p>Pending Requests</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3>{{ $materialRequests->where('status', 'completed')->count() }}</h3>
                                    <p>Completed Requests</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-check"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3>{{ $materialRequests->where('status', 'cancelled')->count() }}</h3>
                                    <p>Cancelled Requests</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-times"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="small-box bg-primary">
                                <div class="inner">
                                    <h3>{{ $materialRequests->where('is_project_related', true)->count() }}</h3>
                                    <p>Project Related</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-project-diagram"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Search and Filter -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="input-group">
                                <input type="text" id="searchInput" class="form-control" placeholder="Search material requests...">
                                <div class="input-group-append">
                                    <button class="btn btn-default">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 text-right">
                            <div class="btn-group">
                                <button type="button" class="btn btn-default" data-toggle="dropdown">
                                    <i class="fas fa-filter"></i> Filter
                                </button>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a class="dropdown-item" href="{{ route('material-requests.index', ['status' => 'pending']) }}">Pending</a>
                                    <a class="dropdown-item" href="{{ route('material-requests.index', ['status' => 'completed']) }}">Completed</a>
                                    <a class="dropdown-item" href="{{ route('material-requests.index', ['status' => 'cancelled']) }}">Cancelled</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="{{ route('material-requests.index', ['type' => 'project']) }}">Project Related</a>
                                    <a class="dropdown-item" href="{{ route('material-requests.index', ['type' => 'standalone']) }}">Standalone</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Material Requests Table -->
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered table-hover table-striped" id="materialRequestsTable">
                            <thead>
                                <tr>
                                    <th>Request #</th>
                                    <th>Contract</th>
                                    <th>Requested By</th>
                                    <th>Status</th>
                                    <th>Created Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($materialRequests as $request)
                                <tr>
                                    <td>MR-{{ $request->id }}</td>
                                    <td>
                                        @if($request->contract)
                                            <a href="{{ route('contracts.show', $request->contract) }}">
                                                {{ $request->contract->contract_number ?? '[N/A]' }} - {{ $request->contract->title ?? '[N/A]' }}
                                            </a>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>{{ $request->user->name ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge badge-{{ $request->status === 'pending' ? 'warning' : ($request->status === 'completed' ? 'success' : 'secondary') }}">
                                            {{ ucfirst($request->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $request->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <a href="{{ route('material-requests.show', $request) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $materialRequests->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Search functionality
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const input = this.value.toLowerCase();
            const rows = document.querySelectorAll('#materialRequestsTable tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(input) ? '' : 'none';
            });
        });
    }
});
</script>
@endpush 
@endsection 