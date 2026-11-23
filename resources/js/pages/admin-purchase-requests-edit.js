// Extracted from resources/views/admin/purchase-requests/edit.blade.php
const __page = window.__page || {};
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
    let rowCount = __page.edit1_0;
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
        const isProjectRelated = document.getElementById('project_related')?.checked;
        const contractId = document.querySelector('[name="contract_id"]')?.value;
        const projectId = document.getElementById('project_id')?.value;

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
        

        if (previouslySelectedSupplierId) {
            $supplierSelect.val(previouslySelectedSupplierId);
        }

        $supplierSelect.prop('required', true);
    });

    // Trigger change event on page load for existing materials to populate suppliers and set selected
    $('.material-select').each(function() {
        $(this).trigger('change');
    });
});
