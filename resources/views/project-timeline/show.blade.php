@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Task Details</h3>
                    <div class="card-tools">
                        <a href="{{ route('project-timeline.index') }}" class="btn btn-default">
                            <i class="fas fa-arrow-left"></i> Back to Timeline
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h4>{{ $task->title }}</h4>
                            <p class="text-muted">
                                Contract: {{ $task->contract->contract_number }} - {{ $task->contract->title }}
                            </p>
                            
                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <h5>Task Information</h5>
                                    <table class="table table-bordered">
                                        <tr>
                                            <th style="width: 200px;">Status</th>
                                            <td>
                                                <span class="badge badge-{{ $task->status === 'completed' ? 'success' : ($task->status === 'in_progress' ? 'info' : ($task->status === 'delayed' ? 'danger' : 'secondary')) }}">
                                                    {{ ucfirst($task->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Priority</th>
                                            <td>
                                                <span class="badge badge-{{ $task->priority === 'high' ? 'danger' : ($task->priority === 'medium' ? 'warning' : 'success') }}">
                                                    {{ ucfirst($task->priority) }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Progress</th>
                                            <td>
                                                <div class="progress">
                                                    <div class="progress-bar" role="progressbar" style="width: {{ $task->progress }}%">
                                                        {{ $task->progress }}%
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Start Date</th>
                                            <td>{{ $task->start_date->format('M d, Y') }}</td>
                                        </tr>
                                        <tr>
                                            <th>End Date</th>
                                            <td>{{ $task->end_date->format('M d, Y') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Duration</th>
                                            <td>{{ $task->duration }} days</td>
                                        </tr>
                                        <tr>
                                            <th>Remaining Days</th>
                                            <td>
                                                @if($task->status === 'completed')
                                                    Completed
                                                @else
                                                    {{ $task->remaining_days }} days
                                                    @if($task->isOverdue())
                                                        <span class="text-danger">(Overdue by {{ $task->days_overdue }} days)</span>
                                                    @endif
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>

                                <div class="col-md-6">
                                    <h5>Assignment Details</h5>
                                    <table class="table table-bordered">
                                        <tr>
                                            <th style="width: 200px;">Room</th>
                                            <td>{{ $task->room ? $task->room->name : 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Scope Type</th>
                                            <td>{{ $task->scopeType ? $task->scopeType->name : 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Assigned To</th>
                                            <td>{{ $task->assignee ? $task->assignee->name : 'Unassigned' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Created By</th>
                                            <td>{{ $task->creator->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Created At</th>
                                            <td>{{ $task->created_at->format('M d, Y H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Last Updated</th>
                                            <td>{{ $task->updated_at->format('M d, Y H:i') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <h5>Description</h5>
                                    <div class="card">
                                        <div class="card-body">
                                            {{ $task->description ?: 'No description provided.' }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if($task->notes)
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <h5>Notes</h5>
                                    <div class="card">
                                        <div class="card-body">
                                            {{ $task->notes }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>

                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Actions</h5>
                                </div>
                                <div class="card-body">
                                    <a href="{{ route('project-timeline.edit', $task) }}" class="btn btn-primary btn-block mb-2">
                                        <i class="fas fa-edit"></i> Edit Task
                                    </a>

                                    @if($task->status !== 'completed')
                                    <form action="{{ route('project-timeline.update-progress', $task) }}" method="POST" class="mb-2">
                                        @csrf
                                        @method('PUT')
                                        <div class="form-group">
                                            <label>Update Progress</label>
                                            <div class="input-group">
                                                <input type="number" name="progress" class="form-control" min="0" max="100" value="{{ $task->progress }}">
                                                <div class="input-group-append">
                                                    <button type="submit" class="btn btn-success">Update</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                    @endif

                                    @if($task->status !== 'delayed' && $task->status !== 'completed')
                                    <form action="{{ route('project-timeline.mark-delayed', $task) }}" method="POST" class="mb-2">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-warning btn-block">
                                            <i class="fas fa-exclamation-triangle"></i> Mark as Delayed
                                        </button>
                                    </form>
                                    @endif

                                    <form action="{{ route('project-timeline.destroy', $task) }}" method="POST" class="mb-2" onsubmit="return confirm('Are you sure you want to delete this task?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-block">
                                            <i class="fas fa-trash"></i> Delete Task
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Event Modal -->
<div class="modal fade" id="addEventModal" tabindex="-1" aria-labelledby="addEventModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addEventModalLabel">Add Event</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="add-event-form">
          <div class="mb-3">
            <label for="event-title" class="form-label">Title</label>
            <input type="text" class="form-control" id="event-title" required>
          </div>
          <div class="mb-3">
            <label for="event-date" class="form-label">Date</label>
            <input type="date" class="form-control" id="event-date" required>
          </div>
          <div class="mb-3">
            <label for="event-description" class="form-label">Description</label>
            <textarea class="form-control" id="event-description"></textarea>
          </div>
          <button type="submit" class="btn btn-primary">Add Event</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@push('styles')
<!-- FullCalendar CSS (npm build will handle this, but for dev you can use CDN) -->
<link href="/node_modules/fullcalendar/main.min.css" rel="stylesheet" />
<style>
  #calendar {
    max-width: 900px;
    margin: 40px auto;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    padding: 20px;
  }
</style>
@endpush

@push('scripts')
<!-- FullCalendar JS (npm build will handle this, but for dev you can use CDN) -->
<script src="/node_modules/fullcalendar/main.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
        },
        selectable: true,
        select: function(info) {
            // Open modal to add event
            document.getElementById('event-date').value = info.startStr;
            var addEventModal = new bootstrap.Modal(document.getElementById('addEventModal'));
            addEventModal.show();
        },
        eventClick: function(info) {
            // Optionally show event details
            alert(info.event.title + "\n" + info.event.start.toLocaleDateString());
        },
        events: function(fetchInfo, successCallback, failureCallback) {
            // Fetch project events and payment schedules via AJAX
            fetch(`/api/project-timeline/events?start=${fetchInfo.startStr}&end=${fetchInfo.endStr}`)
                .then(response => response.json())
                .then(data => successCallback(data))
                .catch(error => failureCallback(error));
        }
    });
    calendar.render();

    // Handle add event form
    document.getElementById('add-event-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const title = document.getElementById('event-title').value;
        const date = document.getElementById('event-date').value;
        const description = document.getElementById('event-description').value;
        fetch('/api/project-timeline/events', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ title, date, description })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                calendar.refetchEvents();
                bootstrap.Modal.getInstance(document.getElementById('addEventModal')).hide();
                this.reset();
            } else {
                alert('Failed to add event');
            }
        });
    });

    // Contract search
    document.getElementById('contract-search').addEventListener('input', function() {
        const query = this.value;
        fetch(`/api/contracts/search?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                // You can show a dropdown or auto-complete here
                // For now, just log the results
                console.log(data);
            });
    });
});
</script>
@endpush 