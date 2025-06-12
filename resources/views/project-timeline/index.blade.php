@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h1>Project Timeline</h1>
    <div class="mb-3">
        <input type="text" id="contract-search" class="form-control" placeholder="Search contract...">
    </div>
    <div id="calendar"></div>
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
<link href="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.10/main.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@6.1.10/main.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/@fullcalendar/timegrid@6.1.10/main.min.css" rel="stylesheet" />
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
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.10/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@6.1.10/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/timegrid@6.1.10/index.global.min.js"></script>
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
            document.getElementById('event-date').value = info.startStr;
            var addEventModal = new bootstrap.Modal(document.getElementById('addEventModal'));
            addEventModal.show();
        },
        eventClick: function(info) {
            alert(info.event.title + "\n" + info.event.start.toLocaleDateString());
        },
        events: function(fetchInfo, successCallback, failureCallback) {
            fetch(`{{ route('api.project-timeline.events') }}?start=${fetchInfo.startStr}&end=${fetchInfo.endStr}`)
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
        fetch(`{{ route('api.project-timeline.events.store') }}`, {
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
        fetch(`{{ route('search.contracts') }}?q=${encodeURIComponent(query)}`)
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