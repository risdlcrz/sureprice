<?php
// DB connection
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'sureprice');
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

$project_id = intval($_GET['project_id'] ?? 0);
$response = [];

if ($project_id > 0) {
    $sql = "SELECT 
                c.scope_of_work, 
                c.start_date, 
                c.end_date, 
                c.total_amount
            FROM contracts c
            WHERE c.id = $project_id";
    $result = $conn->query($sql);
    if ($row = $result->fetch_assoc()) {
        $response = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($response);
