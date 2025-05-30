<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'sureprice');

// Contractor Information (would normally come from database/session)
define('CONTRACTOR_NAME', 'John Doe');
define('CONTRACTOR_COMPANY', 'ABC Construction');
define('CONTRACTOR_STREET', '123 Contractor St');
define('CONTRACTOR_CITY', 'Contractor City');
define('CONTRACTOR_STATE', 'CA');
define('CONTRACTOR_POSTAL', '90001');
define('CONTRACTOR_EMAIL', 'contractor@example.com');
define('CONTRACTOR_PHONE', '(123) 456-7890');

// Create database connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create tables if they don't exist
createTables($conn);

// Handle edit mode
$edit_mode = isset($_GET['edit']);
$existing_contract = null;
$existing_client = null;
$existing_property = null;
$existing_items = [];
$existing_contractor = null;
$existing_client_signature = '';
$existing_contractor_signature = '';

if ($edit_mode) {
    $contract_id = $conn->real_escape_string($_GET['edit']);
    
    // Get existing contract
    $sql = "SELECT * FROM contracts WHERE contract_id = '$contract_id'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $existing_contract = $result->fetch_assoc();

        // Get contractor
        $contractor_id = $existing_contract['contractor_id'];
        $sql = "SELECT * FROM parties WHERE id = $contractor_id";
        $result = $conn->query($sql);
        $existing_contractor = ($result->num_rows > 0) ? $result->fetch_assoc() : [];
        
        // Get client
        $client_id = $existing_contract['client_id'];
        $sql = "SELECT * FROM parties WHERE id = $client_id";
        $result = $conn->query($sql);
        $existing_client = $result->fetch_assoc();
        
        // Get property
        $property_id = $existing_contract['property_id'];
        $sql = "SELECT * FROM properties WHERE id = $property_id";
        $result = $conn->query($sql);
        $existing_property = $result->fetch_assoc();
        
        // Get items
        $sql = "SELECT * FROM contract_items WHERE contract_id = {$existing_contract['id']}";
        $result = $conn->query($sql);
        while ($row = $result->fetch_assoc()) {
            $existing_items[] = $row;
        }
        
        // Get signatures
        $existing_client_signature = $existing_contract['client_signature'] ?? '';
        $existing_contractor_signature = $existing_contract['contractor_signature'] ?? '';
    } else {
        $edit_mode = false;
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    processContractForm($conn);
}

// Handle client search AJAX request
if (isset($_GET['search_client']) && !empty($_GET['search_term'])) {
    searchClient($conn);
    exit();
}

/**
 * Searches for clients in the database
 */
function searchClient($conn) {
    $search_term = $conn->real_escape_string($_GET['search_term']);
    
    $sql = "SELECT * FROM parties 
            WHERE type = 'client' 
            AND (name LIKE '%$search_term%' OR email LIKE '%$search_term%') 
            LIMIT 10";
    
    $result = $conn->query($sql);
    $clients = [];
    
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $clients[] = $row;
        }
    }
    
    header('Content-Type: application/json');
    echo json_encode($clients);
}

/**
 * Creates the required database tables with improved structure
 */
function createTables($conn) {
    // Create parties table (for both clients and contractors)
    $sql = "CREATE TABLE IF NOT EXISTS parties (
        id INT AUTO_INCREMENT PRIMARY KEY,
        type ENUM('contractor', 'client') NOT NULL,
        entity_type ENUM('company', 'person') NOT NULL,
        name VARCHAR(100) NOT NULL,
        company_name VARCHAR(100),
        street VARCHAR(100) NOT NULL,
        city VARCHAR(50) NOT NULL,
        state VARCHAR(50) NOT NULL,
        postal VARCHAR(20) NOT NULL,
        email VARCHAR(100) NOT NULL,
        phone VARCHAR(20) NOT NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    
    if (!$conn->query($sql)) {
        die("Error creating parties table: " . $conn->error);
    }

    // Create properties table
    $sql = "CREATE TABLE IF NOT EXISTS properties (
        id INT AUTO_INCREMENT PRIMARY KEY,
        street VARCHAR(100) NOT NULL,
        city VARCHAR(50) NOT NULL,
        state VARCHAR(50) NOT NULL,
        postal VARCHAR(20) NOT NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    
    if (!$conn->query($sql)) {
        die("Error creating properties table: " . $conn->error);
    }

    // Create contracts table (main table with references)
    $sql = "CREATE TABLE IF NOT EXISTS contracts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        contract_id VARCHAR(10) NOT NULL UNIQUE,
        contractor_id INT NOT NULL,
        client_id INT NOT NULL,
        property_id INT NOT NULL,
        scope_of_work TEXT NOT NULL,
        scope_description TEXT,
        start_date DATE NOT NULL,
        end_date DATE NOT NULL,
        total_amount DECIMAL(10,2) NOT NULL,
        jurisdiction VARCHAR(100) NOT NULL,
        contract_terms TEXT NOT NULL,
        client_signature VARCHAR(255),
        contractor_signature VARCHAR(255),
        status ENUM('draft', 'pending', 'approved', 'rejected') DEFAULT 'draft',
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (contractor_id) REFERENCES parties(id),
        FOREIGN KEY (client_id) REFERENCES parties(id),
        FOREIGN KEY (property_id) REFERENCES properties(id)
    )";
    
    if (!$conn->query($sql)) {
        die("Error creating contracts table: " . $conn->error);
    }

    // Create contract_items table
    $sql = "CREATE TABLE IF NOT EXISTS contract_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        contract_id INT NOT NULL,
        description TEXT NOT NULL,
        quantity DECIMAL(10,2) NOT NULL,
        amount DECIMAL(10,2) NOT NULL,
        total DECIMAL(10,2) NOT NULL,
        FOREIGN KEY (contract_id) REFERENCES contracts(id)
    )";
    
    if (!$conn->query($sql)) {
        die("Error creating contract_items table: " . $conn->error);
    }

    // Create directories if they don't exist
    if (!file_exists('uploads')) {
        mkdir('uploads', 0777, true);
    }
    if (!file_exists('contracts')) {
        mkdir('contracts', 0777, true);
    }
}

/**
 * Processes the contract form submission
 */
