<?php
// Include your header file and other necessary logic here
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Analytics Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./Styles/dbadmin.css">
</head>
<body>
    
    <div class="sidebar">
        <?php include './Include/header_analytics.php'; ?>
    </div>

    <div class="content">
        <h1 class="text-center my-4">Admin Dashboard</h1>

        <div class="top-controls">
            <!-- Optional top controls (if any) go here -->
        </div>

        <div class="row row-cols-1 row-cols-md-3 g-4">
            <!-- Card 1 -->
            <div class="col">
                <div class="card" onclick="window.location.href = './purchase-order.php';">
                    <img src="./Images/analyticsdash1.jpeg" alt="Image 1">
                    <div class="card-body">
                        <h5 class="card-title">Active Purchase Order</h5>
                    </div>
                </div>
            </div>
            <!-- Card 2 -->
            <div class="col">
                <div class="card" onclick="window.location.href = './budget-allocation.php';">
                    <img src="./Images/analyticsdash2.jpg" alt="Image 2">
                    <div class="card-body">
                        <h5 class="card-title">Budget Allocation and Expenditures</h5>
                    </div>
                </div>
            </div>
            <!-- Card 3 -->
            <div class="col">
                <div class="card" onclick="window.location.href = './supplier-rankings.php';">
                    <img src="./Images/analyticsdash3.jpg" alt="Image 3">
                    <div class="card-body">
                        <h5 class="card-title">Supplier Ranking and Performance</h5>
                    </div>
                </div>
            </div>
            <!-- Card 4 -->
            <div class="col">
                <div class="card" onclick="window.location.href = './price-analysis.php';">
                    <img src="./Images/analyticsdash4.jpg" alt="Image 4">
                    <div class="card-body">
                        <h5 class="card-title">Price Trend Analysis</h5>
                    </div>
                </div>
            </div>
            </div>
            </div>
        </div>
    </div>

</body>
</html>
