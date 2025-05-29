<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'sureprice');

// Admin password for sensitive actions
define('ADMIN_PASSWORD', 'admin123'); // Change this to a secure password

// Create database connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get contract ID from URL
$contract_id = isset($_GET['id']) ? $conn->real_escape_string($_GET['id']) : null;

// Fetch contract data
$contract = null;
$items = [];
$client = null;
$contractor = null;
$property = null;

if ($contract_id) {
    // Get contract
    $sql = "SELECT * FROM contracts WHERE contract_id = '$contract_id'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $contract = $result->fetch_assoc();
        
        // Get client
        $client_id = $contract['client_id'];
        $sql = "SELECT * FROM parties WHERE id = $client_id";
        $result = $conn->query($sql);
        $client = $result->fetch_assoc();
        
        // Get contractor
        $contractor_id = $contract['contractor_id'];
        $sql = "SELECT * FROM parties WHERE id = $contractor_id";
        $result = $conn->query($sql);
        $contractor = $result->fetch_assoc();
        
        // Get property
        $property_id = $contract['property_id'];
        $sql = "SELECT * FROM properties WHERE id = $property_id";
        $result = $conn->query($sql);
        $property = $result->fetch_assoc();
        
        // Get items
        $sql = "SELECT * FROM contract_items WHERE contract_id = {$contract['id']}";
        $result = $conn->query($sql);
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }
    }
}

// Handle delete action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_contract'])) {
    if ($_POST['admin_password'] === ADMIN_PASSWORD) {
        // Delete contract
        $conn->begin_transaction();
        try {
            // Delete items first
            $sql = "DELETE FROM contract_items WHERE contract_id = {$contract['id']}";
            $conn->query($sql);
            
            // Then delete contract
            $sql = "DELETE FROM contracts WHERE id = {$contract['id']}";
            $conn->query($sql);
            
            $conn->commit();
            header("Location: contracts_list.php?deleted=1");
            exit();
        } catch (Exception $e) {
            $conn->rollback();
            $error = "Error deleting contract: " . $e->getMessage();
        }
    } else {
        $error = "Incorrect admin password";
    }
}

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $new_status = $conn->real_escape_string($_POST['new_status']);
    $sql = "UPDATE contracts SET status = '$new_status' WHERE id = {$contract['id']}";
    if ($conn->query($sql)) {
        $success = "Contract status updated successfully";
        // Refresh contract data
        $sql = "SELECT * FROM contracts WHERE id = {$contract['id']}";
        $result = $conn->query($sql);
        $contract = $result->fetch_assoc();
    } else {
        $error = "Error updating contract status: " . $conn->error;
    }
}

