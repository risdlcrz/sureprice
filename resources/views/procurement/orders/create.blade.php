@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <div class="card-header bg-white py-3">
                    <h4 class="card-title mb-0">Create Purchase Order</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('procurement.orders.store') }}" method="POST" id="orderForm">
                        @csrf
                        
                        <!-- Basic Information -->
                        <div class="mb-4">
                            <h5 class="mb-3">Basic Information</h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="supplier_id" class="form-label">Supplier</label>
                                    <select class="form-select @error('supplier_id') is-invalid @enderror" 
                                            id="supplier_id" name="supplier_id" required>
                                        <option value="">Select Supplier</option>
                                        @foreach($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}" 
                                                {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                                {{ $supplier->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('supplier_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="due_date" class="form-label">Due Date</label>
                                    <input type="date" class="form-control @error('due_date') is-invalid @enderror" 
                                           id="due_date" name="due_date" value="{{ old('due_date') }}" required>
                                    @error('due_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-12">
                                    <label for="shipping_address" class="form-label">Shipping Address</label>
                                    <textarea class="form-control @error('shipping_address') is-invalid @enderror" 
                                              id="shipping_address" name="shipping_address" rows="2" required>{{ old('shipping_address') }}</textarea>
                                    @error('shipping_address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-12">
                                    <label for="notes" class="form-label">Notes (optional)</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" 
                                              id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Order Items -->
                        <div class="mb-4">
                            <h5 class="mb-3">Order Items</h5>
                            <div id="items-container">
                                <!-- Items will be added here by JavaScript -->
                            </div>
                            <button type="button" class="btn btn-secondary" onclick="addItem()">Add Item</button>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Create Order</button>
                            <a href="{{ route('procurement.orders.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let itemCount = 0;

function addItem(material = null, quantity = 1, unitPrice = 0) {
    const container = document.getElementById('items-container');
    const newItem = document.createElement('div');
    newItem.classList.add('item-row', 'mb-3', 'p-3', 'border', 'rounded');

    let materialOptions = '<option value="">Select Material</option>';
    @foreach($materials as $_material)
        materialOptions += `<option value="{{ $_material->id }}" ${material && material.id === {{ $_material->id }} ? 'selected' : ''}>{{ $_material->name }} ({{ $_material->code }})</option>`;
    @endforeach

    newItem.innerHTML = `
        <div class="row g-3 align-items-end">
            <div class="col-md-5">
                <label class="form-label">Material</label>
                <select class="form-select" name="items[${itemCount}][material_id]" required>
                    ${materialOptions}
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Quantity</label>
                <input type="number" class="form-control" name="items[${itemCount}][quantity]" value="${quantity}" min="1" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Unit Price (₱)</label>
                <input type="number" class="form-control" name="items[${itemCount}][unit_price]" value="${unitPrice}" min="0.01" step="0.01" required>
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-danger d-block w-100 remove-item" onclick="removeItem(this)">X</button>
            </div>
        </div>
    `;
    
    container.appendChild(newItem);
    itemCount++;
}

function removeItem(button) {
    const container = document.getElementById('items-container');
    if (container.children.length > 1) {
        button.closest('.item-row').remove();
    } else {
        alert('At least one item is required.');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const oldItems = @json(old('items', []));
    if (oldItems.length > 0) {
        oldItems.forEach(item => {
            addItem({ id: item.material_id }, item.quantity, item.unit_price);
        });
    } else {
        addItem(); // Add one empty item row by default
    }
});
</script>
@endpush
@endsection 