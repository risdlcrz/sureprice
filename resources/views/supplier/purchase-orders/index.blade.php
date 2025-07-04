@extends('layouts.app')
@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">My Purchase Orders</h1>
    </div>
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
                                @if($po->contract)
                                    <a href="#">{{ $po->contract->contract_number ?? '[No Contract Number]' }} - {{ $po->contract->name ?? $po->contract->title ?? '[No Contract Name]' }}</a>
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>₱{{ number_format($po->total_amount, 2) }}</td>
                            <td><span class="badge bg-{{ $po->status_color }}">{{ ucfirst($po->status) }}</span></td>
                            <td>
                                @if($payment)
                                    <span class="badge bg-{{ $payment->status === 'verified' ? 'success' : ($payment->status === 'for_verification' ? 'info' : ($payment->status === 'rejected' ? 'danger' : 'secondary')) }}">
                                        {{ ucfirst($payment->status) }}
                                    </span>
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
                        <tr><td colspan="7" class="text-center">No purchase orders found.</td></tr>
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