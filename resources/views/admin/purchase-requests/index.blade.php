@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Purchase Requests</h1>
            <a href="{{ route('purchase-requests.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> New Purchase Request
            </a>
        </div>

        <div class="card">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">All Purchase Requests</h5>
                <form method="GET" action="{{ route('purchase-requests.index') }}" class="row mb-0 align-items-end w-100" id="filterForm">
                    <div class="col-md-3 mb-2 mb-md-0">
                        <input type="text" name="search" class="form-control filter-input" placeholder="Search PR number, department, contract, etc..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2 mb-2 mb-md-0">
                        <select name="status" class="form-control filter-input" onchange="this.form.submit()">
                            <option value="">All Statuses</option>
                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-2 mb-md-0">
                        <input type="number" name="per_page" class="form-control filter-input" placeholder="Per Page" value="{{ request('per_page', 10) }}" min="1" max="100">
                    </div>
                    <div class="col-md-3 mb-2 mb-md-0">
                        <!-- Placeholder for future filter options -->
                    </div>
                    <div class="col-md-2 mb-2 mb-md-0 d-flex justify-content-end">
                        <a href="{{ route('purchase-requests.index', ['clear' => 1]) }}" class="btn btn-secondary w-100">
                            <i class="fas fa-times"></i> Clear Filters
                        </a>
                    </div>
                </form>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>PR Number</th>
                                <th>Contract</th>
                                <th>Department</th>
                                <th>Required Date</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($purchaseRequests as $pr)
                                <tr>
                                    <td>{{ $pr->pr_number }}</td>
                                    <td>{{ $pr->contract->contract_id ?? 'N/A' }}</td>
                                    <td>{{ $pr->department }}</td>
                                    <td>{{ $pr->required_date->format('M d, Y') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $pr->status_color }}">
                                            {{ ucfirst($pr->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $pr->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <a href="{{ route('purchase-requests.show', $pr) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if(in_array($pr->status, ['draft', 'pending']))
                                            <a href="{{ route('purchase-requests.edit', $pr) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endif
                                        @if($pr->status === 'draft')
                                            <form action="{{ route('purchase-requests.destroy', $pr) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">No purchase requests found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div>
                        Showing {{ $purchaseRequests->firstItem() ?? 0 }} to {{ $purchaseRequests->lastItem() ?? 0 }} of {{ $purchaseRequests->total() ?? 0 }} purchase requests
                    </div>
                    {{ $purchaseRequests->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // No extra JS needed, form submits on status change or search
</script>
@endpush 