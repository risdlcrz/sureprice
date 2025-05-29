<?php
// DB Config
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'sureprice');
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Fetch all requests
$requests = [];
$sql = "SELECT sr.*, c.contract_id, p.company_name 
        FROM supplier_requests sr
        JOIN contracts c ON sr.project_id = c.id
        JOIN parties p ON c.client_id = p.id
        ORDER BY sr.created_at DESC";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $requests[] = $row;
}

// Handle delete success message
$deleted = isset($_GET['deleted']) ? intval($_GET['deleted']) : 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Supplier Requests</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.0/font/bootstrap-icons.css">
    <style>
        .status-badge {
            font-size: 0.8rem;
            padding: 5px 10px;
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
    </style>
</head>
<body class="p-4">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Supplier Requests</h1>
            <a href="create_request.php" class="btn btn-primary">Create New Request</a>
        </div>
        
        <?php if ($deleted): ?>
            <div class="alert alert-success">Request deleted successfully</div>
        <?php endif; ?>
        
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Type</th>
                        <th>Project</th>
                        <th>Client</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($requests as $req): ?>
                        <tr>
                            <td><?php echo $req['id']; ?></td>
                            <td><?php echo ucfirst($req['request_type']); ?></td>
                            <td><?php echo $req['contract_id']; ?></td>
                            <td><?php echo $req['company_name']; ?></td>
                            <td>
                                <span class="status-badge status-<?php echo $req['status']; ?>">
                                    <?php echo ucfirst($req['status']); ?>
                                </span>
                            </td>
                            <td><?php echo date('M j, Y', strtotime($req['created_at'])); ?></td>
                            <td>
                                <a href="view_request.php?id=<?php echo $req['id']; ?>" class="btn btn-sm btn-info">View</a>
                                <a href="edit_request.php?id=<?php echo $req['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>