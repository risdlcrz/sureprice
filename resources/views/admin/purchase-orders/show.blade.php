@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Purchase Order Details</h1>
            <div>
                @if(in_array($purchaseOrder->status, ['draft', 'pending']))
                    <a href="{{ route('purchase-orders.edit', $purchaseOrder) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                @endif
                <a href="{{ route('purchase-orders.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Purchase Order Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>PO Number:</strong> {{ $purchaseOrder->po_number }}</p>
                                <p><strong>Contract:</strong> {{ $purchaseOrder->contract->contract_id ?? 'N/A' }}</p>
                                <p><strong>Purchase Request:</strong> {{ $purchaseOrder->purchaseRequest->pr_number }}</p>
                                <p><strong>Supplier:</strong> {{ $purchaseOrder->supplier->company_name }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Status:</strong> 
                                    <span class="badge bg-{{ $purchaseOrder->status_color }}">
                                        {{ ucfirst($purchaseOrder->status) }}
                                    </span>
                                </p>
                                <p><strong>Delivery Date:</strong> {{ $purchaseOrder->delivery_date->format('M d, Y') }}</p>
                                <p><strong>Payment Terms:</strong> {{ $purchaseOrder->payment_terms }}</p>
                                <p><strong>Shipping Terms:</strong> {{ $purchaseOrder->shipping_terms }}</p>
                            </div>
                        </div>

                        @if($purchaseOrder->notes)
                            <div class="mt-3">
                                <strong>Notes:</strong>
                                <p class="mb-0">{{ $purchaseOrder->notes }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Items</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Material</th>
                                        <th>Quantity</th>
                                        <th>Unit Price</th>
                                        <th>Total Price</th>
                                        <th>Specifications</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($purchaseOrder->items as $item)
                                        <tr>
                                            <td>{{ $item->material->name }}</td>
                                            <td>{{ number_format($item->quantity, 2) }} {{ $item->material->unit }}</td>
                                            <td>₱{{ number_format($item->unit_price, 2) }}</td>
                                            <td>₱{{ number_format($item->total_price, 2) }}</td>
                                            <td>{{ $item->specifications }}</td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td colspan="3" class="text-end"><strong>Total Amount:</strong></td>
                                        <td colspan="2"><strong>₱{{ number_format($purchaseOrder->total_amount, 2) }}</strong></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Status Updates</h5>
                    </div>
                    <div class="card-body">
                        @if(in_array($purchaseOrder->status, ['draft', 'pending']))
                            <form action="{{ route('purchase-orders.update-status', $purchaseOrder) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="status" class="form-label">Update Status</label>
                                    <select class="form-select" id="status" name="status" required>
                                        <option value="">Select Status</option>
                                        @if($purchaseOrder->status === 'draft')
                                            <option value="pending">Submit for Approval</option>
                                        @endif
                                        @if($purchaseOrder->status === 'pending')
                                            <option value="approved">Approve</option>
                                            <option value="rejected">Reject</option>
                                        @endif
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary">Update Status</button>
                            </form>
                        @else
                            <p class="mb-0">No status updates available for {{ strtolower($purchaseOrder->status) }} purchase orders.</p>
                        @endif
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Timeline</h5>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            <div class="timeline-item">
                                <div class="timeline-marker bg-success"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-0">Created</h6>
                                    <small>{{ $purchaseOrder->created_at->format('M d, Y H:i') }}</small>
                                </div>
                            </div>
                            @if($purchaseOrder->updated_at->gt($purchaseOrder->created_at))
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-info"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-0">Last Updated</h6>
                                        <small>{{ $purchaseOrder->updated_at->format('M d, Y H:i') }}</small>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .timeline {
            position: relative;
            padding-left: 1.5rem;
        }
        .timeline::before {
            content: '';
            position: absolute;
            left: 0.75rem;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #e9ecef;
        }
        .timeline-item {
            position: relative;
            padding-bottom: 1.5rem;
        }
        .timeline-marker {
            position: absolute;
            left: -1.5rem;
            width: 1rem;
            height: 1rem;
            border-radius: 50%;
        }
        .timeline-content {
            padding-left: 0.5rem;
        }
    </style>
@endsection 