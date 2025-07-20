document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('general-recommendation-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const params = new URLSearchParams(new FormData(form)).toString();
            const url = window.location.pathname + '?' + params;
            fetch(url, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                }
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('general-recommendation-tables').innerHTML = data.html;
            });
        });
    }
}); 