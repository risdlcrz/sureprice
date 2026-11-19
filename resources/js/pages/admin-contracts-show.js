// Extracted from resources/views/admin/contracts/show.blade.php
const __page = window.__page || {};
window.contractSignatureUrl = __page.show0_0;
window.contractStatusUrl = __page.show0_1;

// Contract status update function
function updateStatus(status) {
    console.log('updateStatus called with:', status);
    
    // Check if approval is being requested and signatures are missing
    if (status === 'approved') {
        const approveButton = document.querySelector('button[onclick="updateStatus(\'approved\')"]');
        console.log('Approve button found:', approveButton);
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
            text: error.message || 'An error occurred while updating the contract status.',
            confirmButtonText: 'OK'
        });
    });
}

// Signature modal functions
let currentSignatureType = null;
let signaturePad = null;

function showSignatureModal(type) {
    currentSignatureType = type;
    const modal = new bootstrap.Modal(document.getElementById('signatureModal'));
    modal.show();
    
    // Initialize signature pad
    const canvas = document.getElementById('signatureCanvas');
    signaturePad = new SignaturePad(canvas, {
        backgroundColor: 'rgb(255, 255, 255)'
    });
}

function clearSignature() {
    if (signaturePad) {
        signaturePad.clear();
    }
}

function saveSignature() {
    if (!signaturePad || signaturePad.isEmpty()) {
        Swal.fire({
            icon: 'warning',
            title: 'No Signature',
            text: 'Please provide a signature before saving.',
            confirmButtonText: 'OK'
        });
        return;
    }

    const signatureData = signaturePad.toDataURL();
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Create the request body with the correct field name
    const requestBody = {
        signature_type: currentSignatureType,
        [currentSignatureType + '_signature']: signatureData
    };

    console.log('Sending signature request:', {
        url: window.contractSignatureUrl,
        signatureType: currentSignatureType,
        dataLength: signatureData.length,
        requestBody: requestBody
    });

    fetch(window.contractSignatureUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json'
        },
        body: JSON.stringify(requestBody)
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: 'Signature saved successfully!',
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                window.location.reload();
            });
        } else {
            throw new Error(data.message || 'Failed to save signature');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: error.message || 'An error occurred while saving the signature.',
            confirmButtonText: 'OK'
        });
    });
}

// Delete confirmation functions
function showDeleteModal() {
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

function submitDelete() {
    document.getElementById('deleteForm').submit();
}

// Generate payments function
function generatePayments() {
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const contractId = __page.show0_2;
    
    Swal.fire({
        title: 'Generating Payments',
        text: 'Please wait...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    fetch(`${__page.show0_3}/${contractId}/generate-payments`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: data.message,
                showConfirmButton: false,
                timer: 2000
            }).then(() => {
                window.location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message || 'Failed to generate payments.',
                confirmButtonText: 'OK'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'An error occurred while generating payments.',
            confirmButtonText: 'OK'
        });
    });
}

// used by inline handlers / other scripts in the view
Object.assign(window, { updateStatus, showSignatureModal, clearSignature, saveSignature, submitDelete, generatePayments });
