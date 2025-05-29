// Inside contract_form.php - within processContractForm() function

// After handling signatures but before saving contract
if (!empty($signatures['client'])) {
    $full_path = $_SERVER['DOCUMENT_ROOT'] . $signatures['client'];
    if (!file_exists($full_path)) {
        error_log("Signature file missing after save: " . $full_path);
        $signatures['client'] = ''; // Clear invalid path
    }
}

if (!empty($signatures['contractor'])) {
    $full_path = $_SERVER['DOCUMENT_ROOT'] . $signatures['contractor'];
    if (!file_exists($full_path)) {
        error_log("Signature file missing after save: " . $full_path);
        $signatures['contractor'] = ''; // Clear invalid path
    }
}