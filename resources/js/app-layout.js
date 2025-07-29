function toggleMobileMenu() {
    const mobileMenu = document.getElementById('mobileMenu');
    mobileMenu.classList.toggle('active');
}

// Auto-hide alerts after 5 seconds, except those with .alert-static
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert:not(.alert-static)');
        alerts.forEach(function(alert) {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
});

window.PUSHER_APP_KEY = "{{ env('PUSHER_APP_KEY') }}";
window.PUSHER_APP_CLUSTER = "{{ env('PUSHER_APP_CLUSTER') }}";

// Sidebar minimization logic
document.addEventListener('DOMContentLoaded', function() {
    const appContainer = document.getElementById('appContainer');
    const sidebarToggleBtn = document.getElementById('sidebarToggleBtn');
    const sidebarToggleIcon = document.getElementById('sidebarToggleIcon');
    let collapsed = false;
    sidebarToggleBtn.addEventListener('click', function() {
        collapsed = !collapsed;
        if (collapsed) {
            appContainer.classList.add('sidebar-collapsed');
            sidebarToggleIcon.classList.remove('fa-angle-double-left');
            sidebarToggleIcon.classList.add('fa-angle-double-right');
        } else {
            appContainer.classList.remove('sidebar-collapsed');
            sidebarToggleIcon.classList.remove('fa-angle-double-right');
            sidebarToggleIcon.classList.add('fa-angle-double-left');
        }
    });
});

function updateNotificationBadge() {
    fetch('/api/unread-notifications-count')
        .then(response => response.json())
        .then(data => {
            // Update all notification badges
            document.querySelectorAll('.notification-badge, .badge.bg-danger').forEach(badge => {
                if (data.count > 0) {
                    badge.textContent = data.count;
                    badge.style.display = '';
                    badge.style.visibility = 'visible';
                } else {
                    badge.style.display = 'none';
                }
            });
        })
        .catch(error => {
            console.error('Error updating notification badge:', error);
        });
}
setInterval(updateNotificationBadge, 10000); // Poll every 10 seconds
// Also update on page load
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', updateNotificationBadge);
} else {
    updateNotificationBadge();
} 