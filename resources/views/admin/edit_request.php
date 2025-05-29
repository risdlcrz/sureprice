<?php
// DB Config
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'sureprice');
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get request ID
$request_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch request data with project details
$request = null;
$project = null;
$suppliers = [];
$materials = [];

if ($request_id) {
    // Get request details with project information
    $sql = "SELECT sr.*, c.scope_of_work, c.start_date, c.end_date, c.total_amount, c.contract_id, p.company_name
            FROM supplier_requests sr
            JOIN contracts c ON sr.project_id = c.id
            LEFT JOIN parties p ON c.client_id = p.id
            WHERE sr.id = $request_id";
    
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $request = $result->fetch_assoc();
        $project = [
            'scope_of_work' => $request['scope_of_work'] ?? 'Not specified',
            'start_date' => $request['start_date'] ?? null,
            'end_date' => $request['end_date'] ?? null,
            'total_amount' => $request['total_amount'] ?? 0,
            'contract_id' => $request['contract_id'] ?? '',
            'company_name' => $request['company_name'] ?? ''
        ];
    }

    // Get all suppliers
    $suppliers = $conn->query("SELECT * FROM suppliers ORDER BY name")->fetch_all(MYSQLI_ASSOC);
    
    // Get invited suppliers for this request
    $invited_suppliers = $conn->query("SELECT supplier_id FROM request_suppliers WHERE request_id = $request_id")
                            ->fetch_all(MYSQLI_ASSOC);
    $invited_supplier_ids = array_column($invited_suppliers, 'supplier_id');
    
    // Get materials for this request
    $materials = $conn->query("SELECT m.id, m.name, m.description, m.unit_of_measure AS unit, 
                              rm.quantity, rm.notes
                              FROM request_materials rm
                              JOIN materials m ON rm.material_id = m.id
                              WHERE rm.request_id = $request_id")
                     ->fetch_all(MYSQLI_ASSOC);
}

