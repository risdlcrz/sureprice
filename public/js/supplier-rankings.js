// Supplier Rankings JavaScript

// Chart instance
let rankingsChart = null;

// Initialize the page
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Initialize chart
    initializeChart();

    // Set up event listeners
    setupEventListeners();
});

// Initialize the rankings chart
function initializeChart() {
    const ctx = document.getElementById('rankingsChart').getContext('2d');
    rankingsChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: [],
            datasets: [{
                label: 'Supplier Rankings',
                data: [],
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100
                }
            }
        }
    });
}

// Set up event listeners
function setupEventListeners() {
    // Category and order selection change
    const categorySelect = document.getElementById('category');
    const orderSelect = document.getElementById('order');
    if (categorySelect && orderSelect) {
        categorySelect.addEventListener('change', updateRankings);
        orderSelect.addEventListener('change', updateRankings);
    }

    // View supplier details
    document.querySelectorAll('.view-supplier').forEach(button => {
        button.addEventListener('click', function() {
            const supplierId = this.dataset.id;
            viewSupplierDetails(supplierId);
        });
    });

    // Edit supplier button
    const editSupplierBtn = document.getElementById('edit_supplier_btn');
    if (editSupplierBtn) {
        editSupplierBtn.addEventListener('click', function() {
            const supplierId = this.dataset.id;
            editSupplier(supplierId);
        });
    }
}

// Update rankings based on selected category and order
function updateRankings() {
    const category = document.getElementById('category').value;
    const order = document.getElementById('order').value;

    fetch(`/supplier-rankings?category=${category}&order=${order}`)
        .then(response => response.json())
        .then(data => {
            updateChart(data.suppliers);
            updateTable(data.suppliers);
        })
        .catch(error => console.error('Error:', error));
}

// Update the chart with new data
function updateChart(suppliers) {
    const labels = suppliers.map(s => s.company);
    const scores = suppliers.map(s => s.final_score);

    rankingsChart.data.labels = labels;
    rankingsChart.data.datasets[0].data = scores;
    rankingsChart.update();
}

// Update the table with new data
function updateTable(suppliers) {
    const tbody = document.querySelector('table tbody');
    tbody.innerHTML = '';

    suppliers.forEach((supplier, index) => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${index + 1}</td>
            <td>
                <a href="#" class="view-supplier" data-id="${supplier.id}">
                    ${supplier.company}
                </a>
            </td>
            <td>${supplier.contact_person}</td>
            <td>${supplier.email}</td>
            <td>${supplier.mobile_number}</td>
            <td>${supplier.supplier_type}</td>
            <td>
                <div class="rating">
                    ${generateRatingStars(supplier.final_score)}
                    <span class="rating-value">${supplier.final_score.toFixed(1)}</span>
                </div>
            </td>
        `;
        tbody.appendChild(row);
    });

    // Reattach event listeners to new view buttons
    document.querySelectorAll('.view-supplier').forEach(button => {
        button.addEventListener('click', function() {
            const supplierId = this.dataset.id;
            viewSupplierDetails(supplierId);
        });
    });
}

// Generate rating stars HTML
function generateRatingStars(score) {
    const fullStars = Math.floor(score / 20);
    const halfStar = score % 20 >= 10;
    const emptyStars = 5 - fullStars - (halfStar ? 1 : 0);

    let stars = '';
    for (let i = 0; i < fullStars; i++) {
        stars += '<i class="fas fa-star"></i>';
    }
    if (halfStar) {
        stars += '<i class="fas fa-star-half-alt"></i>';
    }
    for (let i = 0; i < emptyStars; i++) {
        stars += '<i class="far fa-star"></i>';
    }

    return stars;
}

// View supplier details
function viewSupplierDetails(supplierId) {
    fetch(`/supplier-rankings/${supplierId}`)
        .then(response => response.json())
        .then(data => {
            // Populate basic information
            document.getElementById('view_company').textContent = data.company;
            document.getElementById('view_supplier_type').textContent = data.supplier_type;
            document.getElementById('view_business_reg_no').textContent = data.business_reg_no;
            document.getElementById('view_contact_person').textContent = data.contact_person;
            document.getElementById('view_designation').textContent = data.designation;
            document.getElementById('view_email').textContent = data.email;
            document.getElementById('view_mobile_number').textContent = data.mobile_number;
            document.getElementById('view_telephone_number').textContent = data.telephone_number;
            document.getElementById('view_address').textContent = data.address;

            // Populate business details
            document.getElementById('view_years_operation').textContent = data.years_operation;
            document.getElementById('view_business_size').textContent = data.business_size;
            
            // Populate materials
            const materialsList = document.getElementById('view_materials');
            materialsList.innerHTML = '';
            data.materials.forEach(material => {
                const badge = document.createElement('span');
                badge.className = 'badge bg-primary me-2 mb-2';
                badge.textContent = material;
                materialsList.appendChild(badge);
            });

            // Populate terms & banking
            document.getElementById('view_payment_terms').textContent = data.payment_terms;
            document.getElementById('view_vat_registered').textContent = data.vat_registered;
            document.getElementById('view_use_sureprice').textContent = data.use_sureprice;
            document.getElementById('view_bank_name').textContent = data.bank_name;
            document.getElementById('view_account_name').textContent = data.account_name;
            document.getElementById('view_account_number').textContent = data.account_number;

            // Populate document links
            updateDocumentLink('view_dti_sec_registration_link', data.dti_sec_registration_path);
            updateDocumentLink('view_accreditation_docs_link', data.accreditation_docs_path);
            updateDocumentLink('view_mayors_permit_link', data.mayors_permit_path);
            updateDocumentLink('view_valid_id_link', data.valid_id_path);
            updateDocumentLink('view_company_profile_link', data.company_profile_path);
            updateDocumentLink('view_price_list_link', data.price_list_path);

            // Populate evaluation history
            const evaluationHistory = document.getElementById('view_evaluation_history');
            evaluationHistory.innerHTML = '';
            data.evaluations.forEach(evaluation => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${new Date(evaluation.evaluation_date).toLocaleDateString()}</td>
                    <td>${evaluation.engagement_score.toFixed(1)}</td>
                    <td>${evaluation.delivery_speed_score.toFixed(1)}</td>
                    <td>${evaluation.performance_score.toFixed(1)}</td>
                    <td>${evaluation.quality_score.toFixed(1)}</td>
                    <td>${evaluation.cost_variance_score.toFixed(1)}</td>
                    <td>${evaluation.sustainability_score.toFixed(1)}</td>
                    <td>${evaluation.final_score.toFixed(1)}</td>
                `;
                evaluationHistory.appendChild(row);
            });

            // Set edit button data
            document.getElementById('edit_supplier_btn').dataset.id = supplierId;

            // Show modal
            const viewModal = new bootstrap.Modal(document.getElementById('viewModal'));
            viewModal.show();
        })
        .catch(error => console.error('Error:', error));
}

