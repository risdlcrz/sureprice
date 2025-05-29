<?php
// DB Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'sureprice');
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create all required tables if they don't exist
$tables = [
    "CREATE TABLE IF NOT EXISTS parties (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        company_name VARCHAR(100),
        email VARCHAR(100),
        phone VARCHAR(20),
        street VARCHAR(100),
        city VARCHAR(50),
        state VARCHAR(50),
        postal VARCHAR(20),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    
    "CREATE TABLE IF NOT EXISTS properties (
        id INT AUTO_INCREMENT PRIMARY KEY,
        street VARCHAR(100),
        city VARCHAR(50),
        state VARCHAR(50),
        postal VARCHAR(20),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    
    "CREATE TABLE IF NOT EXISTS contracts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        contract_id VARCHAR(50) UNIQUE,
        client_id INT,
        contractor_id INT,
        property_id INT,
        scope_of_work VARCHAR(255),
        scope_description TEXT,
        start_date DATE,
        end_date DATE,
        total_amount DECIMAL(12,2),
        contract_terms TEXT,
        jurisdiction VARCHAR(100),
        status ENUM('draft','pending','approved','rejected') DEFAULT 'draft',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (client_id) REFERENCES parties(id),
        FOREIGN KEY (contractor_id) REFERENCES parties(id),
        FOREIGN KEY (property_id) REFERENCES properties(id)
    )",
    
    "CREATE TABLE IF NOT EXISTS contract_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        contract_id INT,
        description VARCHAR(255),
        quantity DECIMAL(10,2),
        amount DECIMAL(10,2),
        total DECIMAL(10,2),
        FOREIGN KEY (contract_id) REFERENCES contracts(id) ON DELETE CASCADE
    )",
    
    "CREATE TABLE IF NOT EXISTS suppliers (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        company_name VARCHAR(100),
        email VARCHAR(100),
        phone VARCHAR(20),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    
    "CREATE TABLE IF NOT EXISTS materials (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        description TEXT,
        unit_of_measure VARCHAR(20) DEFAULT 'EA',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    
    "CREATE TABLE IF NOT EXISTS supplier_requests (
        id INT AUTO_INCREMENT PRIMARY KEY,
        project_id INT,
        request_type ENUM('inquiry', 'quotation', 'purchase'),
        request_details TEXT,
        status ENUM('draft', 'pending', 'approved', 'rejected') DEFAULT 'draft',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (project_id) REFERENCES contracts(id)
    )",
    
    "CREATE TABLE IF NOT EXISTS request_suppliers (
        request_id INT NOT NULL,
        supplier_id INT NOT NULL,
        invited_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        responded BOOLEAN DEFAULT FALSE,
        response_details TEXT,
        responded_at TIMESTAMP NULL,
        PRIMARY KEY (request_id, supplier_id),
        FOREIGN KEY (request_id) REFERENCES supplier_requests(id) ON DELETE CASCADE,
        FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE CASCADE
    )",
    
    "CREATE TABLE IF NOT EXISTS request_materials (
        id INT AUTO_INCREMENT PRIMARY KEY,
        request_id INT NOT NULL,
        material_id INT NOT NULL,
        quantity DECIMAL(10,2) NOT NULL,
        notes TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (request_id) REFERENCES supplier_requests(id) ON DELETE CASCADE,
        FOREIGN KEY (material_id) REFERENCES materials(id) ON DELETE CASCADE
    )"
];

foreach ($tables as $table) {
    if (!$conn->query($table)) {
        die("Error creating table: " . $conn->error);
    }
}

// Insert sample data if tables are empty
function tableIsEmpty($conn, $table) {
    $result = $conn->query("SELECT COUNT(*) as count FROM $table");
    return $result->fetch_assoc()['count'] == 0;
}

