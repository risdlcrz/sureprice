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

                        <!-- Contract Selection (always visible for project-related) -->
                        <div class="row mb-4" id="projectRelatedFields">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="contract_search">Search Contract</label>
                                    <input type="text" id="contract_search" class="form-control" placeholder="Search contracts by number or name...">
                                    <div id="contract_search_results" class="list-group position-absolute w-100" style="z-index: 2000; max-height: 200px; overflow-y: auto;"></div>
                                    <input type="hidden" name="contract_id" id="contract_id" value="{{ request('contract_id') }}">
                                    <div id="selected_contract_info" class="mt-2 p-2 bg-light rounded" style="display: none;">
                                        <strong>Selected Contract:</strong> <span id="contract_display"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Items Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h4>Request Items</h4>
                                <div class="mb-2">
                                    <input type="text" id="materialMasterSearch" class="form-control" placeholder="Search for material in purchase request...">
                                    <div id="materialMasterSearchResults" class="list-group position-absolute w-100" style="z-index: 2000;"></div>
                                </div>
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
                                                    @php
                                                        $materialObj = $item['material_obj'];
                                                    @endphp
                                                    <tr class="item-row">
                                                        <td>
                                                            {{ $item['material_name'] }}
                                                            <input type="hidden" name="items[{{ $index }}][material_id]" value="{{ $item['material_id'] }}">
                                                        </td>
                                                        <td>
                                                            <input type="text" name="items[{{ $index }}][description]" class="form-control" value="{{ $item['description'] }}" placeholder="Enter description (required)">
                                                        </td>
                                                        <td>
                                                            <input type="number" name="items[{{ $index }}][quantity]" class="form-control quantity" step="0.01" value="{{ $item['quantity'] }}" required>
                                                        </td>
                                                        <td>
                                                            <input type="text" name="items[{{ $index }}][unit]" class="form-control unit" value="{{ $item['unit'] }}" readonly>
                                                        </td>
                                                        <td>
                                                            <input type="number" name="items[{{ $index }}][estimated_unit_price]" class="form-control unit-price" step="0.01" value="{{ $item['estimated_unit_price'] }}" required readonly>
                                                        </td>
                                                        <td>
                                                            <input type="number" name="items[{{ $index }}][total_amount]" class="form-control total-amount" value="{{ $item['total_amount'] }}" readonly>
                                                        </td>
                                                        <td>
                                                            <input type="text" name="items[{{ $index }}][preferred_brand]" class="form-control" value="{{ $item['preferred_brand'] }}">
                                                        </td>
                                                        <td>
                                                            <select name="items[{{ $index }}][preferred_supplier_id]" class="form-control supplier-select" @if(!$materialObj || !$materialObj->suppliers || $materialObj->suppliers->isEmpty()) disabled @endif>
                                                                <option value="">Select Supplier</option>
                                                                @if($materialObj && $materialObj->suppliers)
                                                                    @foreach($materialObj->suppliers as $supplier)
                                                                        <option value="{{ $supplier->id }}" {{ (isset($item['preferred_supplier_id']) && $item['preferred_supplier_id'] == $supplier->id) ? 'selected' : '' }}>{{ $supplier->company_name ?? $supplier->name }}</option>
                                                                    @endforeach
                                                                @endif
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input type="text" name="items[{{ $index }}][notes]" class="form-control" value="{{ $item['notes'] }}">
                                                        </td>
                                                        <td>
                                                            <button type="button" class="btn btn-outline-secondary btn-sm replace-material" title="Replace Material">
                                                                <i class="fas fa-exchange-alt"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-danger btn-sm remove-row" title="Remove">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                            <tr class="item-row">
                                                <td></td>
                                                <td>
                                                    <input type="text" name="items[0][description]" class="form-control">
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
                                                    <button type="button" class="btn btn-outline-secondary btn-sm replace-material" title="Replace Material">
                                                        <i class="fas fa-exchange-alt"></i>
                                                    </button>
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

