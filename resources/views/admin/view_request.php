<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'sureprice');
define('ADMIN_PASSWORD', 'admin123');

// Create database connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get request ID from URL
$request_id = isset($_GET['id']) ? intval($_GET['id']) : null;

// Fetch request data with suppliers
$request = null;
$suppliers = [];
$project = null;
$client = null;
$property = null;

if ($request_id) {
    // Get request details
    $sql = "SELECT sr.*, c.contract_id, c.scope_of_work, c.start_date, c.end_date, 
                   c.total_amount, c.client_id, p.company_name, c.property_id
            FROM supplier_requests sr
            JOIN contracts c ON sr.project_id = c.id
            JOIN parties p ON c.client_id = p.id
            WHERE sr.id = $request_id";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $request = $result->fetch_assoc();
        
        // Get invited suppliers
        $suppliers = $conn->query("SELECT s.*, rs.responded, rs.response_details, rs.responded_at, rs.invited_at
                                  FROM request_suppliers rs
                                  JOIN suppliers s ON rs.supplier_id = s.id
                                  WHERE rs.request_id = $request_id")->fetch_all(MYSQLI_ASSOC);
        
        // Get project property details
        $property_id = $request['property_id'];
        if ($property_id) {
            $property = $conn->query("SELECT * FROM properties WHERE id = $property_id")->fetch_assoc();
        }
    }
}

// Handle delete action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_request'])) {
    if ($_POST['admin_password'] === ADMIN_PASSWORD) {
        $conn->begin_transaction();
        try {
            // Delete supplier associations first
            $conn->query("DELETE FROM request_suppliers WHERE request_id = $request_id");
            
            // Then delete the request
            $conn->query("DELETE FROM supplier_requests WHERE id = $request_id");
            
            $conn->commit();
            header("Location: requests_list.php?deleted=1");
            exit();
        } catch (Exception $e) {
            $conn->rollback();
            $error = "Error deleting request: " . $e->getMessage();
        }
    } else {
        $error = "Incorrect admin password";
    }
}

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $new_status = $conn->real_escape_string($_POST['new_status']);
    $sql = "UPDATE supplier_requests SET status = '$new_status' WHERE id = $request_id";
    if ($conn->query($sql)) {
        $success = "Request status updated successfully";
        // Refresh request data with all joins
        $sql = "SELECT sr.*, c.contract_id, c.scope_of_work, c.start_date, c.end_date, 
                       c.total_amount, c.client_id, p.company_name, c.property_id
                FROM supplier_requests sr
                JOIN contracts c ON sr.project_id = c.id
                JOIN parties p ON c.client_id = p.id
                WHERE sr.id = $request_id";
        $result = $conn->query($sql);
        $request = $result->fetch_assoc();
    } else {
        $error = "Error updating request status: " . $conn->error;
    }
}

// Handle supplier removal
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_supplier'])) {
    $supplier_id = intval($_POST['supplier_id']);
    $sql = "DELETE FROM request_suppliers WHERE request_id = $request_id AND supplier_id = $supplier_id";
    if ($conn->query($sql)) {
        $success = "Supplier removed successfully";
        // Refresh suppliers list
        $suppliers = $conn->query("SELECT s.*, rs.responded, rs.response_details, rs.responded_at, rs.invited_at
                                  FROM request_suppliers rs
                                  JOIN suppliers s ON rs.supplier_id = s.id
                                  WHERE rs.request_id = $request_id")->fetch_all(MYSQLI_ASSOC);
    } else {
        $error = "Error removing supplier: " . $conn->error;
    }
}

