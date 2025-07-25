// Custom JS extracted from admin/contracts/show.blade.php
// (CDN includes for SweetAlert2 and SignaturePad should remain in the Blade file or be managed by npm)

let currentSignatureType = null;

function updateStatus(status) {
    const token = document.querySelector('meta[name="csrf-token"]');
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
    if (!csrfToken) {
        console.error('CSRF token is empty');
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'CSRF token is empty. Please refresh the page and try again.'
        });
        return;
    }
    Swal.fire({
        title: 'Updating Status',
        text: 'Please wait...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
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
        const data = await response.json();
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
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: 'Contract status updated successfully!',
                showConfirmButton: false,
                timer: 1500
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

window.showSignatureModal = showSignatureModal;
window.clearSignature = clearSignature;
window.saveSignature = saveSignature; 