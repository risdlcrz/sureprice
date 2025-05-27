document.addEventListener('DOMContentLoaded', function () {
    // Elements
    const companyForm = document.getElementById('company-form');
    const supplierType = document.getElementById('supplier_type');
    const otherSupplierType = document.getElementById('other_supplier_type_group');
    const notifications = document.querySelectorAll('.notification');
    const mobileNumberField = document.querySelector('input[name="mobile_number"]');
    const telephoneNumberField = document.querySelector('input[name="telephone_number"]');
    const errorMessages = document.querySelectorAll('.error-message');

    // ===== Show/hide Other Supplier Type field =====
    if (supplierType) {
        supplierType.addEventListener('change', function () {
            if (this.value === 'Other') {
                otherSupplierType.style.display = 'block';
                otherSupplierType.querySelector('input').setAttribute('required', 'required');
            } else {
                otherSupplierType.style.display = 'none';
                otherSupplierType.querySelector('input').removeAttribute('required');
            }
        });

        // Initialize display based on current value
        if (supplierType.value === 'Other') {
            otherSupplierType.style.display = 'block';
            otherSupplierType.querySelector('input').setAttribute('required', 'required');
        } else {
            otherSupplierType.style.display = 'none';
            otherSupplierType.querySelector('input').removeAttribute('required');
        }
    }

    // ===== Notification Handling =====
    notifications.forEach(notification => {
        setTimeout(() => {
            notification.style.transform = 'translateY(-100%)';
            setTimeout(() => notification.remove(), 500);
        }, 5000);
        notification.addEventListener('click', () => {
            notification.style.transform = 'translateY(-100%)';
            setTimeout(() => notification.remove(), 500);
        });
    });

    // ===== Mobile & Telephone Number Input Handling =====
    if (mobileNumberField) {
        mobileNumberField.setAttribute('maxlength', '11');
        mobileNumberField.addEventListener('input', function () {
            // Only allow numbers and limit to 11 digits
            this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11);
            
            // Check if the number starts with '09' and has 11 digits
            if (this.value.length === 11) {
                if (!this.value.startsWith('09')) {
                    this.setCustomValidity('Mobile number must start with 09');
                    this.reportValidity();
                } else {
                    this.setCustomValidity('');
                }
            } else if (this.value.length > 0) {
                this.setCustomValidity('Mobile number must be 11 digits');
                this.reportValidity();
            } else {
                this.setCustomValidity('');
            }
        });

        // Add blur event to check when user leaves the field
        mobileNumberField.addEventListener('blur', function() {
            if (this.value.length > 0 && (this.value.length !== 11 || !this.value.startsWith('09'))) {
                this.setCustomValidity('Please enter a valid mobile number (e.g. 09123456789)');
                this.reportValidity();
            }
        });
    }

    if (telephoneNumberField) {
        telephoneNumberField.setAttribute('maxlength', '8');
        telephoneNumberField.addEventListener('input', function () {
            this.value = this.value.replace(/[^0-9]/g, '').slice(0, 8);
        });
    }

    // ===== Show Laravel Error Messages =====
    if (errorMessages.length > 0) {
        errorMessages.forEach(message => {
            message.style.display = 'flex';
        });
    }

    const errors = {};
    document.querySelectorAll('.error-message').forEach(el => {
        const fieldName = el.getAttribute('data-field');
        errors[fieldName] = el.textContent;
    });

    if (errors.username || errors.email) {
        const errorMessages = [];
        if (errors.username) errorMessages.push(errors.username);
        if (errors.email) errorMessages.push(errors.email);

        showNotification(errorMessages.join('<br>'), 'error');
    }

    // Notification function
    function showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <i class="fas fa-${type === 'success' ? 'check' : 'exclamation'}-circle"></i>
                <span>${message}</span>
            </div>
            <div class="progress-bar"></div>
        `;

        document.body.appendChild(notification);

        setTimeout(() => {
            notification.style.transform = 'translateY(-100%)';
            setTimeout(() => notification.remove(), 500);
        }, 5000);

        notification.addEventListener('click', () => {
            notification.style.transform = 'translateY(-100%)';
            setTimeout(() => notification.remove(), 500);
        });
    }

    // ===== Enhanced File Upload Handling =====
    document.querySelectorAll('input[type="file"]').forEach(input => {
        const idMappings = {
            'dti_sec_registration': 'dti_sec_preview',
            'accreditations_certifications': 'accreditations_preview',
            'business_permit_mayor_permit': 'business_permit_preview',
            'valid_id_owner_rep': 'valid_id_preview',
            'company_profile_portfolio': 'company_profile_preview',
            'sample_price_list': 'price_list_preview'
        };

        const previewId = idMappings[input.id] ||
            input.id.replace(/_registration|_certifications|_mayor_permit|_owner_rep|_portfolio|_list/g, '') + '_preview';

        const previewContainer = document.getElementById(previewId);
        const uploadLabel = input.closest('.file-upload-label');
        const uploadText = uploadLabel?.querySelector('.file-upload-text');
        const uploadIcon = uploadLabel?.querySelector('i');

        if (previewContainer) {
            previewContainer.style.display = 'none';
        } else {
            console.error(`Preview container not found for input ${input.id}`);
        }

        input.addEventListener('change', function () {
            if (!previewContainer) {
                console.error(`Preview container missing for: ${input.id}`);
                return;
            }

            previewContainer.innerHTML = '';

            if (this.files && this.files.length > 0) {
                if (uploadText) {
                    uploadText.textContent = this.files[0].name;
                    uploadText.style.color = '#333';
                    uploadText.style.fontWeight = '500';
                }

                if (uploadIcon) {
                    uploadIcon.style.color = '#4CAF50';
                }

                previewContainer.style.display = 'block';

                Array.from(this.files).forEach(file => {
                    const previewItem = document.createElement('div');
                    previewItem.className = 'file-preview-item';

                    previewItem.innerHTML = `
                        <span class="file-preview-name">${file.name}</span>
                        <span class="file-preview-remove" title="Remove file">×</span>
                    `;

                    previewContainer.appendChild(previewItem);

                    previewItem.querySelector('.file-preview-remove').addEventListener('click', (e) => {
                        e.stopPropagation();
                        input.value = '';

                        if (uploadText) {
                            uploadText.textContent = 'Choose file (PDF, JPG, PNG)';
                            uploadText.style.color = '#555';
                            uploadText.style.fontWeight = 'normal';
                        }

                        if (uploadIcon) {
                            uploadIcon.style.color = '#555';
                        }

                        previewContainer.style.display = 'none';
                    });
                });
            }
        });
    });
});
