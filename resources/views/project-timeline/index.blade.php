@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <h1 class="h3 mb-0">Project Timeline</h1>
                        <div class="btn-group">
                            <button class="btn btn-outline-primary" id="viewToggle" data-view="calendar">
                                <i class="bi bi-calendar3"></i> Calendar View
                            </button>
                            <button class="btn btn-outline-primary" id="viewToggle" data-view="gantt">
                                <i class="bi bi-bar-chart"></i> Gantt View
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Filters Sidebar -->
        <div class="col-md-3">
            <div class="card sticky-top" style="top: 20px;">
                <div class="card-body">
                    <h5 class="card-title mb-3">Filters</h5>
                    
                    <!-- Search -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Search Contracts</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" 
                                   class="form-control" 
                                   id="searchInput" 
                                   name="term"
                                   placeholder="Search by contract ID, client, or contractor...">
                            <button class="btn btn-primary" type="button" id="searchButton">
                                Search
                            </button>
                        </div>
                    </div>

                    <!-- Status Filter -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Status</label>
                        <div class="d-flex flex-wrap gap-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="all" id="statusAll" checked name="statusFilter">
                                <label class="form-check-label" for="statusAll">All</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="draft" id="statusDraft" name="statusFilter">
                                <label class="form-check-label" for="statusDraft">
                                    <span class="badge bg-warning">Draft</span>
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="approved" id="statusApproved" name="statusFilter">
                                <label class="form-check-label" for="statusApproved">
                                    <span class="badge bg-success">Approved</span>
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="rejected" id="statusRejected" name="statusFilter">
                                <label class="form-check-label" for="statusRejected">
                                    <span class="badge bg-danger">Rejected</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Date Range -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Date Range</label>
                        <div class="input-group mb-2">
                            <span class="input-group-text bg-light">
                                <i class="bi bi-calendar-event"></i>
                            </span>
                            <input type="date" class="form-control" id="startDate">
                        </div>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="bi bi-calendar-event"></i>
                            </span>
                            <input type="date" class="form-control" id="endDate">
                        </div>
                    </div>

                    <!-- Budget Range -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Budget Range</label>
                        <div class="input-group mb-2">
                            <span class="input-group-text bg-light">₱</span>
                            <input type="number" class="form-control" id="minBudget" placeholder="Min">
                        </div>
                        <div class="input-group">
                            <span class="input-group-text bg-light">₱</span>
                            <input type="number" class="form-control" id="maxBudget" placeholder="Max">
                        </div>
                    </div>

                    <!-- Clear Filters -->
                    <button class="btn btn-outline-secondary w-100" id="clearFilters">
                        <i class="bi bi-x-circle"></i> Clear Filters
                    </button>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9">
            <!-- Calendar View -->
            <div id="calendarView" class="card">
                <div class="card-body">
    <div id="calendar"></div>
                </div>
            </div>

            <!-- Gantt View (Initially Hidden) -->
            <div id="ganttView" class="card" style="display: none;">
                <div class="card-body">
                    <div id="ganttChart"></div>
                </div>
</div>

            <!-- Contract Details Modal -->
            <div class="modal fade" id="contractModal" tabindex="-1">
                <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
                            <h5 class="modal-title">Contract Details</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
                            <div class="contract-details">
                                <!-- Details will be populated dynamically -->
                            </div>
                        </div>
                        <div class="modal-footer">
                            <a href="#" class="btn btn-primary" id="viewContractBtn">
                                <i class="bi bi-eye"></i> View Full Contract
                            </a>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          </div>
          </div>
          </div>
      </div>
    </div>
  </div>
</div>

@push('styles')
<link href='https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.10/main.min.css' rel='stylesheet' />
<link href='https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@6.1.10/main.min.css' rel='stylesheet' />
<link href='https://cdn.jsdelivr.net/npm/@fullcalendar/timeline@6.1.10/main.min.css' rel='stylesheet' />
<link href='https://cdn.jsdelivr.net/npm/@fullcalendar/resource-timeline@6.1.10/main.min.css' rel='stylesheet' />
<link href="https://cdn.jsdelivr.net/npm/frappe-gantt@0.6.1/dist/frappe-gantt.min.css" rel="stylesheet">
<style>
/* Calendar Customization */
.fc-event {
    cursor: pointer;
    transition: all 0.2s ease;
    border-radius: 4px; /* Slightly rounded corners */
    font-size: 0.85rem; /* Smaller font */
    padding: 2px 4px; /* Adjust padding */
    margin-bottom: 2px; /* Small space between events */
    border: none; /* Remove default border */
    font-weight: normal; /* Lighter font weight */
    text-align: left; /* Ensure text aligns left */
    white-space: nowrap; /* Prevent text wrapping */
    overflow: hidden; /* Hide overflowing text */
    text-overflow: ellipsis; /* Add ellipsis for hidden text */
}