function processContractForm($conn) {
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Save contractor (or get existing)
        $contractor_id = saveParty($conn, 'contractor', [
            'entity_type' => 'company',
            'name' => CONTRACTOR_NAME,
            'company_name' => CONTRACTOR_COMPANY,
            'street' => CONTRACTOR_STREET,
            'city' => CONTRACTOR_CITY,
            'state' => CONTRACTOR_STATE,
            'postal' => CONTRACTOR_POSTAL,
            'email' => CONTRACTOR_EMAIL,
            'phone' => CONTRACTOR_PHONE
        ], true);
        
        // Save client
        $client_id = saveParty($conn, 'client', [
            'entity_type' => !empty($_POST['company_name']) ? 'company' : 'person',
            'name' => !empty($_POST['company_name']) ? $_POST['company_name'] : $_POST['contact_person'],
            'company_name' => $_POST['company_name'],
            'street' => $_POST['client_street'],
            'city' => $_POST['client_city'],
            'state' => $_POST['client_state'],
            'postal' => $_POST['client_postal'],
            'email' => $_POST['client_email'],
            'phone' => $_POST['client_phone']
        ]);
        
        // Save property
        $property_id = saveProperty($conn, [
            'street' => $_POST['property_street'],
            'city' => $_POST['property_city'],
            'state' => $_POST['property_state'],
            'postal' => $_POST['property_postal']
        ]);
        
        // Process scope of work
        $scope_of_work = processScopeOfWork();
        
        // Process item details
        $item_details = processItemDetails();
        
        // Handle signatures
        $signatures = handleSignatureUploads();
        
        // Use existing contract ID if in edit mode
        $contract_id = isset($_POST['existing_contract_id']) ? $_POST['existing_contract_id'] : generateContractId($conn);
        
        // Save contract
        $contract_data = [
            'contract_id' => $contract_id,
            'contractor_id' => $contractor_id,
            'client_id' => $client_id,
            'property_id' => $property_id,
            'scope_of_work' => $scope_of_work['work'],
            'scope_description' => $scope_of_work['description'],
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
            'total_amount' => $GLOBALS['total_amount'],
            'jurisdiction' => $_POST['jurisdiction'],
            'contract_terms' => $_POST['contract_paragraphs'],
            'client_signature' => $signatures['client'],
            'contractor_signature' => $signatures['contractor']
        ];
        
        $contract_db_id = saveContract($conn, $contract_data);
        
        // Save contract items
        saveContractItems($conn, $contract_db_id, $item_details);
        
        // Commit transaction
        $conn->commit();
        
        // Redirect to view contract page
        header("Location: view_contract.php?id=" . $contract_id);
        exit();
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        $GLOBALS['error'] = "Error saving contract: " . $e->getMessage();
    }
}

/**
 * Handles signature file uploads
 */
function handleSignatureUploads() {
    $signatures = [
        'client' => $_POST['existing_client_signature'] ?? '',
        'contractor' => $_POST['existing_contractor_signature'] ?? ''
    ];
    
    // Handle file uploads
    if (isset($_FILES['client_signature']) && $_FILES['client_signature']['error'] == UPLOAD_ERR_OK) {
        $signatures['client'] = uploadFile('client_signature');
    }
    
    if (isset($_FILES['contractor_signature']) && $_FILES['contractor_signature']['error'] == UPLOAD_ERR_OK) {
        $signatures['contractor'] = uploadFile('contractor_signature');
    }
    
    // Handle canvas signatures if present
    if (!empty($_POST['client_signature_data'])) {
        $signatures['client'] = saveCanvasSignature($_POST['client_signature_data'], 'client');
    }
    
    if (!empty($_POST['contractor_signature_data'])) {
        $signatures['contractor'] = saveCanvasSignature($_POST['contractor_signature_data'], 'contractor');
    }
    
    return $signatures;
}

/**
 * Saves a party (client or contractor) to the database
 */
function saveParty($conn, $type, $data, $updateIfExists = false) {
    if ($updateIfExists) {
        // Check if party already exists
        $sql = "SELECT id FROM parties WHERE type = ? AND email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ss', $type, $data['email']);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['id'];
        }
    }
    
    $sql = "INSERT INTO parties (
        type, entity_type, name, company_name, street, city, state, postal, email, phone
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        'ssssssssss',
        $type,
        $data['entity_type'],
        $data['name'],
        $data['company_name'],
        $data['street'],
        $data['city'],
        $data['state'],
        $data['postal'],
        $data['email'],
        $data['phone']
    );
    
    if (!$stmt->execute()) {
        throw new Exception("Error saving party: " . $stmt->error);
    }
    
    return $conn->insert_id;
}

/**
 * Saves a property to the database
 */
function saveProperty($conn, $data) {
    $sql = "INSERT INTO properties (
        street, city, state, postal
    ) VALUES (?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        'ssss',
        $data['street'],
        $data['city'],
        $data['state'],
        $data['postal']
    );
    
    if (!$stmt->execute()) {
        throw new Exception("Error saving property: " . $stmt->error);
    }
    
    return $conn->insert_id;
}

/**
 * Processes scope of work with improved "Other" option handling
 */
function processScopeOfWork() {
    $selected_work = [];
    $description = $_POST['scope_description'] ?? '';
    
    foreach ($_POST['scope_of_work'] as $work) {
        if ($work === 'Other') {
            // Get the custom text from the other work input
            $other_text = trim($_POST['other_work_text'] ?? '');
            if (!empty($other_text)) {
                $selected_work[] = $other_text;
            }
        } else {
            $selected_work[] = $work;
        }
    }
    
    return [
        'work' => implode(', ', $selected_work),
        'description' => $description
    ];
}

/**
 * Processes item details from the form
 */
function processItemDetails() {
    $item_details = [];
    $total_amount = 0;
    
    if (isset($_POST['item_description'])) {
        foreach ($_POST['item_description'] as $index => $description) {
            $quantity = floatval($_POST['item_quantity'][$index]);
            $amount = floatval($_POST['item_amount'][$index]);
            $total = $quantity * $amount;
            
            $item_details[] = [
                'description' => $description,
                'quantity' => $quantity,
                'amount' => $amount,
                'total' => $total
            ];
            
            $total_amount += $total;
        }
    }
    
    $GLOBALS['total_amount'] = $total_amount;
    return $item_details;
}

/**
 * Generates a unique contract ID in CTXXXX format
 */
