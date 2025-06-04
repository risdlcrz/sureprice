// Supplier Management JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Handle view supplier
    const viewButtons = document.querySelectorAll('.view-supplier');
    viewButtons.forEach(button => {
        button.addEventListener('click', function() {
            const supplierId = this.dataset.id;
            fetchSupplierDetails(supplierId);
        });
    });

    // Handle edit supplier
    const editButtons = document.querySelectorAll('.edit-supplier');
    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const supplierId = this.dataset.id;
            fetchSupplierForEdit(supplierId);
        });
    });

    // Handle delete supplier
    const deleteButtons = document.querySelectorAll('.delete-supplier');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const supplierId = this.dataset.id;
            setupDeleteModal(supplierId);
        });
    });

    // Handle evaluate supplier
    const evaluateButtons = document.querySelectorAll('.evaluate-supplier');
    evaluateButtons.forEach(button => {
        button.addEventListener('click', function() {
            const supplierId = this.dataset.id;
            setupEvaluateModal(supplierId);
        });
    });

    // Handle form submissions
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!this.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            }
            this.classList.add('was-validated');
        });
    });
});

// Fetch supplier details for viewing
function fetchSupplierDetails(supplierId) {
    fetch(`/admin/suppliers/${supplierId}`)
        .then(response => response.json())
        .then(data => {
            // Populate view modal fields
            document.getElementById('view_company_name').textContent = data.company_name;
            document.getElementById('view_business_type').textContent = data.business_type;
            document.getElementById('view_tax_id').textContent = data.tax_id;
            document.getElementById('view_years_in_business').textContent = data.years_in_business;
            document.getElementById('view_contact_person').textContent = data.contact_person;
            document.getElementById('view_contact_position').textContent = data.contact_position;
            document.getElementById('view_phone').textContent = data.phone;
            document.getElementById('view_email').textContent = data.email;
            document.getElementById('view_address').textContent = data.address;
            document.getElementById('view_business_description').textContent = data.business_description;
            document.getElementById('view_payment_terms').textContent = data.payment_terms;
            document.getElementById('view_bank_name').textContent = data.bank_name;
            document.getElementById('view_bank_account').textContent = data.bank_account;
            document.getElementById('view_bank_branch').textContent = data.bank_branch;

            // Populate products
            const productsContainer = document.getElementById('view_products');
            productsContainer.innerHTML = '';
            data.products.forEach(product => {
                const badge = document.createElement('span');
                badge.className = 'badge bg-primary me-2 mb-2';
                badge.textContent = product;
                productsContainer.appendChild(badge);
            });

            // Populate documents
            populateDocument('view_business_permit', data.business_permit);
            populateDocument('view_tax_clearance', data.tax_clearance);
            populateDocument('view_insurance_certificate', data.insurance_certificate);
            populateDocuments('view_other_documents', data.other_documents);

            // Populate evaluation metrics
            if (data.evaluation) {
                document.getElementById('view_quality_score').textContent = data.evaluation.quality_score;
                document.getElementById('view_delivery_score').textContent = data.evaluation.delivery_score;
                document.getElementById('view_price_score').textContent = data.evaluation.price_score;
                document.getElementById('view_communication_score').textContent = data.evaluation.communication_score;
                document.getElementById('view_overall_rating').textContent = data.evaluation.overall_rating;
                document.getElementById('view_last_evaluation_date').textContent = data.evaluation.evaluation_date;
            }

            // Show the modal
            const viewModal = new bootstrap.Modal(document.getElementById('viewSupplierModal'));
            viewModal.show();
        })
        .catch(error => {
            console.error('Error fetching supplier details:', error);
            showAlert('Error loading supplier details', 'danger');
        });
}

// Fetch supplier details for editing
function fetchSupplierForEdit(supplierId) {
    fetch(`/admin/suppliers/${supplierId}/edit`)
        .then(response => response.json())
        .then(data => {
            // Set form action
            const form = document.getElementById('editSupplierForm');
            form.action = form.action.replace(':id', supplierId);

            // Populate form fields
            document.getElementById('edit_company_name').value = data.company_name;
            document.getElementById('edit_business_type').value = data.business_type;
            document.getElementById('edit_tax_id').value = data.tax_id;
            document.getElementById('edit_years_in_business').value = data.years_in_business;
            document.getElementById('edit_contact_person').value = data.contact_person;
            document.getElementById('edit_contact_position').value = data.contact_position;
            document.getElementById('edit_phone').value = data.phone;
            document.getElementById('edit_email').value = data.email;
            document.getElementById('edit_address').value = data.address;
            document.getElementById('edit_business_description').value = data.business_description;
            document.getElementById('edit_payment_terms').value = data.payment_terms;
            document.getElementById('edit_bank_name').value = data.bank_name;
            document.getElementById('edit_bank_account').value = data.bank_account;
            document.getElementById('edit_bank_branch').value = data.bank_branch;

            // Populate products checkboxes
            const productCheckboxes = document.querySelectorAll('input[name="products[]"]');
            productCheckboxes.forEach(checkbox => {
                checkbox.checked = data.products.includes(checkbox.value);
            });

            // Show the modal
            const editModal = new bootstrap.Modal(document.getElementById('editSupplierModal'));
            editModal.show();
        })
        .catch(error => {
            console.error('Error fetching supplier for edit:', error);
            showAlert('Error loading supplier details', 'danger');
        });
}

// Setup delete modal
function setupDeleteModal(supplierId) {
    const form = document.getElementById('deleteSupplierForm');
    form.action = form.action.replace(':id', supplierId);
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteSupplierModal'));
    deleteModal.show();
}

// Setup evaluate modal
function setupEvaluateModal(supplierId) {
    const form = document.getElementById('evaluateSupplierForm');
    form.action = form.action.replace(':id', supplierId);
    document.getElementById('evaluation_date').valueAsDate = new Date();
    const evaluateModal = new bootstrap.Modal(document.getElementById('evaluateSupplierModal'));
    evaluateModal.show();
}

// Populate document link
function populateDocument(containerId, document) {
    const container = document.getElementById(containerId);
    container.innerHTML = '';
    if (document) {
        const link = document.createElement('a');
        link.href = document.url;
        link.className = 'btn btn-sm btn-outline-primary';
        link.textContent = 'View Document';
        link.target = '_blank';
        container.appendChild(link);
    } else {
        container.textContent = 'No document uploaded';
    }
}

// Populate multiple documents
function populateDocuments(containerId, documents) {
    const container = document.getElementById(containerId);
    container.innerHTML = '';
    if (documents && documents.length > 0) {
        documents.forEach(doc => {
            const link = document.createElement('a');
            link.href = doc.url;
            link.className = 'btn btn-sm btn-outline-primary me-2 mb-2';
            link.textContent = doc.name;
            link.target = '_blank';
            container.appendChild(link);
        });
    } else {
        container.textContent = 'No documents uploaded';
    }
}

// Show alert message
function showAlert(message, type = 'success') {
    const alertContainer = document.getElementById('alert-container');
    const alert = document.createElement('div');
    alert.className = `alert alert-${type} alert-dismissible fade show`;
    alert.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    alertContainer.appendChild(alert);
    setTimeout(() => {
        alert.remove();
    }, 5000);
} 