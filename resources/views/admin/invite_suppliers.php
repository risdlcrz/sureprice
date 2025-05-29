<?php
// DB Config
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'sureprice');
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $request_id = intval($_POST['request_id']);
    $supplier_ids = $_POST['suppliers'] ?? [];
    
    if ($request_id && !empty($supplier_ids)) {
        $conn->begin_transaction();
        try {
            foreach ($supplier_ids as $supplier_id) {
                $supplier_id = intval($supplier_id);
                $conn->query("INSERT INTO request_suppliers (request_id, supplier_id) 
                              VALUES ($request_id, $supplier_id)");
            }
            $conn->commit();
            header("Location: view_request.php?id=$request_id&invited=1");
            exit();
        } catch (Exception $e) {
            $conn->rollback();
            header("Location: view_request.php?id=$request_id&error=" . urlencode($e->getMessage()));
            exit();
        }
    } else {
        header("Location: view_request.php?id=$request_id&error=No suppliers selected");
        exit();
    }
} else {
    header("Location: requests_list.php");
    exit();
}