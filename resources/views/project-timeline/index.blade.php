@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h1 class="h3 mb-4">Project Timeline</h1>
                    <!-- Overall Project Progress Bar -->
                    <div id="projectProgressBar" class="progress mb-4" style="height: 40px;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                            <span class="fw-bold">0% Complete</span>
                        </div>
                    </div>
                    <!-- Contract Progress Cards -->
                    <div id="contractProgressDetails" class="row g-4">
                        @foreach($contracts as $contract)
                            <div class="col-12 col-md-12 col-lg-12 mb-4">
                                <div class="card shadow-sm h-100">
                                    <div class="card-body">
                                        <div class="row">
                                            <!-- Left Column: Contract Details -->
                                            <div class="col-md-6 border-end pe-4">
                                                <h5 class="card-title mb-2">{{ $contract->client->name ?? $contract->client ?? 'N/A' }} <small class="text-muted">(Client)</small></h5>
                                                <div class="mb-2"><strong>Contractor:</strong> {{ $contract->contractor->name ?? $contract->contractor ?? 'N/A' }}</div>
                                                <div class="mb-2"><strong>Contract Number:</strong> {{ $contract->contract_number ?? 'N/A' }}</div>
                                                <div class="mb-2">
                                                    <strong>Status:</strong>
                                                    <span class="badge bg-{{ $contract->status === 'APPROVED' ? 'success' : ($contract->status === 'DRAFT' ? 'secondary' : 'info') }}">
                                                        {{ strtoupper($contract->status ?? 'N/A') }}
                                                    </span>
                                                </div>
                                                <div class="mb-2">
                                                    <strong>Start:</strong> {{ $contract->start_date ? \Carbon\Carbon::parse($contract->start_date)->format('M d, Y') : 'N/A' }}
                                                </div>
                                                <div class="mb-2">
                                                    <strong>End:</strong> {{ $contract->end_date ? \Carbon\Carbon::parse($contract->end_date)->format('M d, Y') : 'N/A' }}
                </div>
                                                <div class="mb-2"><strong>Budget:</strong> ₱{{ number_format($contract->total_amount ?? 0, 2) }}</div>
                                                
                                                @php
                                                    $pendingPO = $contract->purchaseOrders->where('status', 'pending')->count();
                                                    $partiallyDeliveredPO = $contract->purchaseOrders->where('status', 'partially_delivered')->count();
                                                    $completedPO = $contract->purchaseOrders->where('status', 'completed')->count();
                                                    $totalPO = $contract->purchaseOrders->count();
                                                @endphp

                                                <div class="mb-2 mt-3">
                                                    <strong>Delivery Status:</strong>
                                                    @if($totalPO > 0)
                                                        <ul class="list-unstyled mb-0 ms-2">
                                                            <li><i class="bi bi-box-seam-fill text-primary"></i> Total POs: {{ $totalPO }}</li>
                                                            @if($pendingPO > 0)
                                                                <li><i class="bi bi-hourglass text-warning"></i> Pending: {{ $pendingPO }}</li>
                                                            @endif
                                                            @if($partiallyDeliveredPO > 0)
                                                                <li><i class="bi bi-arrow-right-circle text-info"></i> Partially Delivered: {{ $partiallyDeliveredPO }}</li>
                                                            @endif
                                                            @if($completedPO > 0)
                                                                <li><i class="bi bi-truck text-success"></i> Delivered: {{ $completedPO }}</li>
                                                            @endif
                                                        </ul>
                                                    @else
                                                        <span class="text-muted">No Purchase Orders.</span>
                                                    @endif
            </div>

                                                <div class="mt-3">
                                                    <a href="{{ route('contracts.show', $contract->id) }}" class="btn btn-primary btn-sm">View Full Contract</a>
                                                    @if($contract->status === 'COMPLETED')
                                                        <button type="button" class="btn btn-warning btn-sm ms-2" data-bs-toggle="modal" data-bs-target="#warrantyModal{{ $contract->id }}">
                                                            <i class="bi bi-shield-check"></i> Request Warranty
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>

                                            <!-- Right Column: Scope of Work & Tasks -->
                                            <div class="col-md-6 ps-4">
                                                <h5 class="card-title mb-3">Scope of Work & Tasks</h5>
                                                @if($contract->rooms->count() > 0)
                                                    @foreach($contract->rooms as $room)
            <div class="mb-3">
                                                            <h6>Room: {{ $room->name ?? 'N/A' }}</h6>
                                                            @if($room->scopeTypes->count() > 0)
                                                                <div class="accordion accordion-flush" id="roomAccordion{{ $room->id }}">
                                                                    @foreach($room->scopeTypes as $scopeType)
                                                                        <div class="accordion-item">
                                                                            <h2 class="accordion-header" id="headingScope{{ $room->id }}{{ $loop->parent->index }}{{ $loop->index }}">
                                                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseScope{{ $room->id }}{{ $loop->parent->index }}{{ $loop->index }}" aria-expanded="false" aria-controls="collapseScope{{ $room->id }}{{ $loop->parent->index }}{{ $loop->index }}">
                                                                                    <strong>{{ $scopeType->name ?? 'N/A' }}</strong> ({{ $scopeType->estimated_days ?? 0 }} days)
                </button>
                                                                            </h2>
                                                                            <div id="collapseScope{{ $room->id }}{{ $loop->parent->index }}{{ $loop->index }}" class="accordion-collapse collapse {{ $loop->index == 0 ? 'show' : '' }}" aria-labelledby="headingScope{{ $room->id }}{{ $loop->parent->index }}{{ $loop->index }}" data-bs-parent="#roomAccordion{{ $room->id }}">
                                                                                <div class="accordion-body">
                                                                                    @if(isset($scopeType->items) && count($scopeType->items) > 0)
                                                                                        <p class="fw-bold mb-1">Tasks:</p>
                                                                                        <ul class="list-unstyled mb-2">
                                                                                            @foreach($scopeType->items as $taskItem)
                                                                                                <li>
                                                                                                    <i class="bi bi-check-circle-fill text-success"></i> {{ $taskItem }}
                                                                                                </li>
                                                                                            @endforeach
                                                                                        </ul>
                                                                                    @else
                                                                                        <p class="text-muted mb-2">No defined tasks for this scope.</p>
                                                                                    @endif

                                                                                    @if($scopeType->materials->count() > 0)
                                                                                        <p class="fw-bold mb-1">Materials:</p>
                                                                                        <ul class="list-unstyled">
                                                                                            @foreach($scopeType->materials as $material)
                                                                                                <li>- {{ $material->name ?? 'N/A' }}</li>
                                                                                            @endforeach
                                                                                        </ul>
                                                                                    @else
                                                                                        <p class="text-muted">No materials defined.</p>
                                                                                    @endif
            </div>
                                </div>
                            </div>
                                                                    @endforeach
                                                                </div>
                                                            @else
                                                                <p class="text-muted">No scope types defined for this room.</p>
                                                            @endif
                                    </div>
                                                    @endforeach
                                                @else
                                                    <p class="text-muted">No rooms or scopes defined for this contract.</p>
                                                @endif
                                    </div>
                                    </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <hr/>
                    <!-- Search and Filters Section -->
                    <div class="mb-3 d-flex align-items-center gap-3">
                        <div class="flex-grow-1">
                            <input type="text" class="form-control" id="searchInput" name="searchInput" placeholder="Search contracts or tasks...">
                        </div>
                        <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#additionalWorkModal">
                            <i class="bi bi-tools"></i> Request Additional Work
                        </button>
                        <a href="{{ route('warranty-requests.index') }}" class="btn btn-warning">
                            <i class="bi bi-shield-check"></i> Warranty Requests
                        </a>
                        <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#filtersCollapse" aria-expanded="false" aria-controls="filtersCollapse">
                            <i class="bi bi-funnel"></i> Filters
                        </button>
                    </div>
                    <div class="collapse mt-2" id="filtersCollapse">
                        <form id="filtersForm" class="row g-3">
                            <div class="col-md-4">
                                <label for="statusFilter" class="form-label">Status</label>
                                <select class="form-select" id="statusFilter" name="statusFilter">
                                    <option value="">All</option>
                                    <option value="approved">Approved</option>
                                    <option value="draft">Draft</option>
                                    <option value="rejected">Rejected</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="startDate" class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="startDate" name="startDate">
                            </div>
                            <div class="col-md-4">
                                <label for="endDate" class="form-label">End Date</label>
                                <input type="date" class="form-control" id="endDate" name="endDate">
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-success">Apply Filters</button>
                                <button type="reset" class="btn btn-secondary">Reset</button>
                            </div>
                        </form>
                    </div>
                    <!-- Calendar Section -->
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Warranty Request Modals -->
@foreach($contracts as $contract)
    @if($contract->status === 'COMPLETED')
        <div class="modal fade" id="warrantyModal{{ $contract->id }}" tabindex="-1" aria-labelledby="warrantyModalLabel{{ $contract->id }}" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="warrantyModalLabel{{ $contract->id }}">Warranty Request - Contract #{{ $contract->contract_number }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="warrantyForm{{ $contract->id }}" class="needs-validation" novalidate>
                            <div class="mb-3">
                                <label for="productName{{ $contract->id }}" class="form-label">Product Name</label>
                                <input type="text" class="form-control" id="productName{{ $contract->id }}" name="productName" required>
                                <div class="invalid-feedback">Please provide the product name.</div>
                            </div>
                            <div class="mb-3">
                                <label for="serialNumber{{ $contract->id }}" class="form-label">Serial Number</label>
                                <input type="text" class="form-control" id="serialNumber{{ $contract->id }}" name="serialNumber" required>
                                <div class="invalid-feedback">Please provide the serial number.</div>
                            </div>
                            <div class="mb-3">
                                <label for="issueDescription{{ $contract->id }}" class="form-label">Issue Description</label>
                                <textarea class="form-control" id="issueDescription{{ $contract->id }}" name="issueDescription" rows="3" required></textarea>
                                <div class="invalid-feedback">Please describe the issue.</div>
                            </div>
                            <div class="mb-3">
                                <label for="purchaseProof{{ $contract->id }}" class="form-label">Proof of Purchase</label>
                                <input type="file" class="form-control" id="purchaseProof{{ $contract->id }}" name="purchaseProof" accept=".pdf,.jpg,.jpeg,.png" required>
                                <div class="form-text">Upload receipt, invoice, or any proof of purchase (PDF, JPG, PNG)</div>
                                <div class="invalid-feedback">Please provide proof of purchase.</div>
                            </div>
                            <div class="mb-3">
                                <label for="issuePhotos{{ $contract->id }}" class="form-label">Photos of the Issue</label>
                                <input type="file" class="form-control" id="issuePhotos{{ $contract->id }}" name="issuePhotos" accept=".jpg,.jpeg,.png" multiple>
                                <div class="form-text">Upload photos showing the issue (optional)</div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" onclick="submitWarrantyRequest({{ $contract->id }})">Submit Request</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endforeach

