<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $project_title = htmlspecialchars($_POST["project_title"]);
    $description = htmlspecialchars($_POST["description"]);
    $date_needed = $_POST["date_needed"];
    $phone_number = $_POST["phone_number"];
    $postal_code = $_POST["postal_code"];
    $shipping_instructions = htmlspecialchars($_POST["shipping_instructions"]);
    $first_name = htmlspecialchars($_POST["first_name"]);
    $last_name = htmlspecialchars($_POST["last_name"]);
    $company_name = htmlspecialchars($_POST["company_name"]);
    $email = htmlspecialchars($_POST["email"]);
    $invited_suppliers = htmlspecialchars($_POST["invited_suppliers"]);

    // File upload
    if (isset($_FILES["project_file"]) && $_FILES["project_file"]["error"] == 0) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir);
        $target_file = $target_dir . basename($_FILES["project_file"]["name"]);
        move_uploaded_file($_FILES["project_file"]["tmp_name"], $target_file);
    }

    echo "<h2>Project submitted successfully!</h2>";
    echo "<p><strong>Project:</strong> $project_title</p>";
    echo "<p><strong>Description:</strong> $description</p>";
    echo "<p><strong>Invited Suppliers:</strong> $invited_suppliers</p>";
} else {
    echo "Invalid request.";
}
?>
