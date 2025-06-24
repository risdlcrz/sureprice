
function showAlertAndRedirect(message, redirectUrl) {
    alert(message);
    window.location.replace(redirectUrl);
}

// Check for import status on page load
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    
    if (urlParams.has('import_success')) {
        showAlertAndRedirect('CSV Import completed successfully.', 'information-management.php');
    } 
    else if (urlParams.has('import_partial')) {
        const duplicates = urlParams.get('duplicates');
        showAlertAndRedirect(`Import partially completed. Some entries were duplicates:\n\n${duplicates}`, 'information-management.php');
    }
    else if (urlParams.has('import_failed')) {
        const duplicates = urlParams.get('duplicates');
        showAlertAndRedirect(`No data imported. All entries were duplicates:\n\n${duplicates}`, 'information-management.php');
    }
    else if (urlParams.has('invalid_csv')) {
        showAlertAndRedirect('Invalid CSV format.', 'information-management.php');
    }
    else if (urlParams.has('upload_error')) {
        showAlertAndRedirect('Error uploading or reading the file.', 'information-management.php');
    }
});