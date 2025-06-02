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
                        <div id="searchResults" class="list-group mt-2 shadow-sm" style="display: none; max-height: 300px; overflow-y: auto; position: absolute; z-index: 1000; width: 100%; background: white;">
                            <!-- Search results will be populated here -->
                        </div>
                    </div>

                    <!-- Status Filter -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Status</label>
                        <div class="d-flex flex-wrap gap-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="all" id="statusAll" checked>
                                <label class="form-check-label" for="statusAll">All</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="draft" id="statusDraft">
                                <label class="form-check-label" for="statusDraft">
                                    <span class="badge bg-warning">Draft</span>
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="approved" id="statusApproved">
                                <label class="form-check-label" for="statusApproved">
                                    <span class="badge bg-success">Approved</span>
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="rejected" id="statusRejected">
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
}

.fc-event:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.fc-event-title {
    font-weight: 500;
}

/* Custom Status Colors */
.status-draft {
    background-color: #ffc107 !important;
    border-color: #ffc107 !important;
}

.status-approved {
    background-color: #198754 !important;
    border-color: #198754 !important;
}

.status-rejected {
    background-color: #dc3545 !important;
    border-color: #dc3545 !important;
}

/* Gantt Chart Customization */
.gantt-container {
    height: 600px;
    overflow-y: auto;
}

.gantt .bar {
    fill: #0d6efd;
}

