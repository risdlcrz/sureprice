@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <h1 class="h3 mb-0">Project Timeline</h1>
                        <div class="btn-group btn-group-timeline">
                            <button class="btn btn-outline-primary" id="viewToggle" data-view="calendar">
                                <i class="bi bi-calendar3"></i> Calendar View
                            </button>
                            <button class="btn btn-outline-primary" id="viewToggle" data-view="gantt">
                                <i class="bi bi-bar-chart"></i> Gantt View
                            </button>
                            <button class="btn btn-outline-primary" id="viewToggle" data-view="progress">
                                <i class="bi bi-hourglass-split"></i> Progress View
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-12">
            <!-- Filter Button -->
            <div class="mb-3">
                <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse" aria-expanded="false" aria-controls="filterCollapse">
                    <i class="bi bi-funnel"></i> Filters
                </button>
            </div>

            <!-- Collapsible Filter Panel -->
            <div class="collapse mb-4" id="filterCollapse">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <!-- Search -->
                            <div class="col-md-4 mb-3">
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
                            <div class="col-md-4 mb-3">
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
                            <div class="col-md-4 mb-3">
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
                            <div class="col-md-4 mb-3">
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
                            <div class="col-12">
                                <button class="btn btn-outline-secondary" id="clearFilters">
                                    <i class="bi bi-x-circle"></i> Clear Filters
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

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

            <!-- Progress View (Initially Hidden) -->
            <div id="progressView" class="card" style="display: none;">
                <div class="card-body">
                    <h4 class="text-center mb-4">Overall Project Completion</h4>
                    <div id="projectProgressBar" class="progress mb-4" style="height: 40px;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                            <span class="fw-bold">0% Complete</span>
                        </div>
                    </div>
                    <hr class="my-4">
                    <h5 class="mb-3">Individual Contract Progress:</h5>
                    <div id="contractProgressDetails" class="mt-4"></div>
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
    margin-top: 20px;
}

.gantt .bar {
    fill: #0d6efd;
    transition: fill 0.3s ease;
}

.gantt .bar-progress {
    fill: #0a4fb9;
}

.gantt .bar-label {
    fill: #fff;
    dominant-baseline: central;
    text-anchor: middle;
    font-size: 12px;
    font-weight: lighter;
}

.gantt .grid-header {
    fill: #ffffff;
    stroke: #e0e0e0;
    stroke-width: 1.4;
}

.gantt .grid-row {
    fill: #ffffff;
}

.gantt .grid-row:nth-child(even) {
    fill: #f5f5f5;
}

.gantt .lower-text, .gantt .upper-text {
    font-size: 12px;
    text-anchor: middle;
}

.gantt .today-highlight {
    fill: #fcf8e3;
    opacity: 0.5;
}

/* Gantt Status Colors */
.gantt .bar.status-draft {
    fill: #f9ab00;
}

.gantt .bar.status-approved {
    fill: #34a853;
}

.gantt .bar.status-rejected {
    fill: #ea4335;
}

.gantt .bar.status-pending {
    fill: #4285f4;
}

.gantt .bar.status-in_progress {
    fill: #0d6efd;
}

.gantt .bar.status-completed {
    fill: #34a853;
}

