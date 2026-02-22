@extends('layouts.app')

@section('content')
<h1 class="h3 mb-4 text-gray-800 text-center" style="font-weight:700;color:#198754;letter-spacing:0.01em;">Material Requests</h1>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-tools">
                        @if(auth()->user()->hasRole('manager'))
                        <a href="{{ route('material-requests.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> New Material Request
                        </a>
                        @endif
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
                        <table class="table table-hover" id="materialRequestsTable">
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

@push('styles')
<style>
body { background: #f5f6f8 !important; }
.card {
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.06);
    border: 1px solid #e8eaed;
    margin-bottom: 1.5rem;
}
.card-header {
    background: #fff;
    border-bottom: 1px solid #e8eaed;
    padding: 1rem 1.25rem;
    display: flex;
    justify-content: flex-start;
    align-items: center;
}
.btn-primary {
    background: #198754 !important;
    background-image: none !important;
    border: none;
    border-radius: 8px;
    font-weight: 600;
}
.btn-primary:hover { background: #157347 !important; }
.small-box {
    border-radius: 10px;
    color: #fff;
    padding: 1rem 1.25rem;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    min-height: 100px;
    position: relative;
    overflow: hidden;
    border: 1px solid rgba(0,0,0,0.06);
}
.small-box.bg-info {
    background: #0dcaf0 !important;
}
.small-box.bg-success {
    background: #198754 !important;
}
.small-box.bg-warning {
    background: #ffc107 !important;
    color: #1f2937;
}
.small-box.bg-primary {
    background: #0d6efd !important;
}
.small-box .inner h3 { font-size: 1.75rem; font-weight: 700; margin-bottom: 0.2rem; }
.small-box .inner p { font-size: 0.9375rem; margin-bottom: 0; font-weight: 500; }
.small-box .icon {
    position: absolute;
    right: 1rem;
    bottom: 1rem;
    font-size: 1.75rem;
    opacity: 0.25;
}
.input-group .form-control {
    border-radius: 8px 0 0 8px;
    border: 1px solid #e8eaed;
    font-size: 0.9375rem;
}
.input-group .form-control:focus {
    border-color: #198754;
    box-shadow: 0 0 0 2px rgba(25, 135, 84, 0.15);
}
.input-group-append .btn {
    border-radius: 0 8px 8px 0;
    background: #198754 !important;
    color: #fff;
    border: none;
}
.input-group-append .btn:hover { background: #157347 !important; }
.btn-default {
    background: #f5f6f8;
    color: #1f2937;
    border: 1px solid #e8eaed;
    border-radius: 8px;
}
.btn-default:hover { background: #e8eaed; }
.table-responsive {
    border-radius: 8px;
    overflow: hidden;
    border: 1px solid #e8eaed;
}
#materialRequestsTable thead th {
    background: #f5f6f8;
    color: #1f2937;
    font-weight: 600;
    font-size: 0.8125rem;
    padding: 0.75rem 1rem;
    border-bottom: 1px solid #e8eaed;
}
#materialRequestsTable tbody td {
    padding: 0.75rem 1rem;
    border-bottom: 1px solid #e8eaed;
    font-size: 0.9375rem;
    background: #fff;
}
#materialRequestsTable tbody tr:last-child td { border-bottom: none; }
#materialRequestsTable tbody tr:hover { background: rgba(25, 135, 84, 0.04); }
.badge { border-radius: 6px; font-weight: 500; }
.btn-info {
    background: #198754 !important;
    background-image: none !important;
    color: #fff;
    border: none;
    border-radius: 6px;
}
.btn-info:hover { background: #157347 !important; color: #fff; }
@media (max-width: 991.98px) {
    .card-header {
        padding: 1rem 0.5rem 0.5rem 0.5rem;
    }
    .card {
        padding: 0.5rem;
    }
    .table th, .table td {
        padding: 0.4rem 0.2rem;
        font-size: 0.93rem;
    }
    .small-box {
        padding: 0.7rem 0.7rem 0.7rem 0.7rem;
        min-height: 80px;
    }
    .small-box .icon {
        font-size: 1.5rem;
        right: 0.7rem;
        bottom: 0.7rem;
    }
}
</style>
@endpush

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