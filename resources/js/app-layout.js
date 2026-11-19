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

// Live unread counters for sidebar badges (<x-nav-count>): one request, every 30s, only while the tab is visible.
const unreadUrl = document.querySelector('meta[name="unread-counts-url"]')?.content;
function updateUnreadBadges() {
    if (!unreadUrl || document.hidden) return;
    fetch(unreadUrl, { headers: { Accept: 'application/json' } })
        .then(r => (r.ok ? r.json() : null))
        .then(counts => {
            if (!counts) return;
            document.querySelectorAll('[data-unread]').forEach(badge => {
                const n = counts[badge.dataset.unread] ?? 0;
                badge.textContent = n;
                badge.style.display = n > 0 ? '' : 'none';
            });
        })
        .catch(() => {});
}
if (unreadUrl) {
    setInterval(updateUnreadBadges, 30000);
    document.addEventListener('visibilitychange', updateUnreadBadges);
}
