<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');

require 'supplier_db.php';

header('Content-Type: application/json');

try {
    if (!isset($_GET['id'])) {
        throw new Exception('Supplier ID is required');
    }

    $supplierId = intval($_GET['id']);
    if ($supplierId <= 0) {
        throw new Exception('Invalid supplier ID');
    }

    $stmt = $pdo->prepare("
        SELECT 
            id,
            company,
            materials,
            price,
            contact_person,
            designation,
            email,
            mobile_number,
            telephone_number,
            address,
            business_reg_no,
            supplier_type,
            business_size,
            years_operation,
            payment_terms,
            vat_registered,
            use_sureprice,
            bank_name,
            account_name,
            account_number,
            dti_sec_registration_path,
            accreditation_docs_path,
            mayors_permit_path,
            valid_id_path,
            company_profile_path,
            price_list_path
        FROM suppliers 
        WHERE id = ?
    ");

    $stmt->execute([$supplierId]);
    $supplier = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$supplier) {
        throw new Exception('Supplier not found');
    }

    // Convert null values to empty strings for optional fields
    $optionalFields = [
        'designation', 'telephone_number', 'business_reg_no', 
        'years_operation', 'payment_terms', 'bank_name', 
        'account_name', 'account_number', 'dti_sec_registration_path',
        'accreditation_docs_path', 'mayors_permit_path', 'valid_id_path',
        'company_profile_path', 'price_list_path'
    ];

    foreach ($optionalFields as $field) {
        if (is_null($supplier[$field])) {
            $supplier[$field] = '';
        }
    }

    // Ensure boolean fields have proper values
    $supplier['vat_registered'] = $supplier['vat_registered'] ?? 'No';
    $supplier['use_sureprice'] = $supplier['use_sureprice'] ?? 'No';

    // Log successful fetch
    error_log("Successfully fetched supplier data for ID: " . $supplierId);
    
    echo json_encode([
        'success' => true,
        'data' => $supplier
    ]);

} catch (Exception $e) {
    error_log("Error in get_supplier.php: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
} 