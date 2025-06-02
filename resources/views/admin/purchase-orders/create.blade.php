@extends('layouts.app')

@section('content')
    <div class="sidebar">
        @include('include.header_project')
    </div>

    <div class="content">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Create Purchase Order</h1>
                <a href="{{ route('purchase-orders.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form action="{{ route('purchase-orders.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="purchase_request_id">Purchase Request</label>
                                    <select class="form-select @error('purchase_request_id') is-invalid @enderror" 
                                            id="purchase_request_id" name="purchase_request_id" required>
                                        <option value="">Select Purchase Request</option>
                                        @foreach($purchaseRequests as $pr)
                                            <option value="{{ $pr->id }}" 
                                                {{ old('purchase_request_id') == $pr->id ? 'selected' : '' }}
                                                data-contract-id="{{ $pr->contract_id }}">
                                                {{ $pr->pr_number }} - {{ $pr->contract->contract_id }}
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
                                                {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                                {{ $supplier->name }}
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
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="delivery_date">Delivery Date</label>
                                    <input type="date" class="form-control @error('delivery_date') is-invalid @enderror" 
                                           id="delivery_date" name="delivery_date" 
                                           value="{{ old('delivery_date') }}" required>
                                    @error('delivery_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="payment_terms">Payment Terms</label>
                                    <input type="text" class="form-control @error('payment_terms') is-invalid @enderror" 
                                           id="payment_terms" name="payment_terms" 
                                           value="{{ old('payment_terms') }}" required>
                                    @error('payment_terms')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="shipping_terms">Shipping Terms</label>
                            <textarea class="form-control @error('shipping_terms') is-invalid @enderror" 
                                      id="shipping_terms" name="shipping_terms" rows="2" required>{{ old('shipping_terms') }}</textarea>
                            @error('shipping_terms')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="card mt-4">
                            <div class="card-header">
                                <h5 class="mb-0">Items</h5>
                            </div>
                            <div class="card-body">
                                <div id="items-container">
                                    <!-- Items will be loaded here based on selected purchase request -->
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-4">
                            <label for="notes">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="text-end mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Create Purchase Order
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.getElementById('purchase_request_id').addEventListener('change', function() {
        const purchaseRequestId = this.value;
        if (purchaseRequestId) {
            fetch(`/api/purchase-requests/${purchaseRequestId}/items`)
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById('items-container');
                    container.innerHTML = '';

                    data.forEach((item, index) => {
                        container.innerHTML += `
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label>Material</label>
                                    <input type="hidden" name="items[${index}][material_id]" value="${item.material_id}">
                                    <input type="text" class="form-control" value="${item.material.name}" readonly>
                                </div>
                                <div class="col-md-2">
                                    <label>Quantity</label>
                                    <input type="number" class="form-control" name="items[${index}][quantity]" 
                                           value="${item.quantity}" min="0.01" step="0.01" required>
                                </div>
                                <div class="col-md-2">
                                    <label>Unit Price</label>
                                    <input type="number" class="form-control" name="items[${index}][unit_price]" 
                                           min="0.01" step="0.01" required>
                                </div>
                                <div class="col-md-4">
                                    <label>Specifications</label>
                                    <input type="text" class="form-control" name="items[${index}][specifications]" 
                                           value="${item.specifications || ''}">
                                </div>
                            </div>
                        `;
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error loading purchase request items');
                });
        } else {
            document.getElementById('items-container').innerHTML = '';
        }
    });
</script>
@endpush 