<!-- Material Search Modal -->
<div class="modal fade" id="materialSearchModal" tabindex="-1" aria-labelledby="materialSearchModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="materialSearchModalLabel">Select Material</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="text" id="modalMaterialSearchInput" class="form-control mb-2" placeholder="Search materials...">
        <div id="modalMaterialSearchResults" class="list-group" style="max-height: 300px; overflow-y: auto;"></div>
        <div id="modalMaterialSearchWarning" class="alert alert-warning mt-2 d-none"></div>
      </div>
    </div>
  </div>
</div>

<!-- Material Replace Modal -->
<div class="modal fade" id="replaceMaterialModal" tabindex="-1" aria-labelledby="replaceMaterialModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="replaceMaterialModalLabel">Replace Material</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="text" id="replaceMaterialSearchInput" class="form-control mb-2" placeholder="Search materials...">
        <div id="replaceMaterialSearchResults" class="list-group" style="max-height: 300px; overflow-y: auto;"></div>
        <div id="replaceMaterialWarning" class="alert alert-warning mt-2 d-none"></div>
        <div id="replaceMaterialConfirmSection" class="d-none mt-3">
            <h6>Confirm Replacement</h6>
            <div id="replaceMaterialSummary"></div>
            <button type="button" class="btn btn-primary mt-2" id="confirmReplaceMaterialBtn">Confirm Replace</button>
        </div>
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
window.contracts = @json($contracts ?? []);

let materialModalMode = 'add'; // 'add' or 'replace'
let materialModalTargetRow = null;
let replaceTargetRow = null;
let selectedReplaceMaterial = null;

function isMaterialInTable(materialId) {
    return Array.from(document.querySelectorAll('.material-id-input')).some(input => input.value == materialId);
}

function addMaterialRowFromMaster(material) {
    if (isMaterialInTable(material.id)) {
        showMaterialModalWarning('Material already in the table!');
        return;
    }
    let rowCount = document.querySelectorAll('.item-row').length;
    const templateRow = document.querySelector('.item-row');
    const newRow = templateRow.cloneNode(true);
    newRow.querySelectorAll('input, select').forEach(input => {
        input.name = input.name.replace(/\[\d+\]/, `[${rowCount}]`);
        input.value = '';
        input.classList.remove('is-invalid');
    });
    newRow.querySelector('.material-name').value = `${material.name} (${material.code})`;
    newRow.querySelector('.material-id-input').value = material.id;
    newRow.querySelector('.unit').value = material.unit;
    newRow.querySelector('.unit-price').value = material.srp_price || material.base_price;
    newRow.querySelector('.total-amount').value = '';
    newRow.querySelector('.supplier-select').innerHTML = '<option value="">Select Supplier</option>';
    if (material.suppliers && material.suppliers.length > 0) {
        material.suppliers.forEach(supplier => {
            const option = document.createElement('option');
            option.value = supplier.id;
            option.textContent = supplier.name;
            newRow.querySelector('.supplier-select').appendChild(option);
        });
    }
    document.getElementById('items-container').appendChild(newRow);
    setupRowCalculations(newRow);
}

function replaceMaterialInRow(row, material) {
    if (isMaterialInTable(material.id)) {
        showMaterialModalWarning('Material already in the table!');
        return;
    }
    row.querySelector('.material-name').value = `${material.name} (${material.code})`;
    row.querySelector('.material-id-input').value = material.id;
    row.querySelector('.unit').value = material.unit;
    row.querySelector('.unit-price').value = material.srp_price || material.base_price;
    row.querySelector('.supplier-select').innerHTML = '<option value="">Select Supplier</option>';
    if (material.suppliers && material.suppliers.length > 0) {
        material.suppliers.forEach(supplier => {
            const option = document.createElement('option');
            option.value = supplier.id;
            option.textContent = supplier.name;
            row.querySelector('.supplier-select').appendChild(option);
        });
    }
}

