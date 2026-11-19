// Extracted from resources/views/admin/contracts/index.blade.php
const __page = window.__page || {};
let currentContractId = null;
const statusModal = new bootstrap.Modal(document.getElementById('statusModal'));

function showStatusModal(contractId) {
    currentContractId = contractId;
    statusModal.show();
}

document.querySelectorAll('.status-btn').forEach(button => {
    button.addEventListener('click', function() {
        const status = this.dataset.status;
        updateStatus(currentContractId, status);
    });
});

function updateStatus(contractId, status) {
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    fetch(`${__page.index0_0}/${contractId}/status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ status: status, _method: 'PATCH' })
    })
    .then(async response => {
        let data;
        try {
            data = await response.clone().json();
        } catch (e) {
            const text = await response.text();
            throw new Error(text);
        }
        if (!response.ok) {
            throw new Error(data.message || 'Failed to update status');
        }
        return data;
    })
    .then(data => {
        if (data.success) {
            // Update the status button text and class
            const statusButton = document.querySelector(`button[onclick="showStatusModal(${contractId})"]`);
            statusButton.textContent = data.status;
            statusButton.className = `btn btn-sm status-badge ${
                data.status.toLowerCase() === 'draft' ? 'btn-warning' : 
                data.status.toLowerCase() === 'approved' ? 'btn-success' : 
                'btn-secondary'
            }`;
            
            // Close the modal
            const statusModal = bootstrap.Modal.getInstance(document.getElementById('statusModal'));
            statusModal.hide();
            
            // Show success message
            alert('Contract status updated successfully');
        } else {
            throw new Error(data.message || 'Failed to update status');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert(error.message || 'Error updating status. Please try again.');
    });
}

let deleteForm = null;

function confirmDelete(contractId) {
    deleteForm = document.getElementById('delete-form-' + contractId);
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}

document.getElementById('confirmDelete').addEventListener('click', function() {
    if (deleteForm) {
        deleteForm.submit();
    }
});

// used by inline handlers / other scripts in the view
Object.assign(window, { showStatusModal, confirmDelete });
