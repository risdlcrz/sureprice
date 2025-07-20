AOS.init({
    duration: 700,
    once: true
});
document.getElementById('requestAllServicesBtn').addEventListener('click', function() {
    const checked = Array.from(document.querySelectorAll('.service-checkbox:checked')).map(cb => cb.value);
    if (checked.length === 0) {
        alert('Please select at least one service.');
        return;
    }
    // Redirect to quotation form with selected categories as query params
    const params = checked.map(cat => 'category[]=' + encodeURIComponent(cat)).join('&');
    window.location.href = window.landingCatalogueQuotationRoute + '?' + params;
}); 