// Handle supplier invitation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['invite_suppliers'])) {
    if (isset($_POST['suppliers']) && is_array($_POST['suppliers'])) {
        $supplier_ids = array_map('intval', $_POST['suppliers']);
        $current_time = date('Y-m-d H:i:s');
        
        $conn->begin_transaction();
        try {
            foreach ($supplier_ids as $supplier_id) {
                // Check if supplier is already invited
                $check_sql = "SELECT COUNT(*) as count FROM request_suppliers 
                              WHERE request_id = $request_id AND supplier_id = $supplier_id";
                $check_result = $conn->query($check_sql);
                $exists = $check_result->fetch_assoc()['count'] > 0;
                
                if (!$exists) {
                    $insert_sql = "INSERT INTO request_suppliers (request_id, supplier_id, invited_at)
                                   VALUES ($request_id, $supplier_id, '$current_time')";
                    if (!$conn->query($insert_sql)) {
                        throw new Exception("Failed to invite supplier ID $supplier_id");
                    }
                }
            }
            
            $conn->commit();
            $success = "Suppliers invited successfully";
            // Refresh suppliers list
            $suppliers = $conn->query("SELECT s.*, rs.responded, rs.response_details, rs.responded_at, rs.invited_at
                                      FROM request_suppliers rs
                                      JOIN suppliers s ON rs.supplier_id = s.id
                                      WHERE rs.request_id = $request_id")->fetch_all(MYSQLI_ASSOC);
        } catch (Exception $e) {
            $conn->rollback();
            $error = "Error inviting suppliers: " . $e->getMessage();
        }
    } else {
        $error = "No suppliers selected for invitation";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Request</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <style>
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
        .supplier-response {
            border-left: 4px solid #0d6efd;
            padding-left: 15px;
            margin-bottom: 15px;
        }
        .timeline {
            position: relative;
            padding-left: 30px;
        }
        .timeline::before {
            content: '';
            position: absolute;
            left: 10px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #dee2e6;
        }
        .timeline-item {
            position: relative;
            margin-bottom: 20px;
        }
        .timeline-item::before {
            content: '';
            position: absolute;
            left: -30px;
            top: 5px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #0d6efd;
            border: 2px solid white;
        }
        .card-header {
            background-color: #f8f9fa;
        }
        .request-type-badge {
            font-size: 0.8rem;
            padding: 4px 8px;
            text-transform: uppercase;
        }
        .rfq-badge {
            background-color: #6610f2;
            color: white;
        }
        .rfi-badge {
            background-color: #20c997;
            color: white;
        }
        .pr-badge {
            background-color: #fd7e14;
            color: white;
        }
        
        /* Select2 dropdown fixes */
        .select2-container--open .select2-dropdown--below {
            z-index: 1060 !important;
            margin-top: 5px;
        }
        
        .select2-results__options {
            max-height: 200px;
            overflow-y: auto;
        }
        
        .select2-container {
            z-index: 1055 !important;
        }
        
        .select2-selection {
            min-height: 38px;
        }
        
        #inviteSupplierModal .modal-body {
            min-height: 300px;
        }
        
        /* Navigation buttons */
        .navigation-buttons {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <!-- Navigation Buttons -->
        <div class="navigation-buttons d-flex justify-content-between mb-4">
            <a href="requests_list.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Requests
            </a>
            <a href="create_request.php" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Create New Request
            </a>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <?php if ($request): ?>
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="mb-1">
                        <?php 
                        $type_badge_class = '';
                        $type_text = '';
                        switch($request['request_type']) {
                            case 'quotation': 
                                $type_badge_class = 'rfq-badge';
                                $type_text = 'RFQ';
                                break;
                            case 'inquiry':
                                $type_badge_class = 'rfi-badge';
                                $type_text = 'RFI';
                                break;
                            case 'purchase':
                                $type_badge_class = 'pr-badge';
                                $type_text = 'PR';
                                break;
                        }
                        ?>
                        <span class="request-type-badge <?php echo $type_badge_class; ?> me-2">
                            <?php echo $type_text; ?>
                        </span>
                        Supplier Request
                    </h1>
                    <div class="text-muted">Project: <?php echo htmlspecialchars($request['contract_id'] ?? 'N/A'); ?></div>
                </div>
                <div>
                    <span class="status-badge status-<?php echo $request['status']; ?>">
                        Status: <?php echo strtoupper($request['status']); ?>
                    </span>
                </div>
            </div>
            
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Request Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <p><strong>Type:</strong> <?php echo ucfirst($request['request_type']); ?></p>
                                    <p><strong>Created:</strong> <?php echo date('F j, Y H:i', strtotime($request['created_at'])); ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Last Updated:</strong> <?php echo date('F j, Y H:i', strtotime($request['updated_at'])); ?></p>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <h6>Request Details</h6>
                                <div class="border p-3 bg-light rounded">
                                    <?php echo nl2br(htmlspecialchars($request['request_details'])); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Project Information</h5>
                        </div>
                        <div class="card-body">
                            <p><strong>Client:</strong> <?php echo htmlspecialchars($request['company_name'] ?? 'N/A'); ?></p>
                            <p><strong>Scope of Work:</strong> <?php echo htmlspecialchars($request['scope_of_work'] ?? 'N/A'); ?></p>
                            <p><strong>Project Period:</strong> 
                                <?php echo isset($request['start_date']) ? date('M j, Y', strtotime($request['start_date'])) : 'N/A'; ?> to 
                                <?php echo isset($request['end_date']) ? date('M j, Y', strtotime($request['end_date'])) : 'N/A'; ?>
                            </p>
                            <p><strong>Total Amount:</strong> ₱<?php echo isset($request['total_amount']) ? number_format($request['total_amount'], 2) : '0.00'; ?></p>
                            
                            <?php if ($property): ?>
                                <hr>
                                <h6>Property Details</h6>
                                <p><?php echo htmlspecialchars($property['street'] ?? ''); ?><br>
                                <?php echo htmlspecialchars($property['city'] ?? ''); ?>, <?php echo htmlspecialchars($property['state'] ?? ''); ?> <?php echo htmlspecialchars($property['postal'] ?? ''); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Supplier Invitations Section -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Supplier Invitations</h5>
                    <?php if ($request['status'] === 'draft'): ?>
                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#inviteSupplierModal">
                            <i class="bi bi-plus"></i> Invite Suppliers
                        </button>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <?php if (empty($suppliers)): ?>
                        <div class="alert alert-info">No suppliers have been invited to this request yet.</div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Supplier</th>
                                        <th>Contact</th>
                                        <th>Status</th>
                                        <th>Response</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($suppliers as $supplier): ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo htmlspecialchars($supplier['company_name'] ?: $supplier['name']); ?></strong>
                                            </td>
                                            <td>
                                                <?php if ($supplier['email']): ?>
                                                    <div><i class="bi bi-envelope me-2"></i><?php echo htmlspecialchars($supplier['email']); ?></div>
                                                <?php endif; ?>
                                                <?php if ($supplier['phone']): ?>
                                                    <div><i class="bi bi-telephone me-2"></i><?php echo htmlspecialchars($supplier['phone']); ?></div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($supplier['responded']): ?>
                                                    <span class="badge bg-success rounded-pill">Responded</span>
                                                    <div class="text-muted small">
                                                        <?php echo date('M j, Y H:i', strtotime($supplier['responded_at'])); ?>
                                                    </div>
                                                <?php else: ?>
                                                    <span class="badge bg-warning text-dark rounded-pill">Pending</span>
                                                    <div class="text-muted small">
                                                        Invited: <?php echo date('M j, Y', strtotime($supplier['invited_at'])); ?>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($supplier['responded'] && $supplier['response_details']): ?>
                                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" 
                                                            data-bs-target="#responseModal<?php echo $supplier['id']; ?>">
                                                        <i class="bi bi-eye"></i> View
                                                    </button>
                                                <?php elseif ($supplier['responded']): ?>
                                                    <span class="text-muted">No details</span>
                                                <?php else: ?>
                                                    <span class="text-muted">Awaiting</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($request['status'] === 'draft'): ?>
                                                    <form method="POST" style="display: inline;">
                                                        <input type="hidden" name="supplier_id" value="<?php echo $supplier['id']; ?>">
                                                        <button type="submit" name="remove_supplier" class="btn btn-sm btn-outline-danger">
                                                            <i class="bi bi-trash"></i> Remove
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                                <a href="mailto:<?php echo htmlspecialchars($supplier['email']); ?>" 
                                                   class="btn btn-sm btn-outline-secondary">
                                                   <i class="bi bi-envelope"></i> Contact
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Response Modals -->
            <?php foreach ($suppliers as $supplier): ?>
                <?php if ($supplier['responded'] && $supplier['response_details']): ?>
                    <div class="modal fade" id="responseModal<?php echo $supplier['id']; ?>" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">
                                        Response from <?php echo htmlspecialchars($supplier['company_name'] ?: $supplier['name']); ?>
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="timeline">
                                        <div class="timeline-item">
                                            <h6>Invitation Sent</h6>
                                            <p class="text-muted small"><?php echo date('F j, Y H:i', strtotime($supplier['invited_at'])); ?></p>
                                        </div>
                                        <div class="timeline-item">
                                            <h6>Response Received</h6>
                                            <p class="text-muted small"><?php echo date('F j, Y H:i', strtotime($supplier['responded_at'])); ?></p>
                                            <div class="supplier-response mt-2">
                                                <?php echo nl2br(htmlspecialchars($supplier['response_details'])); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <a href="mailto:<?php echo htmlspecialchars($supplier['email']); ?>" class="btn btn-primary">
                                        <i class="bi bi-envelope"></i> Contact Supplier
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
            
            <!-- Invite Supplier Modal -->
            <div class="modal fade" id="inviteSupplierModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <form method="POST">
                            <input type="hidden" name="request_id" value="<?php echo $request_id; ?>">
                            <input type="hidden" name="invite_suppliers" value="1">
                            <div class="modal-header">
                                <h5 class="modal-title">Invite Additional Suppliers</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label">Select Suppliers</label>
                                    <select class="form-select select2" name="suppliers[]" multiple="multiple" required 
                                            style="width: 100%;" data-placeholder="Search and select suppliers...">
                                        <?php 
                                        // Get all suppliers not already invited
                                        $all_suppliers = $conn->query("SELECT * FROM suppliers 
                                                                      WHERE id NOT IN (
                                                                          SELECT supplier_id 
                                                                          FROM request_suppliers 
                                                                          WHERE request_id = $request_id
                                                                      )");
                                        while ($supplier = $all_suppliers->fetch_assoc()): ?>
                                            <option value="<?php echo $supplier['id']; ?>">
                                                <?php echo htmlspecialchars($supplier['company_name'] ?: $supplier['name']); ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Invite Suppliers</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Request Management -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Request Management</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="btn-group me-2">
                                <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                    <i class="bi bi-gear"></i> Actions
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="edit_request.php?id=<?php echo $request_id; ?>">
                                        <i class="bi bi-pencil"></i> Edit Request
                                    </a></li>
                                    <li><a class="dropdown-item" href="#">
                                        <i class="bi bi-file-earmark-pdf"></i> Export as PDF
                                    </a></li>
                                    <li><a class="dropdown-item" href="#">
                                        <i class="bi bi-envelope"></i> Send Reminders
                                    </a></li>
                                </ul>
                            </div>
                            
                            <div class="btn-group">
                                <button type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                                    <i class="bi bi-arrow-repeat"></i> Update Status
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#" onclick="updateStatus('draft')">
                                        <i class="bi bi-file-earmark"></i> Set to Draft
                                    </a></li>
                                    <li><a class="dropdown-item" href="#" onclick="updateStatus('pending')">
                                        <i class="bi bi-hourglass"></i> Set to Pending
                                    </a></li>
                                    <li><a class="dropdown-item" href="#" onclick="updateStatus('approved')">
                                        <i class="bi bi-check-circle"></i> Approve
                                    </a></li>
                                    <li><a class="dropdown-item" href="#" onclick="updateStatus('rejected')">
                                        <i class="bi bi-x-circle"></i> Reject
                                    </a></li>
                                </ul>
                            </div>
                        </div>
                        
                        <div>
                            <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                <i class="bi bi-trash"></i> Delete Request
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Delete Confirmation Modal -->
            <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form method="POST">
                            <div class="modal-header">
                                <h5 class="modal-title" id="deleteModalLabel">Delete Request</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>Are you sure you want to delete this request? This action cannot be undone.</p>
                                <div class="mb-3">
                                    <label for="admin_password" class="form-label">Admin Password</label>
                                    <input type="password" class="form-control" id="admin_password" name="admin_password" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" name="delete_request" class="btn btn-danger">Delete</button>
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
            <div class="alert alert-danger">Request not found</div>
            <a href="requests_list.php" class="btn btn-primary">Back to Requests List</a>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize Select2 with proper configuration for modals
            $('.select2').select2({
                theme: 'bootstrap-5',
                placeholder: "Search and select suppliers...",
                width: '100%',
                dropdownParent: $('#inviteSupplierModal'), // Crucial for modal display
                dropdownAutoWidth: true,
                closeOnSelect: false
            });

            // Refresh Select2 when modal opens to ensure proper rendering
            $('#inviteSupplierModal').on('shown.bs.modal', function () {
                $('.select2').select2('close');
                setTimeout(function() {
                    $('.select2').select2('open');
                }, 100);
            });
            
            // Close Select2 when modal hides to prevent display issues
            $('#inviteSupplierModal').on('hidden.bs.modal', function () {
                $('.select2').select2('close');
            });
        });
        
        function updateStatus(status) {
            document.getElementById('new_status').value = status;
            document.getElementById('statusForm').submit();
        }
    </script>
</body>
</html>