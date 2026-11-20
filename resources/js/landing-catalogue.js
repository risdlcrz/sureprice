// Landing page: reveal-on-scroll and the "combine services" quotation shortcut.

// Fade sections in as they scroll into view (no-op when motion is reduced or IntersectionObserver is missing).
const revealEls = document.querySelectorAll('.lp-reveal');
if ('IntersectionObserver' in window && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    document.documentElement.classList.add('lp-animate');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            }
        });
    }, { rootMargin: '0px 0px -8% 0px', threshold: 0.08 });
    revealEls.forEach((el) => observer.observe(el));
}

// Combine several services into one quotation request.
const requestBtn = document.getElementById('requestAllServicesBtn');
const errorEl = document.getElementById('allServicesError');
requestBtn?.addEventListener('click', () => {
    const checked = [...document.querySelectorAll('.service-checkbox:checked')].map((cb) => cb.value);
    if (!checked.length) {
        errorEl?.classList.remove('d-none');
        return;
    }
    const params = checked.map((cat) => 'category[]=' + encodeURIComponent(cat)).join('&');
    window.location.href = window.__page.quotationUrl + '?' + params;
});
document.querySelectorAll('.service-checkbox').forEach((cb) => cb.addEventListener('change', () => errorEl?.classList.add('d-none')));