function generateContractId($conn) {
    // Get the latest contract ID
    $sql = "SELECT MAX(CAST(SUBSTRING(contract_id, 3) AS UNSIGNED)) as max_num FROM contracts";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $next_num = ($row['max_num']) ? $row['max_num'] + 1 : 1;
    
    return 'CT' . str_pad($next_num, 4, '0', STR_PAD_LEFT);
}

/**
 * Saves canvas signature to file
 */
function saveCanvasSignature($data_url, $prefix) {
    $image_data = explode(',', $data_url);
    $image = base64_decode($image_data[1]);
    
    // Ensure uploads directory exists
    $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    $filename = $prefix . '_signature_' . time() . '.png';
    $filepath = $upload_dir . $filename;
    
    if (file_put_contents($filepath, $image)) {
        return '/uploads/' . $filename; // Return web-accessible path
    }
    
    return '';
}

/**
 * Processes uploaded files
 */
function uploadFile($field_name) {
    if ($_FILES[$field_name]['error'] == UPLOAD_ERR_OK) {
        $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/';
        
        // Create directory if it doesn't exist
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $ext = pathinfo($_FILES[$field_name]['name'], PATHINFO_EXTENSION);
        $filename = $field_name . '_' . time() . '.' . $ext;
        $target_file = $upload_dir . $filename;
        
        if (move_uploaded_file($_FILES[$field_name]['tmp_name'], $target_file)) {
            return '/uploads/' . $filename; // Return web-accessible path
        } else {
            error_log("Failed to move uploaded file to: " . $target_file);
        }
    }
    return '';
}

/**
 * Saves contract to database
 */
function saveContract($conn, $data) {
    // Check if we're updating an existing contract
    if (isset($_POST['existing_contract_id'])) {
        $sql = "UPDATE contracts SET 
            contractor_id = ?,
            client_id = ?,
            property_id = ?,
            scope_of_work = ?,
            scope_description = ?,
            start_date = ?,
            end_date = ?,
            total_amount = ?,
            jurisdiction = ?,
            contract_terms = ?,
            client_signature = ?,
            contractor_signature = ?,
            updated_at = CURRENT_TIMESTAMP
            WHERE contract_id = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            'iiissssdsssss',
            $data['contractor_id'],
            $data['client_id'],
            $data['property_id'],
            $data['scope_of_work'],
            $data['scope_description'],
            $data['start_date'],
            $data['end_date'],
            $data['total_amount'],
            $data['jurisdiction'],
            $data['contract_terms'],
            $data['client_signature'],
            $data['contractor_signature'],
            $data['contract_id']
        );
        
        if (!$stmt->execute()) {
            throw new Exception("Error updating contract: " . $stmt->error);
        }
        
        // Get the ID of the updated contract
        $sql = "SELECT id FROM contracts WHERE contract_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $data['contract_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return $row['id'];
    } else {
        // Insert new contract
        $sql = "INSERT INTO contracts (
            contract_id, contractor_id, client_id, property_id, scope_of_work, scope_description,
            start_date, end_date, total_amount, jurisdiction, contract_terms, 
            client_signature, contractor_signature, status
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $status = 'draft'; // Default status
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            'siiissssdsssss',
            $data['contract_id'],
            $data['contractor_id'],
            $data['client_id'],
            $data['property_id'],
            $data['scope_of_work'],
            $data['scope_description'],
            $data['start_date'],
            $data['end_date'],
            $data['total_amount'],
            $data['jurisdiction'],
            $data['contract_terms'],
            $data['client_signature'],
            $data['contractor_signature'],
            $status
        );
        
        if (!$stmt->execute()) {
            throw new Exception("Error saving contract: " . $stmt->error);
        }
        
        return $conn->insert_id;
    }
}

/**
 * Saves contract items to database
 */
