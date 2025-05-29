<?php
$mysqli = new mysqli("localhost", "root", "", "your_database_name");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$term = $_GET['term'] ?? '';
$term = strtolower($mysqli->real_escape_string($term));

$sql = "SELECT id, username, name FROM suppliers 
        WHERE LOWER(username) LIKE '%$term%' OR LOWER(name) LIKE '%$term%' 
        LIMIT 10";

$result = $mysqli->query($sql);
$suppliers = [];

while ($row = $result->fetch_assoc()) {
    $suppliers[] = $row;
}

header('Content-Type: application/json');
echo json_encode($suppliers);
?>
