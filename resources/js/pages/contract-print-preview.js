// Contract print preview (admin/contracts/pdf.blade.php with $preview).
//  - Print: the browser print dialog, for paper.
//  - Save as PDF: a normal link to the server-rendered PDF (ContractController@download), so it only downloads when clicked.
//    If no PDF URL was provided it falls back to the print dialog, where the user picks "Save as PDF".
//  - Close Preview: closes a script-opened window, otherwise the link goes back to the contract.
document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('print-contract')?.addEventListener('click', function () {
        window.print();
    });

    document.querySelector('#save-pdf[data-print-fallback]')?.addEventListener('click', function (e) {
        e.preventDefault();
        window.print();
    });

    document.getElementById('close-preview')?.addEventListener('click', function (e) {
        if (window.opener && !window.opener.closed) {
            e.preventDefault();
            window.close();
        }
    });
});
