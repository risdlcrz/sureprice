// Extracted from resources/views/finance/payments.blade.php
const __page = window.__page || {};
// Ensure Bootstrap modals work properly
document.addEventListener('DOMContentLoaded', function() {
    // Initialize all modals
    var modals = document.querySelectorAll('.modal');
    modals.forEach(function(modal) {
        new bootstrap.Modal(modal);
    });
    
    // Add click event listeners to Pay buttons
    var payButtons = document.querySelectorAll('[data-bs-toggle="modal"]');
    payButtons.forEach(function(button) {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            var targetModal = this.getAttribute('data-bs-target');
            var modal = document.querySelector(targetModal);
            if (modal) {
                var bsModal = new bootstrap.Modal(modal);
                bsModal.show();
            }
        });
    });
});

// Search functionality
document.getElementById('contractSearch').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const contracts = __page.payments0_0;
    const resultsDiv = document.getElementById('searchResults');
    
    if (searchTerm.length < 2) {
        resultsDiv.innerHTML = '';
        return;
    }
    
    const filteredContracts = contracts.filter(contract => {
        const contractNumber = (contract.contract_number || 'Contract #' + contract.id).toLowerCase();
        const clientName = (contract.client?.name || '').toLowerCase();
        return contractNumber.includes(searchTerm) || clientName.includes(searchTerm);
    });
    
    if (filteredContracts.length === 0) {
        resultsDiv.innerHTML = '<div class="alert alert-info">No contracts found matching your search.</div>';
        return;
    }
    
    let html = '<div class="list-group">';
    filteredContracts.forEach(contract => {
        const payments = contract.payments;
        const totalAmount = payments.reduce((sum, p) => sum + parseFloat(p.amount), 0);
        const paidAmount = payments.filter(p => p.status === 'paid').reduce((sum, p) => sum + parseFloat(p.amount), 0);
        const pendingAmount = payments.filter(p => p.status === 'pending').reduce((sum, p) => sum + parseFloat(p.amount), 0);
        
        html += `
            <div class="list-group-item">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1">${contract.contract_number || 'Contract #' + contract.id}</h6>
                        <small>Client: ${contract.client?.name || 'N/A'} | Contractor: ${contract.contractor?.name || 'N/A'}</small>
                    </div>
                    <div class="text-end">
                        <div>₱${totalAmount.toLocaleString('en-US', {minimumFractionDigits: 2})}</div>
                        <small class="text-muted">Pending: ₱${pendingAmount.toLocaleString('en-US', {minimumFractionDigits: 2})}</small>
                    </div>
                    <a href="${__page.payments0_1}/${contract.payments[0].id}" class="btn btn-sm btn-primary">View</a>
                </div>
            </div>
        `;
    });
    html += '</div>';
    
    resultsDiv.innerHTML = html;
});
