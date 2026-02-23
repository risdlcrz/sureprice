// Custom JS extracted from admin/contracts/show.blade.php
// (CDN includes for SweetAlert2 and SignaturePad should remain in the Blade file or be managed by npm)

let currentSignatureType = null;

function updateStatus(status) {
    
    // Check if approval is being requested and signatures are missing
    if (status === 'approved') {
        const approveButton = document.querySelector('button[onclick="updateStatus(\'approved\')"]');
        console.log('Approve button disabled:', approveButton ? approveButton.disabled : 'Button not found');
        
        if (approveButton && approveButton.disabled) {
            Swal.fire({
                icon: 'warning',
                title: 'Cannot Approve',
                text: 'Both contractor and client signatures are required before approval.',
                confirmButtonText: 'OK'
            });
            return;
        }
    }

    const token = document.querySelector('meta[name="csrf-token"]');
    console.log('CSRF token element:', token);
    
    if (!token) {
        console.error('CSRF token meta tag not found');
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'CSRF token not found. Please refresh the page and try again.'
        });
        return;
    }
    const csrfToken = token.getAttribute('content');
    console.log('CSRF token value:', csrfToken ? 'Present' : 'Empty');
    
    if (!csrfToken) {
        console.error('CSRF token is empty');
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'CSRF token is empty. Please refresh the page and try again.'
        });
        return;
    }
    
    console.log('Contract status URL:', window.contractStatusUrl);
    Swal.fire({
        title: 'Updating Status',
        text: 'Please wait...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    console.log('Making fetch request to:', window.contractStatusUrl);
    console.log('Request payload:', { status: status });
    
    fetch(window.contractStatusUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ status: status }),
        credentials: 'same-origin'
    })
    .then(async response => {
        console.log('Response status:', response.status);
        console.log('Response ok:', response.ok);
        
        const data = await response.json();
        console.log('Response data:', data);
        
        if (!response.ok) {
            if (response.status === 419) {
                throw new Error('CSRF token mismatch. Please refresh the page and try again.');
            }
            throw new Error(data.message || `HTTP error! status: ${response.status}`);
        }
        return data;
    })
    .then(data => {
        if (data.success) {
            let message = data.message;
            let icon = 'success';
            
            // Customize message based on status
            if (data.status && data.status.toLowerCase() === 'approved') {
                message = 'Contract approved successfully! Material requests and purchase requests are now enabled.';
                icon = 'success';
            } else if (data.status && data.status.toLowerCase() === 'rejected') {
                message = 'Contract rejected.';
                icon = 'error';
            }
            
            Swal.fire({
                icon: icon,
                title: 'Success',
                text: message,
                showConfirmButton: true,
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.reload();
            });
        } else {
            throw new Error(data.message || 'Unknown error');
        }
    })
    .catch(error => {
    console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: error.message
        });
    });
}

function showDeleteModal() {
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}

function submitDelete() {
    document.getElementById('delete-form').submit();
}

function showSignatureModal(type) {
    currentSignatureType = type;
    const signatureModal = new bootstrap.Modal(document.getElementById('signatureModal'));
    const subtitle = document.getElementById('signatureModalSubtitle');
    subtitle.textContent = `Add ${type === 'contractor' ? 'Contractor' : 'Client'} signature below`;
    signatureModal.show();
    const canvas = document.getElementById('signatureCanvas');
    if (window.signaturePad) {
        window.signaturePad.clear();
    } else {
        window.signaturePad = new SignaturePad(canvas, {
            backgroundColor: 'rgb(255, 255, 255)',
            penColor: 'rgb(0, 0, 0)'
        });
    }
}

function clearSignature() {
    if (window.signaturePad) {
        window.signaturePad.clear();
    }
}

function saveSignature() {
    if (!window.signaturePad || window.signaturePad.isEmpty()) {
        Swal.fire({
            icon: 'warning',
            title: 'No Signature',
            text: 'Please provide a signature before saving.'
        });
        return;
    }
    const signatureData = window.signaturePad.toDataURL();
    Swal.fire({
        title: 'Saving Signature',
        text: 'Please wait...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    fetch(window.contractSignatureUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            signature_type: currentSignatureType,
            [currentSignatureType + '_signature']: signatureData
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: data.message,
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                // Update approval button state if needed
                updateApprovalButtonState(data.can_be_approved);
                window.location.reload();
            });
        } else {
            throw new Error(data.message || 'Unknown error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error saving signature: ' + error.message
        });
    });
}

function updateApprovalButtonState(canBeApproved) {
    const approveButton = document.querySelector('button[onclick="updateStatus(\'approved\')"]');
    if (approveButton) {
        if (canBeApproved) {
            approveButton.disabled = false;
            approveButton.title = '';
        } else {
            approveButton.disabled = true;
            approveButton.title = 'Both contractor and client signatures are required before approval.';
        }
    }
}

window.showSignatureModal = showSignatureModal;
window.clearSignature = clearSignature;
window.saveSignature = saveSignature; 