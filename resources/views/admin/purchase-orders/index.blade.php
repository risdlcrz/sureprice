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
                                        @if($po->status === 'approved')
                                            <button type="button" class="btn btn-sm btn-success" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#completePurchaseOrderModal"
                                                    data-po-id="{{ $po->id }}">
                                                <i class="fas fa-check"></i> Complete
                                            </button>
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
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="estimated_cost" class="form-label">Estimated Cost</label>
                                        <div class="input-group">
                                            <span class="input-group-text">₱</span>
                                            <input type="number" class="form-control" id="estimated_cost" name="estimated_cost" min="0" step="0.01" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="actual_cost" class="form-label">Actual Cost</label>
                                        <div class="input-group">
                                            <span class="input-group-text">₱</span>
                                            <input type="number" class="form-control" id="actual_cost" name="actual_cost" min="0" step="0.01" required>
                                        </div>
                                    </div>
                                </div>
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

    // Handle Purchase Order Completion
    const completePurchaseOrderModal = document.getElementById('completePurchaseOrderModal');
    const completePurchaseOrderForm = document.getElementById('completePurchaseOrderForm');
    const completeButton = document.getElementById('completeButton');
    const spinner = completeButton.querySelector('.spinner-border');
    const successMessage = document.getElementById('completeSuccessMessage');
    let currentPurchaseOrderId = null;

    completePurchaseOrderModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        currentPurchaseOrderId = button.getAttribute('data-po-id');
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