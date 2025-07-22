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
            url: window.quotationRequestApiUrl, // use Blade-provided URL for environment-agnostic requests
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
    let lastTotalDays = 0; // Store the duration for recalculation

    // Always attach the event handler on page load
    const startDateInput = document.getElementById('project_start_date');
    if (startDateInput) {
        startDateInput.onchange = function() {
            // If lastTotalDays is 0, show a warning and do not update end date
            if (!lastTotalDays) {
                alert('Please select a Quotation Request first to determine the project duration.');
                return;
            }
            // Parse as local date to avoid timezone issues
            const [yyyy, mm, dd] = this.value.split('-');
            const start = new Date(Number(yyyy), Number(mm) - 1, Number(dd));
            if (isNaN(start)) return;
            const end = new Date(start);
            end.setDate(start.getDate() + lastTotalDays - 1);
            const yyyyEnd = end.getFullYear();
            const mmEnd = String(end.getMonth() + 1).padStart(2, '0');
            const ddEnd = String(end.getDate()).padStart(2, '0');
            document.getElementById('project_end_date').value = `${yyyyEnd}-${mmEnd}-${ddEnd}`;
            console.log('Start:', start, 'Start.getDate():', start.getDate(), 'Duration:', lastTotalDays, 'End (raw):', end, 'End:', `${yyyyEnd}-${mmEnd}-${ddEnd}`);
            // Sync signature date with contract start date
            const signatureDateInput = document.getElementById('contractor_signature_date');
            if (signatureDateInput) {
                signatureDateInput.value = this.value;
            }
        };
        // Also sync on page load
        const signatureDateInput = document.getElementById('contractor_signature_date');
        if (signatureDateInput) {
            signatureDateInput.value = startDateInput.value;
        }
    }

    $('#quotation_request_id').on('select2:select', function(e) {
        const qrId = e.params.data.id;
        if (!qrId) return;
        fetch(window.quotationRequestApiUrl.replace(/\/$/, '') + '/' + qrId)
            .then(res => res.json())
            .then(data => {
                const client = data.client || {};
                // Debug log for duration
                console.log('QR Start:', data.start_date, 'End:', data.end_date, 'Total Days:', data.total_days);
                // Use only the backend's total_days for duration
                lastTotalDays = data.total_days || 0;
                // Display fields
                const setText = (id, val) => { const el = document.getElementById(id); if (el) el.innerText = val || ''; };
                const setValue = (id, val) => { const el = document.getElementById(id); if (el) el.value = val || ''; };
                setText('client_name_display', client.name);
                setText('client_address_display', client.address);
                setText('scope_of_work_display', data.scope_of_work);
                // Update material cost and labor fee as input values
                setValue('materials_total_display', '₱' + (data.total_materials_cost?.toFixed(2) || '0.00'));
                setValue('labor_fee_display', '₱' + (data.labor_fee?.toFixed(2) || '0.00'));
                setText('grand_total_display', '₱' + (data.grand_total?.toFixed(2) || '0.00'));
                // Input fields
                setValue('property_address', client.address);
                setValue('client_email', client.email);
                setValue('client_phone', client.phone);
                setValue('project_start_date', data.start_date);
                setValue('project_end_date', data.end_date);
                // Sync signature date after setting start date
                const signatureDateInput = document.getElementById('contractor_signature_date');
                const contractStartDateInput = document.getElementById('project_start_date');
                console.log('Signature date input(s):', document.querySelectorAll('#contractor_signature_date'));
                if (signatureDateInput && contractStartDateInput) {
                    // Ensure value is set in YYYY-MM-DD format
                    signatureDateInput.value = contractStartDateInput.value;
                    console.log('Setting signature date to:', contractStartDateInput.value);
                }
                // Display selected scopes
                const scopesDiv = document.getElementById('selected-scopes');
                const scopeSummaryBody = document.getElementById('scope-summary-table-body');
                let summaryRows = [];
                if (data.rooms && Array.isArray(data.rooms)) {
                    let scopes = [];
                    data.rooms.forEach(room => {
                        if (room.scopes && Array.isArray(room.scopes)) {
                            room.scopes.forEach(scope => {
                                if (scope.scope_name) scopes.push(scope.scope_name);
                                // For summary table
                                let supplier = 'none selected';
                                if (scope.selected_supplier && scope.selected_supplier.company_name) {
                                    supplier = scope.selected_supplier.company_name;
                                } else if (scope.supplier_name) {
                                    supplier = scope.supplier_name;
                                }
                                summaryRows.push(`<tr><td>${room.name}</td><td>${scope.scope_name || ''}</td><td>${supplier}</td></tr>`);
                            });
                        }
                    });
                    scopes = [...new Set(scopes)]; // unique
                    if (scopes.length > 0) {
                        scopesDiv.innerHTML = `<ul>${scopes.map(s => `<li>${s}</li>`).join('')}</ul>`;
                    } else {
                        scopesDiv.innerHTML = '<span class="text-muted">No scope selected yet.</span>';
                    }
                }
                // Update scope summary table
                if (scopeSummaryBody) {
                    if (summaryRows.length > 0) {
                        scopeSummaryBody.innerHTML = summaryRows.join('');
                    } else {
                        scopeSummaryBody.innerHTML = '<tr><td colspan="3" class="text-center text-muted">No data yet.</td></tr>';
                    }
                }
                // Add/change event for start date
                // The startDateInput.onchange handler is already attached above.
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
            // Sync signature date to contract start date
            const contractStartDateInput = document.getElementById('project_start_date');
            const contractorSignatureDateInput = document.getElementById('contractor_signature_date');
            if (contractorSignatureDateInput && contractStartDateInput) {
                contractorSignatureDateInput.value = contractStartDateInput.value;
                console.log('Setting signature date after contractor selection to:', contractStartDateInput.value);
            }
            // Sync signature date to contract start date (for span)
            const contractorSignatureDateSpan = document.getElementById('contractor_date_signed_display');
            if (contractorSignatureDateSpan && contractStartDateInput) {
                contractorSignatureDateSpan.innerText = contractStartDateInput.value;
                console.log('Setting signature date after contractor selection to:', contractStartDateInput.value);
            }
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
            // Copy all client address fields to property fields
            const fields = ['street', 'barangay', 'city', 'state', 'postal'];
            fields.forEach(function(field) {
                const clientField = document.getElementById('client_' + field);
                const propertyField = document.getElementById('property_' + field);
                if (clientField && propertyField) {
                    propertyField.value = clientField.value;
                }
            });
        } else {
            if (propertyAddressInput) {
                propertyAddressInput.value = '';
                propertyAddressInput.readOnly = false;
                propertyAddressInput.style.backgroundColor = '#f8f9fa';
                propertyAddressInput.style.color = '#333';
            }
            // Optionally clear property fields if unchecked
            const fields = ['street', 'barangay', 'city', 'state', 'postal'];
            fields.forEach(function(field) {
                const propertyField = document.getElementById('property_' + field);
                if (propertyField) {
                    propertyField.value = '';
                }
            });
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