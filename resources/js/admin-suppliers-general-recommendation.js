document.getElementById('general-recommendation-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const form = e.target;
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