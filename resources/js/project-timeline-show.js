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
            fetch(`/api/project-timeline/events?start=${fetchInfo.startStr}&end=${fetchInfo.endStr}`)
                .then(response => response.json())
                .then(data => successCallback(data))
                .catch(error => failureCallback(error));
        }
    });
    calendar.render();

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

    document.getElementById('contract-search').addEventListener('input', function() {
        const query = this.value;
        fetch(`/api/contracts/search?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                console.log(data);
            });
    });
}); 