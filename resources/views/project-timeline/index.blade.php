@extends('layouts.app')

@section('styles')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
<style>
    .fc-event {
        cursor: pointer;
    }
    .priority-high { border-left: 4px solid #dc3545; }
    .priority-medium { border-left: 4px solid #ffc107; }
    .priority-low { border-left: 4px solid #28a745; }
    .status-pending { background-color: #6c757d; }
    .status-in_progress { background-color: #17a2b8; }
    .status-completed { background-color: #28a745; }
    .status-delayed { background-color: #dc3545; }
    .filter-section {
        background: #f8f9fa;
        padding: 1rem;
        border-radius: 0.5rem;
        margin-bottom: 1rem;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Project Timeline</h3>
                    <div class="card-tools">
                        <a href="{{ route('project-timeline.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> New Task
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <div class="filter-section">
                        <form id="filterForm" class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Contract</label>
                                    <select name="contract_id" class="form-control">
                                        <option value="">All Contracts</option>
                                        @foreach($contracts as $contract)
                                            <option value="{{ $contract->id }}" {{ request('contract_id') == $contract->id ? 'selected' : '' }}>
                                                {{ $contract->contract_number }} - {{ $contract->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select name="status" class="form-control">
                                        <option value="">All Status</option>
                                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="delayed" {{ request('status') == 'delayed' ? 'selected' : '' }}>Delayed</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Priority</label>
                                    <select name="priority" class="form-control">
                                        <option value="">All Priority</option>
                                        <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                                        <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                                        <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Assigned To</label>
                                    <select name="assigned_to" class="form-control">
                                        <option value="">All Users</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ request('assigned_to') == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="btn btn-primary btn-block">Apply Filters</button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Calendar -->
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Task Details Modal -->
<div class="modal fade" id="taskModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Task Details</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="taskDetails"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <a href="#" id="editTaskBtn" class="btn btn-primary">Edit Task</a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: {!! json_encode($tasks) !!},
        eventClick: function(info) {
            showTaskDetails(info.event);
        },
        eventDidMount: function(info) {
            // Add tooltips
            $(info.el).tooltip({
                title: info.event.title,
                placement: 'top',
                trigger: 'hover',
                container: 'body'
            });
        }
    });
    calendar.render();

    // Show task details in modal
    function showTaskDetails(event) {
        var details = `
            <div class="task-details">
                <h4>${event.title}</h4>
                <p><strong>Contract:</strong> ${event.extendedProps.contract}</p>
                <p><strong>Room:</strong> ${event.extendedProps.room || 'N/A'}</p>
                <p><strong>Scope:</strong> ${event.extendedProps.scope || 'N/A'}</p>
                <p><strong>Status:</strong> ${event.extendedProps.status}</p>
                <p><strong>Priority:</strong> ${event.extendedProps.priority}</p>
                <p><strong>Progress:</strong> ${event.extendedProps.progress}%</p>
                <p><strong>Assignee:</strong> ${event.extendedProps.assignee || 'Unassigned'}</p>
            </div>
        `;
        $('#taskDetails').html(details);
        $('#editTaskBtn').attr('href', event.url);
        $('#taskModal').modal('show');
    }

    // Handle filter form submission
    $('#filterForm').on('submit', function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        window.location.href = '{{ route("project-timeline.index") }}?' + formData;
    });
});
</script>
@endsection 