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
                @if($purchaseOrder->status === 'approved')
                    <button type="button" class="btn btn-success" 
                            data-bs-toggle="modal" 
                            data-bs-target="#completePurchaseOrderModal"
                            data-po-id="{{ $purchaseOrder->id }}">
                        <i class="fas fa-check"></i> Complete Order
                    </button>
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
                                <p><strong>Contract:</strong> 
                                    @if($purchaseOrder->contract)
                                        {{ $purchaseOrder->contract->contract_number ?? 'N/A' }} - {{ $purchaseOrder->contract->name ?? $purchaseOrder->contract->title ?? 'N/A' }}
                                    @else
                                        N/A
                                    @endif
                                </p>
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

    <!-- Complete Purchase Order Modal -->
    <div class="modal fade" id="completePurchaseOrderModal" tabindex="-1" aria-labelledby="completePurchaseOrderModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="completePurchaseOrderModalLabel">Complete Purchase Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="completePurchaseOrderForm">
                    @csrf
                    @method('PATCH')
                    <div class="modal-body">
                        <div class="alert alert-success d-none" id="completeSuccessMessage">
                            Purchase order completed successfully!
                        </div>

                        <!-- Delivery Information -->
                        <div class="mb-4">
                            <h6 class="mb-3">Delivery Information</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="delivery_date" class="form-label">Delivery Date</label>
                                        <input type="date" class="form-control" id="delivery_date" name="delivery_date" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label d-block">Delivery Status</label>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="is_on_time" id="ontime" value="1" checked>
                                            <label class="form-check-label" for="ontime">On Time</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="is_on_time" id="delayed" value="0">
                                            <label class="form-check-label" for="delayed">Delayed</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Quality Assessment -->
                        <div class="mb-4">
                            <h6 class="mb-3">Quality Assessment</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="total_units" class="form-label">Total Units Received</label>
                                        <input type="number" class="form-control" id="total_units" name="total_units" min="0" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="defective_units" class="form-label">Defective Units</label>
                                        <input type="number" class="form-control" id="defective_units" name="defective_units" min="0" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group mb-3">
                                <label for="quality_notes" class="form-label">Quality Notes</label>
                                <textarea class="form-control" id="quality_notes" name="quality_notes" rows="3"></textarea>
                            </div>
                        </div>

                        <!-- Cost Information -->
                        <div class="mb-4">
                            <h6 class="mb-3">Cost Information</h6>
                            <div class="table-responsive mb-3">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Material</th>
                                            <th>Quantity</th>
                                            <th>SRP Price</th>
                                            <th>Quoted Price</th>
                                            <th>Total SRP</th>
                                            <th>Total Quoted</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($purchaseOrder->items as $item)
                                            <tr>
                                                <td>{{ $item->material->name }}</td>
                                                <td>{{ number_format($item->quantity, 2) }}</td>
                                                <td>₱{{ number_format($item->material->srp_price, 2) }}</td>
                                                <td>₱{{ number_format($item->unit_price, 2) }}</td>
                                                <td>₱{{ number_format($item->quantity * $item->material->srp_price, 2) }}</td>
                                                <td>₱{{ number_format($item->quantity * $item->unit_price, 2) }}</td>
                                            </tr>
                                        @endforeach
                                        <tr class="table-secondary">
                                            <td colspan="4" class="text-end"><strong>Total:</strong></td>
                                            <td><strong>₱{{ number_format($purchaseOrder->calculateEstimatedCost(), 2) }}</strong></td>
                                            <td><strong>₱{{ number_format($purchaseOrder->calculateActualCost(), 2) }}</strong></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="estimated_cost" class="form-label">Total SRP Cost</label>
                                        <div class="input-group">
                                            <span class="input-group-text">₱</span>
                                            <input type="number" class="form-control" id="estimated_cost" name="estimated_cost" 
                                                   value="{{ $purchaseOrder->calculateEstimatedCost() }}" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="actual_cost" class="form-label">Total Quoted Cost</label>
                                        <div class="input-group">
                                            <span class="input-group-text">₱</span>
                                            <input type="number" class="form-control" id="actual_cost" name="actual_cost" 
                                                   value="{{ $purchaseOrder->calculateActualCost() }}" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="alert {{ $purchaseOrder->calculateActualCost() <= $purchaseOrder->calculateEstimatedCost() ? 'alert-success' : 'alert-warning' }} mb-0">
                                @php
                                    $variance = $purchaseOrder->calculateActualCost() - $purchaseOrder->calculateEstimatedCost();
                                    $variancePercent = $purchaseOrder->calculateEstimatedCost() > 0 
                                        ? ($variance / $purchaseOrder->calculateEstimatedCost()) * 100 
                                        : 0;
                                @endphp
                                <strong>Cost Variance:</strong> 
                                {{ $variance >= 0 ? '+' : '-' }}₱{{ number_format(abs($variance), 2) }} 
                                ({{ number_format($variancePercent, 2) }}%)
                                <br>
                                <small>
                                    @if($variance <= 0)
                                        The quoted prices are within or below SRP.
                                    @else
                                        The quoted prices are above SRP. Consider reviewing the costs.
                                    @endif
                                </small>
                            </div>
                        </div>

                        <input type="hidden" name="is_delivered" value="1">
                        <input type="hidden" name="is_completed" value="1">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success" id="completeButton">
                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                            Complete Purchase Order
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const completePurchaseOrderModal = document.getElementById('completePurchaseOrderModal');
        const completePurchaseOrderForm = document.getElementById('completePurchaseOrderForm');
        const completeButton = document.getElementById('completeButton');
        const spinner = completeButton.querySelector('.spinner-border');
        const successMessage = document.getElementById('completeSuccessMessage');
        let currentPurchaseOrderId = null;

        completePurchaseOrderModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            currentPurchaseOrderId = button.getAttribute('data-po-id');
            
            // Pre-fill estimated cost from total amount
            document.getElementById('estimated_cost').value = '{{ $purchaseOrder->total_amount }}';
            document.getElementById('total_units').value = '{{ $purchaseOrder->items->sum("quantity") }}';
        });

        completePurchaseOrderForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            if (!currentPurchaseOrderId) return;

            try {
                completeButton.disabled = true;
                spinner.classList.remove('d-none');
                successMessage.classList.add('d-none');

                const formData = new FormData(completePurchaseOrderForm);
                const response = await fetch(`/purchase-orders/${currentPurchaseOrderId}/complete`, {
                    method: 'PATCH',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (!response.ok) throw new Error('Failed to complete purchase order');

                successMessage.classList.remove('d-none');
                
                // Reload page after a delay
                setTimeout(() => {
                    window.location.reload();
                }, 1500);

            } catch (error) {
                console.error('Error:', error);
                alert('Failed to complete purchase order. Please try again.');
            } finally {
                completeButton.disabled = false;
                spinner.classList.add('d-none');
            }
        });
    });
    </script>
    @endpush
@endsection 