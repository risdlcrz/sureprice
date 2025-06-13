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
                    custom_class: `status-${contract.status || contract.contract_status || 'pending'}`
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
    function initGanttChart(data) {
        console.log('Initializing Gantt chart with data:', data);
        
        if (!data || !Array.isArray(data) || data.length === 0) {
            console.warn('No valid data available for Gantt chart');
            document.getElementById('ganttChart').innerHTML = '<div class="alert alert-info">No data available for the selected period.</div>';
            return;
        }

        try {
            // Clear existing chart
            const ganttContainer = document.getElementById('ganttChart');
            ganttContainer.innerHTML = '';

            // Perform a final filtering to ensure all tasks are valid for Frappe Gantt
            const cleanedData = data.filter(task => {
                // Explicitly check for typeof string for name, start, and end
                const isValid = task && 
                                typeof task.id === 'string' && task.id !== '' &&
                                typeof task.name === 'string' && task.name !== '' &&
                                typeof task.start === 'string' && task.start !== '' &&
                                typeof task.end === 'string' && task.end !== '';
                if (!isValid) {
                    console.warn('Invalid task data found, skipping for Gantt chart:', task);
                }
                return isValid;
            });

            if (cleanedData.length === 0) {
                console.warn('No valid tasks remaining after cleaning for Gantt chart.');
                document.getElementById('ganttChart').innerHTML = '<div class="alert alert-info">No valid data available for the selected period after processing.</div>';
                return;
            }
            
            console.log('Data sent to Gantt constructor:', cleanedData);
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
                language: 'en'
            });

            // Store the gantt instance
            window.gantt = gantt;

            // Add custom popup after initialization
            gantt.custom_popup_html = function(task) {
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
            };

            // Add click handler for tasks
            gantt.bar_click = function(task) {
                console.log('Task clicked:', task);
            };

            // Add custom styling for status colors after a short delay
            setTimeout(() => {
                cleanedData.forEach(task => {
                    if (!task || !task.id) return;
                    
                    const bar = document.querySelector(`.bar[data-id="${task.id}"]`);
                    if (bar && task.custom_class) {
                        bar.classList.add(task.custom_class);
                    }
                });
            }, 100);

        } catch (error) {
            console.error('Error initializing Gantt chart:', error);
            document.getElementById('ganttChart').innerHTML = `
                <div class="alert alert-danger">
                    Error initializing Gantt chart: ${error.message}
                </div>
            `;
        }
    }

    // Toggle between Calendar and Gantt views
    document.querySelectorAll('#viewToggle').forEach(button => {
        button.addEventListener('click', function() {
            const view = this.dataset.view;
            const calendarView = document.getElementById('calendarView');
            const ganttView = document.getElementById('ganttView');
            
            if (view === 'calendar') {
                calendarView.style.display = 'block';
                ganttView.style.display = 'none';
                calendar.render();
            } else {
                calendarView.style.display = 'none';
                ganttView.style.display = 'block';
                
                // Initialize or refresh Gantt chart
                if (contracts && contracts.length > 0) {
                    initGanttChart(contracts);
                } else {
                    // If no contracts data, fetch it
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
                        if (data.error) {
                            throw new Error(data.error);
                        }
                        contracts = processGanttData(data.gantt || []);
                        initGanttChart(contracts);
                    })
                    .catch(error => {
                        console.error('Error fetching timeline data:', error);
                        document.getElementById('ganttChart').innerHTML = `
                            <div class="alert alert-danger">
                                Error loading Gantt chart: ${error.message}
                            </div>
                        `;
                    });
                }
            }
        });
    });

    // Filter functionality
    function applyFilters() {
        calendar.refetchEvents();
        if (document.getElementById('ganttView').style.display !== 'none') {
            initGanttChart(contracts);
        }
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

    // Add event listeners for all filter inputs
    document.getElementById('startDate').addEventListener('change', applyFilters);
    document.getElementById('endDate').addEventListener('change', applyFilters);
    document.getElementById('minBudget').addEventListener('input', applyFilters);
    document.getElementById('maxBudget').addEventListener('input', applyFilters);
    
    // Search button click
    document.getElementById('searchButton').addEventListener('click', applyFilters);

    // Search input with debounce
    let searchTimeout;
    document.getElementById('searchInput').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(applyFilters, 300);
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

    // Show contract details in modal
    function showContractDetails(contractEvent) {
        const props = contractEvent._def.extendedProps;
        const contractId = contractEvent._def.publicId ? contractEvent._def.publicId.replace('contract-', '') : null;

        document.querySelector('.contract-details').innerHTML = `
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="mb-2">
                        <div class="label">Contract Number</div>
                        <div class="value">${props.contract_id || 'N/A'}</div>
                    </div>
                    <div class="mb-2">
                        <div class="label">Client</div>
                        <div class="value">${props.client || 'N/A'}</div>
                    </div>
                    <div class="mb-2">
                        <div class="label">Contractor</div>
                        <div class="value">${props.contractor || 'N/A'}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-2">
                        <div class="label">Status</div>
                        <div class="value">
                            <span class="badge bg-${getStatusColor(props.status)}">
                                ${props.status ? props.status.toUpperCase() : 'N/A'}
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
                        <div class="value">₱${new Intl.NumberFormat().format(props.budget || 0)}</div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="mb-2">
                        <div class="label">Scope of Work</div>
                        <div class="value">${props.scope || 'N/A'}</div>
                    </div>
                </div>
            </div>
        `;

        const viewContractUrl = contractId ? `/contracts/${contractId}` : '#';
        document.getElementById('viewContractBtn').href = viewContractUrl;
        modal.show();
    }

    // Helper functions
    function getStatusColor(status) {
        return {
            draft: 'warning',
            approved: 'success',
            rejected: 'danger',
            pending: 'info',
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
});
</script>
@endpush 
@endsection 