// Sample parties (clients/contractors)
if (tableIsEmpty($conn, 'parties')) {
    $sampleParties = [
        ["name" => "Acme Corporation", "company_name" => "Acme Corp", "email" => "contact@acme.com", "phone" => "555-1000"],
        ["name" => "John Builder", "company_name" => "Builder Inc", "email" => "john@builder.com", "phone" => "555-1001"]
    ];
    
    foreach ($sampleParties as $party) {
        $stmt = $conn->prepare("INSERT INTO parties (name, company_name, email, phone) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $party['name'], $party['company_name'], $party['email'], $party['phone']);
        $stmt->execute();
    }
}

// Sample properties
if (tableIsEmpty($conn, 'properties')) {
    $sampleProperties = [
        ["street" => "123 Main St", "city" => "Metropolis", "state" => "CA", "postal" => "90001"]
    ];
    
    foreach ($sampleProperties as $property) {
        $stmt = $conn->prepare("INSERT INTO properties (street, city, state, postal) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $property['street'], $property['city'], $property['state'], $property['postal']);
        $stmt->execute();
    }
}

// Sample contracts
if (tableIsEmpty($conn, 'contracts')) {
    $sampleContracts = [
        [
            "contract_id" => "CON-2023-001",
            "client_id" => 1,
            "contractor_id" => 2,
            "property_id" => 1,
            "scope_of_work" => "Office Renovation",
            "start_date" => "2023-01-01",
            "end_date" => "2023-06-30",
            "total_amount" => 50000.00
        ]
    ];
    
    foreach ($sampleContracts as $contract) {
        $stmt = $conn->prepare("INSERT INTO contracts (
            contract_id, client_id, contractor_id, property_id, scope_of_work, 
            start_date, end_date, total_amount
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param(
            "siiisssd", 
            $contract['contract_id'],
            $contract['client_id'],
            $contract['contractor_id'],
            $contract['property_id'],
            $contract['scope_of_work'],
            $contract['start_date'],
            $contract['end_date'],
            $contract['total_amount']
        );
        $stmt->execute();
    }
}

// Sample suppliers
if (tableIsEmpty($conn, 'suppliers')) {
    $sampleSuppliers = [
        ["name" => "John Smith", "company_name" => "ABC Construction Supplies", "email" => "john@abcsupplies.com", "phone" => "555-0101"],
        ["name" => "Maria Garcia", "company_name" => "XYZ Building Materials", "email" => "maria@xyz.com", "phone" => "555-0202"]
    ];
    
    foreach ($sampleSuppliers as $supplier) {
        $stmt = $conn->prepare("INSERT INTO suppliers (name, company_name, email, phone) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $supplier['name'], $supplier['company_name'], $supplier['email'], $supplier['phone']);
        $stmt->execute();
    }
}

// Sample materials
if (tableIsEmpty($conn, 'materials')) {
    $sampleMaterials = [
        ["name" => "Concrete", "description" => "Ready-mix concrete 3000psi", "unit_of_measure" => "kg"],
        ["name" => "Steel Rebar", "description" => "12mm diameter steel reinforcement", "unit_of_measure" => "m"]
    ];
    
    foreach ($sampleMaterials as $material) {
        $stmt = $conn->prepare("INSERT INTO materials (name, description, unit_of_measure) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $material['name'], $material['description'], $material['unit_of_measure']);
        $stmt->execute();
    }
}

// Fetch data for the form
$projects = $conn->query("
    SELECT c.id, c.contract_id, p.company_name 
    FROM contracts c 
    LEFT JOIN parties p ON c.client_id = p.id 
    ORDER BY c.created_at DESC
")->fetch_all(MYSQLI_ASSOC);

$suppliers = $conn->query("SELECT * FROM suppliers ORDER BY name")->fetch_all(MYSQLI_ASSOC);
$materials = $conn->query("SELECT id, name, description, unit_of_measure AS unit FROM materials ORDER BY name")->fetch_all(MYSQLI_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $project_id = intval($_POST['project_id']);
    $request_type = $conn->real_escape_string($_POST['request_type']);
    $request_details = $conn->real_escape_string($_POST['request_details']);
    $supplier_ids = $_POST['suppliers'] ?? [];
    $material_items = $_POST['material_items'] ?? [];
    
    $conn->begin_transaction();
    try {
        // Insert request
        $stmt = $conn->prepare("INSERT INTO supplier_requests (project_id, request_type, request_details, status) VALUES (?, ?, ?, 'draft')");
        $stmt->bind_param("iss", $project_id, $request_type, $request_details);
        $stmt->execute();
        $request_id = $conn->insert_id;
        
        // Insert invited suppliers
        $stmt = $conn->prepare("INSERT INTO request_suppliers (request_id, supplier_id) VALUES (?, ?)");
        foreach ($supplier_ids as $supplier_id) {
            $supplier_id = intval($supplier_id);
            $stmt->bind_param("ii", $request_id, $supplier_id);
            $stmt->execute();
        }
        
        // Insert material items if this is RFQ or PR
        if (in_array($request_type, ['quotation', 'purchase']) && !empty($material_items)) {
            $stmt = $conn->prepare("INSERT INTO request_materials (request_id, material_id, quantity, notes) VALUES (?, ?, ?, ?)");
            foreach ($material_items as $item) {
                $material_id = intval($item['material_id']);
                $quantity = floatval($item['quantity']);
                $notes = $conn->real_escape_string($item['notes']);
                $stmt->bind_param("iids", $request_id, $material_id, $quantity, $notes);
                $stmt->execute();
            }
        }
        
        $conn->commit();
        header("Location: view_request.php?id=$request_id");
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        $error = "Error creating request: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Supplier Request</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <style>
        :root {
            --sap-primary: #0a6ed1;
            --sap-secondary: #6a6d70;
            --sap-success: #107e3e;
            --sap-warning: #e9730c;
            --sap-error: #bb0000;
        }
        
        body {
            background-color: #f7f7f7;
            font-family: '72', '72full', Arial, Helvetica, sans-serif;
        }
        
        .sap-card {
            background: white;
            border-radius: 0.25rem;
            box-shadow: 0 0 0.125rem 0 rgba(0,0,0,0.1), 0 0.125rem 0.5rem 0 rgba(0,0,0,0.1);
            margin-bottom: 1rem;
            border: none;
        }
        
        .sap-card-header {
            background-color: #f5f6f7;
            border-bottom: 1px solid #d9d9d9;
            padding: 1rem 1.5rem;
            font-weight: 600;
        }
        
        .sap-form-label {
            font-weight: 600;
            color: #32363a;
            margin-bottom: 0.5rem;
        }
        
        .sap-form-control {
            border-radius: 0.25rem;
            border: 1px solid #89919a;
            padding: 0.5rem 0.75rem;
            height: calc(1.5em + 1rem + 2px);
        }
        
        .sap-btn-primary {
            background-color: var(--sap-primary);
            border-color: var(--sap-primary);
            color: white;
            font-weight: 600;
        }
        
        .material-item-card {
            border: 1px solid #d9d9d9;
            border-radius: 0.25rem;
            padding: 1rem;
            margin-bottom: 1rem;
            background: white;
        }
        
        .request-type-tabs .nav-link {
            color: var(--sap-secondary);
            font-weight: 600;
            border: none;
            padding: 0.75rem 1.5rem;
        }
        
        .request-type-tabs .nav-link.active {
            color: var(--sap-primary);
            border-bottom: 2px solid var(--sap-primary);
            background: transparent;
        }
        
        .select2-container--bootstrap-5 .select2-selection {
            min-height: 38px;
            border-radius: 0.25rem;
            border: 1px solid #89919a;
        }
        
        .sap-badge {
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.25rem 0.5rem;
            border-radius: 0.75rem;
        }
        
        .badge-rfi {
            background-color: #e3fcef;
            color: var(--sap-success);
        }
        
        .badge-rfq {
            background-color: #ebf8ff;
            color: var(--sap-primary);
        }
        
        .badge-pr {
            background-color: #fff8d6;
            color: var(--sap-warning);
        }
        
        .supplier-response {
            border-left: 4px solid #0d6efd;
            padding-left: 15px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="mb-0">Create Supplier Request</h1>
            <a href="requests_list.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to List
            </a>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form method="POST" id="requestForm">
            <!-- Request Type Selection -->
            <div class="sap-card mb-4">
                <div class="sap-card-header">
                    <h5 class="mb-0">1. Request Type</h5>
                </div>
                <div class="card-body">
                    <ul class="nav request-type-tabs">
                        <li class="nav-item">
                            <a class="nav-link active" href="#" data-type="inquiry" onclick="setRequestType('inquiry')">
                                <span class="sap-badge badge-rfi me-2">RFI</span>
                                Request for Information
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" data-type="quotation" onclick="setRequestType('quotation')">
                                <span class="sap-badge badge-rfq me-2">RFQ</span>
                                Request for Quotation
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" data-type="purchase" onclick="setRequestType('purchase')">
                                <span class="sap-badge badge-pr me-2">PR</span>
                                Purchase Request
                            </a>
                        </li>
                    </ul>
                    <input type="hidden" name="request_type" id="request_type" value="inquiry" required>
                </div>
            </div>
            
            <!-- Project Selection -->
            <div class="sap-card mb-4">
                <div class="sap-card-header">
                    <h5 class="mb-0">2. Project Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="project_id" class="sap-form-label">Select Project</label>
                                <select class="form-select sap-form-control" id="project_id" name="project_id" required>
                                    <option value="">-- Select Project --</option>
                                    <?php foreach ($projects as $proj): ?>
                                        <option value="<?= $proj['id'] ?>">
                                            <?= $proj['contract_id'] . ' - ' . $proj['company_name'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div id="projectDetails" style="display: none;">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-building me-2"></i>
                                    <span id="scope_work" class="fw-semibold"></span>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-calendar-range me-2"></i>
                                    <span id="project_period"></span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-currency-dollar me-2"></i>
                                    <span id="total_amount"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Material Items Section (shown for RFQ and PR) -->
            <div class="sap-card mb-4" id="materialSection" style="display: none;">
                <div class="sap-card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">3. Material Requirements</h5>
                    <button type="button" class="btn btn-sm btn-primary" onclick="addMaterialItem()">
                        <i class="bi bi-plus"></i> Add Item
                    </button>
                </div>
                <div class="card-body">
                    <div id="materialItemsContainer">
                        <div class="alert alert-info" id="noMaterialsAlert">
                            No material items added yet. Click "Add Item" to get started.
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Supplier Selection -->
            <div class="sap-card mb-4">
                <div class="sap-card-header">
                    <h5 class="mb-0">4. Invite Suppliers</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="suppliers" class="sap-form-label">Select Suppliers</label>
                        <select class="form-select select2" id="suppliers" name="suppliers[]" multiple="multiple" required>
                            <?php foreach ($suppliers as $supplier): ?>
                                <option value="<?= $supplier['id'] ?>">
                                    <?= $supplier['company_name'] ? htmlspecialchars($supplier['company_name']) : htmlspecialchars($supplier['name']) ?>
                                    <?php if ($supplier['email']): ?> (<?= htmlspecialchars($supplier['email']) ?>) <?php endif; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div id="selectedSuppliers" class="mt-3">
                        <div class="text-muted">No suppliers selected yet</div>
                    </div>
                </div>
            </div>
            
            <!-- Request Details -->
            <div class="sap-card mb-4">
                <div class="sap-card-header">
                    <h5 class="mb-0">5. Request Details</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="request_details" class="sap-form-label">Instructions & Requirements</label>
                        <textarea class="form-control" id="request_details" name="request_details" rows="6" required></textarea>
                    </div>
                    <div id="dynamicFieldsContainer">
                        <!-- Dynamic fields based on request type will appear here -->
                    </div>
                </div>
            </div>
            
            <!-- Form Actions -->
            <div class="d-flex justify-content-between">
                <button type="button" class="btn btn-outline-secondary" onclick="saveAsDraft()">
                    <i class="bi bi-file-earmark"></i> Save as Draft
                </button>
                <div>
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="bi bi-check-circle"></i> Submit Request
                    </button>
                    <button type="button" class="btn btn-outline-danger">
                        <i class="bi bi-x-circle"></i> Cancel
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Material Item Template -->
    <div id="materialItemTemplate" style="display: none;">
        <div class="material-item-card" data-index="{index}">
            <div class="row">
                <div class="col-md-5">
                    <div class="mb-3">
                        <label class="sap-form-label">Material</label>
                        <select class="form-select material-select" name="material_items[{index}][material_id]" required>
                            <option value="">-- Select Material --</option>
                            <?php foreach ($materials as $material): ?>
                                <option value="<?= $material['id'] ?>" 
                                        data-uom="<?= htmlspecialchars($material['unit']) ?>"
                                        data-desc="<?= htmlspecialchars($material['description'] ?? '') ?>">
                                    <?= htmlspecialchars($material['name']) ?>
                                    <?php if (isset($material['description'])): ?>
                                        (<?= htmlspecialchars($material['description']) ?>)
                                    <?php endif; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="mb-3">
                        <label class="sap-form-label">Quantity</label>
                        <input type="number" class="form-control" name="material_items[{index}][quantity]" min="0.01" step="0.01" required>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label class="sap-form-label">Unit of Measure</label>
                        <input type="text" class="form-control material-uom" value="EA" readonly>
                    </div>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" class="btn btn-outline-danger btn-sm mb-3" onclick="removeMaterialItem(this)">
                        <i class="bi bi-trash"></i> Remove
                    </button>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="mb-0">
                        <label class="sap-form-label">Notes</label>
                        <textarea class="form-control" name="material_items[{index}][notes]" rows="2"></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
    $(document).ready(function() {
        // Initialize Select2 for supplier selection
        $('.select2').select2({
            theme: 'bootstrap-5',
            placeholder: "Search and select suppliers",
            width: '100%'
        });

        // Project details loader
        $("#project_id").change(function() {
            let projectId = this.value;
            if (!projectId) {
                $("#projectDetails").hide();
                return;
            }

            fetch("load_project_data.php?project_id=" + projectId)
                .then(res => res.json())
                .then(data => {
                    $("#scope_work").text(data.scope_of_work);
                    $("#project_period").text(data.start_date + " to " + data.end_date);
                    $("#total_amount").text("₱" + parseFloat(data.total_amount).toLocaleString());
                    $("#projectDetails").show();
                });
        });

        // Update selected suppliers display
        $("#suppliers").on("change", function() {
            const selected = $(this).select2('data');
            let html = '<div class="fw-bold mb-2">Selected Suppliers:</div>';
            
            if (selected.length === 0) {
                html = '<div class="text-muted">No suppliers selected yet</div>';
            } else {
                selected.forEach(supplier => {
                    html += `<div class="d-flex align-items-center mb-2">
                                <i class="bi bi-person-check me-2"></i>
                                <div>
                                    <strong>${supplier.text}</strong>
                                </div>
                            </div>`;
                });
            }
            
            $("#selectedSuppliers").html(html);
        });
    });

    let materialItemCount = 0;

    function addMaterialItem() {
        const container = $("#materialItemsContainer");
        const template = $("#materialItemTemplate").html();
        
        // Remove the "no items" alert if it exists
        $("#noMaterialsAlert").remove();
        
        // Add new item
        const newItem = template.replace(/{index}/g, materialItemCount);
        container.append(newItem);
        
        // Initialize Select2 for the new material select
        $(`.material-select[name="material_items[${materialItemCount}][material_id]"]`).select2({
            theme: 'bootstrap-5',
            placeholder: "Search materials",
            width: '100%'
        }).on('change', function() {
            const selectedOption = $(this).find('option:selected');
            const uom = selectedOption.data('uom');
            $(this).closest('.material-item-card').find('.material-uom').val(uom || 'EA');
        });
        
        materialItemCount++;
    }

    function removeMaterialItem(button) {
        $(button).closest('.material-item-card').remove();
        
        // Show "no items" message if container is empty
        if ($("#materialItemsContainer").children().length === 0) {
            $("#materialItemsContainer").html('<div class="alert alert-info" id="noMaterialsAlert">No material items added yet. Click "Add Item" to get started.</div>');
        }
    }

    function setRequestType(type) {
        // Update the hidden input and UI
        $("#request_type").val(type);
        $(".request-type-tabs .nav-link").removeClass('active');
        $(`.request-type-tabs .nav-link[data-type="${type}"]`).addClass('active');
        
        // Show/hide material section based on type
        if (type === 'inquiry') {
            $("#materialSection").hide();
        } else {
            $("#materialSection").show();
        }
        
        // Update dynamic fields based on type
        updateDynamicFields(type);
    }

    function updateDynamicFields(type) {
        const container = $("#dynamicFieldsContainer");
        container.empty();
        
        switch(type) {
            case 'inquiry':
                container.append(`
                    <div class="mb-3">
                        <label class="sap-form-label">Information Required By</label>
                        <input type="date" class="form-control" name="required_by_date">
                    </div>
                    <div class="mb-3">
                        <label class="sap-form-label">Specific Questions</label>
                        <textarea class="form-control" name="specific_questions" rows="3"></textarea>
                    </div>
                `);
                break;
                
            case 'quotation':
                container.append(`
                    <div class="mb-3">
                        <label class="sap-form-label">Quote Required By</label>
                        <input type="date" class="form-control" name="quote_deadline" required>
                    </div>
                    <div class="mb-3">
                        <label class="sap-form-label">Payment Terms</label>
                        <select class="form-select" name="payment_terms">
                            <option value="">-- Select Payment Terms --</option>
                            <option value="net30">Net 30 Days</option>
                            <option value="net60">Net 60 Days</option>
                            <option value="due_on_receipt">Due on Receipt</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-check-label">
                            <input type="checkbox" class="form-check-input" name="include_tax">
                            Prices should include all taxes
                        </label>
                    </div>
                `);
                break;
                
            case 'purchase':
                container.append(`
                    <div class="mb-3">
                        <label class="sap-form-label">Delivery Required By</label>
                        <input type="date" class="form-control" name="delivery_date" required>
                    </div>
                    <div class="mb-3">
                        <label class="sap-form-label">Delivery Address</label>
                        <textarea class="form-control" name="delivery_address" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="sap-form-label">Approval Reference</label>
                        <input type="text" class="form-control" name="approval_reference">
                    </div>
                `);
                break;
        }
    }

    function saveAsDraft() {
        // In a real implementation, this would submit to a different endpoint
        // or add a parameter to indicate it's a draft
        alert("Draft saved (this would be implemented in your backend)");
        // $("#requestForm").append('<input type="hidden" name="is_draft" value="1">');
        // $("#requestForm").submit();
    }

    // Initialize with RFI as default
    setRequestType('inquiry');
    </script>
</body>
</html>