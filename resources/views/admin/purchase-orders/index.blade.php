@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Purchase Orders</h1>
            <a href="{{ route('purchase-orders.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> New Purchase Order
            </a>
        </div>

        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h5 class="mb-0">All Purchase Orders</h5>
                    </div>
                    <div class="col-auto">
                        <select class="form-select" id="status-filter">
                            <option value="">All Status</option>
                            <option value="draft">Draft</option>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>PO Number</th>
                                <th>Contract</th>
                                <th>Supplier</th>
                                <th>Total Amount</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($purchaseOrders as $po)
                                <tr>
                                    <td>{{ $po->po_number }}</td>
                                    <td>{{ $po->contract->contract_id ?? 'N/A' }}</td>
                                    <td>{{ $po->supplier->company_name }}</td>
                                    <td>₱{{ number_format($po->total_amount, 2) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $po->status_color }}">
                                            {{ ucfirst($po->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $po->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <a href="{{ route('purchase-orders.show', $po) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if(in_array($po->status, ['draft', 'pending']))
                                            <a href="{{ route('purchase-orders.edit', $po) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endif
                                        @if($po->status === 'draft')
                                            <form action="{{ route('purchase-orders.destroy', $po) }}" method="POST" class="d-inline">
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
                                    <td colspan="7" class="text-center">No purchase orders found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div>
                        Showing {{ $purchaseOrders->firstItem() ?? 0 }} to {{ $purchaseOrders->lastItem() ?? 0 }} of {{ $purchaseOrders->total() ?? 0 }} purchase orders
                    </div>
                    {{ $purchaseOrders->links() }}
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