.gantt .bar.status-delayed {
    fill: #ea4335;
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

/* Calendar Customization */
.btn-group-timeline {
    flex-wrap: wrap;
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
        if (selectedStatuses.length > 0) params.append('status', selectedStatuses.join(','));
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
        navLinks: true,
        plugins: [ FullCalendar.dayGridPlugin, FullCalendar.timeGridPlugin, FullCalendar.resourceTimelinePlugin, FullCalendar.interactionPlugin ],
        headerToolbar: {
            left: 'dayGridMonth,dayGridWeek,dayGridDay',
            center: 'title',
            right: 'today prev,next'
        },
        height: 'auto',
        events: function(fetchInfo, successCallback, failureCallback) {
            // Get current filter parameters
            const filterParams = getFilterParams();
            
            // Add date range from calendar view
            const params = new URLSearchParams(filterParams);
            params.append('startDate', fetchInfo.startStr);
            params.append('endDate', fetchInfo.endStr);

            console.log('Fetching timeline data with params:', params.toString());

            // Fetch events with filters
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
                    throw new Error(`Network response was not ok: ${response.status} ${response.statusText}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Received API response:', data);
                
                if (data.error) {
                    throw new Error(data.error);
                }
                
                // Update calendar events
                successCallback(data.calendar || []);
                
                // Store and process Gantt data
                contracts = processGanttData(data.gantt || []);
                
                // If Gantt view is active, refresh it
                if (document.getElementById('ganttView').style.display !== 'none') {
                    initGanttChart(contracts);
                }
            })
            .catch(error => {
                console.error('Error fetching timeline data:', error);
                failureCallback(error);
                
                // Show error message to user
                const errorMessage = error.message || 'An error occurred while fetching timeline data';
                alert(errorMessage);
            });
        },
        eventDidMount: function(info) {
            const event = info.event;
            const props = event._def.extendedProps;
            let statusClass = '';
            if (props.type === 'contract') {
                statusClass = `status-${props.status}`;
            }
            if (statusClass) {
                info.el.classList.add(statusClass);
            }

            // Add tooltip
            let tooltipContent = '';
            if (props.type === 'contract') {
                tooltipContent = `
                    <div class="p-2">
                        <div class="mb-1"><strong>${info.event.title}</strong></div>
                        <div>Client: ${props.client}</div>
                        <div>Contractor: ${props.contractor}</div>
                        <div>Budget: ₱${new Intl.NumberFormat().format(props.budget || 0)}</div>
                        <div>Status: ${props.status ? props.status.toUpperCase() : 'N/A'}</div>
                    </div>
                `;
            } else {
                tooltipContent = `
                    <div class="p-2">
                        <div class="mb-1"><strong>Task: ${info.event.title}</strong></div>
                        <div>Status: ${props.status ? props.status.toUpperCase() : 'N/A'}</div>
                        <div>Priority: ${props.priority ? props.priority.toUpperCase() : 'N/A'}</div>
                        <div>Progress: ${props.progress || 0}%</div>
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
            if (info.event._def.extendedProps.type === 'contract') {
                showContractDetails(info.event);
            }
        }
    });
    calendar.render();

    // Process Gantt data
    function processGanttData(data) {
        console.log('Raw Gantt data received:', data);
        
        if (!data || !Array.isArray(data)) {
            console.warn('Invalid Gantt data format:', data);
            return [];
        }
        
        const processedData = data
            .filter(contract => {
                // Check if we have the minimum required data
                if (!contract) {
                    console.warn('Empty contract object');
                    return false;
                }

                // Ensure we have required fields
                const hasId = contract.id || contract.contract_id || contract.contractId;
                const hasName = contract.name || contract.title || contract.contract_title || contract.contractTitle;
                const rawStartDate = contract.start || contract.start_date || contract.startDate;
                const rawEndDate = contract.end || contract.end_date || contract.endDate;

                if (!hasId || !hasName || !rawStartDate || !rawEndDate) {
                    console.warn('Contract missing required fields (id, name, start, end):', contract);
                    return false;
                }

                const startDateObj = new Date(rawStartDate);
                const endDateObj = new Date(rawEndDate);

                if (isNaN(startDateObj.getTime()) || isNaN(endDateObj.getTime())) {
                    console.warn(`Contract ${hasId} has invalid dates:`, contract);
                    return false;
                }

                return true;
            })
            .map(contract => {
                // Basic required fields
                const contractId = contract.id || contract.contract_id || contract.contractId;
                const title = contract.name || contract.title || contract.contract_title || contract.contractTitle || 'Untitled Task'; // Ensure name is always a string
                
                const startDateObj = new Date(contract.start || contract.start_date || contract.startDate);
                const endDateObj = new Date(contract.end || contract.end_date || contract.endDate);

                // Format dates as YYYY-MM-DD strings for Frappe Gantt
                const startFormatted = startDateObj.toISOString().split('T')[0];
                const endFormatted = endDateObj.toISOString().split('T')[0];

                const task = {
                    id: `contract-${contractId}`,
                    name: title,
                    start: startFormatted,
                    end: endFormatted,
                    progress: parseInt(contract.progress || contract.progress_percentage || 0),
                    dependencies: '',
                    custom_class: `status-${contract.status || contract.contract_status || 'pending'}`,
                    bar_color: contract.bar_color || '#0d6efd' // Use bar_color from backend or default
                };

                // Additional data for tooltip
                task.client = contract.extendedProps?.client || contract.client || contract.client_name || contract.clientName || 'N/A';
                task.contractor = contract.extendedProps?.contractor || contract.contractor || contract.contractor_name || contract.contractorName || 'N/A';
                task.budget = parseFloat(contract.extendedProps?.budget || contract.budget || contract.contract_budget || 0);
                task.status = contract.status || contract.contract_status || 'pending';
                task.scope = contract.extendedProps?.scope || contract.scope || contract.scope_of_work || contract.scopeOfWork || 'N/A';

                console.log('Processed task:', task);
                return task;
            });
            
        console.log('Final processed data:', processedData);
        return processedData;
    }

    // Initialize Gantt chart
    function initGanttChart(ganttData) {
        console.log('Final processed data:', ganttData);
        const ganttChartEl = document.getElementById('ganttChart');
        ganttChartEl.innerHTML = ''; // Clear previous Gantt chart

        // Filter out tasks without valid start/end dates for Gantt chart
        const cleanedData = ganttData.filter(task => task.start && task.end);
        console.table(cleanedData);

        // Initialize the Gantt chart with minimal configuration first
        const gantt = new Gantt("#ganttChart", cleanedData, {
            header_height: 50,
            column_width: 30,
            step: 24,
            view_modes: ['Quarter Day', 'Half Day', 'Day', 'Week', 'Month'],
            bar_height: 20,
            bar_corner_radius: 3,
            arrow_curve: 5,
            padding: 18,
            view_mode: 'Week',
            date_format: 'YYYY-MM-DD',
            language: 'en',
            // Use custom_html to apply dynamic bar colors based on data
            // bar_color: function(task) { return task.bar_color; }, // Frappe Gantt v0.6.1 does not support this directly
            custom_popup_html: function(task) {
                if (!task) return '';
                
                return `
                    <div class="gantt-popup">
                        <h5>${task.name || 'Untitled Contract'}</h5>
                        <p><strong>Client:</strong> ${task.client || 'N/A'}</p>
                        <p><strong>Contractor:</strong> ${task.contractor || 'N/A'}</p>
                        <p><strong>Status:</strong> ${task.status || 'N/A'}</p>
                        <p><strong>Budget:</strong> ${task.budget ? '₱' + task.budget.toLocaleString() : 'N/A'}</p>
                        <p><strong>Scope:</strong> ${task.scope || 'N/A'}</p>
                        <p><strong>Progress:</strong> ${task.progress || 0}%</p>
                    </div>
                `;
            },
            // Handle bar click event
            on_click: function(task) {
                console.log('Gantt task clicked:', task);
                // Find the corresponding FullCalendar event and show details
                const eventId = task.id.replace('contract-', '');
                const fcEvent = calendar.getEventById(eventId);
                if (fcEvent) {
                    showContractDetails(fcEvent);
                } else {
                    // If not found in calendar events, construct a temporary event object
                    const tempEvent = {
                        id: task.id,
                        title: task.name,
                        start: task.start,
                        end: task.end,
                        extendedProps: {
                            type: 'contract',
                            contract_id: task.id.replace('contract-', ''),
                            client: task.client,
                            contractor: task.contractor,
                            status: task.status,
                            budget: task.budget,
                            scope: task.scope,
                            progress: task.progress
                        }
                    };
                    showContractDetails(tempEvent);
                }
            }
        });

        // Store the gantt instance
        window.gantt = gantt;

        // Manually apply bar colors after rendering due to Frappe Gantt limitations
        gantt.bars.forEach(bar => {
            const task = cleanedData.find(d => d.id === bar.task.id);
            if (task && task.bar_color) {
                bar.group.querySelector('.bar').style.fill = task.bar_color;
                bar.group.querySelector('.bar-progress').style.fill = task.bar_color; // Use same color for progress
            }
        });
    }

    // Update Overall Progress Bar
    const overallProgress = {{ $overallProjectProgress ?? 0 }};
    const progressBar = document.getElementById('projectProgressBar').querySelector('.progress-bar');
    const progressBarText = document.getElementById('progressBarText');

    progressBar.style.width = overallProgress + '%';
    progressBar.setAttribute('aria-valuenow', overallProgress);
    progressBar.textContent = `${overallProgress}%`;
    progressBarText.innerHTML = `Overall Project Completion: <span class="fw-bold">${overallProgress}%</span>`;

    // Function to render individual contract progress bars
    function renderContractProgressDetails(contractsData) {
        const contractProgressDetails = document.getElementById('contractProgressDetails');
        contractProgressDetails.innerHTML = ''; // Clear previous content

        if (!contractsData || !Array.isArray(contractsData)) {
            console.warn('Invalid contracts data for rendering individual progress bars:', contractsData);
            return;
        }

        contractsData.forEach(contract => {
            // Ensure contract object has necessary properties
            const contractName = contract.contract_number || contract.title || 'Untitled Contract';
            const contractProgress = contract.progress || 0;
            const progressBarColor = contract.color || '#0d6efd';

            const contractProgressBarHtml = `
                <div class="mb-4 p-3 border rounded shadow-sm bg-white">
                    <h6 class="mb-2 text-primary">${contractName}</h6>
                    <div class="progress" style="height: 25px;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" 
                             role="progressbar" 
                             style="width: ${contractProgress}%; background-color: ${progressBarColor};" 
                             aria-valuenow="${contractProgress}" 
                             aria-valuemin="0" 
                             aria-valuemax="100">
                            ${contractProgress}%
                        </div>
                    </div>
                </div>
            `;
            contractProgressDetails.insertAdjacentHTML('beforeend', contractProgressBarHtml);
        });
    }

    // View Toggle
    document.querySelectorAll('#viewToggle').forEach(button => {
        button.addEventListener('click', function() {
            const view = this.dataset.view;
            document.getElementById('calendarView').style.display = view === 'calendar' ? 'block' : 'none';
            document.getElementById('ganttView').style.display = view === 'gantt' ? 'block' : 'none';
            document.getElementById('progressView').style.display = view === 'progress' ? 'block' : 'none';

            if (view === 'progress') {
                // Fetch contracts data if not already available or refresh
                const filterParams = getFilterParams();
                const params = new URLSearchParams(filterParams);
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
                        throw new Error(`Network response was not ok: ${response.status} ${response.statusText}`);
                    }
                    return response.json();
                })
                .then(data => {
                    // Convert object of objects to array of objects if needed
                    const contractsArray = Object.values(data.calendar);
                    renderContractProgressDetails(contractsArray);
                })
                .catch(error => {
                    console.error('Error fetching contracts for progress view:', error);
                    document.getElementById('contractProgressDetails').innerHTML = `
                        <div class="alert alert-danger">
                            Error loading contract progress: ${error.message}
                        </div>
                    `;
                });
            }
        });
    });

    // Show Contract Details
    function showContractDetails(event) {
        const modal = new bootstrap.Modal(document.getElementById('contractModal'));
        const details = event.extendedProps;
        
        document.querySelector('.contract-details').innerHTML = `
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Client:</strong> ${event.title}</p>
                    <p><strong>Contractor:</strong> ${details.contractor}</p>
                    <p><strong>Status:</strong> <span class="badge bg-${getStatusColor(details.status)}">${details.status}</span></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Start Date:</strong> ${event.start.toLocaleDateString()}</p>
                    <p><strong>End Date:</strong> ${event.end.toLocaleDateString()}</p>
                    <p><strong>Budget:</strong> ₱${details.budget.toLocaleString()}</p>
                </div>
            </div>
        `;
        
        const viewContractBtn = document.getElementById('viewContractBtn');
        // Ensure contractId is always just the number, removing any 'contract-' prefix
        const contractId = event.id.toString().replace('contract-', '');
        viewContractBtn.setAttribute('href', `{{ url('/contracts/') }}/${contractId}`);
        console.log('Contract ID set on button:', contractId);
        modal.show();
    }

    // Initial setup of the Gantt chart and overall progress bar when the page loads
    // The Gantt chart data is already available via `contracts` variable.
    // No need to call initGanttChart here directly, as it's triggered by tab switch or API response.

    // Initial setup for the overall progress bar
    // It's already correctly updated via a direct script block above.

    // Set the initial view to calendar
    document.getElementById('calendarView').style.display = 'block';
    document.getElementById('ganttView').style.display = 'none';
    document.getElementById('progressView').style.display = 'none';

    // Handle click on View Full Contract button in modal
    document.getElementById('viewContractBtn').addEventListener('click', function(e) {
        e.preventDefault(); // Prevent default link behavior
        const contractUrl = this.getAttribute('href');
        if (contractUrl) {
            window.location.href = contractUrl;
        } else {
            console.error('Contract URL not found.');
        }
    });

    function getStatusColor(status) {
        switch(status) {
            case 'approved': return 'success';
            case 'draft': return 'warning';
            case 'rejected': return 'danger';
            default: return 'secondary';
        }
    }
});
</script>
@endpush 
@endsection 