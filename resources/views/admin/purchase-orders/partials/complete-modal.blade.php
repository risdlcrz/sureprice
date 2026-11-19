{{-- Complete an order: delivery quality + costs (POST purchase-orders.complete). Param: $purchaseOrder --}}
<x-modal-dialog id="completePurchaseOrderModal" dialog-class="modal-lg">
    <x-slot:title>Complete Purchase Order</x-slot:title>
            <form id="completePurchaseOrderForm" method="POST">
                @csrf
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
</x-modal-dialog>

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

            const formData = new FormData(this);
            const response = await fetch('{{ route('purchase-orders.complete', $purchaseOrder) }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: formData
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.error || 'Failed to complete purchase order');
            }

            successMessage.classList.remove('d-none');
            successMessage.textContent = data.message || 'Purchase order completed successfully!';
            
            // Reload page after a delay
            setTimeout(() => {
                window.location.reload();
            }, 1500);

        } catch (error) {
            console.error('Error:', error);
            alert(error.message || 'Failed to complete purchase order. Please try again.');
        } finally {
            completeButton.disabled = false;
            spinner.classList.add('d-none');
        }
    });
});
</script>
@endpush
