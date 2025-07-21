// Custom JS extracted from admin/contracts/editor.blade.php
// (CDN includes for Select2 and SignaturePad should remain in the Blade file or be managed by npm)

document.addEventListener('DOMContentLoaded', function() {
    // --- Improved AJAX Quotation Request Dropdown ---
    $('#quotation_request_id').select2({
        width: '100%',
        placeholder: 'Select Quotation Request',
        allowClear: true,
        minimumInputLength: 1,
        ajax: {
            url: '/search/quotation-requests',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return { q: params.term };
            },
            processResults: function(data) {
                return {
                    results: data.data.map(function(item) {
                        return {
                            id: item.id,
                            text: `QR-${item.request_number} - ${item.client_name} (${item.created_at})`,
                            request_number: item.request_number,
                            client_name: item.client_name,
                            created_at: item.created_at
                        };
                    })
                };
            },
            cache: true
        },
        templateResult: function(item) {
            if (!item.id) return item.text;
            return `<strong>QR-${item.request_number}</strong> - ${item.client_name} <span class='text-muted'>(${item.created_at})</span>`;
        },
        templateSelection: function(item) {
            return item.text || '';
        },
        escapeMarkup: function(m) { return m; }
    });
    // --- Quotation Request Autofill ---
    const qrSelect = document.getElementById('quotation_request_id');
    const saveBtn = document.getElementById('save-btn');
    $('#quotation_request_id').on('select2:select', function(e) {
        const qrId = e.params.data.id;
        if (!qrId) return;
        fetch(`/api/quotation-requests/${qrId}`)
            .then(res => res.json())
            .then(data => {
                // Autofill contract fields
                const startDateInput = document.getElementById('project_start_date');
                const endDateInput = document.getElementById('project_end_date');
                if (startDateInput && data.start_date) startDateInput.value = data.start_date;
                if (endDateInput && data.end_date) endDateInput.value = data.end_date;
                // Autofill contract fields (with element existence checks)
                const clientNameDisplay = document.getElementById('client_name_display');
                if (clientNameDisplay) clientNameDisplay.innerText = data.client?.name || '';
                const clientAddressDisplay = document.getElementById('client_address_display');
                if (clientAddressDisplay) clientAddressDisplay.innerText = data.client?.address || '';
                const scopeOfWorkDisplay = document.getElementById('scope_of_work_display');
                if (scopeOfWorkDisplay) scopeOfWorkDisplay.innerText = data.scope_of_work || '';
                const materialsTotalDisplay = document.getElementById('materials_total_display');
                if (materialsTotalDisplay) materialsTotalDisplay.innerText = '₱' + (data.total_materials_cost?.toFixed(2) || '0.00');
                const laborFeeDisplay = document.getElementById('labor_fee_display');
                if (laborFeeDisplay) laborFeeDisplay.innerText = '₱' + (data.labor_fee?.toFixed(2) || '0.00');
                const grandTotalDisplay = document.getElementById('grand_total_display');
                if (grandTotalDisplay) grandTotalDisplay.innerText = '₱' + (data.grand_total?.toFixed(2) || '0.00');
                // Hidden fields for backend (with checks)
                const clientName = document.getElementById('client_name');
                if (clientName) clientName.value = data.client?.name || '';
                const clientStreet = document.getElementById('client_street');
                if (clientStreet) clientStreet.value = data.client?.street || '';
                const clientCity = document.getElementById('client_city');
                if (clientCity) clientCity.value = data.client?.city || '';
                const clientState = document.getElementById('client_state');
                if (clientState) clientState.value = data.client?.state || '';
                const clientPostal = document.getElementById('client_postal');
                if (clientPostal) clientPostal.value = data.client?.postal || '';
                const clientEmail = document.getElementById('client_email');
                if (clientEmail) clientEmail.value = data.client?.email || '';
                const clientPhone = document.getElementById('client_phone');
                if (clientPhone) clientPhone.value = data.client?.phone || '';
                const scopeOfWork = document.getElementById('scope_of_work');
                if (scopeOfWork) scopeOfWork.value = data.scope_of_work || '';
                const materialsTotal = document.getElementById('materials_total');
                if (materialsTotal) materialsTotal.value = data.total_materials_cost?.toFixed(2) || '0.00';
                const laborFee = document.getElementById('labor_fee');
                if (laborFee) laborFee.value = data.labor_fee?.toFixed(2) || '0.00';
                const grandTotal = document.getElementById('grand_total');
                if (grandTotal) grandTotal.value = data.grand_total?.toFixed(2) || '0.00';
                // Autofill client signature name and date
                const clientNameSigned = document.getElementById('client_name_signed_display');
                if (clientNameSigned) clientNameSigned.innerText = data.client?.name || '';
                const clientDateSigned = document.getElementById('client_date_signed_display');
                if (clientDateSigned) clientDateSigned.innerText = (new Date()).toLocaleDateString();
            });
    });
    // --- End Quotation Request Autofill ---

    // Project Timeline logic
    function updateTimeline() {
      const start = document.getElementById('project_start_date').value;
      const end = document.getElementById('project_end_date').value;
      var startDisplay = document.getElementById('timeline_start_display');
      var endDisplay = document.getElementById('timeline_end_display');
      if (startDisplay) startDisplay.innerText = start;
      if (endDisplay) endDisplay.innerText = end;
      // Only enable save if both dates are filled
      const saveBtn = document.getElementById('save-btn');
      if (start && end) {
        saveBtn.disabled = false;
      } else {
        saveBtn.disabled = true;
      }
    }
    document.getElementById('project_start_date').addEventListener('change', updateTimeline);
    document.getElementById('project_end_date').addEventListener('change', updateTimeline);

    // Constructor autofill logic (fix for inline dropdown)
    const constructorSelect = document.getElementById('constructor_id');
    if (constructorSelect) {
        constructorSelect.addEventListener('change', function() {
            const selected = this.options[this.selectedIndex];
            if (!selected || !selected.value) return;
            // Compose name
            const name = (selected.dataset.first_name || '') + ' ' + (selected.dataset.last_name || '');
            const company = selected.dataset.company_name || '';
            const fullName = company ? (name + ' - ' + company) : name;
            // Address
            const address = [selected.dataset.street, selected.dataset.barangay, selected.dataset.city, selected.dataset.state, selected.dataset.postal].filter(Boolean).join(', ');
            // Fill contract blanks
            const nameSpan = document.getElementById('contractor_name_display');
            if (nameSpan) nameSpan.innerText = fullName;
            const addressSpan = document.getElementById('contractor_address_display');
            if (addressSpan) addressSpan.innerText = address;
            // Autofill contractor signature name and date
            const nameSignedSpan = document.getElementById('contractor_name_signed_display');
            if (nameSignedSpan) nameSignedSpan.innerText = fullName;
            const contractorDateSigned = document.getElementById('contractor_date_signed_display');
            if (contractorDateSigned) contractorDateSigned.innerText = (new Date()).toLocaleDateString();
            // Fill hidden fields
            document.getElementById('contractor_name').value = fullName;
            document.getElementById('contractor_street').value = selected.dataset.street || '';
            document.getElementById('contractor_city').value = selected.dataset.city || '';
            document.getElementById('contractor_state').value = selected.dataset.state || '';
            document.getElementById('contractor_postal').value = selected.dataset.postal || '';
            document.getElementById('contractor_email').value = selected.dataset.email || '';
            document.getElementById('contractor_phone').value = selected.dataset.phone || '';
        });
    }

    // Payment plan custom input toggle
    const planSelect = document.getElementById('payment_plan');
    const planCustom = document.getElementById('payment_plan_custom');
    planSelect.addEventListener('change', function() {
        if (this.value === 'custom') {
            planCustom.style.display = '';
            planCustom.required = true;
        } else {
            planCustom.style.display = 'none';
            planCustom.required = false;
        }
    });

    // Autofill property address if 'same as client address' is checked
    const propertyAddressInput = document.getElementById('property_address');
    const sameAsClientCheckbox = document.getElementById('same_as_client_address');
    const clientAddressDisplay = document.getElementById('client_address_display');
    
    function autofillPropertyAddress() {
        if (sameAsClientCheckbox && sameAsClientCheckbox.checked) {
            if (clientAddressDisplay && propertyAddressInput) {
                const clientAddress = clientAddressDisplay.innerText.trim();
                if (clientAddress) {
                    propertyAddressInput.value = clientAddress;
                    propertyAddressInput.readOnly = true;
                    propertyAddressInput.style.backgroundColor = '#e9ecef';
                    propertyAddressInput.style.color = '#6c757d';
                }
            }
        } else {
            if (propertyAddressInput) {
                propertyAddressInput.value = '';
                propertyAddressInput.readOnly = false;
                propertyAddressInput.style.backgroundColor = '#f8f9fa';
                propertyAddressInput.style.color = '#333';
            }
        }
    }
    
    if (sameAsClientCheckbox) {
        sameAsClientCheckbox.addEventListener('change', autofillPropertyAddress);
        // Run on page load in case it's already checked
        autofillPropertyAddress();
    }

    // Signature restrictions based on user role
    function setupSignatureRestrictions() {
        // Check if user is a client (you may need to adjust this based on your auth system)
        const isClient = document.body.classList.contains('client-user') || 
                        window.location.pathname.includes('/client/') ||
                        (typeof window.currentUser !== 'undefined' && window.currentUser.role === 'client');
        
        if (isClient) {
            document.body.classList.add('client-signature-only');
            
            // Disable contractor signature pad
            const contractorPad = document.getElementById('contractor-signature-pad');
            if (contractorPad) {
                contractorPad.style.pointerEvents = 'none';
                contractorPad.style.opacity = '0.6';
            }
            
            // Disable contractor clear button
            const contractorClearBtn = document.querySelector('button[onclick="clearSignature(\'contractor\')"]');
            if (contractorClearBtn) {
                contractorClearBtn.disabled = true;
                contractorClearBtn.style.opacity = '0.6';
            }
        }
    }

    // Initialize signature restrictions
    setupSignatureRestrictions();
    
    // Initialize signature pads
    function initializeSignaturePads() {
        // Check if SignaturePad is available
        if (typeof SignaturePad !== 'undefined') {
            // Initialize contractor signature pad
            const contractorCanvas = document.getElementById('contractor-signature-pad');
            if (contractorCanvas) {
                window.contractorSignaturePad = new SignaturePad(contractorCanvas, {
                    backgroundColor: 'rgb(255, 255, 255)',
                    penColor: 'rgb(0, 0, 0)'
                });
            }
            
            // Initialize client signature pad
            const clientCanvas = document.getElementById('client-signature-pad');
            if (clientCanvas) {
                window.clientSignaturePad = new SignaturePad(clientCanvas, {
                    backgroundColor: 'rgb(255, 255, 255)',
                    penColor: 'rgb(0, 0, 0)'
                });
            }
        }
    }
    
    // Initialize signature pads when DOM is loaded
    initializeSignaturePads();
    
    // Download/Print functionality
    document.getElementById('download-pdf').addEventListener('click', function() {
        // Create a new window for printing
        const printWindow = window.open('', '_blank');
        const contractContent = document.querySelector('.contract-border').innerHTML;
        
        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>Contract Agreement</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 20px; }
                    .contract-title { font-size: 24px; font-weight: bold; text-align: center; margin-bottom: 20px; }
                    .section strong { font-size: 16px; font-weight: bold; margin-bottom: 10px; display: block; }
                    .contract-blank { border-bottom: 1px solid #000; padding: 0 5px; }
                    .contract-inline-input { border-bottom: 1px solid #000; padding: 0 5px; }
                    .signature-pad { border: 1px solid #000; width: 300px; height: 100px; }
                    @media print {
                        .btn { display: none !important; }
                        body { margin: 0; }
                    }
                </style>
            </head>
            <body>
                ${contractContent}
                <script>window.print();</script>
            </body>
            </html>
        `);
        printWindow.document.close();
    });
    
    // Global function for clearing signatures
    window.clearSignature = function(type) {
        if (type === 'contractor' && window.contractorSignaturePad) {
            window.contractorSignaturePad.clear();
            document.getElementById('contractor_signature').value = '';
        } else if (type === 'client' && window.clientSignaturePad) {
            window.clientSignaturePad.clear();
            document.getElementById('client_signature').value = '';
        }
    };
    
    // Save signatures when form is submitted
    document.getElementById('contractEditorForm').addEventListener('submit', function(e) {
        // Check if quotation request is selected
        const quotationRequest = document.getElementById('quotation_request_id');
        if (!quotationRequest.value) {
            alert('Please select a quotation request before submitting the contract.');
            e.preventDefault();
            return;
        }
        
        // Save signatures
        if (window.contractorSignaturePad && !window.contractorSignaturePad.isEmpty()) {
            document.getElementById('contractor_signature').value = window.contractorSignaturePad.toDataURL();
        }
        if (window.clientSignaturePad && !window.clientSignaturePad.isEmpty()) {
            document.getElementById('client_signature').value = window.clientSignaturePad.toDataURL();
        }
    });
}); 