.fc-event:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.fc-event-title {
    font-weight: 500;
    color: #333; /* Darker text for readability */
}

/* Custom Status Colors - adjust to be softer, more like Google Calendar */
.fc-event.status-draft {
    background-color: #fce8b2 !important; /* Softer yellow */
    border-left: 4px solid #f9ab00 !important; /* Google Calendar orange */
    color: #3c3c3c !important;
}

.fc-event.status-approved {
    background-color: #d1f7d1 !important; /* Softer green */
    border-left: 4px solid #34a853 !important; /* Google Calendar green */
    color: #3c3c3c !important;
}

.fc-event.status-rejected {
    background-color: #f4cccc !important; /* Softer red */
    border-left: 4px solid #ea4335 !important; /* Google Calendar red */
    color: #3c3c3c !important;
}

/* Ensure default FullCalendar styles are subtle */
.fc .fc-toolbar-title {
    font-size: 1.5em; /* Adjust title size */
}

.fc .fc-button {
    text-transform: none; /* Prevent uppercase button text */
    font-weight: normal;
}

.fc .fc-daygrid-event {
    margin-top: 1px;
    margin-bottom: 1px;
}

.fc-day-other .fc-daygrid-day-top {
    opacity: 0.5; /* Dim non-month dates */
}

/* General Calendar container styling */
#calendar {
    font-family: 'Roboto', sans-serif; /* Use a common, clean font */
}

/* Gantt Chart Customization */
.gantt-container {
    height: 600px;
    overflow-y: auto;
}

.gantt .bar {
    fill: #0d6efd;
    transition: fill 0.3s ease; /* Smooth transition for color changes */
}

.gantt .bar-progress {
    fill: #0a4fb9;
}

/* Gantt Status Colors (matching calendar) */
.gantt .bar.status-draft .bar-wrapper {
    fill: #f9ab00; /* Orange for draft */
}
.gantt .bar.status-approved .bar-wrapper {
    fill: #34a853; /* Green for approved */
}
.gantt .bar.status-rejected .bar-wrapper {
    fill: #ea4335; /* Red for rejected */
}

/* Modal Customization */
.contract-details {
    font-size: 0.95rem;
}

.contract-details .label {
    font-weight: 600;
    color: #6c757d;
}

.contract-details .value {
    color: #212529;
}

/* Sticky Filters */
.sticky-top {
    z-index: 100;
}

/* Custom Scrollbar */
::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
}

