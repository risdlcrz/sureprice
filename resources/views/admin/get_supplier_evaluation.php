<?php
require 'supplier_db.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');

header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Supplier ID is required']);
    exit;
}

try {
    // Log the request
    error_log("Fetching evaluation for supplier ID: " . $_GET['id']);
    
    // Get the latest evaluation for the supplier
    $stmt = $pdo->prepare("
        SELECT 
            e.*,
            m.total_deliveries,
            m.ontime_deliveries,
            m.total_units,
            m.defective_units,
            m.estimated_cost,
            m.actual_cost
        FROM supplier_evaluations e
        LEFT JOIN supplier_metrics m ON e.supplier_id = m.supplier_id
        WHERE e.supplier_id = ?
        ORDER BY e.evaluation_date DESC
        LIMIT 1
    ");
    
    $stmt->execute([$_GET['id']]);
    $evaluation = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Log the result
    error_log("Query result: " . ($evaluation ? "Found evaluation" : "No evaluation found"));
    
    if (!$evaluation) {
        // If no evaluation exists, return default values
        $defaultValues = [
            'final_score' => 0,
            'engagement_score' => 0,
            'delivery_speed_score' => 0,
            'delivery_ontime_ratio' => 0,
            'performance_score' => 0,
            'quality_score' => 0,
            'defect_ratio' => 0,
            'cost_variance_score' => 0,
            'cost_variance_ratio' => 0,
            'sustainability_score' => 0,
            'total_deliveries' => 0,
            'ontime_deliveries' => 0,
            'total_units' => 0,
            'defective_units' => 0,
            'estimated_cost' => 0,
            'actual_cost' => 0
        ];
        
        error_log("Returning default values for supplier ID: " . $_GET['id']);
        echo json_encode($defaultValues);
        exit;
    }
    
    // Log successful response
    error_log("Successfully fetched evaluation data for supplier ID: " . $_GET['id']);
    echo json_encode($evaluation);
    
} catch (PDOException $e) {
    error_log("Database error in get_supplier_evaluation.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'error' => 'Database error',
        'message' => $e->getMessage()
    ]);
} catch (Exception $e) {
    error_log("General error in get_supplier_evaluation.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'error' => 'Server error',
        'message' => $e->getMessage()
    ]);
} 