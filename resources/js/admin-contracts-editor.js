let contractorPad, clientPad;
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
            const nameSignedSpan = document.getElementById('contractor_name_signed_display');
            if (nameSignedSpan) nameSignedSpan.innerText = fullName;
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

    // Signature pad logic
    contractorPad = new SignaturePad(document.getElementById('contractor-signature-pad'));
    clientPad = new SignaturePad(document.getElementById('client-signature-pad'));
    document.getElementById('contractEditorForm').addEventListener('submit', function(e) {
        document.getElementById('contractor_signature').value = contractorPad.isEmpty() ? '' : contractorPad.toDataURL();
        document.getElementById('client_signature').value = clientPad.isEmpty() ? '' : clientPad.toDataURL();
    });
    document.getElementById('download-pdf').addEventListener('click', function() {
        window.print();
    });

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
        if (sameAsClientCheckbox.checked) {
            if (clientAddressDisplay && propertyAddressInput) {
                propertyAddressInput.value = clientAddressDisplay.innerText.trim();
                propertyAddressInput.readOnly = true;
            }
        } else {
            if (propertyAddressInput) {
                propertyAddressInput.value = '';
                propertyAddressInput.readOnly = false;
            }
        }
    }
    if (sameAsClientCheckbox) {
        sameAsClientCheckbox.addEventListener('change', autofillPropertyAddress);
        autofillPropertyAddress();
    }

    // Timeline autofill from quotation request
    let estimatedDays = 0;
    function updateEndDate() {
        if (!startDateInput.value || !estimatedDays) return;
        const startDate = new Date(startDateInput.value);
        const endDate = new Date(startDate);
        endDate.setDate(startDate.getDate() + Math.ceil(estimatedDays));
        endDateInput.value = endDate.toISOString().slice(0, 10);
    }
    if (window.jQuery && $('#quotation_request_id').data('select2')) {
        const apiBaseUrl = "/api/quotation-requests";
        $('#quotation_request_id').on('select2:select', function(e) {
            const qrId = e.params.data.id;
            if (!qrId) return;
            fetch(`${apiBaseUrl}/${qrId}`)
                .then(res => res.json())
                .then(data => {
                    estimatedDays = data.total_days || 0;
                    updateEndDate();
                });
        });
    } else if (qrSelect) {
        qrSelect.addEventListener('change', function() {
            const qrId = this.value;
            if (!qrId) return;
            fetch(`/api/quotation-requests/${qrId}`)
                .then(res => res.json())
                .then(data => {
                    estimatedDays = data.total_days || 0;
                    updateEndDate();
                });
        });
    }
    if (startDateInput) {
        startDateInput.addEventListener('change', function() {
            updateEndDate();
        });
    }

    // Submit for Approval button logic
    const submitApprovalBtn = document.getElementById('submit-approval-btn');
    if (submitApprovalBtn) {
        submitApprovalBtn.addEventListener('click', function() {
            const form = document.getElementById('contractEditorForm');
            const approvalInput = document.createElement('input');
            approvalInput.type = 'hidden';
            approvalInput.name = 'submit_for_approval';
            approvalInput.value = '1';
            form.appendChild(approvalInput);
            form.submit();
        });
    }

    // Signature pad role-based access and date autofill
    const userType = window.contractEditorUserType || 'manager';
    function setTodayDateString() {
        const d = new Date();
        return d.toLocaleDateString('en-CA');
    }
    function checkSignaturesAndToggleApprovalBtn() {
        const contractorSigned = !contractorPad.isEmpty();
        const clientSigned = !clientPad.isEmpty();
        const btn = document.getElementById('submit-approval-btn');
        if (userType === 'manager' && contractorSigned && clientSigned) {
            btn.style.display = '';
            btn.disabled = false;
        } else {
            btn.style.display = 'none';
        }
    }
    contractorPad.onEnd = function() {
        if (!contractorPad.isEmpty()) {
            const today = setTodayDateString();
            document.getElementById('contractor_date_signed_display').innerText = today;
            document.getElementById('contractor_date_signed').value = today;
        } else {
            document.getElementById('contractor_date_signed_display').innerText = '';
            document.getElementById('contractor_date_signed').value = '';
        }
        checkSignaturesAndToggleApprovalBtn();
    };
    clientPad.onEnd = function() {
        if (!clientPad.isEmpty()) {
            const today = setTodayDateString();
            document.getElementById('client_date_signed_display').innerText = today;
            document.getElementById('client_date_signed').value = today;
        } else {
            document.getElementById('client_date_signed_display').innerText = '';
            document.getElementById('client_date_signed').value = '';
        }
        checkSignaturesAndToggleApprovalBtn();
    };
    if (userType === 'manager') {
        clientPad.off();
        document.getElementById('client-signature-pad').style.pointerEvents = 'none';
        document.getElementById('client-signature-pad').style.opacity = 0.5;
    } else if (userType === 'client') {
        contractorPad.off();
        document.getElementById('contractor-signature-pad').style.pointerEvents = 'none';
        document.getElementById('contractor-signature-pad').style.opacity = 0.5;
    }
});
function clearSignature(type) {
    if(type === 'contractor') contractorPad.clear();
    else clientPad.clear();
} 