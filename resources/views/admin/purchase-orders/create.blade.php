@extends('layouts.app')

@section('content')
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

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="purchase_request_id">Purchase Request</label>
                                <select class="form-select @error('purchase_request_id') is-invalid @enderror" 
                                        id="purchase_request_id" name="purchase_request_id" required>
                                    <option value="">Select Purchase Request</option>
                                    @foreach($purchaseRequests as $pr)
                                        <option value="{{ $pr->id }}" 
                                            data-supplier-id="{{ $pr->supplier_id }}"
                                            {{ old('purchase_request_id') == $pr->id ? 'selected' : '' }}
                                            data-contract-id="{{ $pr->contract_id }}">
                                            {{ $pr->request_number }}
                                            @if(isset($pr->department)) - {{ $pr->department }}@endif
                                            @if(isset($pr->requestedBy->name)) ({{ $pr->requestedBy->name }})@endif
                                            @if($pr->contract)
                                                - {{ $pr->contract->contract_id }}
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
                                        <option value="{{ $supplier->id }}">{{ $supplier->company_name }}</option>
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
                            <div class="table-responsive" style="max-height: 400px;">
                                <table class="table table-bordered align-middle" id="items-table">
                                    <thead style="position: sticky; top: 0; background: #f8f9fa; z-index: 2;">
                                        <tr>
                                            <th>Material</th>
                                            <th>Quantity</th>
                                            <th>Unit Price</th>
                                            <th>Total Price</th>
                                            <th>Specifications</th>
                                        </tr>
                                    </thead>
                                    <tbody id="items-table-body">
                                        <!-- JS will populate rows here -->
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="3" class="text-end">Total Amount:</th>
                                            <th id="total-amount">0.00</th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
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

                    <div id="supplier-display" style="display:none; font-weight: bold; color: #0a58ca; margin-bottom: 10px;"></div>
                    <div id="multi-supplier-warning" class="alert alert-warning" style="display:none;"></div>

                    <div id="supplier-details" style="display:none; max-width: 350px; margin-top: 1rem; border: 1px solid #ddd; border-radius: 6px; background: #f8f9fa; padding: 0.5rem 1rem; font-size: 0.95em;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <h6 style="margin: 0; font-weight: 600;">Supplier Details</h6>
                            <button type="button" id="toggle-supplier-details" class="btn btn-sm btn-link" style="text-decoration: none;">Show</button>
                        </div>
                        <div id="supplier-info" style="display: none;"></div>
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

    
@endsection

