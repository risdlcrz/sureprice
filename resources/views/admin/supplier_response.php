<?php
// DB Config
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'sureprice');
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $request_id = intval($_POST['request_id']);
    $supplier_id = intval($_POST['supplier_id']);
    $response_details = $conn->real_escape_string($_POST['response_details']);
    
    $sql = "UPDATE request_suppliers SET 
            responded = TRUE,
            response_details = '$response_details',
            responded_at = NOW()
            WHERE request_id = $request_id AND supplier_id = $supplier_id";
    
    if ($conn->query($sql)) {
        $success = "Response submitted successfully!";
    } else {
        $error = "Error submitting response: " . $conn->error;
    }
}

// Get request and supplier info
$request_id = isset($_GET['request_id']) ? intval($_GET['request_id']) : 0;
$supplier_id = isset($_GET['supplier_id']) ? intval($_GET['supplier_id']) : 0;

$request = $conn->query("SELECT sr.*, c.contract_id, p.company_name 
                         FROM supplier_requests sr
                         JOIN contracts c ON sr.project_id = c.id
                         JOIN parties p ON c.client_id = p.id
                         WHERE sr.id = $request_id")->fetch_assoc();

$supplier = $conn->query("SELECT * FROM suppliers WHERE id = $supplier_id")->fetch_assoc();

// Check if already responded
$responded = $conn->query("SELECT responded FROM request_suppliers 
                           WHERE request_id = $request_id AND supplier_id = $supplier_id")
                  ->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Submit Response</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="p-4">
<div class="container">
    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <a href="supplier_response.php?request_id=<?php echo $request_id; ?>&supplier_id=<?php echo $supplier_id; ?>" 
           class="btn btn-primary">
           View Response
        </a>
        <?php exit(); ?>
    <?php endif; ?>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    
    <?php if ($request && $supplier): ?>
        <div class="card mb-4">
            <div class="card-header">
                <h2>
                    <?php echo strtoupper($request['request_type']); ?> Response
                    <small class="text-muted">#<?php echo $request['contract_id']; ?></small>
                </h2>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h4>Request Details</h4>
                        <p><strong>Client:</strong> <?php echo htmlspecialchars($request['company_name']); ?></p>
                        <p><strong>Scope:</strong> <?php echo htmlspecialchars($request['scope_of_work']); ?></p>
                        <p><strong>Created:</strong> <?php echo date('F j, Y', strtotime($request['created_at'])); ?></p>
                    </div>
                    <div class="col-md-6">
                        <h4>Your Information</h4>
                        <p><strong>Supplier:</strong> <?php echo htmlspecialchars($supplier['company_name'] ?: $supplier['name']); ?></p>
                        <p><strong>Contact:</strong> <?php echo htmlspecialchars($supplier['email']); ?></p>
                    </div>
                </div>
                
                <?php if ($responded && $responded['responded']): ?>
                    <div class="alert alert-info">
                        <h4>You've already submitted a response</h4>
                        <p>If you need to update your response, please contact the requester directly.</p>
                    </div>
                <?php else: ?>
                    <form method="POST">
                        <input type="hidden" name="request_id" value="<?php echo $request_id; ?>">
                        <input type="hidden" name="supplier_id" value="<?php echo $supplier_id; ?>">
                        
                        <div class="mb-3">
                            <label for="response_details" class="form-label">Your Response</label>
                            <textarea class="form-control" id="response_details" name="response_details" rows="8" required></textarea>
                            <div class="form-text">
                                Please provide detailed information including pricing, availability, and any terms.
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Submit Response</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-danger">Invalid request or supplier information</div>
    <?php endif; ?>
</div>
</body>
</html>