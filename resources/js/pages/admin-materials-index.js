// Extracted from resources/views/admin/materials/index.blade.php
const __page = window.__page || {};
document.addEventListener('DOMContentLoaded', function() {
    // Search and filter functionality
    let searchTimeout;
    const search = document.getElementById('search');
    const category = document.getElementById('category');
    const sort = document.getElementById('sort');
    const perPage = document.getElementById('perPage');

    function updateMaterials() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            const params = new URLSearchParams({
                search: search.value,
                category: category.value,
                sort: sort.value,
                per_page: perPage.value
            });

            window.location.href = `${window.location.pathname}?${params.toString()}`;
        }, 500);
    }

    search.addEventListener('input', updateMaterials);
    category.addEventListener('change', updateMaterials);
    sort.addEventListener('change', updateMaterials);
    perPage.addEventListener('change', updateMaterials);

    // Set initial values from URL params
    const urlParams = new URLSearchParams(window.location.search);
    search.value = urlParams.get('search') || '';
    category.value = urlParams.get('category') || '';
    sort.value = urlParams.get('sort') || 'name';
    perPage.value = urlParams.get('per_page') || '10';

    // Delete material functionality
    const deleteModal = document.getElementById('deleteModal');
    const deleteBtns = document.querySelectorAll('.delete-material');
    const deleteForm = document.getElementById('deleteMaterialForm');
    let materialToDeleteId = null;

    deleteBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            materialToDeleteId = this.dataset.id;
            deleteForm.action = __page.index0_0.replace(':id', materialToDeleteId);
            const modal = new bootstrap.Modal(deleteModal);
            modal.show();
            // Animation reset for row
            const row = btn.closest('tr');
            if (row) {
                row.classList.remove('fade-out', 'fade-out-active');
            }
        });
    });

    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
    if (confirmDeleteBtn) {
        confirmDeleteBtn.addEventListener('click', function() {
            const row = document.querySelector('.delete-material[data-id="' + materialToDeleteId + '"]').closest('tr');
            if (row) {
                row.classList.add('fade-out');
                setTimeout(() => row.classList.add('fade-out-active'), 10);
                setTimeout(() => row.remove(), 410);
            }
            // Submit form after animation
            setTimeout(() => {
                deleteForm.submit();
            }, 420);
        });
    }

    // Load all materials when opening the SRP modal
    const bulkSrpModal = document.getElementById('bulkSrpModal');
    bulkSrpModal.addEventListener('show.bs.modal', async function() {
        try {
            const response = await fetch(__page.index0_1);
            const materials = await response.json();
            
            const tbody = document.getElementById('srpTableBody');
            tbody.innerHTML = '';
            
            materials.forEach(material => {
                const markup = material.base_price > 0 ? 
                    ((material.srp_price - material.base_price) / material.base_price * 100) : 0;
                const prevSrp = material.previous_srp !== null && material.previous_srp !== undefined ? `₱${parseFloat(material.previous_srp).toFixed(2)}` : '<span class="text-muted">N/A</span>';
                const row = `
                    <tr>
                        <td>${material.code}</td>
                        <td>${material.name}</td>
                        <td>${material.category ? material.category.name : ''}</td>
                        <td>${material.unit}</td>
                        <td>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">₱</span>
                                <input type="number" class="form-control base-price" 
                                    value="${material.base_price}" readonly>
                            </div>
                        </td>
                        <td>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">₱</span>
                                <input type="number" class="form-control srp-price" 
                                    data-material-id="${material.id}"
                                    value="${material.srp_price}"
                                    step="0.01"
                                    min="0">
                            </div>
                        </td>
                        <td>${prevSrp}</td>
                        <td>
                            <div class="input-group input-group-sm">
                                <input type="number" class="form-control markup-percent" 
                                    value="${markup.toFixed(2)}"
                                    step="0.01">
                                <span class="input-group-text">%</span>
                            </div>
                        </td>
                    </tr>
                `;
                tbody.insertAdjacentHTML('beforeend', row);
            });

            // Reattach event listeners for the newly created inputs
            attachSrpEventListeners();
        } catch (error) {
            console.error('Error loading materials:', error);
            alert('Failed to load materials. Please try again.');
        }
    });

    function attachSrpEventListeners() {
        // Handle markup percentage changes
        document.querySelectorAll('.markup-percent').forEach(input => {
            input.addEventListener('change', function() {
                const row = this.closest('tr');
                const basePrice = parseFloat(row.querySelector('.base-price').value) || 0;
                const markup = parseFloat(this.value) || 0;
                const srpPrice = basePrice * (1 + markup/100);
                row.querySelector('.srp-price').value = srpPrice.toFixed(2);
            });
        });

        // Handle SRP price changes
        document.querySelectorAll('.srp-price').forEach(input => {
            input.addEventListener('change', function() {
                const row = this.closest('tr');
                const basePrice = parseFloat(row.querySelector('.base-price').value) || 0;
                const srpPrice = parseFloat(this.value) || 0;
                const markup = basePrice > 0 ? ((srpPrice - basePrice) / basePrice * 100) : 0;
                row.querySelector('.markup-percent').value = markup.toFixed(2);
            });
        });
    }

    // Save SRP prices
    document.getElementById('saveSrpPrices').addEventListener('click', async function() {
        const updates = [];
        document.querySelectorAll('.srp-price').forEach(input => {
            const id = input.dataset.materialId;
            const srp_price = input.value;
            if (id && srp_price) {
                updates.push({ id, srp_price });
            }
        });

        if (updates.length === 0) {
            alert('No materials to update');
            return;
        }

        try {
            const response = await fetch(__page.index0_2, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ materials: updates })
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Failed to update SRP prices');
            }

            const modal = bootstrap.Modal.getInstance(document.getElementById('bulkSrpModal'));
            modal.hide();
            
            // Show success message
            alert('SRP prices updated successfully');
            window.location.reload();

        } catch (error) {
            console.error('Error:', error);
            alert(error.message || 'Failed to update SRP prices. Please try again.');
        }
    });

    // Add SRP search functionality
    const srpSearchInput = document.getElementById('srpSearchInput');
    const clearSrpSearch = document.getElementById('clearSrpSearch');

    srpSearchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const rows = document.querySelectorAll('#srpTableBody tr');
        
        rows.forEach(row => {
            const code = row.cells[0].textContent.toLowerCase();
            const name = row.cells[1].textContent.toLowerCase();
            const category = row.cells[2].textContent.toLowerCase();
            
            if (code.includes(searchTerm) || name.includes(searchTerm) || category.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    clearSrpSearch.addEventListener('click', function() {
        srpSearchInput.value = '';
        const rows = document.querySelectorAll('#srpTableBody tr');
        rows.forEach(row => row.style.display = '');
    });

    // Add supplier prices modal functionality
    const viewSupplierButtons = document.querySelectorAll('.view-suppliers');

    // Get the correct base URL for API
    const apiBase = __page.index0_3;

    viewSupplierButtons.forEach(button => {
        button.addEventListener('click', async function() {
            const materialId = this.dataset.materialId;
            try {
                const response = await fetch(`${apiBase}/${materialId}/suppliers`);
                const data = await response.json();
                const suppliers = data.suppliers || [];
                const basePrice = parseFloat(data.base_price) || 0;
                const tbody = document.getElementById('supplierPricesBody');
                tbody.innerHTML = '';
                
                suppliers.forEach(supplier => {
                    const variance = calculateVariance(supplier.price, basePrice);
                    const row = `
                        <tr>
                            <td>${supplier.company_name}</td>
                            <td>₱${supplier.price ? parseFloat(supplier.price).toFixed(2) : 'N/A'}</td>
                            <td>${supplier.lead_time || 'N/A'}</td>
                            <td>${supplier.last_updated ? new Date(supplier.last_updated).toLocaleDateString() : 'N/A'}</td>
                            <td>
                                <span class="badge ${variance.class}">
                                    ${variance.percentage}%
                                </span>
                            </td>
                        </tr>
                    `;
                    tbody.insertAdjacentHTML('beforeend', row);
                });
            } catch (error) {
                console.error('Error fetching supplier prices:', error);
            }
        });
    });

    function calculateVariance(supplierPrice, basePrice) {
        if (!basePrice || basePrice === 0 || !supplierPrice) return { percentage: 0, class: 'bg-secondary' };
        const variance = ((supplierPrice - basePrice) / basePrice) * 100;
        const formattedVariance = variance.toFixed(2);
        return {
            percentage: formattedVariance,
            class: variance < 0 ? 'bg-success' : variance > 0 ? 'bg-danger' : 'bg-secondary'
        };
    }

    // Animate modals on show
    ['bulkSrpModal', 'supplierPricesModal', 'deleteModal'].forEach(function(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.addEventListener('show.bs.modal', function() {
                const modalDialog = modal.querySelector('.modal-dialog');
                if (modalDialog) {
                    modalDialog.classList.add('fade-in-up');
                    setTimeout(() => modalDialog.classList.remove('fade-in-up'), 500);
                }
            });
        }
    });

    setTimeout(function() {
        var card = document.getElementById('materialsFadeinCard');
        if(card) card.classList.add('active');
    }, 100);
});
