document.addEventListener('DOMContentLoaded', function() {
    // --- Progress Bar ---
    const overallProgress = window.overallProjectProgress || 0;
    const progressBar = document.getElementById('projectProgressBar').querySelector('.progress-bar');
    progressBar.style.width = overallProgress + '%';
    progressBar.setAttribute('aria-valuenow', overallProgress);
    progressBar.innerHTML = `<span class='fw-bold'>${overallProgress}% Complete</span>`;

    // --- Contract Progress Cards (Removed JS rendering, now handled by Blade) ---
    // Original renderContractProgressDetails function and its call are removed.

    // --- Calendar ---
    const calendarEl = document.getElementById('calendar');
    const calendar = new FullCalendar.Calendar(calendarEl, {
        plugins: [ FullCalendar.dayGridPlugin ],
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,dayGridWeek,dayGridDay'
        },
        events: window.calendarEvents || [],
        eventContent: function(arg) {
            const props = arg.event.extendedProps || {};
            const contractNumber = props.contract_number;
            const contractorName = props.contractor || 'N/A';
            
            return {
                html: `
                    <div class="fc-event-main-frame">
                        <div class="fc-event-title-container">
                            <div class="fc-event-title">${arg.event.title}</div>
                            <div class="fc-event-contractor">${contractNumber}</div>
                        </div>
                    </div>
                `
            };
        },
        eventDidMount: function(info) {
            const event = info.event;
            const props = event.extendedProps || {};
            
            // Add status class
            let statusClass = '';
            if (props.status) {
                statusClass = `status-${String(props.status).toLowerCase().replace(/\s+/g, '_')}`;
            }
            if (statusClass) {
                info.el.classList.add(statusClass);
            }
            
            // Add contractor class
            if (props.contractor_id) {
                info.el.classList.add(`contractor-${props.contractor_id}`);
            }
            
            // Enhanced tooltip
            let tooltipContent = `
                <div class='p-2'>
                    <strong>${event.title}</strong><br/>
                    <strong>Contract Details:</strong><br/>
                    Contract Number: ${props.contract_number}<br/>
                    Contractor: ${props.contractor || 'N/A'}<br/>
                    <strong>Project Details:</strong><br/>
                    Room: ${props.room || 'N/A'}<br/>
                    Scope: ${props.scope || 'N/A'}<br/>
                    Status: ${props.status || 'N/A'}<br/>
                    Progress: ${props.progress || 0}%<br/>
                </div>
            `;
            info.el.title = tooltipContent.replace(/<br\/>/g, '\n');
        },
        eventClick: function(info) {
            // Show detailed modal with all information
            const props = info.event.extendedProps || {};
            const modalContent = `
                <div class="modal fade" id="eventModal" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">${info.event.title}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <h6>Contract Information</h6>
                                <p>Contract Number: ${props.contract_number}<br/>
                                Contractor: ${props.contractor || 'N/A'}</p>
                                
                                <h6>Project Details</h6>
                                <p>Room: ${props.room || 'N/A'}<br/>
                                Scope: ${props.scope || 'N/A'}<br/>
                                Status: ${props.status || 'N/A'}<br/>
                                Progress: ${props.progress || 0}%</p>
                                
                                <h6>Timeline</h6>
                                <p>Start: ${info.event.start.toLocaleDateString()}<br/>
                                End: ${info.event.end.toLocaleDateString()}</p>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            // Remove existing modal if any
            const existingModal = document.getElementById('eventModal');
            if (existingModal) {
                existingModal.remove();
            }
            
            // Add new modal to body
            document.body.insertAdjacentHTML('beforeend', modalContent);
            
            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('eventModal'));
            modal.show();
        }
    });
    calendar.render();
});

function submitWarrantyRequest(contractId) {
    const form = document.getElementById(`warrantyForm${contractId}`);
    if (!form.checkValidity()) {
        form.classList.add('was-validated');
        return;
    }

    const formData = new FormData(form);
    formData.append('contract_id', contractId);

    // Show loading state
    const submitBtn = form.closest('.modal').querySelector('.btn-primary');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Submitting...';

    // Submit the form data
    fetch('/api/warranty-requests', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Your warranty request has been submitted successfully.',
                confirmButtonText: 'OK'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Close the modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById(`warrantyModal${contractId}`));
                    modal.hide();
                    // Reset the form
                    form.reset();
                    form.classList.remove('was-validated');
                }
            });
        } else {
            throw new Error(data.message || 'Something went wrong');
        }
    })
    .catch(error => {
        // Show error message
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: error.message || 'Failed to submit warranty request. Please try again.',
            confirmButtonText: 'OK'
        });
    })
    .finally(() => {
        // Reset button state
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
}

function submitAdditionalWorkRequest() {
    const form = document.getElementById('additionalWorkForm');
    if (!form.checkValidity()) {
        form.classList.add('was-validated');
        return;
    }

    const formData = new FormData(form);

    // Show loading state
    const submitBtn = document.getElementById('additionalWorkModal').querySelector('.btn-primary');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Submitting...';

    // Submit the form data
    fetch('/api/additional-work-requests', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Your additional work request has been submitted successfully.',
                confirmButtonText: 'OK'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Close the modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('additionalWorkModal'));
                    modal.hide();
                    // Reset the form
                    form.reset();
                    form.classList.remove('was-validated');
                }
            });
        } else {
            throw new Error(data.message || 'Something went wrong');
        }
    })
    .catch(error => {
        // Show error message
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: error.message || 'Failed to submit additional work request. Please try again.',
            confirmButtonText: 'OK'
        });
    })
    .finally(() => {
        // Reset button state
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
} 