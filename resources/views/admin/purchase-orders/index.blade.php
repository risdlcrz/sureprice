@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="h3 mb-4 text-center page-title">Purchase Orders</h1>
        <div class="d-flex justify-content-end align-items-center mb-4">
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
                                    <td>
                                        @if($po->contract)
                                            <a href="{{ route('contracts.show', $po->contract) }}">
                                                {{ $po->contract->contract_number ?? '[No Contract Number]' }} - {{ $po->contract->name ?? $po->contract->title ?? '[No Contract Name]' }}
                                            </a>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>{{ $po->supplier->company_name }}</td>
                                    <td>₱{{ number_format($po->total_amount, 2) }}</td>
                                    <td>
                                        <x-status-badge :status="$po->status" />
                                    </td>
                                    <td>{{ $po->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <a href="{{ route('purchase-orders.show', $po) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($po->isEditable())
                                            <a href="{{ route('purchase-orders.edit', $po) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endif
                                        @if($po->isEditable())
                                            <form action="{{ route('purchase-orders.destroy', $po) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                        @if($po->canComplete())
                                            <a href="{{ route('purchase-orders.show', $po) }}" class="btn btn-sm btn-success"><i class="fas fa-check"></i> Complete</a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <x-empty-row cols="7">No purchase orders found</x-empty-row>
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
document.addEventListener('DOMContentLoaded', function() {
    const statusFilter = document.getElementById('status-filter');
    if (statusFilter) {
        statusFilter.addEventListener('change', function() {
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
            statusFilter.value = currentStatus;
        }
    }

});
</script>
@endpush

