document.addEventListener('DOMContentLoaded', function() {
    // Date validation
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');

    startDateInput.addEventListener('change', function() {
        endDateInput.min = this.value;
    });

    endDateInput.addEventListener('change', function() {
        startDateInput.max = this.value;
    });
}); 