function saveContractItems($conn, $contract_id, $items) {
    // First delete existing items if updating
    if (isset($_POST['existing_contract_id'])) {
        $sql = "DELETE FROM contract_items WHERE contract_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $contract_id);
        if (!$stmt->execute()) {
            throw new Exception("Error deleting existing contract items: " . $stmt->error);
        }
    }
    
    // Then insert new items
    $sql = "INSERT INTO contract_items (
        contract_id, description, quantity, amount, total
    ) VALUES (?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    
    foreach ($items as $item) {
        $stmt->bind_param(
            'isddd',
            $contract_id,
            $item['description'],
            $item['quantity'],
            $item['amount'],
            $item['total']
        );
        
        if (!$stmt->execute()) {
            throw new Exception("Error saving contract item: " . $stmt->error);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $edit_mode ? 'Edit' : 'Create'; ?> Contract Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            background-image: url('https://images.unsplash.com/photo-1564501049412-61c2a3083791?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1932&q=80');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-color: rgba(255, 255, 255, 0.9);
            background-blend-mode: overlay;
        }
        .container {
            max-width: 1500px;
            background-color: rgba(255, 255, 255, 0.95);
            padding: 30px;
            padding-right: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            margin-top: 30px;
            margin-bottom: 30px;
        }
        .contract-section {
            margin-bottom: 30px;
            padding: 15px;
            background-color: #f8f9fa;
        }
        .contract-section h4 {
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 5px;
        }
        .signature-pad {
            border: 1px solid #ddd;
            background-color: white;
            margin-bottom: 10px;
        }
        canvas {
            width: 100%;
            height: 150px;
            background-color: #f8f9fa;
        }
        .form-section {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        .other-work-input {
            margin-top: 5px;
            display: none;
        }
        .scope-work-option {
            margin-bottom: 5px;
        }
        .postal-code {
            width: 100px;
        }
        .search-results {
            position: absolute;
            z-index: 1000;
            width: 100%;
            max-height: 200px;
            overflow-y: auto;
            background: white;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            display: none;
        }
        .search-result-item {
            padding: 8px 12px;
            cursor: pointer;
        }
        .search-result-item:hover {
            background-color: #f8f9fa;
        }
        .form-control, .form-select {
            background-color: #fff;
        }
        h1, h2, h3, h4 {
            color: #2c3e50;
        }
        .container .row {
            padding-right: 15px;
        }

        .signature-preview {
            max-width: 200px;
            max-height: 100px;
            border: 1px solid #ddd;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container mt-4 mb-5">
        <h1 class="text-center mb-4"><?php echo $edit_mode ? 'Edit' : 'Create'; ?> Contract Agreement</h1>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form method="POST" enctype="multipart/form-data" id="contractForm">
            <!-- Contractor Information -->
            <div class="form-section">
                <h2>Contractor Information</h2>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="contractor_name" class="form-label">Contractor Name</label>
                        <input type="text" class="form-control" id="contractor_name" name="contractor_name" 
                            value="<?php echo $edit_mode ? (isset($existing_contractor['name']) ? htmlspecialchars($existing_contractor['name']) : htmlspecialchars(CONTRACTOR_NAME)) : htmlspecialchars(CONTRACTOR_NAME); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="contractor_company" class="form-label">Company Name</label>
                        <input type="text" class="form-control" id="contractor_company" name="contractor_company" 
                            value="<?php echo $edit_mode ? (isset($existing_contractor['company_name']) ? htmlspecialchars($existing_contractor['company_name']) : htmlspecialchars(CONTRACTOR_COMPANY)) : htmlspecialchars(CONTRACTOR_COMPANY); ?>" required>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="contractor_street" class="form-label">Street Address</label>
                        <input type="text" class="form-control" id="contractor_street" name="contractor_street" 
                            value="<?php echo $edit_mode ? (isset($existing_contractor['street']) ? htmlspecialchars($existing_contractor['street']) : htmlspecialchars(CONTRACTOR_STREET)) : htmlspecialchars(CONTRACTOR_STREET); ?>" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="contractor_city" class="form-label">City</label>
                        <input type="text" class="form-control" id="contractor_city" name="contractor_city" 
                            value="<?php echo $edit_mode ? (isset($existing_contractor['city']) ? htmlspecialchars($existing_contractor['city']) : htmlspecialchars(CONTRACTOR_CITY)) : htmlspecialchars(CONTRACTOR_CITY); ?>" required>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="contractor_state" class="form-label">State/Province</label>
                        <input type="text" class="form-control" id="contractor_state" name="contractor_state" 
                            value="<?php echo $edit_mode ? (isset($existing_contractor['state']) ? htmlspecialchars($existing_contractor['state']) : htmlspecialchars(CONTRACTOR_STATE)) : htmlspecialchars(CONTRACTOR_STATE); ?>" required>
                    </div>
                    <div class="col-md-1 mb-3">
                        <label for="contractor_postal" class="form-label">Postal Code</label>
                        <input type="text" class="form-control postal-code" id="contractor_postal" name="contractor_postal" 
                            value="<?php echo $edit_mode ? (isset($existing_contractor['postal']) ? htmlspecialchars($existing_contractor['postal']) : htmlspecialchars(CONTRACTOR_POSTAL)) : htmlspecialchars(CONTRACTOR_POSTAL); ?>" pattern="[0-9\-]*" title="Only numbers and hyphens are allowed" required>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="contractor_email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="contractor_email" name="contractor_email" 
                            value="<?php echo $edit_mode ? (isset($existing_contractor['email']) ? htmlspecialchars($existing_contractor['email']) : htmlspecialchars(CONTRACTOR_EMAIL)) : htmlspecialchars(CONTRACTOR_EMAIL); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="contractor_phone" class="form-label">Phone Number</label>
                        <input type="tel" class="form-control" id="contractor_phone" name="contractor_phone" 
                            value="<?php echo $edit_mode ? (isset($existing_contractor['phone']) ? htmlspecialchars($existing_contractor['phone']) : htmlspecialchars(CONTRACTOR_PHONE)) : htmlspecialchars(CONTRACTOR_PHONE); ?>" 
                            pattern="[0-9()\- +]*" title="Only numbers, parentheses, hyphens, and plus signs are allowed" required>
                    </div>
                </div>
            </div>
            
            <!-- Client Information -->
            <div class="form-section">
                <h2>Client Information</h2>
                                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="input-group">
                            <input type="text" class="form-control" id="client_search" placeholder="Search client by name or email">
                            <button class="btn btn-outline-secondary" type="button" id="client_search_btn">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                        <div class="search-results" id="client_search_results"></div>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="company_name" class="form-label">Company Name</label>
                        <input type="text" class="form-control" id="company_name" name="company_name" value="<?php echo $edit_mode ? htmlspecialchars($existing_client['company_name'] ?? '') : ''; ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="contact_person" class="form-label">Contact Person</label>
                        <input type="text" class="form-control" id="contact_person" name="contact_person" value="<?php echo $edit_mode ? htmlspecialchars($existing_client['entity_type'] === 'person' ? $existing_client['name'] : '') : ''; ?>">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="client_street" class="form-label">Street Address</label>
                        <input type="text" class="form-control" id="client_street" name="client_street" value="<?php echo $edit_mode ? htmlspecialchars($existing_client['street'] ?? '') : ''; ?>" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="client_city" class="form-label">City</label>
                        <input type="text" class="form-control" id="client_city" name="client_city" value="<?php echo $edit_mode ? htmlspecialchars($existing_client['city'] ?? '') : ''; ?>" required>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="client_state" class="form-label">State/Province</label>
                        <input type="text" class="form-control" id="client_state" name="client_state" value="<?php echo $edit_mode ? htmlspecialchars($existing_client['state'] ?? '') : ''; ?>" required>
                    </div>
                    <div class="col-md-1 mb-3">
                        <label for="client_postal" class="form-label">Postal Code</label>
                        <input type="text" class="form-control postal-code" id="client_postal" name="client_postal" 
                            value="<?php echo $edit_mode ? htmlspecialchars($existing_client['postal'] ?? '') : htmlspecialchars(CONTRACTOR_POSTAL); ?>" 
                            pattern="[0-9\-]*" title="Only numbers and hyphens are allowed" required>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="client_email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="client_email" name="client_email" value="<?php echo $edit_mode ? htmlspecialchars($existing_client['email'] ?? '') : ''; ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="client_phone" class="form-label">Phone Number</label>
                        <input type="tel" class="form-control" id="client_phone" name="client_phone" 
                            value="<?php echo $edit_mode ? htmlspecialchars($existing_client['phone'] ?? '') : ''; ?>" 
                            pattern="[0-9()\- +]*" title="Only numbers, parentheses, hyphens, and plus signs are allowed" required>
                    </div>
                </div>
            </div>
            
            <!-- Property Information -->
            <div class="form-section">
                <h2>Property Information</h2>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="property_street" class="form-label">Street Address</label>
                        <input type="text" class="form-control" id="property_street" name="property_street" value="<?php echo $edit_mode ? htmlspecialchars($existing_property['street'] ?? '') : ''; ?>" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="property_city" class="form-label">City</label>
                        <input type="text" class="form-control" id="property_city" name="property_city" value="<?php echo $edit_mode ? htmlspecialchars($existing_property['city'] ?? '') : ''; ?>" required>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="property_state" class="form-label">State/Province</label>
                        <input type="text" class="form-control" id="property_state" name="property_state" value="<?php echo $edit_mode ? htmlspecialchars($existing_property['state'] ?? '') : ''; ?>" required>
                    </div>
                    <div class="col-md-1 mb-3">
                        <label for="property_postal" class="form-label">Postal Code</label>
                        <input type="text" class="form-control postal-code" id="property_postal" name="property_postal" 
                            value="<?php echo $edit_mode ? htmlspecialchars($existing_property['postal'] ?? '') : htmlspecialchars(CONTRACTOR_POSTAL); ?>" 
                            pattern="[0-9\-]*" title="Only numbers and hyphens are allowed" required>
                    </div>
                </div>
            </div>
            
            <!-- Scope of Work -->
            <div class="form-section">
                <h2>Scope of Work</h2>
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label class="form-label">Select Scope of Work:</label>
                        <?php 
                        $scope_works = [];
                        if ($edit_mode && !empty($existing_contract['scope_of_work'])) {
                            $scope_works = explode(', ', $existing_contract['scope_of_work']);
                        }
                        ?>
                        <div class="form-check scope-work-option">
                            <input class="form-check-input scope-work" type="checkbox" name="scope_of_work[]" id="scope_design" value="Design Services" 
                                <?php echo in_array('Design Services', $scope_works) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="scope_design">Design Services</label>
                        </div>
                        <div class="form-check scope-work-option">
                            <input class="form-check-input scope-work" type="checkbox" name="scope_of_work[]" id="scope_construction" value="Construction" 
                                <?php echo in_array('Construction', $scope_works) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="scope_construction">Construction</label>
                        </div>
                        <div class="form-check scope-work-option">
                            <input class="form-check-input scope-work" type="checkbox" name="scope_of_work[]" id="scope_renovation" value="Renovation" 
                                <?php echo in_array('Renovation', $scope_works) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="scope_renovation">Renovation</label>
                        </div>
                        <div class="form-check scope-work-option">
                            <input class="form-check-input scope-work" type="checkbox" name="scope_of_work[]" id="scope_maintenance" value="Maintenance" 
                                <?php echo in_array('Maintenance', $scope_works) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="scope_maintenance">Maintenance</label>
                        </div>
                        <div class="form-check scope-work-option">
                            <input class="form-check-input scope-work" type="checkbox" name="scope_of_work[]" id="scope_other" value="Other" 
                                <?php echo (count(array_diff($scope_works, ['Design Services', 'Construction', 'Renovation', 'Maintenance'])) > 0) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="scope_other">Other</label>
                            <input type="text" class="form-control other-work-input mt-2" id="other_work_text" name="other_work_text" 
                                placeholder="Specify other work" value="<?php 
                                if ($edit_mode) {
                                    $other_works = array_diff($scope_works, ['Design Services', 'Construction', 'Renovation', 'Maintenance']);
                                    echo htmlspecialchars(implode(', ', $other_works));
                                }
                                ?>">
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="scope_description" class="form-label">Scope Description</label>
                        <textarea class="form-control" id="scope_description" name="scope_description" rows="4"><?php 
                            echo $edit_mode ? htmlspecialchars($existing_contract['scope_description'] ?? '') : ''; 
                        ?></textarea>
                    </div>
                </div>
            </div>
            
            <!-- Project Sections -->
            <div class="contract-section">
                <h4>AGREEMENT</h4>
                <p id="agreementClausePreview">[Agreement clause will appear here]</p>
            </div>
            
            <div class="contract-section">
                <h4>SERVICE</h4>
                <p id="serviceClausePreview">[Service clause will appear here]</p>
            </div>
            
            <div class="contract-section">
                <h4>PROJECT PERIOD</h4>
                <p id="projectPeriodPreview">[Project period clause will appear here]</p>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" 
                            value="<?php echo $edit_mode ? htmlspecialchars($existing_contract['start_date'] ?? '') : ''; ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" 
                            value="<?php echo $edit_mode ? htmlspecialchars($existing_contract['end_date'] ?? '') : ''; ?>" required>
                    </div>
                </div>
            </div>
            
            <!-- Amount -->
            <div class="form-section">
                <h2>Amount</h2>
                <div class="row">
                    <div class="col-md-12">
                        <div id="item_container">
                            <?php if ($edit_mode && !empty($existing_items)): ?>
                                <?php foreach ($existing_items as $index => $item): ?>
                                    <div class="row item-row mb-2">
                                        <div class="col-md-5">
                                            <input type="text" class="form-control" name="item_description[]" 
                                                value="<?php echo htmlspecialchars($item['description']); ?>" required>
                                        </div>
                                        <div class="col-md-2">
                                            <input type="number" class="form-control quantity" name="item_quantity[]" 
                                                value="<?php echo htmlspecialchars($item['quantity']); ?>" min="0" step="0.01" required>
                                        </div>
                                        <div class="col-md-2">
                                            <input type="number" class="form-control amount" name="item_amount[]" 
                                                value="<?php echo htmlspecialchars($item['amount']); ?>" min="0" step="0.01" required>
                                        </div>
                                        <div class="col-md-2">
                                            <input type="text" class="form-control total" 
                                                value="<?php echo number_format($item['total'], 2); ?>" readonly>
                                        </div>
                                        <div class="col-md-1">
                                            <button type="button" class="btn btn-danger btn-sm remove-item">×</button>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <button type="button" class="btn btn-secondary mt-2" id="add_item">Add Item</button>
                    </div>
                </div>
                
                <div class="row mt-3">
                    <div class="col-md-3 offset-md-9">
                        <div class="input-group mb-3">
                            <span class="input-group-text">Total:</span>
                            <input type="text" class="form-control" id="total_amount" name="total_amount" 
                                value="<?php echo $edit_mode ? number_format($existing_contract['total_amount'], 2) : '0.00'; ?>" readonly>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Contract Terms -->
            <div class="contract-section">
                <h4>CONTRACT TERMS</h4>
                <textarea class="form-control" id="contract_paragraphs" name="contract_paragraphs" rows="8" required><?php
if ($edit_mode) {
    echo htmlspecialchars($existing_contract['contract_terms'] ?? '');
} else {
    echo "1. PAYMENT TERMS: The Client agrees to pay the Contractor the total amount specified above according to the following schedule: 50% upon signing this agreement, 40% upon completion of 50% of the work, and the remaining 10% upon final completion and acceptance of all work.\n\n";
    echo "2. CHANGE ORDERS: Any changes to the scope of work must be agreed upon in writing by both parties and may result in additional charges and time extensions.\n\n";
    echo "3. WARRANTIES: The Contractor warrants that all work will be performed in a professional manner consistent with industry standards. Materials will be of good quality unless otherwise specified.\n\n";
    echo "4. TERMINATION: Either party may terminate this agreement with written notice if the other party fails to cure a material breach within 14 days of receiving written notice of such breach.";
}
?></textarea>
            </div>
            
            <div class="contract-section">
                <h4>JURISDICTION</h4>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="jurisdiction" class="form-label">State Jurisdiction</label>
                        <input type="text" class="form-control" id="jurisdiction" name="jurisdiction" 
                            value="<?php echo $edit_mode ? htmlspecialchars($existing_contract['jurisdiction'] ?? '') : ''; ?>" required>
                    </div>
                </div>
            </div>
            
            <!-- Signatures -->
            <div class="form-section">
                <h2>Signatures</h2>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Client Signature</label>
                        <div>
                            <input type="file" class="form-control mb-2" id="client_signature_upload" name="client_signature" accept="image/*">
                            <?php if ($edit_mode && !empty($existing_client_signature)): ?>
                                <div class="mb-2">
                                    <p class="text-muted">Current signature:</p>
                                    <img src="<?php echo htmlspecialchars($existing_client_signature); ?>" class="signature-preview">
                                </div>
                            <?php endif; ?>
                            <small class="text-muted">OR draw signature below:</small>
                            <div class="signature-pad">
                                <canvas id="clientSignatureCanvas"></canvas>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-secondary mt-2" onclick="clearSignature('client')">Clear</button>
                            <input type="hidden" id="client_signature_data" name="client_signature_data">
                        </div>
                        <label class="form-label mt-3">Client Name</label>
                        <input type="text" class="form-control" id="signed_client_name" readonly>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Contractor Signature</label>
                        <div>
                            <input type="file" class="form-control mb-2" id="contractor_signature_upload" name="contractor_signature" accept="image/*">
                            <?php if ($edit_mode && !empty($existing_contractor_signature)): ?>
                                <div class="mb-2">
                                    <p class="text-muted">Current signature:</p>
                                    <img src="<?php echo htmlspecialchars($existing_contractor_signature); ?>" class="signature-preview">
                                </div>
                            <?php endif; ?>
                            <small class="text-muted">OR draw signature below:</small>
                            <div class="signature-pad">
                                <canvas id="contractorSignatureCanvas"></canvas>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-secondary mt-2" onclick="clearSignature('contractor')">Clear</button>
                            <input type="hidden" id="contractor_signature_data" name="contractor_signature_data">
                        </div>
                        <label class="form-label mt-3">Contractor Name</label>
                        <input type="text" class="form-control" id="signed_contractor_name" readonly>
                    </div>
                </div>
            </div>
            
            <!-- Hidden Fields -->
            <?php if ($edit_mode): ?>
                <input type="hidden" name="existing_contract_id" value="<?php echo htmlspecialchars($existing_contract['contract_id']); ?>">
                <input type="hidden" name="existing_client_signature" value="<?php echo htmlspecialchars($existing_client_signature); ?>">
                <input type="hidden" name="existing_contractor_signature" value="<?php echo htmlspecialchars($existing_contractor_signature); ?>">
            <?php endif; ?>

            <!-- Submit Button -->
            <div class="row">
                <div class="col-md-12 text-center">
                    <button type="submit" class="btn btn-primary btn-lg"><?php echo $edit_mode ? 'Update' : 'Submit'; ?> Contract</button>
                    <?php if ($edit_mode): ?>
                        <a href="view_contract.php?id=<?php echo htmlspecialchars($existing_contract['contract_id']); ?>" class="btn btn-secondary btn-lg ms-2">Cancel</a>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>

    <!-- Signature Pad JS -->
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
    <script>
        // Initialize Signature Pads
        const clientCanvas = document.getElementById('clientSignatureCanvas');
        const contractorCanvas = document.getElementById('contractorSignatureCanvas');
        
        // Set canvas size properly
        function resizeCanvas(canvas) {
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            canvas.getContext("2d").scale(ratio, ratio);
        }
        
        resizeCanvas(clientCanvas);
        resizeCanvas(contractorCanvas);
        
        const clientSignaturePad = new SignaturePad(clientCanvas, {
            backgroundColor: 'rgb(255, 255, 255)'
        });
        const contractorSignaturePad = new SignaturePad(contractorCanvas, {
            backgroundColor: 'rgb(255, 255, 255)'
        });
        
        // Handle window resize
        window.addEventListener('resize', () => {
            resizeCanvas(clientCanvas);
            resizeCanvas(contractorCanvas);
        });

        // Clear signature function
        function clearSignature(type) {
            if (type === 'client') {
                clientSignaturePad.clear();
                document.getElementById('client_signature_data').value = '';
            } else {
                contractorSignaturePad.clear();
                document.getElementById('contractor_signature_data').value = '';
            }
        }
        
        // Handle "Other" scope of work option
        document.getElementById('scope_other').addEventListener('change', function() {
            const otherInput = document.getElementById('other_work_text');
            otherInput.style.display = this.checked ? 'block' : 'none';
            if (!this.checked) {
                otherInput.value = '';
            }
            updateContractPreview();
        });
        
        // Show other work input if "Other" is checked on page load
        if (document.getElementById('scope_other').checked) {
            document.getElementById('other_work_text').style.display = 'block';
        }
        
        // Update other work text when changed
        document.getElementById('other_work_text').addEventListener('input', updateContractPreview);
        
        // Client search functionality
        const clientSearch = document.getElementById('client_search');
        const clientSearchBtn = document.getElementById('client_search_btn');
        const clientSearchResults = document.getElementById('client_search_results');
        
        function searchClients() {
            const searchTerm = clientSearch.value.trim();
            if (searchTerm.length < 2) {
                clientSearchResults.style.display = 'none';
                return;
            }
            
            fetch(`?search_client=1&search_term=${encodeURIComponent(searchTerm)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.length > 0) {
                        clientSearchResults.innerHTML = '';
                        data.forEach(client => {
                            const item = document.createElement('div');
                            item.className = 'search-result-item';
                            item.textContent = `${client.name} (${client.email})`;
                            item.addEventListener('click', () => {
                                fillClientForm(client);
                                clientSearchResults.style.display = 'none';
                            });
                            clientSearchResults.appendChild(item);
                        });
                        clientSearchResults.style.display = 'block';
                    } else {
                        clientSearchResults.style.display = 'none';
                    }
                });
        }
        
        clientSearchBtn.addEventListener('click', searchClients);
        clientSearch.addEventListener('input', searchClients);
        
        // Hide search results when clicking outside
        document.addEventListener('click', (e) => {
            if (!clientSearch.contains(e.target) && !clientSearchResults.contains(e.target)) {
                clientSearchResults.style.display = 'none';
            }
        });
        
        // Fill client form with selected client data
        function fillClientForm(client) {
            // Fill fields
            document.getElementById('company_name').value = client.company_name || '';
            document.getElementById('contact_person').value = client.entity_type === 'person' ? client.name : '';
            
            document.getElementById('client_street').value = client.street;
            document.getElementById('client_city').value = client.city;
            document.getElementById('client_state').value = client.state;
            document.getElementById('client_postal').value = client.postal;
            document.getElementById('client_email').value = client.email;
            document.getElementById('client_phone').value = client.phone;
            
            updateContractPreview();
        }
        
        // Update contract clauses in real-time
        function updateContractPreview() {
            // Get values from form
            const scopeWork = Array.from(document.querySelectorAll('input[name="scope_of_work[]"]:checked'))
                .map(el => {
                    if (el.id === 'scope_other' && el.checked) {
                        const otherText = document.getElementById('other_work_text').value;
                        return otherText || 'Other';
                    }
                    return el.value;
                })
                .join(', ') || '[Scope of Work]';
                
            const contractorName = document.getElementById('contractor_name').value || '[Contractor Name]';
            const contractorCompany = document.getElementById('contractor_company').value || '[Contractor Company]';
            const contractorStreet = document.getElementById('contractor_street').value || '[Street]';
            const contractorCity = document.getElementById('contractor_city').value || '[City]';
            const contractorState = document.getElementById('contractor_state').value || '[State]';
            const contractorPostal = document.getElementById('contractor_postal').value || '[Postal]';
            
            const clientCompany = document.getElementById('company_name').value;
            const clientContact = document.getElementById('contact_person').value;
            const clientName = clientCompany || clientContact || '[Client Name]';
            
            const clientStreet = document.getElementById('client_street').value || '[Street]';
            const clientCity = document.getElementById('client_city').value || '[City]';
            const clientState = document.getElementById('client_state').value || '[State]';
            const clientPostal = document.getElementById('client_postal').value || '[Postal]';
            
            const propertyStreet = document.getElementById('property_street').value || '[Street]';
            const propertyCity = document.getElementById('property_city').value || '[City]';
            const propertyState = document.getElementById('property_state').value || '[State]';
            const propertyPostal = document.getElementById('property_postal').value || '[Postal]';
            
            // Build addresses
            const contractorAddress = `${contractorStreet}, ${contractorCity}, ${contractorState} ${contractorPostal}`;
            const clientAddress = `${clientStreet}, ${clientCity}, ${clientState} ${clientPostal}`;
            const propertyAddress = `${propertyStreet}, ${propertyCity}, ${propertyState} ${propertyPostal}`;
            
            // Update preview
            document.getElementById('agreementClausePreview').textContent = 
                `This ${scopeWork} is executed by and between ${contractorName} (${contractorCompany}) with address at ${contractorAddress} hereafter known as "Contractor" and ${clientName} with address at ${clientAddress} hereafter known as "Client".`;
                
            document.getElementById('serviceClausePreview').textContent = 
                `The Contractor agrees to provide and perform ${scopeWork} for the Client's property with address located at ${propertyAddress}.`;
                
            document.getElementById('projectPeriodPreview').textContent = 
                "This project shall commence and is scheduled to be completed on the following date periods unless otherwise reasonable delays would arise where such delay or interference is not caused by the Contractor, such as but not limited to cause by third party, inclement weather, fortuitous events, including acts of God:";
        }
        
        // Add event listeners to all relevant fields
        const fieldsToWatch = [
            'contractor_name', 'contractor_company', 'contractor_street', 'contractor_city', 'contractor_state', 'contractor_postal',
            'company_name', 'contact_person', 'client_street', 'client_city', 'client_state', 'client_postal',
            'property_street', 'property_city', 'property_state', 'property_postal'
        ];
        
        fieldsToWatch.forEach(id => {
            document.getElementById(id)?.addEventListener('input', updateContractPreview);
        });
        
        document.querySelectorAll('input[name="scope_of_work[]"]').forEach(el => {
            el.addEventListener('change', updateContractPreview);
        });
        
        // Auto-fill client address same as property address
        document.getElementById('property_street').addEventListener('change', function() {
            if (confirm('Is the client address the same as the property address?')) {
                document.getElementById('client_street').value = this.value;
                document.getElementById('client_city').value = document.getElementById('property_city').value;
                document.getElementById('client_state').value = document.getElementById('property_state').value;
                document.getElementById('client_postal').value = document.getElementById('property_postal').value;
                updateContractPreview();
            }
        });
        
        // Item management
        let itemCounter = 0;
        
        document.getElementById('add_item').addEventListener('click', function() {
            const container = document.getElementById('item_container');
            const newRow = document.createElement('div');
            newRow.className = 'row item-row mb-2';
            newRow.innerHTML = `
                <div class="col-md-5">
                    <input type="text" class="form-control" name="item_description[]" placeholder="Item Description" required>
                </div>
                <div class="col-md-2">
                    <input type="number" class="form-control quantity" name="item_quantity[]" placeholder="Qty" min="0" step="0.01" required>
                </div>
                <div class="col-md-2">
                    <input type="number" class="form-control amount" name="item_amount[]" placeholder="Amount" min="0" step="0.01" required>
                </div>
                <div class="col-md-2">
                    <input type="text" class="form-control total" placeholder="Total" readonly>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger btn-sm remove-item">×</button>
                </div>
            `;
            container.appendChild(newRow);
            
            // Add event listeners to new inputs
            const quantityInput = newRow.querySelector('.quantity');
            const amountInput = newRow.querySelector('.amount');
            const totalInput = newRow.querySelector('.total');
            
            quantityInput.addEventListener('input', calculateItemTotal);
            amountInput.addEventListener('input', calculateItemTotal);
            
            // Remove item button
            newRow.querySelector('.remove-item').addEventListener('click', function() {
                container.removeChild(newRow);
                calculateGrandTotal();
            });
            
            itemCounter++;
        });
        
        function calculateItemTotal(e) {
            const row = e.target.closest('.item-row');
            const quantity = parseFloat(row.querySelector('.quantity').value) || 0;
            const amount = parseFloat(row.querySelector('.amount').value) || 0;
            const total = quantity * amount;
            row.querySelector('.total').value = total.toFixed(2);
            calculateGrandTotal();
        }
        
        function calculateGrandTotal() {
            let grandTotal = 0;
            document.querySelectorAll('.item-row').forEach(row => {
                const total = parseFloat(row.querySelector('.total').value) || 0;
                grandTotal += total;
            });
            document.getElementById('total_amount').value = grandTotal.toFixed(2);
        }
        
        // Handle form submission
        document.getElementById('contractForm').addEventListener('submit', function(e) {
            // Save signatures if drawn
            if (!clientSignaturePad.isEmpty()) {
                document.getElementById('client_signature_data').value = clientSignaturePad.toDataURL('image/png');
            }
            
            if (!contractorSignaturePad.isEmpty()) {
                document.getElementById('contractor_signature_data').value = contractorSignaturePad.toDataURL('image/png');
            }
            
            // Set signed names
            const clientCompany = document.getElementById('company_name').value;
            const clientContact = document.getElementById('contact_person').value;
            const clientName = clientCompany || clientContact;
            
            document.getElementById('signed_client_name').value = clientName;
            document.getElementById('signed_contractor_name').value = document.getElementById('contractor_name').value;
            
            // Validate at least one scope of work is selected
            const scopeCheckboxes = document.querySelectorAll('input[name="scope_of_work[]"]:checked');
            if (scopeCheckboxes.length === 0) {
                e.preventDefault();
                alert('Please select at least one scope of work');
                return;
            }
            
            // Validate "Other" scope has text if checked
            if (document.getElementById('scope_other').checked) {
                const otherText = document.getElementById('other_work_text').value.trim();
                if (!otherText) {
                    e.preventDefault();
                    alert('Please specify the "Other" scope of work');
                    return;
                }
            }
            
            // Validate at least one item exists
            const items = document.querySelectorAll('.item-row');
            if (items.length === 0) {
                e.preventDefault();
                alert('Please add at least one item');
                return;
            }
            
            // Validate dates
            const startDate = new Date(document.getElementById('start_date').value);
            const endDate = new Date(document.getElementById('end_date').value);
            
            if (startDate >= endDate) {
                e.preventDefault();
                alert('End date must be after start date');
                return;
            }
        });
        
        // Initial setup
        document.addEventListener('DOMContentLoaded', function() {
            // Add first item row if not in edit mode or no existing items
            if (!<?php echo $edit_mode ? 'true' : 'false'; ?> || <?php echo empty($existing_items) ? 'true' : 'false'; ?>) {
                document.getElementById('add_item').click();
            }
            
            // Initial preview update
            updateContractPreview();
            
            // Calculate grand total if in edit mode with existing items
            if (<?php echo $edit_mode && !empty($existing_items) ? 'true' : 'false'; ?>) {
                calculateGrandTotal();
            }
        });

        function validatePhoneInput(input) {
            // Allow: backspace, delete, tab, escape, enter
            if ([46, 8, 9, 27, 13].includes(input.keyCode) ||
                // Allow: Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
                (input.keyCode == 65 && input.ctrlKey === true) ||
                (input.keyCode == 67 && input.ctrlKey === true) ||
                (input.keyCode == 86 && input.ctrlKey === true) ||
                (input.keyCode == 88 && input.ctrlKey === true) ||
                // Allow: home, end, left, right
                (input.keyCode >= 35 && input.keyCode <= 39)) {
                return;
            }
            
            // Only allow numbers and these symbols: ()-+ and space
            const allowedChars = /[0-9()\-+ ]/;
            if (!allowedChars.test(String.fromCharCode(input.keyCode))) {
                input.preventDefault();
            }
        }

        // Add event listeners to phone fields
        document.getElementById('contractor_phone').addEventListener('keydown', validatePhoneInput);
        document.getElementById('client_phone').addEventListener('keydown', validatePhoneInput);

        // Also validate on paste
        document.getElementById('contractor_phone').addEventListener('paste', function(e) {
            const pasteData = e.clipboardData.getData('text/plain');
            if (!/^[0-9()\-+ ]*$/.test(pasteData)) {
                e.preventDefault();
            }
        });

        document.getElementById('client_phone').addEventListener('paste', function(e) {
            const pasteData = e.clipboardData.getData('text/plain');
            if (!/^[0-9()\-+ ]*$/.test(pasteData)) {
                e.preventDefault();
            }
        });

        function validatePostalInput(input) {
            // Same allow conditions as phone validation
            
            // Only allow numbers and hyphen
            const allowedChars = /[0-9\-]/;
            if (!allowedChars.test(String.fromCharCode(input.keyCode))) {
                input.preventDefault();
            }
        }

        // Apply to all postal code fields
        document.querySelectorAll('.postal-code').forEach(field => {
            field.addEventListener('keydown', validatePostalInput);
            field.addEventListener('paste', function(e) {
                const pasteData = e.clipboardData.getData('text/plain');
                if (!/^[0-9\-]*$/.test(pasteData)) {
                    e.preventDefault();
                }
            });
        });
    </script>
</body>
</html>