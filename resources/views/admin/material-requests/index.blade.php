@extends('layouts.app')

@section('content')
<h1 class="h3 mb-4 text-gray-800 text-center" style="font-weight:700;color:#198754;letter-spacing:0.01em;">Material Requests</h1>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
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

@push('styles')
<style>
body {
    background: linear-gradient(135deg, #f8fafc 0%, #e9ecef 100%) !important;
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
.btn-primary {
    border-radius: 2rem;
    font-weight: 600;
    font-size: 1.1rem;
    background: linear-gradient(90deg, #38b6ff 0%, #2563eb 100%);
    border: none;
    box-shadow: 0 2px 8px #38b6ff33;
    transition: background 0.2s, color 0.2s, box-shadow 0.2s;
}
.btn-primary:hover {
    filter: brightness(1.08);
    box-shadow: 0 4px 16px #38b6ff33;
}
.small-box {
    border-radius: 1.1rem;
    box-shadow: 0 2px 12px rgba(44,62,80,0.06);
    color: #fff;
    padding: 1.2rem 1.5rem 1.2rem 1.5rem;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    min-height: 110px;
    position: relative;
    overflow: hidden;
    margin-bottom: 0.5rem;
}
.small-box.bg-info {
    background: linear-gradient(90deg, #38b6ff 0%, #00c6fb 100%);
}
.small-box.bg-success {
    background: linear-gradient(90deg, #43e97b 0%, #38f9d7 100%);
}
.small-box.bg-warning {
    background: linear-gradient(90deg, #ffc107 0%, #ffecb3 100%);
    color: #222;
}
.small-box.bg-primary {
    background: linear-gradient(90deg, #2563eb 0%, #38b6ff 100%);
}
.small-box .inner h3 {
    font-size: 2.1rem;
    font-weight: 700;
    margin-bottom: 0.2rem;
}
.small-box .inner p {
    font-size: 1.08rem;
    margin-bottom: 0;
    font-weight: 500;
}
.small-box .icon {
    position: absolute;
    right: 1.2rem;
    bottom: 1.2rem;
    font-size: 2.2rem;
    opacity: 0.18;
}
.input-group .form-control {
    border-radius: 1.2rem 0 0 1.2rem;
    border: 1px solid #d1d5db;
    background: #f8fafc;
    font-size: 1.08rem;
    padding: 0.85rem 1.1rem;
    transition: border-color 0.2s, box-shadow 0.2s;
}
.input-group .form-control:focus {
    border-color: #38b6ff;
    box-shadow: 0 0 0 2px #38b6ff33;
    background: #fff;
}
.input-group-append .btn {
    border-radius: 0 1.2rem 1.2rem 0;
    background: #38b6ff;
    color: #fff;
    font-size: 1.2rem;
    border: none;
    box-shadow: 0 2px 8px #38b6ff33;
    transition: background 0.2s, color 0.2s, box-shadow 0.2s;
}
.input-group-append .btn:hover {
    background: #2563eb;
    color: #fff;
}
.btn-group .btn {
    border-radius: 2rem !important;
    font-weight: 600;
    font-size: 1.08rem;
    border: none;
    box-shadow: 0 2px 8px #38b6ff33;
    transition: background 0.2s, color 0.2s, box-shadow 0.2s;
}
.btn-default {
    background: #e9ecef;
    color: #495057;
}
.btn-default:hover {
    background: #d1d5db;
    color: #222;
}
.table-responsive {
    border-radius: 1.1rem;
    overflow-x: auto;
    box-shadow: 0 4px 24px rgba(44,62,80,0.08), 0 1.5px 6px rgba(44,62,80,0.04);
    background: #fff;
    max-width: 100%;
}
.table {
    margin-bottom: 0;
    background: #fff;
    border-radius: 1.1rem;
    overflow: hidden;
    font-size: 0.97rem;
}
.table th, .table td {
    vertical-align: middle;
    padding: 0.7rem 0.5rem;
    border: none;
    background: #f8fafc;
    text-align: center;
}
.table thead th {
    background: #f1f5f9;
    font-weight: 700;
    color: #198754;
    border-bottom: 2px solid #e3e3e3;
    text-align: center;
}
.table-hover tbody tr:hover {
    background: #e3f2fd44;
}
.badge {
    font-size: 0.95em;
    font-weight: 600;
    letter-spacing: 0.01em;
    box-shadow: 0 1px 4px #ffc10722;
    border-radius: 0.7em;
    padding: 0.5em 1em;
}
.badge-warning {
    background-color: #ffc107;
    color: #212529;
}
.badge-success {
    background-color: #28a745;
    color: #fff;
}
.badge-secondary {
    background-color: #6c757d;
    color: #fff;
}
.btn-info {
    background: linear-gradient(90deg, #38b6ff 0%, #2563eb 100%);
    color: #fff;
    border: none;
}
.btn-info:hover {
    filter: brightness(1.08);
    color: #fff;
}
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