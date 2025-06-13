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
                    <div class="card-body">
                        <!-- Calendar View -->
                        <div id="calendarView">
                            <div id="calendar"></div>
                        </div>

                        <!-- Gantt View (Initially Hidden) -->
                        <div id="ganttView" style="display: none;">
                            <div id="ganttChart"></div>
                        </div>

                        <!-- Progress View (Initially Hidden) -->
                        <div id="progressView" style="display: none;">
                            <div id="projectProgressBar" class="progress" style="height: 30px;">
                                <div class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                    0%
                                </div>
                            </div>
                            <p class="mt-2 text-center" id="progressBarText">Overall Project Completion</p>
                            <div id="contractProgressDetails" class="mt-4"></div>
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
.btn-group-timeline {
    flex-wrap: wrap;
}

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
    background-color: #fce8b2;
    border-left: 4px solid #f9ab00;
    color: #3c3c3c;
}

.fc-event.status-approved {
    background-color: #d1f7d1;
    border-left: 4px solid #34a853;
    color: #3c3c3c;
}

.fc-event.status-rejected {
    background-color: #f4cccc;
    border-left: 4px solid #ea4335;
    color: #3c3c3c;
}

.gantt-container {
    height: 600px;
    overflow-y: auto;
}

/* Remove fixed fill for gantt bars to allow dynamic coloring */
/* .gantt .bar {
    fill: #0d6efd;
}

.gantt .bar-progress {
    fill: #0a4fb9;
} */

/* New classes for Gantt bar colors */
/* .gantt-color-0 .bar { fill: #3498db; } .gantt-color-0 .bar-progress { fill: #217dbb; }
.gantt-color-1 .bar { fill: #e74c3c; } .gantt-color-1 .bar-progress { fill: #b53b31; }
.gantt-color-2 .bar { fill: #2ecc71; } .gantt-color-2 .bar-progress { fill: #23a059; }
.gantt-color-3 .bar { fill: #f39c12; } .gantt-color-3 .bar-progress { fill: #c17c0e; }
.gantt-color-4 .bar { fill: #9b59b6; } .gantt-color-4 .bar-progress { fill: #7a468d; }
.gantt-color-5 .bar { fill: #1abc9c; } .gantt-color-5 .bar-progress { fill: #158f77; }
.gantt-color-6 .bar { fill: #d35400; } .gantt-color-6 .bar-progress { fill: #a54100; }
.gantt-color-7 .bar { fill: #c0392b; } .gantt-color-7 .bar-progress { fill: #9a2e22; } */
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
        bar_progress_color: '#0a4fb9', // Default progress color
        custom_popup_html: function(task) {
            return `
                <div class="gantt-popup">
                    <h5>${task.name}</h5>
                    <p>Start: ${task.start}</p>
                    <p>End: ${task.end}</p>
                </div>
            `;
        },
        on_click: function(task) {
            // Use the bar_color property for event coloring if available
            if (task.bar_color) {
                // Find the event in FullCalendar events by ID and update its color
                const event = calendar.getEventById(task.id.replace('contract-', ''));
                if (event) {
                    event.setProp('color', task.bar_color);
                }
            }
            showContractDetails(task);
        }
    });

    // Update Progress Bar
    const overallProgress = {{ $overallProgress ?? 0 }};
    const progressBar = document.getElementById('projectProgressBar').querySelector('.progress-bar');
    const progressBarText = document.getElementById('progressBarText');

    progressBar.style.width = overallProgress + '%';
    progressBar.setAttribute('aria-valuenow', overallProgress);
    progressBar.textContent = overallProgress + '%';
    progressBarText.textContent = `Overall Project Completion: ${overallProgress}%`;

    // Function to render individual contract progress bars
    function renderContractProgressDetails() {
        const contractProgressDetails = document.getElementById('contractProgressDetails');
        contractProgressDetails.innerHTML = ''; // Clear previous content

        ganttData.forEach(contract => {
            const contractProgressBarHtml = `
                <div class="mb-3">
                    <h6>${contract.name} Progress:</h6>
                    <div class="progress" style="height: 25px;">
                        <div class="progress-bar" role="progressbar" style="width: ${contract.progress || 0}%; background-color: ${contract.bar_color || '#0d6efd'};" 
                             aria-valuenow="${contract.progress || 0}" aria-valuemin="0" aria-valuemax="100">
                            ${contract.progress || 0}%
                        </div>
                    </div>
                </div>
            `;
            contractProgressDetails.insertAdjacentHTML('beforeend', contractProgressBarHtml);
        });
    }

    // Initial render of individual contract progress bars
    // This will only render if the progress view is initially visible, which it is not.
    // We will call it explicitly when the tab is switched.
    // renderContractProgressDetails(); // Removed initial call here

    // View Toggle
    document.querySelectorAll('#viewToggle').forEach(button => {
        button.addEventListener('click', function() {
            const view = this.dataset.view;
            document.getElementById('calendarView').style.display = view === 'calendar' ? 'block' : 'none';
            document.getElementById('ganttView').style.display = view === 'gantt' ? 'block' : 'none';
            document.getElementById('progressView').style.display = view === 'progress' ? 'block' : 'none';

            if (view === 'progress') {
                renderContractProgressDetails();
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