.gantt .bar-progress {
    fill: #0a4fb9;
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
<script src='https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.10/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@6.1.10/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/@fullcalendar/timeline@6.1.10/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/@fullcalendar/resource-timeline@6.1.10/main.min.js'></script>
<script src="https://cdn.jsdelivr.net/npm/frappe-gantt@0.6.1/dist/frappe-gantt.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize variables
    let calendar, ganttChart;
    let contracts = [];
    const modal = new bootstrap.Modal(document.getElementById('contractModal'));

    // Initialize FullCalendar
    calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,dayGridWeek,dayGridDay'
        },
        height: 'auto',
        events: '/api/contracts/timeline',
        eventDidMount: function(info) {
            const event = info.event;
            const props = event.extendedProps;
            
            // Add status class
            info.el.classList.add(`status-${props.status}`);
            
            // Add tooltip
            tippy(info.el, {
                content: `
                    <div class="p-2">
                        <div class="mb-1"><strong>${event.title}</strong></div>
                        <div>Client: ${props.client}</div>
                        <div>Contractor: ${props.contractor}</div>
                        <div>Budget: ₱${new Intl.NumberFormat().format(props.budget)}</div>
                        <div>Status: ${props.status.toUpperCase()}</div>
                    </div>
                `,
                allowHTML: true,
                theme: 'light',
                placement: 'top'
            });
        },
        eventClick: function(info) {
            showContractDetails(info.event);
        }
    });

    // Initialize Gantt Chart
    function initGanttChart(data) {
        const tasks = data.map(contract => ({
            id: contract.id,
            name: contract.title,
            start: contract.start,
            end: contract.end,
            progress: contract.extendedProps.status === 'approved' ? 100 : 
                     contract.extendedProps.status === 'draft' ? 50 : 0,
            dependencies: []
        }));

        ganttChart = new Gantt("#ganttChart", tasks, {
            view_modes: ['Day', 'Week', 'Month'],
            view_mode: 'Month',
            on_click: (task) => {
                const contract = contracts.find(c => c.id === task.id);
                if (contract) {
                    showContractDetails(contract);
                }
            }
        });
    }

    // Fetch and initialize data
    fetch('/api/contracts/timeline')
        .then(response => response.json())
        .then(data => {
            contracts = data;
            calendar.render();
            initGanttChart(data);
            applyFilters();
        });

    // View Toggle
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
                ganttChart.refresh(getFilteredData());
            }
        });
    });

    // Filter functionality
    function applyFilters() {
        const filteredData = getFilteredData();
        calendar.removeAllEvents();
        calendar.addEventSource(filteredData);
        if (ganttChart) {
            ganttChart.refresh(filteredData.map(contract => ({
                id: contract.id,
                name: contract.title,
                start: contract.start,
                end: contract.end,
                progress: contract.extendedProps.status === 'approved' ? 100 : 
                         contract.extendedProps.status === 'draft' ? 50 : 0
            })));
        }
    }

    function getFilteredData() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        const selectedStatuses = Array.from(document.querySelectorAll('input[type="checkbox"]:checked'))
            .map(cb => cb.value)
            .filter(value => value !== 'all');
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;
        const minBudget = parseFloat(document.getElementById('minBudget').value) || 0;
        const maxBudget = parseFloat(document.getElementById('maxBudget').value) || Infinity;

        return contracts.filter(contract => {
            const matchesSearch = 
                contract.title.toLowerCase().includes(searchTerm) ||
                contract.extendedProps.client.toLowerCase().includes(searchTerm) ||
                contract.extendedProps.contractor.toLowerCase().includes(searchTerm);

            const matchesStatus = 
                selectedStatuses.length === 0 || 
                selectedStatuses.includes(contract.extendedProps.status);

            const matchesDate = 
                (!startDate || contract.start >= startDate) &&
                (!endDate || contract.end <= endDate);

            const matchesBudget =
                contract.extendedProps.budget >= minBudget &&
                contract.extendedProps.budget <= maxBudget;

            return matchesSearch && matchesStatus && matchesDate && matchesBudget;
        });
    }

    // Show contract details in modal
    function showContractDetails(contract) {
        const props = contract.extendedProps;
        document.querySelector('.contract-details').innerHTML = `
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="mb-2">
                        <div class="label">Contract ID</div>
                        <div class="value">${contract.title}</div>
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
                            ${formatDate(contract.start)} - ${formatDate(contract.end)}
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

        document.getElementById('viewContractBtn').href = `/contracts/${contract.id}`;
        modal.show();
    }

    // Helper functions
    function getStatusColor(status) {
        return {
            draft: 'warning',
            approved: 'success',
            rejected: 'danger'
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
                document.querySelectorAll('input[type="checkbox"]:not([value="all"])').forEach(cb => {
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

    // Clear filters
    document.getElementById('clearFilters').addEventListener('click', function() {
        document.getElementById('searchInput').value = '';
        document.getElementById('statusAll').checked = true;
        document.querySelectorAll('input[type="checkbox"]:not([value="all"])').forEach(cb => {
            cb.checked = false;
        });
        document.getElementById('startDate').value = '';
        document.getElementById('endDate').value = '';
        document.getElementById('minBudget').value = '';
        document.getElementById('maxBudget').value = '';
        applyFilters();
    });

    // Enhanced search functionality
    const searchInput = document.getElementById('searchInput');
    const searchButton = document.getElementById('searchButton');
    const searchResults = document.getElementById('searchResults');
    let searchTimeout;

    function performSearch() {
        const term = searchInput.value.trim();
        if (term.length < 2) {
            searchResults.style.display = 'none';
            return;
        }

        // Show loading state
        searchResults.style.display = 'block';
        searchResults.innerHTML = '<div class="p-3 text-center"><div class="spinner-border text-primary" role="status"></div></div>';

        // Fetch search results
        fetch(`/api/contracts/search?term=${encodeURIComponent(term)}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.length > 0) {
                searchResults.innerHTML = data.map(contract => `
                    <a href="#" class="list-group-item list-group-item-action" data-contract-id="${contract.id}">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-1">Contract #${contract.contract_id}</h6>
                            <span class="badge bg-${getStatusColor(contract.status)}">
                                ${contract.status.toUpperCase()}
                            </span>
                        </div>
                        <p class="mb-1">Client: ${contract.client_name}</p>
                        <p class="mb-1">Contractor: ${contract.contractor_name}</p>
                        <small class="text-muted">
                            ${formatDate(contract.start_date)} - ${formatDate(contract.end_date)}
                        </small>
                    </a>
                `).join('');

                // Add click handlers for search results
                document.querySelectorAll('#searchResults .list-group-item').forEach(item => {
                    item.addEventListener('click', function(e) {
                        e.preventDefault();
                        const contractId = this.dataset.contractId;
                        const contract = data.find(c => c.id === parseInt(contractId));
                        if (contract) {
                            // Focus calendar on the contract date
                            calendar.gotoDate(new Date(contract.start_date));
                            
                            // Highlight the contract event
                            const event = calendar.getEventById(contractId);
                            if (event) {
                                event.setProp('backgroundColor', '#007bff');
                                setTimeout(() => {
                                    event.setProp('backgroundColor', getStatusColor(contract.status));
                                }, 2000);
                            }
                            
                            // Show contract details
                            showContractDetails({
                                id: contract.id,
                                title: `Contract #${contract.contract_id}`,
                                extendedProps: {
                                    client: contract.client_name,
                                    contractor: contract.contractor_name,
                                    scope: contract.scope_of_work,
                                    budget: contract.budget_allocation,
                                    status: contract.status
                                }
                            });
                        }
                        searchResults.style.display = 'none';
                    });
                });
            } else {
                searchResults.innerHTML = '<div class="p-3 text-center text-muted">No contracts found</div>';
            }
        })
        .catch(error => {
            console.error('Search error:', error);
            searchResults.innerHTML = '<div class="p-3 text-center text-danger">Error performing search</div>';
        });
    }

    // Search on button click
    searchButton.addEventListener('click', performSearch);

    // Search on input with debounce
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        if (this.value.length >= 2) {
            searchTimeout = setTimeout(performSearch, 300);
        } else {
            searchResults.style.display = 'none';
        }
    });

    // Handle Enter key
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            performSearch();
        }
    });

    // Close search results when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !searchResults.contains(e.target) && !searchButton.contains(e.target)) {
            searchResults.style.display = 'none';
        }
    });
});
</script>
@endpush
@endsection 