function showMaterialModalWarning(msg) {
    const warn = document.getElementById('modalMaterialSearchWarning');
    warn.textContent = msg;
    warn.classList.remove('d-none');
}
function clearMaterialModalWarning() {
    const warn = document.getElementById('modalMaterialSearchWarning');
    warn.textContent = '';
    warn.classList.add('d-none');
}

function openMaterialModal(mode, targetRow = null) {
    materialModalMode = mode;
    materialModalTargetRow = targetRow;
    document.getElementById('modalMaterialSearchInput').value = '';
    document.getElementById('modalMaterialSearchResults').innerHTML = '';
    clearMaterialModalWarning();
    const modal = new bootstrap.Modal(document.getElementById('materialSearchModal'));
    modal.show();
    setTimeout(() => document.getElementById('modalMaterialSearchInput').focus(), 300);
}

document.addEventListener('DOMContentLoaded', function() {
    // Show/hide project related fields
    const projectRelatedFields = document.getElementById('projectRelatedFields');
    const projectRelatedRadio = document.getElementById('project_related');
    
    projectRelatedRadio.addEventListener('change', function() {
        projectRelatedFields.style.display = this.checked ? 'flex' : 'none';
    });

    // Contract search functionality
    const contractSearch = document.getElementById('contract_search');
    const contractSearchResults = document.getElementById('contract_search_results');
    const contractIdInput = document.getElementById('contract_id');
    const selectedContractInfo = document.getElementById('selected_contract_info');
    const contractDisplay = document.getElementById('contract_display');

    // Set initial contract if provided
    if (contractIdInput.value) {
        const contract = window.contracts.find(c => c.id == contractIdInput.value);
        if (contract) {
            contractSearch.value = `${contract.contract_number} - ${contract.name || contract.title || ''}`;
            contractDisplay.textContent = `${contract.contract_number} - ${contract.name || contract.title || ''}`;
            selectedContractInfo.style.display = 'block';
            loadContractMaterials(contract.id);
        }
    }

    contractSearch.addEventListener('input', function() {
        const query = this.value.trim();
        contractSearchResults.innerHTML = '';
        
        if (query.length < 2) {
            contractSearchResults.style.display = 'none';
            return;
        }

        const matches = window.contracts.filter(contract => 
            contract.contract_number.toLowerCase().includes(query.toLowerCase()) ||
            (contract.name && contract.name.toLowerCase().includes(query.toLowerCase())) ||
            (contract.title && contract.title.toLowerCase().includes(query.toLowerCase()))
        );

        if (matches.length > 0) {
            matches.forEach(contract => {
                const item = document.createElement('a');
                item.href = '#';
                item.classList.add('list-group-item', 'list-group-item-action');
                item.textContent = `${contract.contract_number} - ${contract.name || contract.title || ''}`;
                item.dataset.contractId = contract.id;
                item.dataset.contractNumber = contract.contract_number;
                item.dataset.contractName = contract.name || contract.title || '';
                contractSearchResults.appendChild(item);
            });
            contractSearchResults.style.display = 'block';
        } else {
            contractSearchResults.innerHTML = '<div class="list-group-item">No contracts found</div>';
            contractSearchResults.style.display = 'block';
        }
    });

    contractSearchResults.addEventListener('click', function(e) {
        if (e.target.classList.contains('list-group-item-action')) {
            e.preventDefault();
            const contractId = e.target.dataset.contractId;
            const contractNumber = e.target.dataset.contractNumber;
            const contractName = e.target.dataset.contractName;
            
            contractIdInput.value = contractId;
            contractSearch.value = `${contractNumber} - ${contractName}`;
            contractDisplay.textContent = `${contractNumber} - ${contractName}`;
            selectedContractInfo.style.display = 'block';
            contractSearchResults.style.display = 'none';
            
            // Load materials from contract
            loadContractMaterials(contractId);
        }
    });

    // Hide contract search results when clicking outside
    document.addEventListener('click', function(e) {
        if (!contractSearch.contains(e.target) && !contractSearchResults.contains(e.target)) {
            contractSearchResults.style.display = 'none';
        }
    });

    function loadContractMaterials(contractId) {
        fetch(`${baseUrl}/api/contracts/${contractId}/items`)
            .then(response => response.json())
            .then(data => {
                const itemsContainer = document.getElementById('items-container');
                itemsContainer.innerHTML = ''; // Clear existing items
                
                if (data.length === 0) {
                    // If no items, add a single empty row
                    document.getElementById('addRow').click();
                    return;
                }

                data.forEach((item, index) => {
                    const row = document.createElement('tr');
                    row.className = 'item-row';
                    
                    const materialName = item.material ? item.material.name : (item.material_name || 'N/A');
                    const materialId = item.material ? item.material.id : (item.material_id || '');
                    const description = item.description || (item.material ? item.material.description : '');
                    const unit = item.unit || (item.material ? item.material.unit : '');
                    const unitPrice = item.amount || (item.material ? (item.material.srp_price || item.material.base_price) : 0);
                    const totalAmount = item.total || (item.quantity * unitPrice);

                    row.innerHTML = `
                        <td>
                            ${materialName}
                            <input type="hidden" name="items[${index}][material_id]" value="${materialId}">
                        </td>
                        <td>
                            <input type="text" name="items[${index}][description]" class="form-control" value="${description}" placeholder="Enter description (required)">
                        </td>
                        <td>
                            <input type="number" name="items[${index}][quantity]" class="form-control quantity" step="0.01" value="${item.quantity}" required>
                        </td>
                        <td>
                            <input type="text" name="items[${index}][unit]" class="form-control unit" value="${unit}" readonly>
                        </td>
                        <td>
                            <input type="number" name="items[${index}][estimated_unit_price]" class="form-control unit-price" step="0.01" value="${unitPrice}" required>
                        </td>
                        <td>
                            <input type="number" name="items[${index}][total_amount]" class="form-control total-amount" value="${totalAmount.toFixed(2)}" readonly>
                        </td>
                        <td>
                            <input type="text" name="items[${index}][preferred_brand]" class="form-control">
                        </td>
                        <td>
                            <select name="items[${index}][preferred_supplier_id]" class="form-control supplier-select">
                                <option value="">Select Supplier</option>
                            </select>
                        </td>
                        <td>
                            <input type="text" name="items[${index}][notes]" class="form-control" value="From contract">
                        </td>
                        <td>
                            <button type="button" class="btn btn-outline-secondary btn-sm replace-material" title="Replace Material">
                                <i class="fas fa-exchange-alt"></i>
                            </button>
                            <button type="button" class="btn btn-danger btn-sm remove-row" title="Remove">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    `;
                    itemsContainer.appendChild(row);
                    setupRowCalculations(row);
                    
                    const supplierSelect = row.querySelector('.supplier-select');
                    if (item.material && item.material.suppliers && item.material.suppliers.length > 0) {
                        item.material.suppliers.forEach(supplier => {
                            const option = document.createElement('option');
                            option.value = supplier.id;
                            option.textContent = supplier.company_name || supplier.name;
                            supplierSelect.appendChild(option);
                        });
                    }
                });
            })
            .catch(error => {
                console.error('Error loading contract materials:', error);
            });
    }

    // Add new row
    let rowCount = document.querySelectorAll('.item-row').length; // Start rowCount based on existing rows
    const itemsContainer = document.getElementById('items-container'); // Get the tbody for items

    document.getElementById('addRow').addEventListener('click', function() {
        const itemsContainer = document.getElementById('items-container');
        const templateRow = document.querySelector('.item-row');
        if (!templateRow) {
            console.error("No template row found. Please ensure at least one .item-row exists initially or provide a hidden template.");
            return;
        }
        // Clone the first row
        const newRow = templateRow.cloneNode(true);
        // Determine the new index
        let maxIndex = -1;
        itemsContainer.querySelectorAll('.item-row').forEach(row => {
            const input = row.querySelector('input[name^="items["]');
            if (input) {
                const match = input.name.match(/items\[(\d+)\]/);
                if (match && parseInt(match[1]) > maxIndex) {
                    maxIndex = parseInt(match[1]);
                }
            }
        });
        const newIndex = maxIndex + 1;
        // Update all input/select names and clear values
        newRow.querySelectorAll('input, select, textarea').forEach(input => {
            if (input.name) {
                input.name = input.name.replace(/items\[\d+\]/, `items[${newIndex}]`);
            }
            if (input.type === 'text' || input.type === 'number' || input.tagName === 'TEXTAREA') {
                input.value = '';
            }
            if (input.tagName === 'SELECT') {
                input.selectedIndex = 0;
            }
            input.classList.remove('is-invalid');
        });
        
        const firstCell = newRow.querySelector('td:first-child');
        firstCell.innerHTML = '';

        const secondCell = newRow.querySelector('td:nth-child(2)');
        secondCell.querySelector('input').placeholder = "Enter description (required)";

        itemsContainer.appendChild(newRow);
        setupRowCalculations(newRow);
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
        const searchInput = container.querySelector('.material-name');
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

        // Only add event listeners if the element exists
        if (searchInput) {
        searchInput.addEventListener('input', performSearch);
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
        if (searchResultsDiv) {
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
        }
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
        // Only setup search if the row has a .material-name input (i.e., addable/searchable rows)
        if (row.querySelector('.material-name')) {
        setupMaterialSearch(row);
        }
        setupRowCalculations(row);
    });

    // Initially hide project related fields if not checked on load
    if (!projectRelatedRadio.checked) {
        projectRelatedFields.style.display = 'none';
    }

    // The top search bar should only filter table rows by material name
    const searchInput = document.getElementById('materialMasterSearch');
    if (searchInput) {
    searchInput.addEventListener('input', function() {
        const filter = this.value.toLowerCase();
        document.querySelectorAll('#itemsTable tbody tr').forEach(function(row) {
                // Only filter by the plain text in the first cell (material name)
                const materialCell = row.querySelector('td:first-child');
            let text = '';
            if (materialCell) {
                    text = materialCell.textContent.toLowerCase();
            }
            row.style.display = text.includes(filter) ? '' : 'none';
        });
    });
    }

    // Replace button opens modal
    $(document).on('click', '.replace-material', function() {
        replaceTargetRow = $(this).closest('tr');
        selectedReplaceMaterial = null;
        $('#replaceMaterialSearchInput').val('');
        $('#replaceMaterialSearchResults').empty();
        $('#replaceMaterialWarning').addClass('d-none').text('');
        $('#replaceMaterialConfirmSection').addClass('d-none');
        $('#replaceMaterialSummary').empty();
        $('#replaceMaterialModal').modal('show');
    });

    // Helper to get display price (always base price)
    function getMaterialBasePrice(material) {
        const base = parseFloat(material.base_price);
        if (!isNaN(base) && base > 0) return base;
        return 0;
    }

    $('#replaceMaterialSearchInput').on('input', function() {
        const query = $(this).val().trim().toLowerCase();
        const resultsDiv = $('#replaceMaterialSearchResults');
        resultsDiv.empty();
        $('#replaceMaterialWarning').addClass('d-none').text('');
        $('#replaceMaterialConfirmSection').addClass('d-none');
        $('#replaceMaterialSummary').empty();
        if (query.length < 2) return;
        const matches = window.materials.filter(mat =>
            mat.name.toLowerCase().includes(query) ||
            (mat.code && mat.code.toLowerCase().includes(query))
        );
        if (matches.length === 0) {
            resultsDiv.html('<div class="list-group-item">No materials found</div>');
            return;
        }
        matches.forEach(material => {
            const price = getMaterialBasePrice(material);
            const item = $('<a href="#" class="list-group-item list-group-item-action"></a>');
            item.text(`${material.name} (${material.code || ''}) - ${material.unit} - ₱${parseFloat(price).toFixed(2)}`);
            item.data('material', material);
            resultsDiv.append(item);
        });
    });

    $('#replaceMaterialSearchResults').on('click', '.list-group-item-action', function(e) {
            e.preventDefault();
        selectedReplaceMaterial = $(this).data('material');
        // Compute new quantity and total using contract dimensions if available
        let contractQuantity = 1;
        if (window.contractRoomArea && selectedReplaceMaterial.is_per_area && selectedReplaceMaterial.coverage_rate) {
            contractQuantity = Math.ceil(window.contractRoomArea / selectedReplaceMaterial.coverage_rate);
        } else if (selectedReplaceMaterial.minimum_quantity) {
            contractQuantity = selectedReplaceMaterial.minimum_quantity;
        }
        const unitPrice = getMaterialBasePrice(selectedReplaceMaterial);
        const total = (contractQuantity * unitPrice).toFixed(2);
        // Show confirmation section
        $('#replaceMaterialSummary').html(`
            <strong>Material:</strong> ${selectedReplaceMaterial.name}<br>
            <strong>Description:</strong> ${selectedReplaceMaterial.description || ''}<br>
            <strong>Unit:</strong> ${selectedReplaceMaterial.unit}<br>
            <strong>Quantity:</strong> ${contractQuantity}<br>
            <strong>Base Price:</strong> ₱${unitPrice.toFixed(2)}<br>
            <strong>Total:</strong> ₱${total}
        `);
        $('#replaceMaterialConfirmSection').removeClass('d-none');
        // Store for confirm
        selectedReplaceMaterial._computedQuantity = contractQuantity;
        selectedReplaceMaterial._computedTotal = total;
    });

    $('#confirmReplaceMaterialBtn').on('click', function() {
        if (!replaceTargetRow || !selectedReplaceMaterial) return;
        // Find the index for this row
        const index = replaceTargetRow.index();
        // Update the Material cell (plain text + hidden input)
        replaceTargetRow.find('td').eq(0).html(`
            ${selectedReplaceMaterial.name}
            <input type="hidden" name="items[${index}][material_id]" value="${selectedReplaceMaterial.id}">
        `);
        // Update the Description cell (input field)
        replaceTargetRow.find('input[name="items['+index+'][description]"]').val(selectedReplaceMaterial.description || '');
        // Always use computed quantity from modal (contract area if per-area)
        replaceTargetRow.find('input[name="items['+index+'][quantity]"]').val(selectedReplaceMaterial._computedQuantity);
        // Always use base price for estimated unit price
        const basePrice = getMaterialBasePrice(selectedReplaceMaterial);
        replaceTargetRow.find('input[name="items['+index+'][unit]"]').val(selectedReplaceMaterial.unit);
        replaceTargetRow.find('input[name="items['+index+'][estimated_unit_price]"]').val(basePrice);
        // Update total amount
        replaceTargetRow.find('input[name="items['+index+'][total_amount]"]').val(selectedReplaceMaterial._computedTotal);
        // Optionally update suppliers dropdown
        const supplierSelect = replaceTargetRow.find('select.supplier-select');
        supplierSelect.empty().append('<option value="">Select Supplier</option>');
        if (selectedReplaceMaterial.suppliers && selectedReplaceMaterial.suppliers.length > 0) {
            selectedReplaceMaterial.suppliers.forEach(supplier => {
                supplierSelect.append(`<option value="${supplier.id}">${supplier.company_name || supplier.name}</option>`);
            });
        }
        $('#replaceMaterialModal').modal('hide');
    });
});
</script>
@endpush
@endsection