// Function to get correct signature path
function getSignaturePath($path) {
    if (empty($path)) {
        return null;
    }

    // Remove any URL parameters if present
    $path = strtok($path, '?');

    // Case 1: Already a valid URL
    if (filter_var($path, FILTER_VALIDATE_URL)) {
        return $path;
    }

    // Case 2: Absolute server path
    if (realpath($path) === $path) {
        return file_exists($path) ? $path : null;
    }

    // Case 3: Relative to document root (most common)
    $doc_root = rtrim($_SERVER['DOCUMENT_ROOT'], '/');
    $full_path = $doc_root . '/' . ltrim($path, '/');

    if (file_exists($full_path)) {
        return '/' . ltrim($path, '/');
    }

    // Case 4: Just a filename - check in uploads directory
    $uploads_path = $doc_root . '/uploads/' . basename($path);
    if (file_exists($uploads_path)) {
        return '/uploads/' . basename($path);
    }

    // Case 5: Try raw path as last resort
    if (file_exists($path)) {
        return $path;
    }

    return null;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Contract</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.0/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .contract-container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin-top: 20px;
        }
        .signature-img {
            max-width: 200px;
            max-height: 100px;
            border: 1px solid #ddd;
            background-color: white;
        }
        .status-badge {
            font-size: 1rem;
            padding: 8px 12px;
            border-radius: 20px;
        }
        .status-draft {
            background-color: #6c757d;
            color: white;
        }
        .status-pending {
            background-color: #ffc107;
            color: black;
        }
        .status-approved {
            background-color: #28a745;
            color: white;
        }
        .status-rejected {
            background-color: #dc3545;
            color: white;
        }
        .action-buttons {
            margin-bottom: 20px;
        }
        .title-section {
            margin-bottom: 30px;
        }
        .top-menu {
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        .signature-error {
            color: red;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <?php if ($contract): ?>
            <div class="title-section">
                <h1 class="text-center mb-2">Contract Agreement</h1>
                <h3 class="text-center text-muted">Contract ID: <?php echo htmlspecialchars($contract['contract_id']); ?></h3>
            </div>
            
            <div class="top-menu">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <a href="contracts_list.php" class="btn btn-outline-secondary me-2">
                            <i class="bi bi-arrow-left"></i> Back to Contracts
                        </a>
                        
                        <a href="contract_form.php" class="btn btn-primary me-2">Create New</a>
                        <a href="contract_form.php?edit=<?php echo $contract['contract_id']; ?>" class="btn btn-warning me-2">Edit</a>
                        
                        <!-- Status update dropdown -->
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                Update Status
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" onclick="updateStatus('draft')">Draft</a></li>
                                <li><a class="dropdown-item" href="#" onclick="updateStatus('pending')">Pending Approval</a></li>
                                <li><a class="dropdown-item" href="#" onclick="updateStatus('approved')">Approve</a></li>
                                <li><a class="dropdown-item" href="#" onclick="updateStatus('rejected')">Reject</a></li>
                            </ul>
                        </div>
                        
                        <!-- PDF Download -->
                        <a href="contracts/<?php echo htmlspecialchars($contract['contract_id']); ?>.pdf" class="btn btn-success me-2" target="_blank">
                            <i class="bi bi-file-earmark-pdf"></i> Download PDF
                        </a>
                    </div>
                    
                    <div>
                        <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <i class="bi bi-trash"></i> Delete
                        </button>
                    </div>
                </div>
                
                <!-- Current status display -->
                <div class="mb-3">
                    <span class="status-badge status-<?php echo $contract['status'] ?? 'draft'; ?>">
                        Status: <?php echo strtoupper($contract['status'] ?? 'DRAFT'); ?>
                    </span>
                </div>
            </div>
            
            <div class="contract-container">
                <!-- Contractor Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h4>Contractor Information</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Name:</strong> <?php echo htmlspecialchars($contractor['name']); ?></p>
                                <p><strong>Company:</strong> <?php echo htmlspecialchars($contractor['company_name']); ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Address:</strong><br>
                                    <?php echo htmlspecialchars($contractor['street']); ?><br>
                                    <?php echo htmlspecialchars($contractor['city']); ?>, <?php echo htmlspecialchars($contractor['state']); ?> <?php echo htmlspecialchars($contractor['postal']); ?>
                                </p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Email:</strong> <?php echo htmlspecialchars($contractor['email']); ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Phone:</strong> <?php echo htmlspecialchars($contractor['phone']); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Property Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h4>Property Information</h4>
                    </div>
                    <div class="card-body">
                        <p><strong>Address:</strong><br>
                            <?php echo htmlspecialchars($property['street']); ?><br>
                            <?php echo htmlspecialchars($property['city']); ?>, <?php echo htmlspecialchars($property['state']); ?> <?php echo htmlspecialchars($property['postal']); ?>
                        </p>
                    </div>
                </div>
                
                <!-- Scope of Work -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h4>Scope of Work</h4>
                    </div>
                    <div class="card-body">
                        <p><strong>Work Types:</strong> <?php echo htmlspecialchars($contract['scope_of_work']); ?></p>
                        <?php if (!empty($contract['scope_description'])): ?>
                            <p><strong>Description:</strong><br><?php echo nl2br(htmlspecialchars($contract['scope_description'])); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Project Period -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h4>Project Period</h4>
                    </div>
                    <div class="card-body">
                        <p>Start Date: <?php echo date('F j, Y', strtotime($contract['start_date'])); ?></p>
                        <p>End Date: <?php echo date('F j, Y', strtotime($contract['end_date'])); ?></p>
                    </div>
                </div>
                
                <!-- Amount -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h4>Amount</h4>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Description</th>
                                    <th>Quantity</th>
                                    <th>Amount</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($items as $item): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($item['description']); ?></td>
                                        <td><?php echo $item['quantity']; ?></td>
                                        <td>$<?php echo number_format($item['amount'], 2); ?></td>
                                        <td>$<?php echo number_format($item['total'], 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                <tr>
                                    <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                    <td><strong>$<?php echo number_format($contract['total_amount'], 2); ?></strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Contract Terms -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h4>Contract Terms</h4>
                    </div>
                    <div class="card-body">
                        <?php echo nl2br(htmlspecialchars($contract['contract_terms'])); ?>
                    </div>
                </div>
                
                <!-- Jurisdiction -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h4>Jurisdiction</h4>
                    </div>
                    <div class="card-body">
                        <p><?php echo htmlspecialchars($contract['jurisdiction']); ?></p>
                    </div>
                </div>
                
                <!-- Signatures -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h4>Signatures</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Client Signature -->
                            <div class="col-md-6">
                                <h5>Client Signature</h5>
                                <?php if (!empty($contract['client_signature'])): ?>
                                    <?php
                                    $sig_path = getSignaturePath($contract['client_signature']);
                                    if ($sig_path !== null): ?>
                                        <img src="<?php echo htmlspecialchars($sig_path); ?>" class="signature-img mb-2" 
                                            onerror="this.onerror=null;this.style.display='none';this.nextElementSibling.style.display='block'">
                                        <div class="alert alert-warning" style="display:none">
                                            Signature image failed to load:<br>
                                            <?php echo htmlspecialchars($contract['client_signature']); ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-warning">
                                            Signature file not found at:<br>
                                            <?php echo htmlspecialchars($contract['client_signature']); ?>
                                        </div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <p>No signature provided</p>
                                <?php endif; ?>
                                <p><strong>Name:</strong> <?php echo htmlspecialchars($client['name']); ?></p>
                                <p><strong>Date:</strong> <?php echo date('F j, Y', strtotime($contract['created_at'])); ?></p>
                            </div>
                            <div class="col-md-6">
                                <h5>Contractor Signature</h5>
                                <?php if (!empty($contract['contractor_signature'])): ?>
                                    <?php
                                    $contractor_sig_path = getSignaturePath($contract['contractor_signature']);
                                    if ($contractor_sig_path !== null): ?>
                                        <img src="<?php echo htmlspecialchars($contractor_sig_path); ?>" class="signature-img mb-2" 
                                            onerror="this.onerror=null;this.style.display='none';this.nextElementSibling.style.display='block'">
                                        <div class="alert alert-warning" style="display:none">
                                            Signature image failed to load:<br>
                                            <?php echo htmlspecialchars($contract['contractor_signature']); ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-warning">
                                            Signature file not found at:<br>
                                            <?php echo htmlspecialchars($contract['contractor_signature']); ?>
                                        </div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <p>No signature provided</p>
                                <?php endif; ?>
                                <p><strong>Name:</strong> <?php echo htmlspecialchars($contractor['name']); ?></p>
                                <p><strong>Date:</strong> <?php echo date('F j, Y', strtotime($contract['created_at'])); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- PDF Download -->
                <div class="text-center mt-4">
                    <a href="contracts/<?php echo htmlspecialchars($contract['contract_id']); ?>.pdf" class="btn btn-primary" target="_blank">Download PDF</a>
                </div>
            </div>
            
            <!-- Delete Confirmation Modal -->
            <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form method="POST">
                            <div class="modal-header">
                                <h5 class="modal-title" id="deleteModalLabel">Delete Contract</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>Are you sure you want to delete this contract? This action cannot be undone.</p>
                                <div class="mb-3">
                                    <label for="admin_password" class="form-label">Admin Password</label>
                                    <input type="password" class="form-control" id="admin_password" name="admin_password" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" name="delete_contract" class="btn btn-danger">Delete</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Status Update Form (hidden) -->
            <form method="POST" id="statusForm" style="display: none;">
                <input type="hidden" name="update_status" value="1">
                <input type="hidden" name="new_status" id="new_status">
            </form>
            
        <?php else: ?>
            <div class="alert alert-danger">Contract not found</div>
            <a href="contracts_list.php" class="btn btn-primary">Back to Contracts List</a>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function updateStatus(status) {
            document.getElementById('new_status').value = status;
            document.getElementById('statusForm').submit();
        }
    </script>
</body>
</html>