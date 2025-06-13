@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Purchase Requests</h3>
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
                    <div class="row mb-4">
                        <div class="col-md-3 mb-3">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3>{{ $purchaseRequests->where('status', 'pending')->count() }}</h3>
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
                                    <h3>{{ $purchaseRequests->where('status', 'approved')->count() }}</h3>
                                    <p>Approved Requests</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-check"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3>{{ $purchaseRequests->where('status', 'rejected')->count() }}</h3>
                                    <p>Rejected Requests</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-times"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="small-box bg-primary">
                                <div class="inner">
                                    <h3>{{ $purchaseRequests->where('is_project_related', true)->count() }}</h3>
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
                                                    <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Are you sure you want to approve this request?')">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('purchase-requests.reject', $request) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to reject this request?')">
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
@endsection 