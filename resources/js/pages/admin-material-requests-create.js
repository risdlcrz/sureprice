// Extracted from resources/views/admin/material-requests/create.blade.php
const __page = window.__page || {};
document.addEventListener('DOMContentLoaded', function() {
    let itemIndex = __page.create0_0;
    const materials = __page.create0_1;
    let currentSupplierRow = null;

    // Handle contract change
    var contractSelect = document.getElementById('contract_id');
    if (contractSelect) {
        contractSelect.addEventListener('change', function() {
            const contractId = this.value;
            if (contractId) {
                window.location.href = '' + __page.create0_2 + '?contract_id=' + contractId;
            }
        });
    }

    // Add new item row
    var addRowBtn = document.getElementById('addRow');
    if (addRowBtn) {
        addRowBtn.addEventListener('click', function() {
            const newRow = `
                <tr class="item-row">
                    <td>
                        <select name="items[${itemIndex}][material_id]" class="form-control material-select" required>
                            <option value="">Select Material</option>
                            ${materials.map(m => `<option value="${m.id}">${m.name}</option>`).join('')}
                        </select>
                    </td>
                    <td class="unit">
                        <select name="items[${itemIndex}][unit]" class="form-control unit-select" required>
                            <option value="">Select Unit</option>
                            <option value="pcs">Pcs</option>
                            <option value="kg">Kg</option>
                            <option value="m">M</option>
                            <option value="set">Set</option>
                            <option value="box">Box</option>
                        </select>
                    </td>
                    <td>
                        <span class="badge bg-secondary stock-display">0.00</span>
                    </td>
                    <td>
                        <input type="number" name="items[${itemIndex}][quantity]" class="form-control quantity" step="0.01" required>
                    </td>
                    <td>
                        <select name="items[${itemIndex}][preferred_supplier_id]" class="form-control supplier-select">
                            <option value="">Select Supplier</option>
                        </select>
                    </td>
                    <td>
                        <button type="button" class="btn btn-outline-success btn-sm recommend-supplier-btn" data-material-id="" data-material-name="">\
                            <i class="fas fa-lightbulb"></i> Recommend Supplier
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-sm remove-row" title="Remove"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
            `;
            document.getElementById('items-container').insertAdjacentHTML('beforeend', newRow);
            itemIndex++;
        });
    }

    // Delegate recommend supplier button click
    document.getElementById('items-container').addEventListener('click', function(e) {
        const btn = e.target.closest('.recommend-supplier-btn');
        if (btn) {
            const row = btn.closest('.item-row');
            currentSupplierRow = row;
            let materialName = '';
            let materialId = '';
            // Try to get from select or from static data
            const select = row.querySelector('.material-select');
            if (select) {
                materialId = select.value;
                materialName = select.options[select.selectedIndex]?.text || '';
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
        fetch('' + __page.create0_3 + '?' + params, {
            method: 'GET',
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('supplier-recommendation-results').innerHTML = data.html;
        });
    });

    // Delegate select supplier button click in modal
    document.getElementById('supplier-recommendation-results').addEventListener('click', function(e) {
        const btn = e.target.closest('.select-supplier-btn');
        if (btn && currentSupplierRow) {
            const supplierName = btn.getAttribute('data-supplier-name');
            const supplierId = btn.getAttribute('data-supplier-id');
            const supplierSelect = currentSupplierRow.querySelector('.supplier-select');
            if (supplierSelect) {
                supplierSelect.value = supplierId;
            }
            // Close modal
            const modalEl = document.getElementById('supplierRecommendationModal');
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();
        }
    });

    // Handle material selection to populate supplier dropdown and update stock
    document.addEventListener('change', function(e) {
        if (e.target.matches('.material-select')) {
            const row = e.target.closest('.item-row');
            const materialId = e.target.value;
            const supplierSelect = row.querySelector('.supplier-select');
            const stockDisplay = row.querySelector('.stock-display');
            
            if (materialId) {
                // Update stock display
                if (stockDisplay) {
                    // Fetch stock information for this material
                    fetch(`${__page.create0_4}/${materialId}/stock`)
                        .then(response => response.json())
                        .then(data => {
                            const totalStock = data.total_stock || 0;
                            stockDisplay.textContent = totalStock.toFixed(2);
                            stockDisplay.className = `badge bg-${totalStock > 0 ? 'success' : 'danger'} stock-display`;
                        })
                        .catch(error => {
                            console.error('Error fetching stock:', error);
                            stockDisplay.textContent = '0.00';
                            stockDisplay.className = 'badge bg-secondary stock-display';
                        });
                }
                
                // Update supplier dropdown
                if (supplierSelect) {
                    // Clear existing options
                    supplierSelect.innerHTML = '<option value="">Select Supplier</option>';
                    
                    // Fetch suppliers for this material
                    fetch(`${__page.create0_5}/${materialId}/suppliers`)
                        .then(response => response.json())
                        .then(data => {
                            data.suppliers.forEach(supplier => {
                                const option = document.createElement('option');
                                option.value = supplier.id;
                                option.textContent = supplier.company_name;
                                supplierSelect.appendChild(option);
                            });
                        })
                        .catch(error => {
                            console.error('Error fetching suppliers:', error);
                        });
                }
            }
        }
    });
});