<!-- Additional Work Request Modal -->
<div class="modal fade" id="additionalWorkModal" tabindex="-1" aria-labelledby="additionalWorkModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="additionalWorkModalLabel">Request Additional Work</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Info box for clients about additional work rules -->
                <div class="alert alert-info alert-static" style="font-size: 1rem;">
                    <strong>Please Note:</strong>
                    <ol class="mb-0">
                        <li>If you request additional work <b>within the original contract time period</b>, you will only be charged for the <b>material cost</b>.</li>
                        <li>If the additional work <b>extends the original contract time period</b>, you will be charged for both <b>labor cost and material cost</b>.</li>
                        <li>If you request additional work <b>after the contract period has ended</b>, a <b>new contract</b> must be created, as work can only be performed within the allotted contract period.</li>
                    </ol>
                </div>
                <form id="additionalWorkForm" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label for="contractId" class="form-label">Contract</label>
                        <select class="form-select" id="contractId" name="contract_id" required>
                            <option value="">Select Contract</option>
                            @foreach($contracts as $contract)
                                <option value="{{ $contract->id }}">{{ $contract->contract_number }} - {{ $contract->client->name ?? $contract->client ?? 'N/A' }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback">Please select a contract.</div>
                    </div>
                    <div class="mb-3">
                        <label for="workType" class="form-label">Work Type</label>
                        <select class="form-select" id="workType" name="work_type" required>
                            <option value="">Select Work Type</option>
                            <option value="installation">Installation</option>
                            <option value="maintenance">Maintenance</option>
                            <option value="repair">Repair</option>
                            <option value="upgrade">Upgrade</option>
                            <option value="other">Other</option>
                        </select>
                        <div class="invalid-feedback">Please select a work type.</div>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Work Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                        <div class="invalid-feedback">Please provide a description.</div>
                    </div>
                    <div class="mb-3">
                        <label for="materials" class="form-label">Materials (comma-separated)</label>
                        <input type="text" class="form-control" id="materials" name="materials" placeholder="e.g. Cement, Paint, Tiles" required>
                        <div class="invalid-feedback">Please list required materials.</div>
                    </div>
                    <div class="mb-3">
                        <label for="estimatedHours" class="form-label">Estimated Hours</label>
                        <input type="number" class="form-control" id="estimatedHours" name="estimated_hours" min="0.5" step="0.5" required>
                        <div class="invalid-feedback">Please provide estimated hours.</div>
                    </div>
                    <div class="mb-3">
                        <label for="requiredSkills" class="form-label">Required Skills</label>
                        <input type="text" class="form-control" id="requiredSkills" name="required_skills">
                    </div>
                    <div class="mb-3">
                        <label for="laborNotes" class="form-label">Labor Notes</label>
                        <textarea class="form-control" id="laborNotes" name="labor_notes" rows="2"></textarea>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-6">
                            <label for="preferredStartDate" class="form-label">Preferred Start Date</label>
                            <input type="date" class="form-control" id="preferredStartDate" name="preferred_start_date" required>
                            <div class="invalid-feedback">Please select a start date.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="preferredEndDate" class="form-label">Preferred End Date</label>
                            <input type="date" class="form-control" id="preferredEndDate" name="preferred_end_date" required>
                            <div class="invalid-feedback">Please select an end date.</div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="timelineNotes" class="form-label">Timeline Notes</label>
                        <textarea class="form-control" id="timelineNotes" name="timeline_notes" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="additionalNotes" class="form-label">Additional Notes</label>
                        <textarea class="form-control" id="additionalNotes" name="additional_notes" rows="2"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="submitAdditionalWorkRequest()">Submit Request</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet" crossorigin="anonymous" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" crossorigin="anonymous" />
<style>
.progress-bar {
    font-size: 1.2rem;
}
.card {
    border-radius: 1rem;
}
.card-body {
    background: #fff;
}
.accordion-button:focus {
    box-shadow: none;
}
.contract-card:hover {
    box-shadow: 0 0 0 4px #0d6efd33, 0 2px 8px rgba(0,0,0,0.08);
    border-color: #0d6efd;
}
.accordion-button:not(.collapsed) {
    background-color: #e9ecef;
    color: #495057;
}
.accordion-body {
    background-color: #f8f9fa;
    border-radius: 0.25rem;
    padding: 1rem;
}
.contract-card .card-body .row .col-md-6:first-child {
    border-right: 1px solid #dee2e6;
}
.contract-card .card-body .row .col-md-6:last-child {
    padding-left: 1.5rem;
}
/* Color coding for FullCalendar events by status */
.status-approved {
    background-color: #198754 !important;
    color: #fff !important;
    border: none !important;
}
.status-draft {
    background-color: #6c757d !important;
    color: #fff !important;
    border: none !important;
}
.status-rejected {
    background-color: #dc3545 !important;
    color: #fff !important;
    border: none !important;
}
.status-pending {
    background-color: #ffc107 !important;
    color: #212529 !important;
    border: none !important;
}
.status-in_progress {
    background-color: #0dcaf0 !important;
    color: #212529 !important;
    border: none !important;
}
.status-completed {
    background-color: #198754 !important;
    color: #fff !important;
    border: none !important;
}
.status-delayed {
    background-color: #fd7e14 !important;
    color: #fff !important;
    border: none !important;
}

/* Contractor-specific styling */
.contractor-1 { border-left: 4px solid #198754 !important; }
.contractor-2 { border-left: 4px solid #0dcaf0 !important; }
.contractor-3 { border-left: 4px solid #6f42c1 !important; }
.contractor-4 { border-left: 4px solid #fd7e14 !important; }
.contractor-5 { border-left: 4px solid #20c997 !important; }

.fc-event {
    padding: 2px 4px !important;
    margin: 1px 0 !important;
    border-radius: 4px !important;
}

.fc-event-title {
    font-weight: 500 !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}

.fc-event-contractor {
    font-size: 0.8em !important;
    opacity: 0.9 !important;
    display: block !important;
    margin-top: 2px !important;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js" crossorigin="anonymous"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- Progress Bar ---
    const overallProgress = {{ $overallProjectProgress ?? 0 }};
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
        events: @json($calendarEvents ?? []),
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
</script>
@endpush 