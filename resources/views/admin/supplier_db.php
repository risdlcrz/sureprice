<?php
// supplier_db.php - Dedicated connection for supplier management

error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = 'localhost';
$dbname = 'supplier_management';
$username = 'root'; // Change to your MySQL username
$password = '';     // Change to your MySQL password

try {
    // First try to connect to MySQL without selecting a database
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create the database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS $dbname");
    
    // Connect to the specific database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if tables exist
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    
    // Only create tables if they don't exist
    if (!in_array('suppliers', $tables)) {
        // Create suppliers table
        $pdo->exec("CREATE TABLE suppliers (
            id INT AUTO_INCREMENT PRIMARY KEY,
            company VARCHAR(255) NOT NULL,
            materials TEXT NOT NULL,
            price DECIMAL(10,2) NOT NULL,
            rating DECIMAL(3,2) DEFAULT 0.00,
            contact_person VARCHAR(255),
            designation VARCHAR(255),
            email VARCHAR(255),
            mobile_number VARCHAR(50),
            telephone_number VARCHAR(50),
            address TEXT,
            business_reg_no VARCHAR(255),
            supplier_type VARCHAR(50),
            business_size VARCHAR(50),
            years_operation INT,
            payment_terms VARCHAR(50),
            vat_registered ENUM('Yes', 'No'),
            use_sureprice ENUM('Yes', 'No'),
            bank_name VARCHAR(255),
            account_name VARCHAR(255),
            account_number VARCHAR(255),
            dti_sec_registration_path VARCHAR(255),
            accreditation_docs_path VARCHAR(255),
            mayors_permit_path VARCHAR(255),
            valid_id_path VARCHAR(255),
            company_profile_path VARCHAR(255),
            price_list_path VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )");

        // Create supplier evaluations table
        $pdo->exec("CREATE TABLE supplier_evaluations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            supplier_id INT NOT NULL,
            engagement_score DECIMAL(3,2) NOT NULL,
            delivery_speed_score DECIMAL(3,2) NOT NULL,
            delivery_ontime_ratio DECIMAL(5,2) NOT NULL,
            performance_score DECIMAL(3,2) NOT NULL,
            quality_score DECIMAL(3,2) NOT NULL,
            defect_ratio DECIMAL(5,2) NOT NULL,
            cost_variance_score DECIMAL(3,2) NOT NULL,
            cost_variance_ratio DECIMAL(5,2) NOT NULL,
            sustainability_score DECIMAL(3,2) NOT NULL,
            final_score DECIMAL(3,2) NOT NULL,
            evaluation_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE CASCADE
        )");

        // Create supplier metrics table
        $pdo->exec("CREATE TABLE supplier_metrics (
            id INT AUTO_INCREMENT PRIMARY KEY,
            supplier_id INT NOT NULL,
            total_deliveries INT NOT NULL DEFAULT 0,
            ontime_deliveries INT NOT NULL DEFAULT 0,
            total_units INT NOT NULL DEFAULT 0,
            defective_units INT NOT NULL DEFAULT 0,
            estimated_cost DECIMAL(10,2) NOT NULL DEFAULT 0,
            actual_cost DECIMAL(10,2) NOT NULL DEFAULT 0,
            measurement_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE CASCADE
        )");

        // Insert sample data only if tables were just created
        $sampleData = [
            ['ABC Corp', 'Steel, Copper', 130000, 4.5],
            ['XYZ Ltd', 'Aluminum, Zinc', 85000, 4.2],
            ['LMN Inc', 'Plastic, Rubber', 100000, 3.8]
        ];
        
        $stmt = $pdo->prepare("INSERT INTO suppliers (company, materials, price, rating) VALUES (?, ?, ?, ?)");
        foreach ($sampleData as $data) {
            $stmt->execute($data);
            
            // Add sample evaluation data
            $supplierId = $pdo->lastInsertId();
            $evalStmt = $pdo->prepare("INSERT INTO supplier_evaluations (
                supplier_id, engagement_score, delivery_speed_score, delivery_ontime_ratio,
                performance_score, quality_score, defect_ratio, cost_variance_score,
                cost_variance_ratio, sustainability_score, final_score
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            
            // Generate random sample scores between 3.5 and 5.0
            $scores = array_map(function() {
                return rand(35, 50) / 10;
            }, range(1, 7));
            
            $evalStmt->execute([
                $supplierId,
                $scores[0], // engagement_score
                $scores[1], // delivery_speed_score
                rand(85, 100), // delivery_ontime_ratio
                $scores[2], // performance_score
                $scores[3], // quality_score
                rand(0, 15), // defect_ratio
                $scores[4], // cost_variance_score
                rand(-10, 10), // cost_variance_ratio
                $scores[5], // sustainability_score
                $scores[6]  // final_score
            ]);
            
            // Add sample metrics data
            $metricsStmt = $pdo->prepare("INSERT INTO supplier_metrics (
                supplier_id, total_deliveries, ontime_deliveries,
                total_units, defective_units, estimated_cost, actual_cost
            ) VALUES (?, ?, ?, ?, ?, ?, ?)");
            
            $totalDeliveries = rand(50, 100);
            $totalUnits = rand(1000, 5000);
            $estimatedCost = rand(100000, 500000);
            
            $metricsStmt->execute([
                $supplierId,
                $totalDeliveries,
                rand(ceil($totalDeliveries * 0.8), $totalDeliveries), // ontime_deliveries
                $totalUnits,
                rand(0, ceil($totalUnits * 0.15)), // defective_units
                $estimatedCost,
                $estimatedCost + rand(-50000, 50000) // actual_cost
            ]);
        }
    }
    
    error_log("Database connection successful");
    
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    die("Database connection failed: " . $e->getMessage());
}

return $pdo;
?>