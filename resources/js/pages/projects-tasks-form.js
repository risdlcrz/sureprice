// Task form (project tasks + project timeline): date bounds, status/progress sync, scope options per room.
const $ = (id) => document.getElementById(id);
const start = $('start_date');
const due = $('due_date');
const status = $('status');
const progress = $('progress');
const room = $('room_id');
const scope = $('scope_type_id');

if (start && due) {
    if (!start.value && !status) start.value = new Date().toISOString().slice(0, 10); // new task: default to today
    const syncDue = () => {
        due.min = start.value;
        if (due.value && due.value < start.value) due.value = start.value;
    };
    start.addEventListener('change', syncDue);
    syncDue();
}

if (status && progress) {
    status.addEventListener('change', () => {
        if (status.value === 'completed') progress.value = 100;
        else if (status.value === 'pending') progress.value = 0;
    });
    progress.addEventListener('change', () => {
        const p = Number(progress.value);
        if (p >= 100) status.value = 'completed';
        else if (p > 0 && status.value === 'pending') status.value = 'in_progress';
    });
}

if (room && scope) {
    const filterScopes = () => {
        scope.querySelectorAll('option[data-room]').forEach((o) => {
            o.hidden = Boolean(room.value) && o.dataset.room !== room.value;
            if (o.hidden && o.selected) scope.value = '';
        });
    };
    room.addEventListener('change', filterScopes);
    filterScopes();
}
