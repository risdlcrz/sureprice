@extends('layouts.app')

@section('content')
<div class="container mt-4 mb-5">
    <h1 class="text-center mb-4">{{ $edit_mode ? 'Edit' : 'Create' }} Contract Agreement</h1>
    
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    
    <form method="POST" action="{{ $edit_mode ? route('contracts.update', $contract->id) : route('contracts.store') }}" enctype="multipart/form-data" id="contractForm">
        @csrf
        @if($edit_mode)
            @method('PUT')
        @endif

        <!-- Contractor Information -->
        <div class="form-section">
            <h2>Contractor Information</h2>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="contractor_name" class="form-label">Contractor Name</label>
                    <input type="text" class="form-control" id="contractor_name" name="contractor_name" 
                        value="{{ old('contractor_name', $edit_mode ? $existing_contractor->name ?? config('contractor.name') : config('contractor.name')) }}" required>
                </div>
                <!-- ... Rest of the contractor fields ... -->
            </div>
        </div>

        <!-- Client Information -->
        <div class="form-section">
            <h2>Client Information</h2>
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <input type="text" class="form-control" id="client_search" placeholder="Search client by name or email">
                        <button class="btn btn-outline-secondary" type="button" id="client_search_btn">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                    <div class="search-results" id="client_search_results"></div>
                </div>
            </div>
            <!-- ... Rest of the client fields ... -->
        </div>

        <!-- Items Section -->
        <div class="form-section">
            <h2>Items</h2>
            <div class="row">
                <div class="col-md-12">
                    <div id="item_container">
                        @if($edit_mode && !empty($existing_items))
                            @foreach($existing_items as $index => $item)
                                <div class="row item-row mb-2">
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <input type="text" class="form-control material-search" 
                                                placeholder="Search material" 
                                                data-index="{{ $index }}"
                                                value="{{ $item->description }}">
                                            <input type="hidden" name="item_material_id[]" value="{{ $item->material_id ?? '' }}">
                                        </div>
                                        <div class="material-search-results"></div>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" class="form-control quantity" name="item_quantity[]" 
                                            value="{{ $item->quantity }}" min="0" step="0.01" required>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" class="form-control amount" name="item_amount[]" 
                                            value="{{ $item->amount }}" min="0" step="0.01" required>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="text" class="form-control total" 
                                            value="{{ number_format($item->total, 2) }}" readonly>
                                    </div>
                                    <div class="col-md-1">
                                        <div class="form-check">
                                            <input class="form-check-input has-preferred-suppliers" type="checkbox" 
                                                {{ $item->has_preferred_suppliers ? 'checked' : '' }}>
                                            <label class="form-check-label">Preferred</label>
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-danger btn-sm remove-item">×</button>
                                    </div>
                                    <div class="col-12 supplier-section" style="display: none;">
                                        <select class="form-select supplier-select" name="item_supplier_id[]">
                                            <option value="">Select Supplier</option>
                                        </select>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    <button type="button" class="btn btn-secondary mt-2" id="add_item">Add Item</button>
                </div>
            </div>
        </div>

        <!-- ... Rest of the form ... -->

        <!-- Submit Button -->
        <div class="row">
            <div class="col-md-12 text-center">
                <button type="submit" class="btn btn-primary btn-lg">{{ $edit_mode ? 'Update' : 'Submit' }} Contract</button>
                @if($edit_mode)
                    <a href="{{ route('contracts.show', $contract->id) }}" class="btn btn-secondary btn-lg ms-2">Cancel</a>
                @endif
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    // Material search functionality
    function initializeMaterialSearch() {
        $('.material-search').each(function() {
            const searchInput = $(this);
            const searchResults = searchInput.closest('.input-group').siblings('.material-search-results');
            const materialIdInput = searchInput.siblings('input[name="item_material_id[]"]');
            const hasPreferredSuppliersCheckbox = searchInput.closest('.item-row').find('.has-preferred-suppliers');
            const supplierSection = searchInput.closest('.item-row').find('.supplier-section');
            const supplierSelect = supplierSection.find('.supplier-select');

            searchInput.on('input', function() {
                const query = $(this).val().trim();
                if (query.length < 2) {
                    searchResults.hide();
                    return;
                }

                $.get('{{ route("materials.search") }}', { query: query })
                    .done(function(data) {
                        searchResults.empty();
                        data.forEach(function(material) {
                            const div = $('<div>')
                                .addClass('search-result-item')
                                .text(material.name)
                                .data('material', material);
                            searchResults.append(div);
                        });
                        searchResults.show();
                    });
            });

            searchResults.on('click', '.search-result-item', function() {
                const material = $(this).data('material');
                searchInput.val(material.name);
                materialIdInput.val(material.id);
                searchResults.hide();

                // Update price if available
                const amountInput = searchInput.closest('.item-row').find('.amount');
                if (material.default_price) {
                    amountInput.val(material.default_price);
                }

                // Show/hide preferred suppliers checkbox based on material
                hasPreferredSuppliersCheckbox.prop('checked', material.has_preferred_suppliers);
                if (material.has_preferred_suppliers) {
                    loadSuppliers(material.id, supplierSelect);
                    supplierSection.show();
                } else {
                    supplierSection.hide();
                }

                calculateItemTotal(amountInput);
            });
        });
    }

    // Load suppliers for a material
    function loadSuppliers(materialId, supplierSelect) {
        $.get(`{{ url('materials') }}/${materialId}/suppliers`, { preferred: true })
            .done(function(suppliers) {
                supplierSelect.empty().append('<option value="">Select Supplier</option>');
                suppliers.forEach(function(supplier) {
                    supplierSelect.append(
                        $('<option>')
                            .val(supplier.id)
                            .text(supplier.name)
                            .data('price', supplier.pivot.price)
                    );
                });
            });
    }

    // Add new item row
    $('#add_item').click(function() {
        const newRow = $(`
            <div class="row item-row mb-2">
                <div class="col-md-4">
                    <div class="input-group">
                        <input type="text" class="form-control material-search" placeholder="Search material">
                        <input type="hidden" name="item_material_id[]">
                    </div>
                    <div class="material-search-results"></div>
                </div>
                <div class="col-md-2">
                    <input type="number" class="form-control quantity" name="item_quantity[]" placeholder="Qty" min="0" step="0.01" required>
                </div>
                <div class="col-md-2">
                    <input type="number" class="form-control amount" name="item_amount[]" placeholder="Amount" min="0" step="0.01" required>
                </div>
                <div class="col-md-2">
                    <input type="text" class="form-control total" placeholder="Total" readonly>
                </div>
                <div class="col-md-1">
                    <div class="form-check">
                        <input class="form-check-input has-preferred-suppliers" type="checkbox">
                        <label class="form-check-label">Preferred</label>
                    </div>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger btn-sm remove-item">×</button>
                </div>
                <div class="col-12 supplier-section" style="display: none;">
                    <select class="form-select supplier-select" name="item_supplier_id[]">
                        <option value="">Select Supplier</option>
                    </select>
                </div>
            </div>
        `);
        
        $('#item_container').append(newRow);
        initializeMaterialSearch();
    });

    // Initialize existing rows
    initializeMaterialSearch();

    // ... Rest of your existing JavaScript ...
</script>
@endpush

@push('styles')
<style>
    /* ... Your existing styles ... */
    
    .material-search-results {
        position: absolute;
        z-index: 1000;
        width: 100%;
        max-height: 200px;
        overflow-y: auto;
        background: white;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        display: none;
        margin-top: 2px;
    }

    .supplier-section {
        margin-top: 10px;
        padding-left: 15px;
    }
</style>
@endpush 