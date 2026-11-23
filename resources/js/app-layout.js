// App shell behaviour: mobile drawer, collapsible sidebar, active nav link, flash auto-hide, live unread badges.
import './upload-limits.js';

const root = document.documentElement;

// Mobile drawer
function toggleMobileMenu(force) {
    const open = root.classList.toggle('mobile-menu-open', force);
    document.querySelectorAll('[data-mobile-menu-toggle]').forEach((el) => el.setAttribute('aria-expanded', String(open)));
}
window.toggleMobileMenu = toggleMobileMenu;
document.addEventListener('click', (e) => {
    if (e.target.closest('[data-mobile-menu-toggle]')) toggleMobileMenu();
});
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && root.classList.contains('mobile-menu-open')) toggleMobileMenu(false);
});

// Collapsible desktop sidebar, remembered across pages (restored early in the layout <head>)
const toggleBtn = document.getElementById('sidebarToggleBtn');
function syncToggle() {
    const collapsed = root.classList.contains('sidebar-collapsed');
    const label = collapsed ? 'Expand sidebar' : 'Collapse sidebar';
    toggleBtn?.setAttribute('title', label);
    toggleBtn?.setAttribute('aria-label', label);
}
toggleBtn?.addEventListener('click', () => {
    const collapsed = root.classList.toggle('sidebar-collapsed');
    localStorage.setItem('sp.sidebar', collapsed ? 'collapsed' : 'expanded');
    syncToggle();
});
syncToggle();

// In the collapsed rail only icons show, so expose each link's text as a tooltip.
document.querySelectorAll('.left-header .nav-buttons .btn, .left-header .sidebar-bottom .btn').forEach((link) => {
    if (!link.title) link.title = link.textContent.replace(/\d+\s*$/, '').trim();
});

// Highlight the current page in the sidebar when the markup did not mark one active.
function markActiveLinks() {
    const here = window.location.href.split(/[?#]/)[0].replace(/\/+$/, '');
    const appRoot = (window.appUrl ? window.appUrl('') : window.location.origin).replace(/\/+$/, '');
    document.querySelectorAll('.nav-buttons').forEach((nav) => {
        const links = [...nav.querySelectorAll('a.btn[href]')];
        if (!links.length || nav.querySelector('a.btn.active')) return;
        let best = null;
        let bestLen = 0;
        links.forEach((a) => {
            const href = a.href.split(/[?#]/)[0].replace(/\/+$/, '');
            // The site root ("Home") only matches itself, not every page under it.
            const isRoot = href === appRoot;
            const matches = here === href || (!isRoot && here.startsWith(href + '/'));
            if (matches && href.length > bestLen) {
                best = a;
                bestLen = href.length;
            }
        });
        best?.classList.add('active');
        best?.setAttribute('aria-current', 'page');
    });
}
markActiveLinks();

// Auto-hide flash alerts after 5 seconds, except those with .alert-static
setTimeout(() => {
    if (!window.bootstrap?.Alert) return;
    document.querySelectorAll('.content > .alert:not(.alert-static)').forEach((alert) => {
        window.bootstrap.Alert.getOrCreateInstance(alert).close();
    });
}, 5000);

// Live unread counters for sidebar badges (<x-nav-count>): one request, every 30s, only while the tab is visible.
const unreadUrl = document.querySelector('meta[name="unread-counts-url"]')?.content;
function updateUnreadBadges() {
    if (!unreadUrl || document.hidden) return;
    fetch(unreadUrl, { headers: { Accept: 'application/json' } })
        .then((r) => (r.ok ? r.json() : null))
        .then((counts) => {
            if (!counts) return;
            document.querySelectorAll('[data-unread]').forEach((badge) => {
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
