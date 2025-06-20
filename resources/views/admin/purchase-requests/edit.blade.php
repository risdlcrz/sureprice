@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Purchase Request</h3>
                    <div class="card-tools">
                        <a href="{{ route('purchase-requests.show', $purchaseRequest) }}" class="btn btn-default">
                            <i class="fas fa-arrow-left"></i> Back to Details
                        </a>
                    </div>
                </div>
                <form action="{{ route('purchase-requests.update', $purchaseRequest) }}" method="POST" id="purchaseRequestForm">
                    @csrf
                    @method('PUT')
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
                        @if($purchaseRequest->contract_id)
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Request Type</label>
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input type="radio" id="project_related" name="is_project_related" value="1" class="custom-control-input" checked disabled>
                                            <label class="custom-control-label" for="project_related">Project Related</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="is_project_related" value="1">
                            <input type="hidden" name="contract_id" value="{{ $purchaseRequest->contract_id }}">
                        @else
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Request Type</label>
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input type="radio" id="standalone" name="is_project_related" value="0" class="custom-control-input" checked disabled>
                                            <label class="custom-control-label" for="standalone">Standalone Request</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="is_project_related" value="0">
                        @endif

                        <!-- Project/Contract Selection -->
                        @if($purchaseRequest->contract_id && $purchaseRequest->contract)
                            <div class="row mb-4" id="projectRelatedFields" style="display: flex;">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="contract_id">Contract</label>
                                        <input type="text" class="form-control bg-light" value="{{ $purchaseRequest->contract->contract_number . ' - ' . ($purchaseRequest->contract->name ?? $purchaseRequest->contract->title ?? '') }}" readonly>
                                        <input type="hidden" name="contract_id" value="{{ $purchaseRequest->contract_id }}">
                                    </div>
                                </div>
                            </div>
                        @endif

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
                                        <tbody>
                                            @foreach($purchaseRequest->items as $index => $item)
                                                <tr class="item-row">
                                                    <td>
                                                        <select name="items[{{ $index }}][material_id]" class="form-control material-select" required>
                                                            <option value="">Select Material</option>
                                                            @foreach($materials as $material)
                                                                <option value="{{ $material->id }}"
                                                                    data-unit="{{ $material->unit }}"
                                                                    data-suppliers='@json($material->suppliers->map(fn($s) => ["id"=>$s->id,"name"=>$s->company_name]))'
                                                                    {{ $item->material_id == $material->id ? 'selected' : '' }}>
                                                                    {{ $material->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="items[{{ $index }}][description]" class="form-control" value="{{ $item->description }}" required>
                                                    </td>
                                                    <td>
                                                        <input type="number" name="items[{{ $index }}][quantity]" class="form-control quantity" step="0.01" value="{{ $item->quantity }}" required>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="items[{{ $index }}][unit]" class="form-control unit" value="{{ $item->unit }}" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="number" name="items[{{ $index }}][estimated_unit_price]" class="form-control unit-price" step="0.01" value="{{ $item->estimated_unit_price }}" required>
                                                    </td>
                                                    <td>
                                                        <input type="number" name="items[{{ $index }}][total_amount]" class="form-control total-amount" value="{{ $item->total_amount }}" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="items[{{ $index }}][preferred_brand]" class="form-control" value="{{ $item->preferred_brand }}">
                                                    </td>
                                                    <td>
                                                        <select name="items[{{ $index }}][preferred_supplier_id]" class="form-control supplier-select" required data-selected-supplier="{{ $item->preferred_supplier_id }}">
                                                            <option value="">Select Supplier</option>
                                                            <!-- Options will be dynamically populated based on selected material -->
                                                        </select>
                                                        <script>console.log('Blade Debug: Item Index {{ $index }}, Preferred Supplier ID: {{ $item->preferred_supplier_id }}');</script>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="items[{{ $index }}][notes]" class="form-control" value="{{ $item->notes }}">
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-danger btn-sm remove-row">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
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
                                    <textarea name="notes" id="notes" class="form-control" rows="3">{{ $purchaseRequest->notes }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Purchase Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Show/hide project related fields
    const projectRelatedFields = document.getElementById('projectRelatedFields');
    const projectRelatedRadio = document.getElementById('project_related');
    
    // Only add event listener if the radio button exists (i.e., not hidden by conditional Blade logic)
    if (projectRelatedRadio) {
        projectRelatedRadio.addEventListener('change', function() {
            if (projectRelatedFields) {
                projectRelatedFields.style.display = this.checked ? 'flex' : 'none';
            }
        });
    }

    // Add new row
    let rowCount = {{ $purchaseRequest->items->count() }};
    document.getElementById('addRow').addEventListener('click', function() {
        const tbody = document.querySelector('#itemsTable tbody');
        const newRow = tbody.querySelector('.item-row').cloneNode(true);
        
        // Update input names
        newRow.querySelectorAll('input, select').forEach(input => {
            input.name = input.name.replace(/\[\d+\]/, `[${rowCount}]`);
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
            row.querySelector('.unit').value = unit;
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
        var suppliers = [];
        if (materialId) {
            var option = $(this).find('option:selected');
            suppliers = option.data('suppliers') || []; // This should be an array of {id, name}
        }
        var $supplierSelect = $row.find('.supplier-select');
        $supplierSelect.empty().append('<option value="">Select Supplier</option>');
        suppliers.forEach(function(supplier) {
            $supplierSelect.append('<option value="'+supplier.id+'">'+supplier.name+'</option>');
        });

        // Set the previously selected supplier from the data attribute on the select itself
        var previouslySelectedSupplierId = $supplierSelect.data('selected-supplier');
        
        console.log('--- Material Change Debug ---');
        console.log('Material ID:', materialId);
        console.log('Available Suppliers (from data-suppliers):', suppliers);
        console.log('Previously Selected Supplier ID (from data-selected-supplier):', previouslySelectedSupplierId);

        if (previouslySelectedSupplierId) {
            $supplierSelect.val(previouslySelectedSupplierId);
            console.log('Attempted to set supplier value to:', previouslySelectedSupplierId);
        }
        console.log('Final Supplier Select Value:', $supplierSelect.val());
        console.log('----------------------------');

        $supplierSelect.prop('required', true);
    });

    // Trigger change event on page load for existing materials to populate suppliers and set selected
    $('.material-select').each(function() {
        $(this).trigger('change');
    });
});
</script>
@endpush
@endsection 