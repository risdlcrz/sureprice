document.addEventListener('DOMContentLoaded', function () {
    // Elements
    const companyForm = document.getElementById('company-form');
    const supplierType = document.getElementById('supplier_type');
    const otherSupplierType = document.getElementById('other_supplier_type_group');
    const notifications = document.querySelectorAll('.notification');
    const mobileNumberField = document.querySelector('input[name="mobile_number"]');
    const telephoneNumberField = document.querySelector('input[name="telephone_number"]');
    const errorMessages = document.querySelectorAll('.error-message');
    const registrationForm = document.getElementById('registrationForm');

    // ===== Show/hide Other Supplier Type field =====
    if (supplierType) {
        supplierType.addEventListener('change', function () {
            if (this.value === 'Other') {
                otherSupplierType.style.display = 'block';
                otherSupplierType.querySelector('input').setAttribute('required', 'required');
            } else {
                otherSupplierType.style.display = 'none';
                otherSupplierType.querySelector('input').removeAttribute('required');
                otherSupplierType.querySelector('input').value = '';
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
                const file = this.files[0];
                
                // Validate file type
                const allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
                if (!allowedTypes.includes(file.type)) {
                    showNotification('Please select only PDF, JPG, or PNG files.', 'error');
                    this.value = '';
                    return;
                }

                // Validate file size (10MB max)
                const maxSize = 10 * 1024 * 1024; // 10MB
                if (file.size > maxSize) {
                    showNotification('File size must be less than 10MB.', 'error');
                    this.value = '';
                    return;
                }

                // Update UI to show file is selected
                if (uploadText) {
                    uploadText.textContent = file.name;
                    uploadText.classList.add('has-file');
                }

                if (uploadIcon) {
                    uploadIcon.style.color = '#02912d';
                }

                if (uploadLabel) {
                    uploadLabel.classList.add('has-file');
                }

                previewContainer.style.display = 'block';
                previewContainer.classList.add('show');

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
                        uploadText.classList.remove('has-file');
                    }

                    if (uploadIcon) {
                        uploadIcon.style.color = '#555';
                    }

                    if (uploadLabel) {
                        uploadLabel.classList.remove('has-file');
                    }

                    previewContainer.style.display = 'none';
                    previewContainer.classList.remove('show');
                });

                // Show success notification
                showNotification(`File "${file.name}" selected successfully!`, 'success');
            }
        });
    });

    // ===== Form Validation Enhancement =====
    if (registrationForm) {
        registrationForm.addEventListener('submit', function(e) {
            let hasClientSideErrors = false;
            const requiredFields = this.querySelectorAll('[required]');
            const errorMessages = [];

            // Custom required file fields (now handled in JS)
            const requiredFileFields = [
                'dti_sec_registration',
                'business_permit_mayor_permit',
                'valid_id_owner_rep'
            ];
            requiredFileFields.forEach(fieldId => {
                const fileInput = document.getElementById(fieldId);
                const formGroup = fileInput?.closest('.form-group');
                if (fileInput && (!fileInput.files || fileInput.files.length === 0)) {
                    hasClientSideErrors = true;
                    if (formGroup) {
                        formGroup.classList.add('has-error');
                        let errorSpan = formGroup.querySelector('.error-message');
                        if (!errorSpan) {
                            errorSpan = document.createElement('span');
                            errorSpan.className = 'error-message';
                            formGroup.appendChild(errorSpan);
                        }
                        errorSpan.innerHTML = `<i class="fas fa-exclamation-circle"></i> This document is required`;
                        errorSpan.style.display = 'flex';
                    }
                    errorMessages.push(`${fieldId.replace(/_/g, ' ')} is required`);
                }
            });

            // Clear previous error styling and messages (but preserve server-side errors)
            document.querySelectorAll('.form-group').forEach(group => {
                group.classList.remove('has-error');
                const existingError = group.querySelector('.error-message');
                if (existingError && !existingError.hasAttribute('data-server-error')) {
                    existingError.remove();
                }
            });

            // Check required fields (non-file)
            requiredFields.forEach(field => {
                if (field.type !== 'file') {
                    if (!field.value.trim()) {
                        const formGroup = field.closest('.form-group');
                        if (formGroup) {
                            formGroup.classList.add('has-error');
                            hasClientSideErrors = true;
                            const label = formGroup.querySelector('label');
                            const fieldName = label ? label.textContent.replace('*', '').trim() : 'Required field';
                            let errorSpan = formGroup.querySelector('.error-message');
                            if (!errorSpan) {
                                errorSpan = document.createElement('span');
                                errorSpan.className = 'error-message';
                                formGroup.appendChild(errorSpan);
                            }
                            errorSpan.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${fieldName} is required`;
                            errorSpan.style.display = 'flex';
                            errorMessages.push(`${fieldName} is required`);
                        }
                    }
                }
            });

            // Check password confirmation
            const password = this.querySelector('input[name="password"]');
            const passwordConfirmation = this.querySelector('input[name="password_confirmation"]');
            if (password && passwordConfirmation && password.value !== passwordConfirmation.value) {
                const formGroup = passwordConfirmation.closest('.form-group');
                if (formGroup) {
                    formGroup.classList.add('has-error');
                    hasClientSideErrors = true;
                }
                let errorSpan = formGroup.querySelector('.error-message');
                if (!errorSpan) {
                    errorSpan = document.createElement('span');
                    errorSpan.className = 'error-message';
                    formGroup.appendChild(errorSpan);
                }
                errorSpan.innerHTML = '<i class="fas fa-exclamation-circle"></i> Password confirmation does not match';
                errorSpan.style.display = 'flex';
                errorMessages.push('Password confirmation does not match');
            }

            // Only prevent submission if there are client-side validation errors
            if (hasClientSideErrors) {
                e.preventDefault();
                const errorText = errorMessages.length > 0 ? 
                    `Please fix the following errors:<br>• ${errorMessages.slice(0, 3).join('<br>• ')}` :
                    'Please fill in all required fields correctly.';
                showNotification(errorText, 'error');
                const firstError = document.querySelector('.form-group.has-error');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                return false;
            }
            // If no client-side errors, allow form submission to proceed
        });
    }

    // ===== Real-time validation feedback =====
    document.querySelectorAll('input, select, textarea').forEach(field => {
        field.addEventListener('blur', function() {
            const formGroup = this.closest('.form-group');
            if (formGroup) {
                if (this.hasAttribute('required') && !this.value.trim()) {
                    formGroup.classList.add('has-error');
                } else {
                    formGroup.classList.remove('has-error');
                }
            }
        });

        field.addEventListener('input', function() {
            const formGroup = this.closest('.form-group');
            if (formGroup && formGroup.classList.contains('has-error')) {
                if (this.value.trim()) {
                    formGroup.classList.remove('has-error');
                }
            }
        });
    });

    // ===== Enhanced Error Display =====
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

    // ===== Notification function =====
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

    // ===== Auto-save form data to localStorage =====
    // Removed localStorage functionality to prevent form data persistence
    // Users can use browser's built-in form autofill if needed
});
