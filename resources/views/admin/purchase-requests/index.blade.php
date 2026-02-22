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
                        <table class="table table-hover" id="purchaseRequestsTable">
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
}
.card-tools .btn-primary {
    background: #198754 !important;
    background-image: none !important;
    border: none;
    border-radius: 8px;
    font-weight: 600;
}
.card-tools .btn-primary:hover {
    background: #157347 !important;
}
.stats-row {
    display: flex;
    gap: 1rem;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
}
.stats-card {
    flex: 1 1 180px;
    border-radius: 10px;
    padding: 1rem 1.25rem;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    min-width: 160px;
    border: 1px solid rgba(0,0,0,0.06);
    transition: box-shadow 0.2s;
}
.stats-card:hover { box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
.stats-card.bg-info {
    background: #0dcaf0 !important;
    color: #fff;
}
.stats-card.bg-success {
    background: #198754 !important;
    color: #fff;
}
.stats-card.bg-warning {
    background: #ffc107 !important;
    color: #1f2937;
}
.stats-card.bg-primary {
    background: #0d6efd !important;
    color: #fff;
}
.stats-card .icon { font-size: 1.75rem; opacity: 0.3; margin-left: 0.5rem; }
.stats-card .fs-2 { font-size: 2rem !important; font-weight: 700; }
.stats-card .fw-semibold { font-size: 0.9375rem; font-weight: 600; }
.input-group-append .btn {
    background: #198754 !important;
    color: #fff;
    border: none;
    border-radius: 0 8px 8px 0;
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
#purchaseRequestsTable thead th {
    background: #f5f6f8;
    color: #1f2937;
    font-weight: 600;
    font-size: 0.8125rem;
    padding: 0.75rem 1rem;
    border-bottom: 1px solid #e8eaed;
}
#purchaseRequestsTable tbody td {
    padding: 0.75rem 1rem;
    border-bottom: 1px solid #e8eaed;
    font-size: 0.9375rem;
}
#purchaseRequestsTable tbody tr:last-child td { border-bottom: none; }
#purchaseRequestsTable tbody tr:hover { background: rgba(25, 135, 84, 0.04); }
#purchaseRequestsTable.table-striped tbody tr:nth-of-type(odd) { background: #fff; }
#purchaseRequestsTable.table-striped tbody tr:hover { background: rgba(25, 135, 84, 0.04); }
.badge { border-radius: 6px; font-weight: 500; }
.btn-group .btn { border-radius: 6px; }
@media (max-width: 1100px) {
    .stats-row { flex-direction: column; }
    .stats-card { min-width: 0; width: 100%; }
}
</style>
@endpush 
@endsection 