@push('scripts')
<script>
    const apiBase = "{{ url('api') }}";
    const suppliers = @json($suppliers ?? []);
    const purchaseRequests = @json($purchaseRequests);

    function getSupplierNameById(id) {
        const supplier = suppliers.find(s => s.id == id);
        return supplier ? supplier.company_name : '';
    }

    function getUniqueSuppliers(items) {
        const supplierIds = items.map(i => i.supplier_id).filter(Boolean);
        return [...new Set(supplierIds)];
    }

    function renderSupplierDropdown(suppliersList, selectedSupplierId = null) {
        const supplierSelect = document.getElementById('supplier_id');
        supplierSelect.innerHTML = '<option value="">Select Supplier</option>';
        suppliersList.forEach(supplierId => {
            const supplier = suppliers.find(s => s.id == supplierId);
            if (supplier) {
                supplierSelect.innerHTML += `<option value="${supplier.id}">${supplier.company_name}</option>`;
            }
        });
        // Always leave the dropdown enabled and unselected by default
            supplierSelect.value = '';
        supplierSelect.removeAttribute('readonly');
        // Remove any previous hidden input
        const prevHidden = document.getElementById('hidden_supplier_id');
        if (prevHidden) prevHidden.remove();
    }

    function renderItemsTable(items, filterSupplierId = null) {
        const tbody = document.getElementById('items-table-body');
        tbody.innerHTML = '';
        let totalAmount = 0;
        items.forEach((item, index) => {
            if (filterSupplierId && item.supplier_id != filterSupplierId) return;
            const quantity = parseInt(item.quantity) || 0;
            const unitPrice = parseFloat(item.estimated_unit_price || item.unit_price) || 0;
            const totalPrice = quantity * unitPrice;
            totalAmount += totalPrice;
            tbody.innerHTML += `
                <tr>
                    <td>
                        <input type="hidden" name="items[${index}][material_id]" value="${item.material_id}">
                        <input type="text" class="form-control" value="${item.material?.name || ''}" readonly>
                    </td>
                    <td>
                        <input type="number" class="form-control item-qty" name="items[${index}][quantity]" value="${quantity}" min="1" step="1" required data-index="${index}" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                    </td>
                    <td>
                        <input type="number" class="form-control item-unit-price" name="items[${index}][unit_price]" value="${unitPrice.toFixed(2)}" min="0.01" step="0.01" required data-index="${index}">
                    </td>
                    <td>
                        <input type="text" class="form-control item-total-price" value="${totalPrice.toFixed(2)}" readonly tabindex="-1">
                    </td>
                    <td>
                        <input type="text" class="form-control" name="items[${index}][specifications]" value="${item.specifications || ''}">
                    </td>
                </tr>
            `;
        });
        document.getElementById('total-amount').textContent = totalAmount.toFixed(2);
    }

    function recalculateTotals() {
        const rows = document.querySelectorAll('#items-table-body tr');
        let totalAmount = 0;
        rows.forEach((row, idx) => {
            const qtyInput = row.querySelector('.item-qty');
            const priceInput = row.querySelector('.item-unit-price');
            const totalInput = row.querySelector('.item-total-price');
            const quantity = parseInt(qtyInput.value) || 0;
            const unitPrice = parseFloat(priceInput.value) || 0;
            const totalPrice = quantity * unitPrice;
            totalInput.value = totalPrice.toFixed(2);
            totalAmount += totalPrice;
        });
        document.getElementById('total-amount').textContent = totalAmount.toFixed(2);
    }

    document.getElementById('purchase_request_id').addEventListener('change', function() {
        const purchaseRequestId = this.value;
        if (purchaseRequestId) {
            fetch(`${apiBase}/purchase-requests/${purchaseRequestId}/items`)
                .then(response => response.json())
                .then(data => {
                    // Add .material if missing (for compatibility)
                    data.forEach(item => {
                        if (!item.material && item.material_id && purchaseRequests) {
                            const pr = purchaseRequests.find(pr => pr.id == purchaseRequestId);
                            if (pr && pr.items) {
                                const found = pr.items.find(i => i.material_id == item.material_id);
                                if (found && found.material) item.material = found.material;
                            }
                        }
                    });
                    const uniqueSuppliers = getUniqueSuppliers(data);
                    renderSupplierDropdown(uniqueSuppliers);
                    // Only render items table when supplier is selected
                    document.getElementById('supplier_id').addEventListener('change', function() {
                        renderItemsTable(data, this.value);
                    });
                    // Clear items table until supplier is chosen
                    renderItemsTable([], null);
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error loading purchase request items');
                });
        } else {
            renderItemsTable([]);
            renderSupplierDropdown([]);
        }
    });

    // Collapsible Supplier Details
    let supplierDetailsVisible = false;
    const detailsDiv = document.getElementById('supplier-details');
    const infoDiv = document.getElementById('supplier-info');
    const toggleBtn = document.getElementById('toggle-supplier-details');

    document.getElementById('supplier_id').addEventListener('change', function() {
        const supplierId = this.value;
        const supplier = suppliers.find(s => s.id == supplierId);
        if (supplier) {
            infoDiv.innerHTML = `
                <div><strong>Name:</strong> ${supplier.company_name}</div>
                <div><strong>Email:</strong> ${supplier.email || ''}</div>
                <div><strong>Phone:</strong> ${supplier.phone || ''}</div>
                <div><strong>Address:</strong> ${supplier.address || ''}</div>
            `;
            detailsDiv.style.display = 'block';
            infoDiv.style.display = 'none';
            supplierDetailsVisible = false;
            toggleBtn.textContent = 'Show';
        } else {
            detailsDiv.style.display = 'none';
            infoDiv.innerHTML = '';
        }
    });

    toggleBtn.addEventListener('click', function() {
        supplierDetailsVisible = !supplierDetailsVisible;
        infoDiv.style.display = supplierDetailsVisible ? 'block' : 'none';
        toggleBtn.textContent = supplierDetailsVisible ? 'Hide' : 'Show';
    });

    document.querySelector('form').addEventListener('submit', function(e) {
        alert('Form is trying to submit!');
    });

    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('item-qty')) {
            // Remove any non-digit characters
            e.target.value = e.target.value.replace(/[^0-9]/g, '');
        }
    });

    // Prevent typing decimal points
    const qtyInputs = document.querySelectorAll('.item-qty');
    qtyInputs.forEach(input => {
        input.addEventListener('keydown', function(e) {
            if (e.key === '.' || e.key === ',' || e.key === 'e') {
                e.preventDefault();
            }
        });
    });
</script>
@endpush 