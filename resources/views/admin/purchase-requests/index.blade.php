@extends('layouts.app')

@section('content')
<h1 class="h3 mb-4 text-gray-800 text-center" style="font-weight:700;color:#198754;letter-spacing:0.01em;">Purchase Requests</h1>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-end align-items-center">
                    <div class="card-tools">
                        <a href="{{ route('purchase-requests.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> New Purchase Request
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
                    <div class="stats-row">
                        <div class="stats-card bg-info">
                            <div class="d-flex align-items-center mb-2">
                                <span class="fs-2 fw-bold me-2">{{ $purchaseRequests->where('status', 'pending')->count() }}</span>
                                <span class="icon"><i class="fas fa-clock"></i></span>
                            </div>
                            <div class="fw-semibold">Pending Requests</div>
                        </div>
                        <div class="stats-card bg-success">
                            <div class="d-flex align-items-center mb-2">
                                <span class="fs-2 fw-bold me-2">{{ $purchaseRequests->where('status', 'approved')->count() }}</span>
                                <span class="icon"><i class="fas fa-check"></i></span>
                            </div>
                            <div class="fw-semibold">Approved Requests</div>
                        </div>
                        <div class="stats-card bg-warning">
                            <div class="d-flex align-items-center mb-2">
                                <span class="fs-2 fw-bold me-2">{{ $purchaseRequests->where('status', 'rejected')->count() }}</span>
                                <span class="icon"><i class="fas fa-times"></i></span>
                            </div>
                            <div class="fw-semibold">Rejected Requests</div>
                        </div>
                        <div class="stats-card bg-primary">
                            <div class="d-flex align-items-center mb-2">
                                <span class="fs-2 fw-bold me-2">{{ $purchaseRequests->where('is_project_related', true)->count() }}</span>
                                <span class="icon"><i class="fas fa-project-diagram"></i></span>
                            </div>
                            <div class="fw-semibold">Project Related</div>
                        </div>
                    </div>

                    <!-- Search and Filter -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="input-group">
                                <input type="text" id="searchInput" class="form-control" placeholder="Search purchase requests...">
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
                                    <a class="dropdown-item" href="{{ route('purchase-requests.index', ['status' => 'pending']) }}">Pending</a>
                                    <a class="dropdown-item" href="{{ route('purchase-requests.index', ['status' => 'approved']) }}">Approved</a>
                                    <a class="dropdown-item" href="{{ route('purchase-requests.index', ['status' => 'rejected']) }}">Rejected</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="{{ route('purchase-requests.index', ['type' => 'project']) }}">Project Related</a>
                                    <a class="dropdown-item" href="{{ route('purchase-requests.index', ['type' => 'standalone']) }}">Standalone</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Purchase Requests Table -->
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered table-hover table-striped" id="purchaseRequestsTable">
                            <thead>
                                <tr>
                                    <th>Request #</th>
                                    <th>Project/Contract</th>
                                    <th>Requested By</th>
                                    <th>Total Amount</th>
                                    <th>Status</th>
                                    <th>Created Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($purchaseRequests as $request)
                                <tr>
                                    <td>{{ $request->request_number }}</td>
                                    <td>
                                        @if($request->is_project_related)
                                            @if($request->contract)
                                                <a href="{{ route('contracts.show', $request->contract) }}">
                                                    {{ $request->contract->contract_number ?? '[No Contract Number]' }} - {{ $request->contract->name ?? $request->contract->title ?? '[No Contract Name]' }}
                                                </a>
                                            @else
                                                <span class="text-muted">Project Related</span>
                                            @endif
                                        @else
                                            <span class="text-muted">Standalone</span>
                                        @endif
                                    </td>
                                    <td>{{ $request->requestedBy->name }}</td>
                                    <td>{{ number_format($request->total_amount, 2) }}</td>
                                    <td>
                                        <span class="badge badge-{{ $request->status === 'pending' ? 'warning' : ($request->status === 'approved' ? 'success' : 'danger') }}">
                                            {{ ucfirst($request->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $request->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('purchase-requests.show', $request) }}" class="btn btn-sm btn-info me-1">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($request->status === 'pending')
                                                <a href="{{ route('purchase-requests.edit', $request) }}" class="btn btn-sm btn-primary me-1">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('purchase-requests.destroy', $request) }}" method="POST" class="d-inline me-1">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this request?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            @if($request->status === 'pending' && (auth()->user()->role === 'procurement' || auth()->user()->role === 'admin'))
                                                <form action="{{ route('purchase-requests.approve', $request) }}" method="POST" class="d-inline me-1">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success" {{ $request->status === 'approved' ? 'disabled' : '' }} onclick="return confirm('Are you sure you want to approve this request?')">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('purchase-requests.reject', $request) }}" method="POST" class="d-inline me-1">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-danger" {{ $request->status === 'rejected' ? 'disabled' : '' }} onclick="return confirm('Are you sure you want to reject this request?')">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $purchaseRequests->links() }}
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
            const rows = document.querySelectorAll('#purchaseRequestsTable tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(input) ? '' : 'none';
            });
        });
    }
});
</script>
@endpush

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
}
.card-tools .btn-primary {
    border-radius: 2rem;
    font-weight: 600;
    font-size: 1.1rem;
    background: linear-gradient(90deg, #38b6ff 0%, #2563eb 100%);
    border: none;
    box-shadow: 0 2px 8px #38b6ff33;
    transition: background 0.2s, color 0.2s, box-shadow 0.2s;
}
.card-tools .btn-primary:hover {
    filter: brightness(1.08);
    box-shadow: 0 4px 16px #38b6ff33;
}
.stats-row {
    display: flex;
    gap: 1.5rem;
    margin-bottom: 2rem;
    flex-wrap: wrap;
    justify-content: space-between;
}
.stats-card {
    flex: 1 1 220px;
    background: linear-gradient(120deg, #e0f7fa 0%, #b2ebf2 100%);
    border-radius: 1.2rem;
    box-shadow: 0 2px 12px #38b6ff22;
    padding: 1.5rem 1.2rem 1.2rem 1.5rem;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    min-width: 220px;
    margin-bottom: 0.5rem;
    position: relative;
    transition: box-shadow 0.18s, transform 0.18s;
}
.stats-card:hover {
    box-shadow: 0 6px 24px #38b6ff33;
    transform: translateY(-2px) scale(1.03);
}
.stats-card.bg-info {
    background: linear-gradient(120deg, #38b6ff 0%, #b2ebf2 100%);
    color: #fff;
}
.stats-card.bg-success {
    background: linear-gradient(120deg, #22c55e 0%, #bbf7d0 100%);
    color: #fff;
}
.stats-card.bg-warning {
    background: linear-gradient(120deg, #facc15 0%, #fef9c3 100%);
    color: #7c4700;
}
.stats-card.bg-primary {
    background: linear-gradient(120deg, #2563eb 0%, #a5b4fc 100%);
    color: #fff;
}
.stats-card .icon {
    position: static;
    font-size: 2.2rem;
    opacity: 0.25;
    margin-left: 0.5rem;
}
.stats-card .fs-2 {
    font-size: 2.5rem !important;
    font-weight: 700;
    line-height: 1;
}
.stats-card .fw-semibold {
    font-size: 1.15rem;
    font-weight: 600;
    margin-top: 0.2rem;
}
.search-bar-container {
    box-shadow: 0 2px 12px rgba(56,189,248,0.10);
    background: #fff;
    border-radius: 32px;
    margin-bottom: 24px;
    align-items: center;
    padding: 0.25rem 0.5rem;
    max-width: 600px;
}
#searchInput.form-control {
    border: none;
    box-shadow: none;
    background: #fff;
    font-size: 1.15rem;
    border-radius: 32px 0 0 32px;
}
#searchInput.form-control:focus {
    box-shadow: none;
    background: #f7fafc;
}
.input-group-append .btn {
    border-radius: 0 32px 32px 0;
    background: #38b6ff;
    color: #fff;
    font-size: 1.2rem;
    border: none;
    transition: background 0.2s;
}
.input-group-append .btn:hover {
    background: #2563eb;
}
.btn-default, .btn-default:focus {
    border-radius: 2rem;
    background: #f1f5f9;
    color: #2563eb;
    border: none;
    font-weight: 500;
    transition: background 0.2s, color 0.2s;
}
.btn-default:hover {
    background: #e0f2fe;
    color: #198754;
}
.table-responsive {
    border-radius: 1.1rem;
    overflow: hidden;
    box-shadow: 0 4px 24px rgba(44,62,80,0.08), 0 1.5px 6px rgba(44,62,80,0.04);
    background: #fff;
}
.table {
    margin-bottom: 0;
}
.table th, .table td {
    vertical-align: middle;
    padding: 1rem 0.75rem;
    border: none;
}
.table thead th {
    background: #f1f5f9;
    font-weight: 700;
    color: #198754;
    border-bottom: 2px solid #e3e3e3;
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
.btn-group .btn {
    border-radius: 1.2rem;
    font-weight: 500;
    font-size: 1rem;
    margin-right: 4px;
    transition: background 0.18s, color 0.18s;
}
.btn-group .btn:last-child {
    margin-right: 0;
}
@media (max-width: 1100px) {
    .stats-row {
        flex-direction: column;
        gap: 1rem;
    }
    .stats-card {
        min-width: 0;
        width: 100%;
    }
}
</style>
@endpush 
@endsection 