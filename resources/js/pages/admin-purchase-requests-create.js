// Extracted from resources/views/admin/purchase-requests/create.blade.php
const __page = window.__page || {};
const baseUrl = __page.create0_0;

// Ensure these are globally available or passed to functions as needed
window.suppliers = __page.create0_1;
window.materials = __page.create0_2;
window.contracts = __page.create0_3;
window.bestSuppliers = __page.create0_4;

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
    // Use helper to populate supplier dropdown with data-price
    const supplierSelect = newRow.querySelector('.supplier-select');
    if (material.suppliers && material.suppliers.length > 0) {
        populateSupplierDropdown(supplierSelect, material.suppliers);
    } else {
        supplierSelect.innerHTML = '<option value="">Select Supplier</option>';
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
    // Use helper to populate supplier dropdown with data-price
    const supplierSelect = row.querySelector('.supplier-select');
    if (material.suppliers && material.suppliers.length > 0) {
        populateSupplierDropdown(supplierSelect, material.suppliers);
    } else {
        supplierSelect.innerHTML = '<option value="">Select Supplier</option>';
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
        fetch(`${baseUrl}/contracts/${contractId}/items`)
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
                            <button type="button" class="btn btn-outline-secondary btn-sm replace-material" title="Replace Material">
                                <i class="fas fa-exchange-alt"></i>
                            </button>
                            <button type="button" class="btn btn-outline-danger btn-sm remove-row" title="Remove">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    `;
                    itemsContainer.appendChild(row);
                    setupRowCalculations(row);
                    
                    const supplierSelect = row.querySelector('.supplier-select');
                    if (item.material && item.material.suppliers && item.material.suppliers.length > 0) {
                        populateSupplierDropdown(supplierSelect, item.material.suppliers);
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

                // Use helper to populate supplier dropdown with data-price
                populateSupplierDropdown(supplierSelect, suppliers);

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
        const supplierSelect = replaceTargetRow.find('select.supplier-select')[0];
        supplierSelect.innerHTML = '';
        let suppliersToDisplay = (selectedReplaceMaterial.suppliers && selectedReplaceMaterial.suppliers.length > 0) ? selectedReplaceMaterial.suppliers : window.suppliers;
        // Use helper to populate supplier dropdown with data-price
        populateSupplierDropdown(supplierSelect, suppliersToDisplay);
        supplierSelect.disabled = false;
        $('#replaceMaterialModal').modal('hide');
    });

    // Prefill supplier for single item restock requests
    if (window.prefillItems && window.prefillItems.length === 1) {
        const materialId = window.prefillItems[0].material_id;
        const bestSupplier = window.bestSuppliers[materialId];
        if (bestSupplier) {
            const supplierSelect = document.querySelector('.supplier-select');
            if (supplierSelect) {
                supplierSelect.value = bestSupplier.id;
            }
        }
    }

    // Delegate recommend supplier button click
    document.getElementById('items-container').addEventListener('click', function(e) {
        const btn = e.target.closest('.recommend-supplier-btn');
        if (btn) {
            const row = btn.closest('.item-row');
            let materialName = '';
            let materialId = '';
            // Try to get from select or from static data
            const select = row.querySelector('[name*="material_id"]');
            if (select) {
                materialId = select.value || select.getAttribute('value');
                materialName = select.options ? (select.options[select.selectedIndex]?.text || '') : '';
            } else {
                materialId = btn.getAttribute('data-material-id');
                materialName = btn.getAttribute('data-material-name');
            }
            document.getElementById('modalMaterialId').value = materialId;
            document.getElementById('modalMaterialName').textContent = materialName;
            document.getElementById('supplier-recommendation-results').innerHTML = '';
            var modal = new bootstrap.Modal(document.getElementById('supplierRecommendationModal'));
            modal.show();
        }
    });

    // Handle recommendation form submit
    document.getElementById('supplier-recommendation-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const form = e.target;
        const params = new URLSearchParams(new FormData(form)).toString();
        fetch('' + __page.create0_5 + '?' + params, {
            method: 'GET',
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            const resultsDiv = document.getElementById('supplier-recommendation-results');
            if (!data.recommended || data.recommended.length === 0) {
                resultsDiv.innerHTML = '<div class="alert alert-warning">No supplier recommendations found.</div>';
                return;
            }

            let html = `
                <div class="table-responsive mt-3">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Supplier Name</th>
                                <th>On-Time Delivery Rate</th>
                                <th>Avg. Defect Rate</th>
                                <th>Score</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

            data.recommended.forEach(rec => {
                html += `
                    <tr>
                        <td>${rec.supplier.name}</td>
                        <td>${parseFloat(rec.supplier.on_time_delivery_rate).toFixed(2)}%</td>
                        <td>${parseFloat(rec.supplier.average_defect_rate).toFixed(2)}%</td>
                        <td>${parseFloat(rec.score).toFixed(2)}</td>
                        <td>
                            <button type="button" 
                                class="btn btn-primary btn-sm select-supplier-btn"
                                data-supplier-id="${rec.supplier.id}"
                                data-supplier-name="${rec.supplier.name}">
                                Select
                            </button>
                        </td>
                    </tr>
                `;
            });

            html += '</tbody></table></div>';
            resultsDiv.innerHTML = html;
        });
    });

    // Delegate select supplier button click in modal
    document.getElementById('supplier-recommendation-results').addEventListener('click', function(e) {
        const btn = e.target.closest('.select-supplier-btn');
        if (btn) {
            const supplierName = btn.getAttribute('data-supplier-name');
            const supplierId = btn.getAttribute('data-supplier-id');
            const supplierSelect = document.querySelector('.supplier-select');
            if (supplierSelect) {
                supplierSelect.value = supplierId;
            }
            // Close modal
            const modalEl = document.getElementById('supplierRecommendationModal');
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();
        }
    });

    // When populating supplier dropdowns in JS, add data-price
    function populateSupplierDropdown(select, suppliers) {
        select.innerHTML = '<option value="">Select Supplier</option>';
        suppliers.forEach(supplier => {
            const option = document.createElement('option');
            option.value = supplier.id;
            option.textContent = supplier.name || supplier.company_name;
            option.setAttribute('data-price', supplier.price || (supplier.pivot ? supplier.pivot.price : ''));
            select.appendChild(option);
        });
    }

    // Update all JS places that fill supplier dropdowns to use populateSupplierDropdown
    // Example for material search:
    // suppliers.forEach(supplier => { ... })
    // becomes:
    // populateSupplierDropdown(supplierSelect, suppliers);

    // Add event listener to update unit price when supplier changes
    document.getElementById('items-container').addEventListener('change', function(e) {
        if (e.target.classList.contains('supplier-select')) {
            const select = e.target;
            const selectedOption = select.options[select.selectedIndex];
            const price = selectedOption.getAttribute('data-price');
            if (price) {
                const row = select.closest('.item-row');
                const unitPriceInput = row.querySelector('.unit-price');
                unitPriceInput.value = price;
                // Trigger total recalculation
                const quantityInput = row.querySelector('.quantity');
                const event = new Event('input', { bubbles: true });
                quantityInput.dispatchEvent(event);
            }
        }
    });
});
