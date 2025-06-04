@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>{{ isset($purchaseOrder) ? 'Edit Purchase Order' : 'Create Purchase Order' }}</h1>
            <a href="{{ route('purchase-orders.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="{{ isset($purchaseOrder) ? route('purchase-orders.update', $purchaseOrder) : route('purchase-orders.store') }}" 
                      method="POST" id="purchaseOrderForm">
                    @csrf
                    @if(isset($purchaseOrder))
                        @method('PUT')
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="purchase_request_id">Purchase Request</label>
                                <select class="form-select @error('purchase_request_id') is-invalid @enderror" 
                                        id="purchase_request_id" name="purchase_request_id" 
                                        {{ isset($purchaseOrder) ? 'disabled' : 'required' }}>
                                    <option value="">Select Purchase Request</option>
                                    @foreach($purchaseRequests as $pr)
                                        <option value="{{ $pr->id }}" 
                                            {{ (old('purchase_request_id', $purchaseOrder->purchase_request_id ?? '') == $pr->id) ? 'selected' : '' }}
                                            data-contract-id="{{ $pr->contract_id }}">
                                            {{ $pr->pr_number }}
                                            @if($pr->contract)
                                                - Contract: {{ $pr->contract->contract_id }}
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('purchase_request_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="supplier_id">Supplier</label>
                                <select class="form-select @error('supplier_id') is-invalid @enderror" 
                                        id="supplier_id" name="supplier_id" required>
                                    <option value="">Select Supplier</option>
                                    @foreach($suppliers ?? [] as $supplier)
                                        <option value="{{ $supplier->id }}"
                                            {{ (old('supplier_id', $purchaseOrder->supplier_id ?? '') == $supplier->id) ? 'selected' : '' }}>
                                            {{ $supplier->company_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('supplier_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="delivery_date">Delivery Date</label>
                                <input type="date" class="form-control @error('delivery_date') is-invalid @enderror" 
                                       id="delivery_date" name="delivery_date" 
                                       value="{{ old('delivery_date', isset($purchaseOrder) ? $purchaseOrder->delivery_date->format('Y-m-d') : '') }}" 
                                       required>
                                @error('delivery_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="payment_terms">Payment Terms</label>
                                <input type="text" class="form-control @error('payment_terms') is-invalid @enderror" 
                                       id="payment_terms" name="payment_terms" 
                                       value="{{ old('payment_terms', $purchaseOrder->payment_terms ?? '') }}" 
                                       required>
                                @error('payment_terms')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="shipping_terms">Shipping Terms</label>
                                <input type="text" class="form-control @error('shipping_terms') is-invalid @enderror" 
                                       id="shipping_terms" name="shipping_terms" 
                                       value="{{ old('shipping_terms', $purchaseOrder->shipping_terms ?? '') }}" 
                                       required>
                                @error('shipping_terms')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="mb-0">Items</h5>
                        </div>
                        <div class="card-body">
                            <div id="items-container">
                                @if(isset($purchaseOrder))
                                    @foreach($purchaseOrder->items as $index => $item)
                                        <div class="item-row mb-4">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Material</label>
                                                        <select class="form-select" name="items[{{ $index }}][material_id]" required>
                                                            <option value="">Select Material</option>
                                                            @foreach($materials ?? [] as $material)
                                                                <option value="{{ $material->id }}" 
                                                                    {{ $item->material_id == $material->id ? 'selected' : '' }}>
                                                                    {{ $material->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label>Quantity</label>
                                                        <input type="number" class="form-control quantity" 
                                                               name="items[{{ $index }}][quantity]" 
                                                               value="{{ $item->quantity }}" min="0.01" step="0.01" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label>Unit Price</label>
                                                        <input type="number" class="form-control unit-price" 
                                                               name="items[{{ $index }}][unit_price]" 
                                                               value="{{ $item->unit_price }}" min="0.01" step="0.01" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Specifications</label>
                                                        <input type="text" class="form-control" 
                                                               name="items[{{ $index }}][specifications]" 
                                                               value="{{ $item->specifications }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-1">
                                                    <label class="d-block">&nbsp;</label>
                                                    <button type="button" class="btn btn-danger remove-item">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            <button type="button" class="btn btn-secondary" id="add-item">
                                <i class="fas fa-plus"></i> Add Item
                            </button>
                        </div>
                    </div>

                    <div class="form-group mt-4">
                        <label for="notes">Notes</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                                  id="notes" name="notes" rows="3">{{ old('notes', $purchaseOrder->notes ?? '') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="text-end mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> {{ isset($purchaseOrder) ? 'Update' : 'Create' }} Purchase Order
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    let itemIndex = {{ isset($purchaseOrder) ? $purchaseOrder->items->count() : 0 }};

    function createItemRow() {
        const template = `
            <div class="item-row mb-4">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Material</label>
                            <select class="form-select" name="items[\${itemIndex}][material_id]" required>
                                <option value="">Select Material</option>
                                @foreach($materials ?? [] as $material)
                                    <option value="{{ $material->id }}">{{ $material->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Quantity</label>
                            <input type="number" class="form-control quantity" 
                                   name="items[\${itemIndex}][quantity]" min="0.01" step="0.01" required>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Unit Price</label>
                            <input type="number" class="form-control unit-price" 
                                   name="items[\${itemIndex}][unit_price]" min="0.01" step="0.01" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Specifications</label>
                            <input type="text" class="form-control" 
                                   name="items[\${itemIndex}][specifications]">
                        </div>
                    </div>
                    <div class="col-md-1">
                        <label class="d-block">&nbsp;</label>
                        <button type="button" class="btn btn-danger remove-item">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        itemIndex++;
        return template;
    }

    document.getElementById('add-item').addEventListener('click', function() {
        const container = document.getElementById('items-container');
        container.insertAdjacentHTML('beforeend', createItemRow());
    });

    document.getElementById('items-container').addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-item') || e.target.closest('.remove-item')) {
            const row = e.target.closest('.item-row');
            row.remove();
        }
    });

    // Add at least one item row if there are none
    if (document.querySelectorAll('.item-row').length === 0) {
        document.getElementById('add-item').click();
    }

    // Handle purchase request selection
    document.getElementById('purchase_request_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const contractId = selectedOption.dataset.contractId;
        // You can add logic here to load materials based on the selected purchase request
    });
</script>
@endpush 