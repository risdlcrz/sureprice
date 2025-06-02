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
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h5 class="mb-0">All Purchase Requests</h5>
                    </div>
                    <div class="col-auto">
                        <select class="form-select" id="status-filter">
                            <option value="">All Status</option>
                            <option value="draft">Draft</option>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                </div>
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
                                    <td>{{ $pr->contract->contract_id }}</td>
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
    document.getElementById('status-filter').addEventListener('change', function() {
        const status = this.value;
        const url = new URL(window.location.href);
        if (status) {
            url.searchParams.set('status', status);
        } else {
            url.searchParams.delete('status');
        }
        window.location.href = url.toString();
    });

    // Set the current status in the filter
    const currentStatus = new URLSearchParams(window.location.search).get('status');
    if (currentStatus) {
        document.getElementById('status-filter').value = currentStatus;
    }
</script>
@endpush 