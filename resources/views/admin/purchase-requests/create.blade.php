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
                                        <label class="custom-control-label" for="project_related">Project Related</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Project/Contract Selection (initially hidden) -->
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
                                                            <select name="items[{{ $index }}][material_id]" class="form-control material-select" required>
                                                                <option value="">Select Material</option>
                                                                @foreach($materials as $material)
                                                                    <option value="{{ $material->id }}"
                                                                        data-unit="{{ $material->unit }}"
                                                                        data-suppliers="{{ $material->suppliers->pluck('id')->toJson() }}"
                                                                        data-price="{{ $material->srp_price ?? $material->base_price }}"
                                                                        {{ $item['material_id'] == $material->id ? 'selected' : '' }}>
                                                                        {{ $material->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
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
                                                    <select name="items[0][material_id]" class="form-control material-select" required>
                                                        <option value="">Select Material</option>
                                                        @foreach($materials as $material)
                                                            <option value="{{ $material->id }}"
                                                                data-unit="{{ $material->unit }}"
                                                                data-suppliers="{{ $material->suppliers->pluck('id')->toJson() }}"
                                                                data-price="{{ $material->srp_price ?? $material->base_price }}">
                                                                {{ $material->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
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
    let rowCount = 1;
    document.getElementById('addRow').addEventListener('click', function() {
        const tbody = document.querySelector('#itemsTable tbody');
        const newRow = tbody.querySelector('.item-row').cloneNode(true);
        
        // Update input names
        newRow.querySelectorAll('input, select').forEach(input => {
            input.name = input.name.replace('[0]', `[${rowCount}]`);
            input.value = '';
        });
        
        tbody.appendChild(newRow);
        rowCount++;
    });

    // Remove row
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-row') || e.target.parentElement.classList.contains('remove-row')) {
            const tbody = document.querySelector('#itemsTable tbody');
            if (tbody.children.length > 1) {
                e.target.closest('tr').remove();
            }
        }
    });

    // Calculate totals
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('quantity') || e.target.classList.contains('unit-price')) {
            const row = e.target.closest('tr');
            const quantity = parseFloat(row.querySelector('.quantity').value) || 0;
            const unitPrice = parseFloat(row.querySelector('.unit-price').value) || 0;
            row.querySelector('.total-amount').value = (quantity * unitPrice).toFixed(2);
        }
    });

    // Update unit when material is selected
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('material-select')) {
            const row = e.target.closest('tr');
            const selectedOption = e.target.options[e.target.selectedIndex];
            const unit = selectedOption.dataset.unit;
            const price = selectedOption.dataset.price;
            row.querySelector('.unit').value = unit;
            if (price && row.querySelector('.unit-price')) {
                row.querySelector('.unit-price').value = price;
            }
        }
    });

    // Form validation
    document.getElementById('purchaseRequestForm').addEventListener('submit', function(e) {
        const isProjectRelated = document.getElementById('project_related').checked;
        const contractId = document.getElementById('contract_id').value;
        const projectId = document.getElementById('project_id').value;

        if (isProjectRelated && !contractId && !projectId) {
            e.preventDefault();
            alert('Please select either a contract or project for project-related requests.');
        }
    });

    // On material change, update supplier dropdown
    $(document).on('change', '.material-select', function() {
        var $row = $(this).closest('tr');
        var materialId = $(this).val();
        var supplierIds = [];
        if (materialId) {
            var option = $(this).find('option:selected');
            supplierIds = option.data('suppliers') || [];
        }
        var $supplierSelect = $row.find('.supplier-select');
        $supplierSelect.empty().append('<option value="">Select Supplier</option>');
        if (supplierIds.length === 0) {
            $supplierSelect.append('<option value="">No suppliers available</option>');
        } else {
            supplierIds.forEach(function(supplierId) {
                var supplier = window.suppliers.find(function(s) { return s.id == supplierId; });
                if (supplier) {
                    $supplierSelect.append('<option value="'+supplier.id+'">'+supplier.company_name+'</option>');
                }
            });
        }
        $supplierSelect.prop('required', true);
    });

    // On page load, trigger change for all material selects to populate suppliers
    $('.material-select').each(function() { $(this).trigger('change'); });

    // Show/hide project related fields
    function toggleProjectRelatedFields() {
        const isProjectRelated = document.getElementById('project_related').checked;
        document.getElementById('projectRelatedFields').style.display = isProjectRelated ? 'flex' : 'none';
        // Clear items table if switching to standalone
        if (!isProjectRelated) {
            const itemsContainer = document.getElementById('items-container');
            itemsContainer.innerHTML = `<tr class="item-row">
                <td>
                    <select name="items[0][material_id]" class="form-control material-select" required>
                        <option value="">Select Material</option>
                        ${window.materials.map(material => `<option value="${material.id}" data-unit="${material.unit}" data-suppliers='${JSON.stringify(material.suppliers.map(s => s.id))}' data-price="${material.srp_price ?? material.base_price}">${material.name}</option>`).join('')}
                    </select>
                </td>
                <td><input type="text" name="items[0][description]" class="form-control" required></td>
                <td><input type="number" name="items[0][quantity]" class="form-control quantity" step="0.01" required></td>
                <td><input type="text" name="items[0][unit]" class="form-control unit" readonly></td>
                <td><input type="number" name="items[0][estimated_unit_price]" class="form-control unit-price" step="0.01" required></td>
                <td><input type="number" name="items[0][total_amount]" class="form-control total-amount" readonly></td>
                <td><input type="text" name="items[0][preferred_brand]" class="form-control"></td>
                <td><select name="items[0][preferred_supplier_id]" class="form-control supplier-select" required><option value="">Select Supplier</option></select></td>
                <td><input type="text" name="items[0][notes]" class="form-control"></td>
                <td><button type="button" class="btn btn-danger btn-sm remove-row"><i class="fas fa-trash"></i></button></td>
            </tr>`;
            // Trigger change to populate suppliers
            $('.material-select').each(function() { $(this).trigger('change'); });
        }
    }
    document.getElementById('standalone').addEventListener('change', toggleProjectRelatedFields);
    document.getElementById('project_related').addEventListener('change', toggleProjectRelatedFields);
    // On page load, set correct state
    window.addEventListener('DOMContentLoaded', function() {
        toggleProjectRelatedFields();
    });

    // Auto-populate contract items when contract is selected
    const contractSelect = document.getElementById('contract_id');
    const itemsContainer = document.getElementById('items-container');
    if (contractSelect && itemsContainer) {
        contractSelect.addEventListener('change', function() {
            const contractId = this.value;
            if (!contractId) return;
            itemsContainer.innerHTML = `<tr><td colspan="10" class="text-center"><div class="spinner-border text-primary" role="status"></div></td></tr>`;
            fetch(`${baseUrl}/contracts/${contractId}/items`)
                .then(response => response.json())
                .then(items => {
                    if (!items.length) {
                        itemsContainer.innerHTML = `<tr><td colspan="10" class="text-center">No items found for this contract.</td></tr>`;
                        return;
                    }
                    itemsContainer.innerHTML = '';
                    items.forEach((item, index) => {
                        // Build material select with all options and correct data attributes
                        let materialOptions = '<option value="">Select Material</option>';
                        window.materials.forEach(function(material) {
                            const selected = material.id == item.material_id ? 'selected' : '';
                            materialOptions += `<option value="${material.id}" data-unit="${material.unit}" data-suppliers='${JSON.stringify(material.suppliers.map(s => s.id))}' data-price="${material.srp_price ?? material.base_price}" ${selected}>${material.name}</option>`;
                        });
                        itemsContainer.innerHTML += `
                            <tr class="item-row">
                                <td>
                                    <select name="items[${index}][material_id]" class="form-control material-select" required>
                                        ${materialOptions}
                                    </select>
                                </td>
                                <td>
                                    <input type="text" name="items[${index}][description]" class="form-control" value="${item.material_name || ''}" required>
                                </td>
                                <td>
                                    <input type="number" name="items[${index}][quantity]" class="form-control quantity" step="0.01" value="${item.quantity}" required>
                                </td>
                                <td>
                                    <input type="text" name="items[${index}][unit]" class="form-control unit" value="${item.unit}" readonly>
                                </td>
                                <td>
                                    <input type="number" name="items[${index}][estimated_unit_price]" class="form-control unit-price" step="0.01" value="${item.amount}" required>
                                </td>
                                <td>
                                    <input type="number" name="items[${index}][total_amount]" class="form-control total-amount" value="${item.total}" readonly>
                                </td>
                                <td>
                                    <input type="text" name="items[${index}][preferred_brand]" class="form-control">
                                </td>
                                <td>
                                    <select name="items[${index}][preferred_supplier_id]" class="form-control supplier-select" required>
                                        <option value="">Select Supplier</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" name="items[${index}][notes]" class="form-control">
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-sm remove-row">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                    // After rendering, trigger change to populate suppliers
                    $('.material-select').each(function() { $(this).trigger('change'); });
                })
                .catch(error => {
                    itemsContainer.innerHTML = `<tr><td colspan="10" class="text-center text-danger">Error loading contract items.</td></tr>`;
                });
        });
    }
});
</script>
@endpush
@endsection 