// Fetch all projects for dropdown
$projects = $conn->query("SELECT c.id, c.contract_id, p.company_name 
                         FROM contracts c 
                         LEFT JOIN parties p ON c.client_id = p.id 
                         ORDER BY c.created_at DESC")->fetch_all(MYSQLI_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $project_id = intval($_POST['project_id']);
    $request_type = $conn->real_escape_string($_POST['request_type']);
    $request_details = $conn->real_escape_string($_POST['request_details']);
    $supplier_ids = $_POST['suppliers'] ?? [];
    $material_items = $_POST['material_items'] ?? [];
    
    $conn->begin_transaction();
    try {
        // Update request
        $stmt = $conn->prepare("UPDATE supplier_requests SET 
                               project_id = ?, 
                               request_type = ?, 
                               request_details = ?
                               WHERE id = ?");
        $stmt->bind_param("issi", $project_id, $request_type, $request_details, $request_id);
        $stmt->execute();
        
        // Update invited suppliers
        // First remove existing suppliers
        $conn->query("DELETE FROM request_suppliers WHERE request_id = $request_id");
        
        // Then add the new selections
        $stmt = $conn->prepare("INSERT INTO request_suppliers (request_id, supplier_id) VALUES (?, ?)");
        foreach ($supplier_ids as $supplier_id) {
            $supplier_id = intval($supplier_id);
            $stmt->bind_param("ii", $request_id, $supplier_id);
            $stmt->execute();
        }
        
        // Update material items if this is RFQ or PR
        if (in_array($request_type, ['quotation', 'purchase'])) {
            // First remove existing materials
            $conn->query("DELETE FROM request_materials WHERE request_id = $request_id");
            
            // Then add the new items
            if (!empty($material_items)) {
                $stmt = $conn->prepare("INSERT INTO request_materials (request_id, material_id, quantity, notes) VALUES (?, ?, ?, ?)");
                foreach ($material_items as $item) {
                    $material_id = intval($item['material_id']);
                    $quantity = floatval($item['quantity']);
                    $notes = $conn->real_escape_string($item['notes']);
                    $stmt->bind_param("iids", $request_id, $material_id, $quantity, $notes);
                    $stmt->execute();
                }
            }
        }
        
        $conn->commit();
        header("Location: view_request.php?id=$request_id");
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        $error = "Error updating request: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Supplier Request</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.0/font/bootstrap-icons.css">
    <style>
        /* Your existing CSS styles */
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
        
        .action-buttons {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<div class="container py-4">
    <div class="action-buttons d-flex justify-content-between mb-4">
        <a href="requests_list.php" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Requests
        </a>
        <a href="create_request.php" class="btn btn-primary">
            <i class="bi bi-plus"></i> Create New Request
        </a>
    </div>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    
    <?php if ($request): ?>
        <h2>Edit Request #<?php echo $request_id; ?></h2>
        
        <form method="POST" id="requestForm">
            <!-- Request Type Selection -->
            <div class="sap-card mb-4">
                <div class="sap-card-header">
                    <h5 class="mb-0">Request Type</h5>
                </div>
                <div class="card-body">
                    <select class="form-select" id="request_type" name="request_type" required>
                        <option value="inquiry" <?= $request['request_type'] === 'inquiry' ? 'selected' : '' ?>>Request for Information (RFI)</option>
                        <option value="quotation" <?= $request['request_type'] === 'quotation' ? 'selected' : '' ?>>Request for Quotation (RFQ)</option>
                        <option value="purchase" <?= $request['request_type'] === 'purchase' ? 'selected' : '' ?>>Purchase Request (PR)</option>
                    </select>
                </div>
            </div>
            
            <!-- Project Selection -->
            <div class="sap-card mb-4">
                <div class="sap-card-header">
                    <h5 class="mb-0">Project Information</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="project_id" class="form-label">Select Project</label>
                        <select class="form-select" id="project_id" name="project_id" required>
                            <option value="">-- Select Project --</option>
                            <?php foreach ($projects as $proj): ?>
                                <option value="<?= $proj['id'] ?>" <?= $proj['id'] == $request['project_id'] ? 'selected' : '' ?>>
                                    <?= $proj['contract_id'] . ' - ' . $proj['company_name'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div id="projectDetails">
                        <h6>Project Details</h6>
                        <p><strong>Scope of Work:</strong> <?= htmlspecialchars($project['scope_of_work']) ?></p>
                        <p><strong>Project Period:</strong> 
                            <?= $project['start_date'] ? date('F j, Y', strtotime($project['start_date'])) : 'Not specified' ?> 
                            to 
                            <?= $project['end_date'] ? date('F j, Y', strtotime($project['end_date'])) : 'Not specified' ?>
                        </p>
                        <p><strong>Total Amount:</strong> ₱<?= number_format($project['total_amount'], 2) ?></p>
                    </div>
                </div>
            </div>
            
            <!-- Material Items Section (shown for RFQ and PR) -->
            <?php if (in_array($request['request_type'], ['quotation', 'purchase'])): ?>
            <div class="sap-card mb-4" id="materialSection">
                <div class="sap-card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Material Requirements</h5>
                    <button type="button" class="btn btn-sm btn-primary" onclick="addMaterialItem()">
                        <i class="bi bi-plus"></i> Add Item
                    </button>
                </div>
                <div class="card-body">
                    <div id="materialItemsContainer">
                        <?php if (empty($materials)): ?>
                            <div class="alert alert-info" id="noMaterialsAlert">
                                No material items added yet. Click "Add Item" to get started.
                            </div>
                        <?php else: ?>
                            <?php foreach ($materials as $index => $material): ?>
                                <div class="material-item-card" data-index="<?= $index ?>">
                                    <div class="row">
                                        <div class="col-md-5">
                                            <div class="mb-3">
                                                <label class="form-label">Material</label>
                                                <select class="form-select material-select" name="material_items[<?= $index ?>][material_id]" required>
                                                    <option value="">-- Select Material --</option>
                                                    <?php 
                                                    $all_materials = $conn->query("SELECT id, name, description, unit_of_measure AS unit FROM materials ORDER BY name")
                                                                     ->fetch_all(MYSQLI_ASSOC);
                                                    foreach ($all_materials as $mat): ?>
                                                        <option value="<?= $mat['id'] ?>" 
                                                            data-uom="<?= htmlspecialchars($mat['unit']) ?>"
                                                            <?= $mat['id'] == $material['id'] ? 'selected' : '' ?>>
                                                            <?= htmlspecialchars($mat['name']) ?>
                                                            <?= $mat['description'] ? '(' . htmlspecialchars($mat['description']) . ')' : '' ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="mb-3">
                                                <label class="form-label">Quantity</label>
                                                <input type="number" class="form-control" name="material_items[<?= $index ?>][quantity]" 
                                                    value="<?= htmlspecialchars($material['quantity']) ?>" min="0.01" step="0.01" required>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label class="form-label">Unit of Measure</label>
                                                <input type="text" class="form-control material-uom" 
                                                    value="<?= htmlspecialchars($material['unit']) ?>" readonly>
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
                                                <label class="form-label">Notes</label>
                                                <textarea class="form-control" name="material_items[<?= $index ?>][notes]" rows="2"><?= htmlspecialchars($material['notes']) ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Supplier Selection -->
            <div class="sap-card mb-4">
                <div class="sap-card-header">
                    <h5 class="mb-0">Invite Suppliers</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="suppliers" class="form-label">Select Suppliers</label>
                        <select class="form-select select2" id="suppliers" name="suppliers[]" multiple="multiple" required>
                            <?php foreach ($suppliers as $supplier): ?>
                                <option value="<?= $supplier['id'] ?>"
                                    <?= in_array($supplier['id'], $invited_supplier_ids) ? 'selected' : '' ?>>
                                    <?= $supplier['company_name'] ? htmlspecialchars($supplier['company_name']) : htmlspecialchars($supplier['name']) ?>
                                    <?= $supplier['email'] ? '(' . htmlspecialchars($supplier['email']) . ')' : '' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            
            <!-- Request Details -->
            <div class="sap-card mb-4">
                <div class="sap-card-header">
                    <h5 class="mb-0">Request Details</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="request_details" class="form-label">Instructions & Requirements</label>
                        <textarea class="form-control" id="request_details" name="request_details" rows="6" required><?= htmlspecialchars($request['request_details']) ?></textarea>
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Update Request
                </button>
                <a href="view_request.php?id=<?= $request_id ?>" class="btn btn-outline-secondary">
                    <i class="bi bi-x-circle"></i> Cancel
                </a>
            </div>
        </form>
    <?php else: ?>
        <div class="alert alert-danger">Request not found</div>
        <a href="requests_list.php" class="btn btn-primary">Back to Requests List</a>
    <?php endif; ?>
</div>

<!-- Material Item Template (hidden) -->
<div id="materialItemTemplate" style="display: none;">
    <div class="material-item-card" data-index="{index}">
        <div class="row">
            <div class="col-md-5">
                <div class="mb-3">
                    <label class="form-label">Material</label>
                    <select class="form-select material-select" name="material_items[{index}][material_id]" required>
                        <option value="">-- Select Material --</option>
                        <?php 
                        $all_materials = $conn->query("SELECT id, name, description, unit_of_measure AS unit FROM materials ORDER BY name")
                                     ->fetch_all(MYSQLI_ASSOC);
                        foreach ($all_materials as $material): ?>
                            <option value="<?= $material['id'] ?>" 
                                data-uom="<?= htmlspecialchars($material['unit']) ?>">
                                <?= htmlspecialchars($material['name']) ?>
                                <?= $material['description'] ? '(' . htmlspecialchars($material['description']) . ')' : '' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <div class="mb-3">
                    <label class="form-label">Quantity</label>
                    <input type="number" class="form-control" name="material_items[{index}][quantity]" min="0.01" step="0.01" required>
                </div>
            </div>
            <div class="col-md-3">
                <div class="mb-3">
                    <label class="form-label">Unit of Measure</label>
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
                    <label class="form-label">Notes</label>
                    <textarea class="form-control" name="material_items[{index}][notes]" rows="2"></textarea>
                </div>
            </div>
        </div>
    </div>
</div>

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

    // Initialize material selects with Select2
    $('.material-select').select2({
        theme: 'bootstrap-5',
        placeholder: "Search materials",
        width: '100%'
    });

    // Update UOM when material is selected
    $(document).on('change', '.material-select', function() {
        const selectedOption = $(this).find('option:selected');
        const uom = selectedOption.data('uom');
        $(this).closest('.material-item-card').find('.material-uom').val(uom || 'EA');
    });
});

let materialItemCount = <?= count($materials) ?>;

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

// Show/hide material section based on request type
$("#request_type").change(function() {
    const type = $(this).val();
    if (type === 'inquiry') {
        $("#materialSection").hide();
    } else {
        $("#materialSection").show();
    }
});
</script>
</body>
</html>