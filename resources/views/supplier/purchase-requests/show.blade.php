@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Purchase Request Details</h3>
                </div>
                <div class="card-body">
                    <!-- Request Information -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h4>Request Information</h4>
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 200px;">Request Number</th>
                                    <td>{{ $purchaseRequest->request_number }}</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'pending' => 'warning',
                                                'pending_admin_approval' => 'warning',
                                                'pending_supplier_approval' => 'info',
                                                'approved' => 'success',
                                                'rejected' => 'danger',
                                                'cancelled' => 'secondary'
                                            ];
                                            $statusColor = $statusColors[$purchaseRequest->status] ?? 'secondary';
                                        @endphp
                                        <span class="badge badge-{{ $statusColor }}">
                                            {{ ucfirst(str_replace('_', ' ', $purchaseRequest->status)) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Requested By</th>
                                    <td>{{ $purchaseRequest->requestedBy?->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Created Date</th>
                                    <td>{{ $purchaseRequest->created_at->format('M d, Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h4>Financial Summary</h4>
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 200px;">Total Amount</th>
                                    <td class="text-right">{{ $purchaseRequest->total_amount ? number_format($purchaseRequest->total_amount, 2) : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Number of Items</th>
                                    <td class="text-right">{{ $purchaseRequest->items?->count() ?? 0 }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Request Items -->
                    <div class="row">
                        <div class="col-12">
                            <h4>Request Items</h4>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Material</th>
                                            <th>Description</th>
                                            <th>Quantity</th>
                                            <th>Unit</th>
                                            <th>Notes</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($purchaseRequest->items as $item)
                                            <tr>
                                                <td>{{ $item->material?->name ?? 'N/A' }}</td>
                                                <td>{{ $item->description }}</td>
                                                <td class="text-right">{{ $item->quantity ? number_format($item->quantity, 2) : 'N/A' }}</td>
                                                <td>{{ $item->unit }}</td>
                                                <td>{{ $item->notes ?? 'N/A' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Supplier Approval Section -->
                    @if($purchaseRequest->status === 'pending_supplier_approval')
                        <div class="card mt-4">
                            <div class="card-header">
                                <h4 class="card-title">Approval Actions</h4>
                            </div>
                            <div class="card-body">
                                @if(!$purchaseRequest->supplier_approved)
                                    <p class="text-info mb-3">
                                        <i class="fas fa-info-circle"></i> 
                                        This purchase request has been approved by admin and is now pending your approval.
                                    </p>
                                    @if($purchaseRequest->admin_approved_at)
                                        <p class="text-success mb-3">
                                            <i class="fas fa-check-circle"></i> 
                                            Approved by admin on {{ $purchaseRequest->admin_approved_at->format('M d, Y H:i') }}
                                        </p>
                                    @endif
                                    <form action="{{ route('supplier.purchase-requests.approve', $purchaseRequest) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-success" onclick="return confirm('Are you sure you want to approve this request?')">
                                            <i class="fas fa-check"></i> Approve Request
                                        </button>
                                    </form>
                                @else
                                    <p class="text-success">
                                        <i class="fas fa-check-circle"></i> 
                                        You have approved this request on {{ $purchaseRequest->supplier_approved_at->format('M d, Y H:i') }}.
                                    </p>
                                @endif
                            </div>
                        </div>
                    @elseif($purchaseRequest->status === 'approved')
                        <div class="card mt-4">
                            <div class="card-header">
                                <h4 class="card-title">Approval Status</h4>
                            </div>
                            <div class="card-body">
                                <p class="text-success">
                                    <i class="fas fa-check-circle"></i> 
                                    This purchase request has been fully approved and is ready for purchase order creation.
                                </p>
                                @if($purchaseRequest->supplier_approved_at)
                                    <p>You approved on: {{ $purchaseRequest->supplier_approved_at->format('M d, Y H:i') }}</p>
                                @endif
                            </div>
                        </div>
                    @elseif($purchaseRequest->status === 'pending_admin_approval')
                        <div class="card mt-4">
                            <div class="card-header">
                                <h4 class="card-title">Status Information</h4>
                            </div>
                            <div class="card-body">
                                <p class="text-warning">
                                    <i class="fas fa-clock"></i> 
                                    This purchase request is pending admin approval.
                                </p>
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>
@endsection 