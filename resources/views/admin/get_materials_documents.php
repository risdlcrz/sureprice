<?php
require 'supplier_db.php';

header('Content-Type: application/json');

if (!isset($_GET['supplier_id'])) {
    echo json_encode(['error' => 'Supplier ID not provided']);
    exit;
}

$supplierId = intval($_GET['supplier_id']);

try {
    $stmt = $pdo->prepare("
        SELECT * FROM materials_documents 
        WHERE supplier_id = ? 
        ORDER BY upload_date DESC
    ");
    $stmt->execute([$supplierId]);
    $documents = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($documents);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
