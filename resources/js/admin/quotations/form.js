import $ from 'jquery';
import 'select2';
import 'select2/dist/css/select2.css';

$(document).ready(function() {
    const standaloneToggle = document.getElementById('standaloneToggle');
    const purchaseRequestSection = document.getElementById('purchaseRequestSection');
    const prMaterialsSection = document.getElementById('prMaterialsSection');
    const manualMaterialSection = document.getElementById('manualMaterialSection');
    const purchaseRequestId = document.getElementById('purchase_request_id');

    function toggleQuotationTypeSections() {
        if (standaloneToggle.checked) {
            purchaseRequestSection.style.display = 'none';
            prMaterialsSection.style.display = 'none';
            manualMaterialSection.style.display = 'block';
            purchaseRequestId.removeAttribute('required');
            purchaseRequestId.value = '';
            document.querySelector('#prMaterials tbody').innerHTML = '';
            $('#selectedMaterialsTable tbody input[name^="materials"]').each(function() {
                $(this).prop('required', true);
            });
        } else {
            purchaseRequestSection.style.display = 'block';
            prMaterialsSection.style.display = 'block';
            manualMaterialSection.style.display = 'none';
            purchaseRequestId.setAttribute('required', 'required');
            $('#selectedMaterialsTable tbody input[name^="materials"]').each(function() {
                $(this).prop('required', false);
            });
            const selectedPrOption = purchaseRequestId.options[purchaseRequestId.selectedIndex];
            if (selectedPrOption && selectedPrOption.value) {
                const prMaterials = JSON.parse(selectedPrOption.dataset.materials || '[]');
                populatePrMaterialsTable(prMaterials);
            }
        }
    }

    function populatePrMaterialsTable(items) {
        let html = '';
        if (items.length > 0) {
            items.forEach(item => {
                const totalAmount = (item.quantity && item.estimated_unit_price) ? (item.quantity * item.estimated_unit_price).toFixed(2) : '&mdash;';
                html += `
                    <tr>
                        <td>${item.material.name}</td>
                        <td>${item.quantity}</td>
                        <td>${item.material.unit}</td>
                        <td>${totalAmount}</td>
                    </tr>
                `;
            });
        } else {
            html = `<tr><td colspan="4" class="text-center text-muted">No materials in this Purchase Request.</td></tr>`;
        }
        document.querySelector('#prMaterials tbody').innerHTML = html;
    }

    toggleQuotationTypeSections();
    if (purchaseRequestId.value) {
        const selectedPrOption = purchaseRequestId.options[purchaseRequestId.selectedIndex];
        const prMaterials = JSON.parse(selectedPrOption.dataset.materials || '[]');
        populatePrMaterialsTable(prMaterials);
    }

    standaloneToggle.addEventListener('change', toggleQuotationTypeSections);
    purchaseRequestId.addEventListener('change', function() {
        const selectedPrOption = this.options[this.selectedIndex];
        if (selectedPrOption && selectedPrOption.value) {
            const prMaterials = JSON.parse(selectedPrOption.dataset.materials || '[]');
            populatePrMaterialsTable(prMaterials);
        } else {
            document.querySelector('#prMaterials tbody').innerHTML = '<tr><td colspan="4" class="text-center text-muted">Select a Purchase Request to view materials.</td></tr>';
        }
    });

    const materialSearchInput = document.getElementById('material_search');
    const materialSearchResults = document.getElementById('material_search_results');
    const selectedMaterialsTableBody = document.querySelector('#selectedMaterialsTable tbody');
    const suppliersSelect = document.getElementById('suppliers');
    const selectedMaterialIds = new Set();

    function addMaterialToTable(material) {
        if (selectedMaterialIds.has(material.id)) {
            alert('Material already added to the quotation.');
            return;
        }
        const row = `
            <tr data-material-id="${material.id}">
                <td>
                    ${material.name} (${material.code})
                    <input type="hidden" name="materials[${material.id}][id]" value="${material.id}">
                </td>
                <td>
                    <input type="number" name="materials[${material.id}][quantity]" class="form-control form-control-sm" value="1" min="0.01" step="0.01" required>
                </td>
                <td>${material.unit || 'Pcs'}</td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm remove-material">Remove</button>
                </td>
            </tr>
        `;
        selectedMaterialsTableBody.insertAdjacentHTML('beforeend', row);
        selectedMaterialIds.add(material.id);
        const newRow = selectedMaterialsTableBody.querySelector(`tr[data-material-id="${material.id}"]`);
        if (newRow) {
            newRow.querySelector('input[name^="materials"]').setAttribute('required', 'required');
        }
    }

    materialSearchInput.addEventListener('keyup', async function() {
        const query = this.value;
        const selectedSupplierIds = Array.from(suppliersSelect.selectedOptions).map(option => option.value);
        if (query.length > 2 && selectedSupplierIds.length > 0) {
            materialSearchResults.style.display = 'block';
            materialSearchResults.innerHTML = '<a href="#" class="list-group-item list-group-item-action disabled">Searching...</a>';
            try {
                const response = await fetch(`/api/materials/search-by-supplier?query=${query}&suppliers=${selectedSupplierIds.join(',')}`);
                const materials = await response.json();
                if (materials.length > 0) {
                    materialSearchResults.innerHTML = '';
                    materials.forEach(material => {
                        const materialItem = document.createElement('a');
                        materialItem.href = '#';
                        materialItem.classList.add('list-group-item', 'list-group-item-action');
                        materialItem.textContent = `${material.name} (${material.unit || 'Pcs'}) - ₱${material.price || 'N/A'}`;
                        materialItem.addEventListener('click', function(e) {
                            e.preventDefault();
                            addMaterialToTable(material);
                            materialSearchInput.value = '';
                            materialSearchResults.style.display = 'none';
                        });
                        materialSearchResults.appendChild(materialItem);
                    });
                } else {
                    materialSearchResults.innerHTML = '<a href="#" class="list-group-item list-group-item-action disabled">No materials found for selected suppliers.</a>';
                }
            } catch (error) {
                console.error('Error searching materials:', error);
                materialSearchResults.innerHTML = '<a href="#" class="list-group-item list-group-item-action disabled text-danger">Error searching materials.</a>';
            }
        } else if (selectedSupplierIds.length === 0) {
            materialSearchResults.style.display = 'block';
            materialSearchResults.innerHTML = '<a href="#" class="list-group-item list-group-item-action disabled text-warning">Select at least one supplier to search for materials.</a>';
        } else {
            materialSearchResults.style.display = 'none';
            materialSearchResults.innerHTML = '';
        }
    });

    document.getElementById('clear_material_search').addEventListener('click', function() {
        materialSearchInput.value = '';
        materialSearchResults.style.display = 'none';
        materialSearchResults.innerHTML = '';
    });

    selectedMaterialsTableBody.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-material')) {
            const materialId = e.target.closest('tr').dataset.materialId;
            selectedMaterialIds.delete(parseInt(materialId));
            e.target.closest('tr').remove();
        }
    });

    $('#suppliers').select2({
        theme: 'bootstrap4',
        placeholder: 'Select Suppliers',
        allowClear: true
    });

    $('#suppliers').on('change', function() {
        if (materialSearchInput.value.length > 2) {
            materialSearchInput.dispatchEvent(new Event('keyup'));
        }
    });
}); 