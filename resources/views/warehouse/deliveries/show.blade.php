@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Delivery Details</h1>
            <p class="text-muted mb-0">#{{ $delivery->delivery_number }}</p>
        </div>
        <a href="{{ route('warehouse.deliveries.index') }}" class="btn btn-secondary">Back to Deliveries</a>
    </div>

    <!-- Delivery Info Card -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <h6 class="text-muted mb-1">Type</h6>
                    <p class="mb-0">
                        <span class="badge bg-{{ $delivery->type === 'incoming' ? 'success' : 'primary' }}">
                            {{ ucfirst($delivery->type) }}
                        </span>
                    </p>
                </div>
                <div class="col-md-3">
                    <h6 class="text-muted mb-1">Status</h6>
                    <p class="mb-0">
                        <span class="badge bg-{{ $delivery->status_color }}">
                            {{ ucfirst($delivery->status) }}
                        </span>
                    </p>
                </div>
                <div class="col-md-3">
                    <h6 class="text-muted mb-1">Expected Date</h6>
                    <p class="mb-0">{{ $delivery->expected_date->format('M d, Y') }}</p>
                </div>
                <div class="col-md-3">
                    <h6 class="text-muted mb-1">Total Items</h6>
                    <p class="mb-0">{{ $delivery->items_count }} items</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Delivery Items -->
    <div class="card mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">Delivery Items</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Material</th>
                        <th>Quantity</th>
                        <th>Unit</th>
                        @if($delivery->status === 'pending')
                        <th>Received Quantity</th>
                        <th>Notes</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($delivery->items as $item)
                    <tr>
                        <td>
                            <div>{{ $item->material->name }}</div>
                            <small class="text-muted">{{ $item->material->code }}</small>
                        </td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ $item->material->unit }}</td>
                        @if($delivery->status === 'pending')
                        <td>
                            <input type="number" 
                                   class="form-control form-control-sm received-quantity" 
                                   name="items[{{ $item->id }}][received_quantity]" 
                                   value="{{ $item->quantity }}"
                                   min="0"
                                   max="{{ $item->quantity }}"
                                   data-item-id="{{ $item->id }}">
                        </td>
                        <td>
                            <input type="text" 
                                   class="form-control form-control-sm" 
                                   name="items[{{ $item->id }}][notes]"
                                   placeholder="Add notes">
                        </td>
                        @endif
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ $delivery->status === 'pending' ? '5' : '3' }}" class="text-center text-muted">
                            No items found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($delivery->status === 'pending')
    <!-- Process Delivery Form -->
    <div class="card">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">Process Delivery</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('warehouse.deliveries.process', $delivery) }}" method="POST" id="processDeliveryForm">
                @csrf
                <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="completed">Complete Delivery</option>
                        <option value="partial">Partial Delivery</option>
                        <option value="cancelled">Cancel Delivery</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="notes" class="form-label">Notes</label>
                    <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Process Delivery</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const processForm = document.getElementById('processDeliveryForm');
    if (processForm) {
        processForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Collect all received quantities
            const receivedQuantities = {};
            document.querySelectorAll('.received-quantity').forEach(input => {
                receivedQuantities[input.dataset.itemId] = input.value;
            });

            // Add received quantities to form data
            const formData = new FormData(processForm);
            formData.append('received_quantities', JSON.stringify(receivedQuantities));

            // Submit form
            fetch(processForm.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = data.redirect;
                } else {
                    alert(data.message || 'An error occurred while processing the delivery.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while processing the delivery.');
            });
        });
    }
});
</script>
@endpush
@endsection 