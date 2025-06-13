@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="card-title">Contracts Timeline</h3>
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
                    <div class="card-body">
                        <!-- Calendar View -->
                        <div id="calendarView">
                            <div id="calendar"></div>
                        </div>

                        <!-- Gantt View (Initially Hidden) -->
                        <div id="ganttView" style="display: none;">
                            <div id="ganttChart"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
                <button type="button" class="btn btn-primary" id="viewContractBtn" onclick="redirectToContractDetails()">
                    <i class="bi bi-eye"></i> View Full Contract
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
.fc-event {
    cursor: pointer;
    transition: all 0.2s ease;
    border-radius: 4px;
    font-size: 0.85rem;
    padding: 2px 4px;
    margin-bottom: 2px;
    border: none;
    font-weight: normal;
}

.fc-event:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.fc-event.status-draft {
    background-color: #fce8b2 !important;
    border-left: 4px solid #f9ab00 !important;
    color: #3c3c3c !important;
}

.fc-event.status-approved {
    background-color: #d1f7d1 !important;
    border-left: 4px solid #34a853 !important;
    color: #3c3c3c !important;
}

.fc-event.status-rejected {
    background-color: #f4cccc !important;
    border-left: 4px solid #ea4335 !important;
    color: #3c3c3c !important;
}

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
    // Initialize FullCalendar
    const calendarEl = document.getElementById('calendar');
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: JSON.parse(@json($contracts)),
        eventClick: function(info) {
            showContractDetails(info.event);
        }
    });
    calendar.render();

    // Initialize Gantt Chart
    const ganttData = JSON.parse(@json($ganttTasks));

    const gantt = new Gantt("#ganttChart", ganttData, {
        header_height: 50,
        column_width: 30,
        step: 24,
        view_mode: 'Week',
        bar_height: 20,
        bar_corner_radius: 3,
        arrow_curve: 5,
        padding: 18,
        view_modes: ['Quarter Day', 'Half Day', 'Day', 'Week', 'Month'],
        custom_popup_html: function(task) {
            return `
                <div class="gantt-popup">
                    <h5>${task.name}</h5>
                    <p>Start: ${task.start}</p>
                    <p>End: ${task.end}</p>
                </div>
            `;
        }
    });

    // View Toggle
    document.querySelectorAll('#viewToggle').forEach(button => {
        button.addEventListener('click', function() {
            const view = this.dataset.view;
            document.getElementById('calendarView').style.display = view === 'calendar' ? 'block' : 'none';
            document.getElementById('ganttView').style.display = view === 'gantt' ? 'block' : 'none';
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
        const contractId = event.id; // Assuming event.id is the contract ID
        viewContractBtn.setAttribute('data-contract-id', contractId);
        console.log('Contract ID set on button:', contractId);
        modal.show();
    }

    function redirectToContractDetails() {
        const viewContractBtn = document.getElementById('viewContractBtn');
        const contractId = viewContractBtn.getAttribute('data-contract-id');
        if (contractId) {
            // Get the base URL from Laravel's helper function
            const baseUrl = '{{ url('/') }}';
            const targetUrl = `${baseUrl}/contracts/${contractId}`;
            console.log('Redirecting to:', targetUrl);
            window.location.href = targetUrl;
        } else {
            console.error('Contract ID not found for redirection.');
        }
    }

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