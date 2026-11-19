@extends('layouts.app')
@section('content')
<div class="container">
    <x-page-header title="My Purchase Orders" />
    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('supplier.purchase-orders.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Status</option>
                        <option value="draft">Draft</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="sort" class="form-label">Sort By</label>
                    <select class="form-select" id="sort" name="sort">
                        <option value="created_at">Created Date</option>
                        <option value="po_number">PO Number</option>
                        <option value="total_amount">Total Amount</option>
                        <option value="status">Status</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="perPage" class="form-label">Per Page</label>
                    <select class="form-select" id="perPage" name="perPage">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                    <a href="{{ route('supplier.purchase-orders.index') }}" class="btn btn-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>PO Number</th>
                        <th>Contract</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                        <th>Payment Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchaseOrders as $po)
                        @php $payment = $po->payments->first(); @endphp
                        <tr>
                            <td>{{ $po->po_number }}</td>
                            <td>
                                {{ $po->contract?->contract_number ?? 'N/A' }}
                            </td>
                            <td>₱{{ number_format($po->total_amount, 2) }}</td>
                            <td><x-status-badge :status="$po->status" /></td>
                            <td>
                                @if($payment)
                                    <x-status-badge :status="$payment->status" />
                                @else
                                    <span class="badge bg-secondary">Unpaid</span>
                                @endif
                            </td>
                            <td>{{ $po->created_at->format('M d, Y') }}</td>
                            <td>
                                <a href="{{ route('supplier.purchase-orders.show', $po) }}" class="btn btn-sm btn-info">View</a>
                            </td>
                        </tr>
                    @empty
                        <x-empty-row cols="7">No purchase orders found.</x-empty-row>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($purchaseOrders->hasPages())
        <div class="card-footer">
            {{ $purchaseOrders->links() }}
        </div>
        @endif
    </div>
</div>
@endsection 