// Extracted from resources/views/warranty-requests/index.blade.php
const __page = window.__page || {};
document.addEventListener('DOMContentLoaded', function() {
    // Filter button toggle
    const filterBtn = document.getElementById('filterBtn');
    const filtersCollapse = document.getElementById('filtersCollapse');
    
    filterBtn.addEventListener('click', function() {
        const bsCollapse = new bootstrap.Collapse(filtersCollapse);
        bsCollapse.toggle();
    });

    // Export functionality
    document.getElementById('exportBtn').addEventListener('click', function() {
        const status = document.getElementById('statusFilter').value;
        const dateFrom = document.getElementById('dateFrom').value;
        const dateTo = document.getElementById('dateTo').value;
        const search = document.getElementById('searchInput').value;

        let exportUrl = __page.index0_0;
        const params = new URLSearchParams();

        if (status) params.append('status', status);
        if (dateFrom) params.append('date_from', dateFrom);
        if (dateTo) params.append('date_to', dateTo);
        if (search) params.append('search', search);

        if (params.toString()) {
            exportUrl += '?' + params.toString();
        }

        window.location.href = exportUrl;
    });

    // Filter form submission
    document.getElementById('filtersForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const params = new URLSearchParams(formData);
        window.location.href = '' + __page.index0_1 + '?' + params.toString();
    });

    // Add Warranty Request Submit
    document.getElementById('submitAddWarranty').addEventListener('click', function() {
        const form = document.getElementById('addWarrantyForm');
        if (!form.checkValidity()) {
            form.classList.add('was-validated');
            return;
        }
        const formData = new FormData(form);
        // Show loading state
        const submitBtn = this;
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Submitting...';
        fetch(__page.index0_2, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Warranty request added successfully.',
                    confirmButtonText: 'OK'
                }).then(() => {
                    // Close modal, reset form, reload page
                    const modal = bootstrap.Modal.getInstance(document.getElementById('addWarrantyModal'));
                    modal.hide();
                    form.reset();
                    form.classList.remove('was-validated');
                    window.location.reload();
                });
            } else {
                throw new Error(data.message || 'Something went wrong');
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: error.message || 'Failed to add warranty request. Please try again.',
                confirmButtonText: 'OK'
            });
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        });
    });

    let materialCount = 1;

    // Add Material Row
    document.getElementById('addMaterial').addEventListener('click', function() {
        const materialsContainer = document.getElementById('materialsContainer');
        const template = document.querySelector('.material-item').cloneNode(true);
        template.querySelectorAll('input').forEach(input => input.value = ''); // Clear values for new row
        template.querySelector('.material-id-input').value = '';
        template.querySelector('.unit-display').value = '';
        template.querySelector('.material-search-results').innerHTML = '';
        template.querySelector('.remove-material').style.display = 'block';
        // Update names for new row
        template.querySelectorAll('[name*="materials[0]"]').forEach(element => {
            element.name = element.name.replace('[0]', `[${materialCount}]`);
        });
        materialsContainer.appendChild(template);
        setupMaterialSearch(template); // Initialize search for the new row
        materialCount++;
    });

    // Remove Material Row
    document.getElementById('materialsContainer').addEventListener('click', function(e) {
        if (e.target.closest('.remove-material')) {
            e.target.closest('.material-item').remove();
        }
    });

    // Handle material selection and unit display
    document.getElementById('materialsContainer').addEventListener('input', function(e) {
        if (e.target.classList.contains('material-search-input')) {
            const searchInput = e.target;
            const materialItem = searchInput.closest('.material-item');
            const materialIdInput = materialItem.querySelector('.material-id-input');
            const unitDisplay = materialItem.querySelector('.unit-display');
            
            // Clear previously selected material if user starts typing again
            materialIdInput.value = '';
            unitDisplay.value = '';
            materialItem.querySelector('.material-search-results').innerHTML = '';
        }
    });

    document.getElementById('materialsContainer').addEventListener('click', function(e) {
        if (e.target.classList.contains('list-group-item-action')) {
            e.preventDefault(); // Prevent default link behavior
            const selectedResult = e.target;
            const materialId = selectedResult.dataset.materialId;
            const materialName = selectedResult.dataset.materialName;
            const unit = selectedResult.dataset.unit;

            const materialItem = selectedResult.closest('.material-item');
            materialItem.querySelector('.material-search-input').value = materialName;
            materialItem.querySelector('.material-id-input').value = materialId;
            materialItem.querySelector('.unit-display').value = unit;
            materialItem.querySelector('.material-search-results').innerHTML = ''; // Clear results
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
        const unitDisplay = container.querySelector('.unit-display');

        const performSearch = debounce(function() {
            const query = searchInput.value.trim();
            if (query.length < 2) {
                searchResultsDiv.innerHTML = '';
                return;
            }

            fetch(`${__page.index0_3}?query=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(materials => {
                    searchResultsDiv.innerHTML = '';
                    if (materials.length > 0) {
                        materials.forEach(material => {
                            const item = document.createElement('a');
                            item.href = '#';
                            item.classList.add('list-group-item', 'list-group-item-action');
                            item.dataset.materialId = material.id;
                            item.dataset.materialName = `${material.name} (${material.code})`;
                            item.dataset.unit = material.unit;
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

    }

    // Initial setup for the first row
    setupMaterialSearch(document.querySelector('.material-item'));

    // Additional Work Request Form Handling
    const additionalWorkForm = document.getElementById('additionalWorkForm');

    // Form Validation and Submission
    document.getElementById('submitAdditionalWork').addEventListener('click', function() {
        if (!additionalWorkForm.checkValidity()) {
            additionalWorkForm.classList.add('was-validated');
            return;
        }

        const formData = new FormData(additionalWorkForm);
        const data = Object.fromEntries(formData.entries());
        
        // Convert materials array
        const materials = [];
        document.querySelectorAll('.material-item').forEach((item, index) => {
            materials.push({
                material_id: item.querySelector('.material-select').value,
                quantity: item.querySelector('input[name$="[quantity]"]').value,
                notes: item.querySelector('input[name$="[notes]"]').value
            });
        });
        data.materials = materials;

        // Submit the form
        fetch(__page.index0_4, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': __page.index0_5
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Additional work request submitted successfully');
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while submitting the request');
        });
    });
});