::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: #555;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.10/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@6.1.10/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/timeline@6.1.10/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/resource-timeline@6.1.10/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/interaction@6.1.10/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/frappe-gantt@0.6.1/dist/frappe-gantt.min.js"></script>
<script src="https://unpkg.com/@popperjs/core@2"></script>
<script src="https://unpkg.com/tippy.js@6"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize variables
    let calendar, ganttChart;
    let contracts = []; // This will hold the Gantt data
    const modal = new bootstrap.Modal(document.getElementById('contractModal'));

    // Helper function to get filter parameters
    function getFilterParams() {
        const searchTerm = document.getElementById('searchInput').value.trim();
        const selectedStatuses = Array.from(document.querySelectorAll('input[name="statusFilter"]:checked'))
            .map(cb => cb.value)
            .filter(value => value !== 'all');
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;
        const minBudget = parseFloat(document.getElementById('minBudget').value) || '';
        const maxBudget = parseFloat(document.getElementById('maxBudget').value) || '';

        const params = new URLSearchParams();
        if (searchTerm) params.append('term', searchTerm);
        if (selectedStatuses.length > 0) params.append('status', selectedStatuses.join(',')); // Join multiple statuses
        if (startDate) params.append('startDate', startDate);
        if (endDate) params.append('endDate', endDate);
        if (minBudget) params.append('minBudget', minBudget);
        if (maxBudget) params.append('maxBudget', maxBudget);

        return params.toString();
    }

    // Initialize FullCalendar
    calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
        schedulerLicenseKey: 'CC-Attribution-NonCommercial-NoDerivatives',
        initialView: 'dayGridMonth',
        navLinks: true, // Make day/week numbers clickable
        plugins: [ FullCalendar.dayGridPlugin, FullCalendar.timeGridPlugin, FullCalendar.resourceTimelinePlugin, FullCalendar.interactionPlugin ], // Add necessary plugins
        headerToolbar: {
            left: 'dayGridMonth,dayGridWeek,dayGridDay', // View buttons on left
            center: 'title',
            right: 'today prev,next' // Today button and navigation on right
        },
        height: 'auto',
        events: function(fetchInfo, successCallback, failureCallback) {
            const params = new URLSearchParams({
                startDate: fetchInfo.startStr,
                endDate: fetchInfo.endStr,
                term: document.getElementById('searchInput')?.value || '',
                status: document.querySelector('input[name="statusFilter"]:checked')?.value || 'all'
            });

            fetch(`{{ url('/api/contracts/timeline') }}?${params.toString()}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                credentials: 'same-origin'
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                console.log('Received calendar data:', data);
                // Update calendar events
                successCallback(data.calendar);
                
                // Store Gantt data
                contracts = data.gantt;
                
                // If Gantt view is active, refresh it
                if (document.getElementById('ganttView').style.display !== 'none') {
                    initGanttChart(contracts);
                }
            })
            .catch(error => {
                console.error('Error fetching calendar events:', error);
                failureCallback(error);
            });
        },
        eventDidMount: function(info) {
            const event = info.event;
            const props = event.extendedProps;
            let statusClass = '';
            if (event.type === 'contract') {
                statusClass = `status-${props.status}`;
            }
            // else if (event.type === 'task') {
            //     statusClass = `status-${props.status}`;
            // }
            if (statusClass) {
                info.el.classList.add(statusClass);
            }

            // Add tooltip (keep this)
            let tooltipContent = '';
            if (event.type === 'contract') {
                tooltipContent = `
                    <div class="p-2">
                        <div class="mb-1"><strong>${event.title}</strong></div>
                        <div>Client: ${props.client}</div>
                        <div>Contractor: ${props.contractor}</div>
                        <div>Budget: ₱${new Intl.NumberFormat().format(props.budget)}</div>
                        <div>Status: ${props.status.toUpperCase()}</div>
                    </div>
                `;
            } else if (event.type === 'task') {
                 tooltipContent = `
                    <div class="p-2">
                        <div class="mb-1"><strong>Task: ${event.title}</strong></div>
                        <div>Status: ${props.status.toUpperCase()}</div>
                        <div>Priority: ${props.priority.toUpperCase()}</div>
                        <div>Progress: ${props.progress}%</div>
                    </div>
                `;
            }

            tippy(info.el, {
                content: tooltipContent,
                allowHTML: true,
                theme: 'light',
                placement: 'top'
            });
        },
        eventClick: function(info) {
            if (info.event.type === 'contract') {
                showContractDetails(info.event);
            }
            // Add logic for task click if needed
        }
    });
    calendar.render(); // Render calendar initially

    // Initialize Gantt Chart
    function initGanttChart(data) {
        if (!data || !Array.isArray(data) || data.length === 0) {
            console.warn('No data available for Gantt chart');
            return;
        }

        // Ensure Gantt is re-initialized or refreshed with new data
        if (ganttChart) {
            ganttChart.refresh(data);
        } else {
            ganttChart = new Gantt("#ganttChart", data, {
                header_height: 50,
                column_width: 30,
                step: 24,
                view_modes: ['Day', 'Week', 'Month'],
                view_mode: 'Month',
                bar_height: 20,
                bar_corner_radius: 3,
                arrow_curve: 5,
                padding: 18,
                date_format: 'YYYY-MM-DD',
                on_click: (task) => {
                    const contract = calendar.getEventById(`contract-${task.id}`);
                    if (contract) {
                        showContractDetails(contract);
                    }
                }
            });
        }
    }

    // View Toggle - ensure Gantt is refreshed after toggle
    document.querySelectorAll('#viewToggle').forEach(button => {
        button.addEventListener('click', function() {
            const view = this.dataset.view;
            if (view === 'calendar') {
                document.getElementById('calendarView').style.display = 'block';
                document.getElementById('ganttView').style.display = 'none';
                calendar.render();
            } else {
                document.getElementById('calendarView').style.display = 'none';
                document.getElementById('ganttView').style.display = 'block';
                if (contracts && contracts.length > 0) {
                    initGanttChart(contracts);
                } else {
                    calendar.refetchEvents();
                }
            }
        });
    });

    // Filter functionality
    function applyFilters() {
        calendar.refetchEvents(); // Tells FullCalendar to re-fetch events with current filters
        // Gantt chart will be updated when its view is toggled or when calendar fetches new data.
        // If Gantt is active, force a refresh
        if (document.getElementById('ganttView').style.display !== 'none') {
            initGanttChart(contracts); // Use the updated contracts from refetchEvents
        }
    }

    // Show contract details in modal
    function showContractDetails(contractEvent) {
        const props = contractEvent.extendedProps;
        const contractId = contractEvent.id.replace('contract-', '');
        document.querySelector('.contract-details').innerHTML = `
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="mb-2">
                        <div class="label">Contract Number</div>
                        <div class="value">${contractEvent.title.replace('Contract: ', '')}</div>
                    </div>
                    <div class="mb-2">
                        <div class="label">Client</div>
                        <div class="value">${props.client}</div>
                    </div>
                    <div class="mb-2">
                        <div class="label">Contractor</div>
                        <div class="value">${props.contractor}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-2">
                        <div class="label">Status</div>
                        <div class="value">
                            <span class="badge bg-${getStatusColor(props.status)}">
                                ${props.status.toUpperCase()}
                            </span>
                        </div>
                    </div>
                    <div class="mb-2">
                        <div class="label">Duration</div>
                        <div class="value">
                            ${formatDate(contractEvent.start)} - ${formatDate(contractEvent.end)}
                        </div>
                    </div>
                    <div class="mb-2">
                        <div class="label">Budget</div>
                        <div class="value">₱${new Intl.NumberFormat().format(props.budget)}</div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="mb-2">
                        <div class="label">Scope of Work</div>
                        <div class="value">${props.scope}</div>
                    </div>
                </div>
            </div>
        `;

        document.getElementById('viewContractBtn').href = `/contracts/${contractId}`; // Use extracted ID
        modal.show();
    }

    // Helper functions
    function getStatusColor(status) {
        return {
            draft: 'warning',
            approved: 'success',
            rejected: 'danger',
            pending: 'info', // Added pending status for tasks/events
            in_progress: 'primary',
            completed: 'success',
            delayed: 'danger'
        }[status] || 'secondary';
    }

    function formatDate(dateStr) {
        return new Date(dateStr).toLocaleDateString('en-US', {
            month: 'short',
            day: 'numeric',
            year: 'numeric'
        });
    }

    // Event listeners for filters
    document.querySelectorAll('input[type="checkbox"]').forEach(cb => {
        cb.addEventListener('change', function() {
            if (this.value === 'all' && this.checked) {
                document.querySelectorAll('input[name="statusFilter"]:not([value="all"])').forEach(cb => {
                    cb.checked = false;
                });
            } else if (this.checked) {
                document.getElementById('statusAll').checked = false;
            }
            applyFilters();
            });
    });
    document.getElementById('startDate').addEventListener('change', applyFilters);
    document.getElementById('endDate').addEventListener('change', applyFilters);
    document.getElementById('minBudget').addEventListener('input', applyFilters);
    document.getElementById('maxBudget').addEventListener('input', applyFilters);
    
    // Search on button click (performSearch is replaced by applyFilters)
    document.getElementById('searchButton').addEventListener('click', applyFilters);

    // Search on input with debounce
    let searchTimeout;
    document.getElementById('searchInput').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        // Only filter if there are 2 or more characters, or if clearing search
        if (this.value.length >= 2 || this.value.length === 0) {
            searchTimeout = setTimeout(applyFilters, 300);
        }
    });

    // Handle Enter key for search input
    document.getElementById('searchInput').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            applyFilters();
        }
    });

    // Clear filters
    document.getElementById('clearFilters').addEventListener('click', function() {
        document.getElementById('searchInput').value = '';
        document.getElementById('statusAll').checked = true;
        document.querySelectorAll('input[name="statusFilter"]:not([value="all"])').forEach(cb => {
            cb.checked = false;
        });
        document.getElementById('startDate').value = '';
        document.getElementById('endDate').value = '';
        document.getElementById('minBudget').value = '';
        document.getElementById('maxBudget').value = '';
        applyFilters();
    });

    // Initial filter application to load data (if any pre-existing filters)
    // applyFilters(); // FullCalendar's initial render will call events function

});
</script>
@endpush 
@endsection 