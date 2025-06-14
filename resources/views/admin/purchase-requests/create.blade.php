@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Create Purchase Request</h3>
                    <div class="card-tools">
                        <a href="{{ route('purchase-requests.index') }}" class="btn btn-default">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <form action="{{ route('purchase-requests.store') }}" method="POST" id="purchaseRequestForm">
                    @csrf
                    <div class="card-body">
                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        <!-- Request Type Selection -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Request Type</label>
                                    <div class="custom-control custom-radio custom-control-inline">
                                        <input type="radio" id="standalone" name="is_project_related" value="0" class="custom-control-input" {{ request('contract_id') ? '' : 'checked' }}>
                                        <label class="custom-control-label" for="standalone">Standalone Request</label>
                                    </div>
                                    <div class="custom-control custom-radio custom-control-inline">
                                        <input type="radio" id="project_related" name="is_project_related" value="1" class="custom-control-input" {{ request('contract_id') ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="project_related">Contract Related</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Contract Selection (initially hidden) -->
                        <div class="row mb-4" id="projectRelatedFields" style="display: none;">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="contract_id">Contract</label>
                                    @if(request('contract_id'))
                                        @php
                                            $selectedContract = $contracts->firstWhere('id', request('contract_id'));
                                        @endphp
                                        <input type="text" class="form-control bg-light" value="{{ $selectedContract ? ($selectedContract->contract_number . ' - ' . ($selectedContract->name ?? $selectedContract->title ?? '') ) : '' }}" readonly>
                                        <input type="hidden" name="contract_id" value="{{ request('contract_id') }}">
                                    @else
                                        <select name="contract_id" id="contract_id" class="form-control">
                                            <option value="">Select a contract</option>
                                            @foreach($contracts as $contract)
                                                <option value="{{ $contract->id }}" {{ (request('contract_id') == $contract->id) ? 'selected' : '' }}>
                                                    {{ $contract->contract_number }} - {{ $contract->name ?? $contract->title ?? '' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Items Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h4>Request Items</h4>
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="itemsTable">
                                        <thead>
                                            <tr>
                                                <th>Material</th>
                                                <th>Description</th>
                                                <th>Quantity</th>
                                                <th>Unit</th>
                                                <th>Estimated Unit Price</th>
                                                <th>Total Amount</th>
                                                <th>Preferred Brand</th>
                                                <th>Preferred Supplier</th>
                                                <th>Notes</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="items-container">
                                            @if(isset($prefillItems) && count($prefillItems) > 0)
                                                @foreach($prefillItems as $index => $item)
                                                    <tr class="item-row">
                                                        <td>
                                                            <div class="material-search-container position-relative">
                                                                <input type="text" class="form-control material-search-input" placeholder="Search material..." value="{{ $item['material_name'] ?? (App\Models\Material::find($item['material_id'])->name ?? '') }}" required>
                                                                <input type="hidden" class="material-id-input" name="items[{{ $index }}][material_id]" value="{{ $item['material_id'] }}" required>
                                                                <div class="material-search-results list-group position-absolute w-100" style="z-index: 1000;"></div>
                                                                <div class="invalid-feedback">Please select a material.</div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <input type="text" name="items[{{ $index }}][description]" class="form-control" value="{{ $item['description'] }}" required>
                                                        </td>
                                                        <td>
                                                            <input type="number" name="items[{{ $index }}][quantity]" class="form-control quantity" step="0.01" value="{{ $item['quantity'] }}" required>
                                                        </td>
                                                        <td>
                                                            <input type="text" name="items[{{ $index }}][unit]" class="form-control unit" value="{{ $item['unit'] }}" readonly>
                                                        </td>
                                                        <td>
                                                            <input type="number" name="items[{{ $index }}][estimated_unit_price]" class="form-control unit-price" step="0.01" value="{{ $item['estimated_unit_price'] }}" required>
                                                        </td>
                                                        <td>
                                                            <input type="number" name="items[{{ $index }}][total_amount]" class="form-control total-amount" value="{{ $item['total_amount'] }}" readonly>
                                                        </td>
                                                        <td>
                                                            <input type="text" name="items[{{ $index }}][preferred_brand]" class="form-control" value="{{ $item['preferred_brand'] }}">
                                                        </td>
                                                        <td>
                                                            <select name="items[{{ $index }}][preferred_supplier_id]" class="form-control supplier-select" required>
                                                                <option value="">Select Supplier</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input type="text" name="items[{{ $index }}][notes]" class="form-control" value="{{ $item['notes'] }}">
                                                        </td>
                                                        <td>
                                                            <button type="button" class="btn btn-danger btn-sm remove-row">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                            <tr class="item-row">
                                                <td>
                                                    <div class="material-search-container position-relative">
                                                        <input type="text" class="form-control material-search-input" placeholder="Search material..." required>
                                                        <input type="hidden" class="material-id-input" name="items[0][material_id]" required>
                                                        <div class="material-search-results list-group position-absolute w-100" style="z-index: 1000;"></div>
                                                        <div class="invalid-feedback">Please select a material.</div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <input type="text" name="items[0][description]" class="form-control" required>
                                                </td>
                                                <td>
                                                    <input type="number" name="items[0][quantity]" class="form-control quantity" step="0.01" required>
                                                </td>
                                                <td>
                                                    <input type="text" name="items[0][unit]" class="form-control unit" readonly>
                                                </td>
                                                <td>
                                                    <input type="number" name="items[0][estimated_unit_price]" class="form-control unit-price" step="0.01" required>
                                                </td>
                                                <td>
                                                    <input type="number" name="items[0][total_amount]" class="form-control total-amount" readonly>
                                                </td>
                                                <td>
                                                    <input type="text" name="items[0][preferred_brand]" class="form-control">
                                                </td>
                                                <td>
                                                        <select name="items[0][preferred_supplier_id]" class="form-control supplier-select" required>
                                                        <option value="">Select Supplier</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="text" name="items[0][notes]" class="form-control">
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-danger btn-sm remove-row">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                                <button type="button" class="btn btn-success" id="addRow">
                                    <i class="fas fa-plus"></i> Add Item
                                </button>
                            </div>
                        </div>

                        <!-- Notes Section -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="notes">Notes</label>
                                    <textarea name="notes" id="notes" class="form-control" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Create Purchase Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
const baseUrl = '{{ url("/") }}';

// Ensure these are globally available or passed to functions as needed
window.suppliers = @json($suppliers ?? []);
window.materials = @json($materials ?? []);

document.addEventListener('DOMContentLoaded', function() {
    // Show/hide project related fields
    const projectRelatedFields = document.getElementById('projectRelatedFields');
    const projectRelatedRadio = document.getElementById('project_related');
    
    projectRelatedRadio.addEventListener('change', function() {
        projectRelatedFields.style.display = this.checked ? 'flex' : 'none';
    });

    // Add new row
    let rowCount = document.querySelectorAll('.item-row').length; // Start rowCount based on existing rows
    const itemsContainer = document.getElementById('items-container'); // Get the tbody for items

    document.getElementById('addRow').addEventListener('click', function() {
        const templateRow = document.querySelector('.item-row');
        if (!templateRow) {
            console.error("No template row found. Please ensure at least one .item-row exists initially or provide a hidden template.");
            return;
        }

        const newRow = templateRow.cloneNode(true); // Clone the first row
        
        // Clear values and update names for new row
        newRow.querySelectorAll('input, select').forEach(input => {
            input.name = input.name.replace(/\[\d+\]/, `[${rowCount}]`);
            input.value = '';
            input.classList.remove('is-invalid'); // Clear validation styles
        });

        // Clear search input and hidden material ID for the new row
        newRow.querySelector('.material-search-input').value = '';
        newRow.querySelector('.material-id-input').value = '';
        newRow.querySelector('.material-search-results').innerHTML = '';
        newRow.querySelector('.unit').value = ''; // Clear unit
        newRow.querySelector('.total-amount').value = ''; // Clear total amount
        newRow.querySelector('.supplier-select').innerHTML = '<option value="">Select Supplier</option>'; // Clear suppliers

        itemsContainer.appendChild(newRow);
        setupMaterialSearch(newRow); // Initialize search for the new row
        setupRowCalculations(newRow); // Initialize calculations for the new row
        rowCount++;
    });

    // Remove row
    itemsContainer.addEventListener('click', function(e) {
        if (e.target.closest('.remove-row')) {
            e.target.closest('.item-row').remove();
            // Note: Re-indexing names after removal is more complex and might not be strictly necessary
            // for simple form submissions if your backend handles it flexibly.
            // If strict 0-indexed arrays are required, you'd need to re-index all row names here.
        }
    });

    // Debounce function
    function debounce(func, delay) {
        let timeout;
        return function(...args) {
            const context = this;
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(context, args), delay);
        };
    }

    // Setup Material Search functionality
    function setupMaterialSearch(container) {
        const searchInput = container.querySelector('.material-search-input');
        const materialIdInput = container.querySelector('.material-id-input');
        const searchResultsDiv = container.querySelector('.material-search-results');
        const unitInput = container.querySelector('.unit');
        const unitPriceInput = container.querySelector('.unit-price');
        const supplierSelect = container.querySelector('.supplier-select');

        const performSearch = debounce(function() {
            const query = searchInput.value.trim();
            if (query.length < 2) {
                searchResultsDiv.innerHTML = '';
                return;
            }

            fetch(`${baseUrl}/api/materials/search?query=${query}`)
                .then(response => response.json())
                .then(materials => {
                    searchResultsDiv.innerHTML = '';
                    if (materials.length > 0) {
                        materials.forEach(material => {
                            const item = document.createElement('a');
                            item.href = '#';
                            item.classList.add('list-group-item', 'list-group-item-action');
                            item.dataset.materialId = material.id;
                            item.dataset.materialName = material.name;
                            item.dataset.materialCode = material.code;
                            item.dataset.unit = material.unit;
                            item.dataset.price = material.srp_price || material.base_price; // Use srp_price if available, else base_price
                            item.dataset.suppliers = JSON.stringify(material.suppliers || []); // Pass suppliers as JSON string
                            item.textContent = `${material.name} (${material.code}) - ${material.unit}`;
                            searchResultsDiv.appendChild(item);
                        });
                    } else {
                        searchResultsDiv.innerHTML = '<div class="list-group-item">No materials found</div>';
                    }
                })
                .catch(error => {
                    console.error('Error searching materials:', error);
                    searchResultsDiv.innerHTML = '<div class="list-group-item text-danger">Error searching</div>';
                });
        }, 300);

        searchInput.addEventListener('input', performSearch);

        // Handle selection from search results
        searchResultsDiv.addEventListener('click', function(e) {
            if (e.target.classList.contains('list-group-item-action')) {
                e.preventDefault();
                const selectedResult = e.target;
                const materialId = selectedResult.dataset.materialId;
                const materialName = selectedResult.dataset.materialName;
                const materialCode = selectedResult.dataset.materialCode;
                const unit = selectedResult.dataset.unit;
                const price = selectedResult.dataset.price;
                const suppliers = JSON.parse(selectedResult.dataset.suppliers);

                searchInput.value = `${materialName} (${materialCode})`; // Display full name in input
                materialIdInput.value = materialId;
                unitInput.value = unit;
                unitPriceInput.value = price;
                searchResultsDiv.innerHTML = ''; // Clear results

                // Populate preferred supplier dropdown
                supplierSelect.innerHTML = '<option value="">Select Supplier</option>';
                suppliers.forEach(supplier => {
                    const option = document.createElement('option');
                    option.value = supplier.id;
                    option.textContent = supplier.name;
                    supplierSelect.appendChild(option);
                });

                // Trigger change to recalculate total
                const quantityInput = container.querySelector('.quantity');
                const event = new Event('input', { bubbles: true });
                quantityInput.dispatchEvent(event);
            }
        });

        // Clear hidden ID and unit if search input is cleared
        searchInput.addEventListener('change', function() {
            if (searchInput.value.trim() === '') {
                materialIdInput.value = '';
                unitInput.value = '';
                unitPriceInput.value = '';
                supplierSelect.innerHTML = '<option value="">Select Supplier</option>'; // Clear suppliers
                const quantityInput = container.querySelector('.quantity');
                const event = new Event('input', { bubbles: true });
                quantityInput.dispatchEvent(event); // Trigger recalculation
            }
        });
    }

    // Function to set up quantity and unit price calculations for a row
    function setupRowCalculations(row) {
        const quantityInput = row.querySelector('.quantity');
        const unitPriceInput = row.querySelector('.unit-price');
        const totalAmountInput = row.querySelector('.total-amount');

        const calculateTotal = function() {
            const quantity = parseFloat(quantityInput.value) || 0;
            const unitPrice = parseFloat(unitPriceInput.value) || 0;
            const total = (quantity * unitPrice).toFixed(2);
            totalAmountInput.value = total;
        };

        quantityInput.addEventListener('input', calculateTotal);
        unitPriceInput.addEventListener('input', calculateTotal);
    }

    // Initialize search and calculations for all existing rows
    document.querySelectorAll('.item-row').forEach(row => {
        setupMaterialSearch(row);
        setupRowCalculations(row);
    });

    // Initially hide project related fields if not checked on load
    if (!projectRelatedRadio.checked) {
        projectRelatedFields.style.display = 'none';
    }
});
</script>
@endpush
@endsection