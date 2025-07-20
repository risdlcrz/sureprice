document.addEventListener('DOMContentLoaded', function() {
    // Mark notification as read on click
    document.querySelectorAll('.notification-item').forEach(function(item) {
        item.addEventListener('click', function(e) {
            var id = this.getAttribute('data-id');
            var isRead = this.getAttribute('data-read') === '1';
            if (!isRead) {
                fetch('/manager/notifications/' + id + '/mark-as-read', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': window.csrfToken,
                        'Accept': 'application/json',
                    },
                }).then(res => {
                    if (res.ok) {
                        this.classList.remove('fw-bold', 'bg-white');
                        this.classList.add('bg-light');
                        this.setAttribute('data-read', '1');
                        var badge = this.querySelector('.badge.bg-warning');
                        if (badge) badge.remove();
                    }
                });
            }
        });
    });
    // Clear read notifications
    document.getElementById('clearReadForm').addEventListener('submit', function(e) {
        e.preventDefault();
        fetch(this.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': window.csrfToken,
                'Accept': 'application/json',
            },
        }).then(res => res.json()).then(data => {
            if (data.success) {
                document.querySelectorAll('.notification-item[data-read="1"]').forEach(function(item) {
                    item.remove();
                });
            }
        });
    });
}); 