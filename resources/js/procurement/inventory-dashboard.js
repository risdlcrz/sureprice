// Inventory Management: client-side filter of the rows on the current page.
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('inventorySearch');
    const searchInput = document.getElementById('searchInput');
    if (!searchInput) return;

    const filter = function () {
        const term = searchInput.value.trim().toLowerCase();
        document.querySelectorAll('#inventoryTable tbody tr').forEach(function (row) {
            row.style.display = row.textContent.toLowerCase().includes(term) ? '' : 'none';
        });
    };

    // 'input' also fires when the search field's clear (×) button is used
    searchInput.addEventListener('input', filter);
    form?.addEventListener('submit', function (e) {
        e.preventDefault();
        filter();
    });
});