// Update document link
function updateDocumentLink(elementId, path) {
    const link = document.getElementById(elementId);
    if (path) {
        link.href = path;
        link.style.display = 'inline-block';
    } else {
        link.style.display = 'none';
    }
}

// Edit supplier
function editSupplier(supplierId) {
    // Close view modal
    const viewModal = bootstrap.Modal.getInstance(document.getElementById('viewModal'));
    viewModal.hide();

    // Fetch supplier data and populate edit form
    fetch(`/supplier-rankings/${supplierId}/edit`)
        .then(response => response.json())
        .then(data => {
            // Populate form fields
            document.getElementById('company').value = data.company;
            document.getElementById('supplier_type').value = data.supplier_type;
            document.getElementById('business_reg_no').value = data.business_reg_no;
            document.getElementById('contact_person').value = data.contact_person;
            document.getElementById('designation').value = data.designation;
            document.getElementById('email').value = data.email;
            document.getElementById('mobile_number').value = data.mobile_number;
            document.getElementById('telephone_number').value = data.telephone_number;
            document.getElementById('address').value = data.address;
            document.getElementById('years_operation').value = data.years_operation;
            document.getElementById('business_size').value = data.business_size;
            document.getElementById('payment_terms').value = data.payment_terms;
            document.getElementById('vat_registered').value = data.vat_registered;
            document.getElementById('use_sureprice').value = data.use_sureprice;
            document.getElementById('bank_name').value = data.bank_name;
            document.getElementById('account_name').value = data.account_name;
            document.getElementById('account_number').value = data.account_number;

            // Check materials
            data.materials.forEach(material => {
                const checkbox = document.querySelector(`input[name="materials[]"][value="${material}"]`);
                if (checkbox) {
                    checkbox.checked = true;
                }
            });

            // Update form action
            const form = document.querySelector('#addModal form');
            form.action = `/supplier-rankings/${supplierId}`;
            form.insertAdjacentHTML('afterbegin', '<input type="hidden" name="_method" value="PUT">');

            // Show edit modal
            const editModal = new bootstrap.Modal(document.getElementById('addModal'));
            editModal.show();
        })
        .catch(error => console.error('Error:', error));
} 