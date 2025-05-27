<?php
// Simulated data with more realistic prices in Philippine Peso (PHP)
// Prices are adjusted to reflect current material prices in the Philippines

$suppliers = [
    ['company' => 'ABC Corp', 'materials' => 'Steel, Copper', 'price' => 130000, 'rating' => 4.5],  // 2500 USD -> ~130000 PHP
    ['company' => 'XYZ Ltd', 'materials' => 'Aluminum, Zinc', 'price' => 85000, 'rating' => 3.9],   // 1600 USD -> ~85000 PHP
    ['company' => 'LMN Inc', 'materials' => 'Plastic, Rubber', 'price' => 100000, 'rating' => 4.2],  // 1800 USD -> ~100000 PHP
    ['company' => 'Delta Traders', 'materials' => 'Glass, Fiber', 'price' => 105000, 'rating' => 4.0],  // 2000 USD -> ~105000 PHP
    ['company' => 'Omega Supplies', 'materials' => 'Wood, Cement', 'price' => 90000, 'rating' => 4.6],   // 1700 USD -> ~90000 PHP
    ['company' => 'Alpha Industries', 'materials' => 'Copper, Zinc', 'price' => 160000, 'rating' => 4.7],  // 3200 USD -> ~160000 PHP
    ['company' => 'Beta Tech', 'materials' => 'Rubber, Steel', 'price' => 70000, 'rating' => 4.1],    // 1400 USD -> ~70000 PHP
    ['company' => 'Gamma Solutions', 'materials' => 'Glass, Cement', 'price' => 65000, 'rating' => 3.7], // 1300 USD -> ~65000 PHP
    ['company' => 'Epsilon Co', 'materials' => 'Wood, Aluminum', 'price' => 110000, 'rating' => 4.4],  // 2100 USD -> ~110000 PHP
    ['company' => 'Zeta Trading', 'materials' => 'Plastic, Copper', 'price' => 120000, 'rating' => 4.0],  // 2200 USD -> ~120000 PHP
    ['company' => 'Eta Suppliers', 'materials' => 'Rubber, Zinc', 'price' => 75000, 'rating' => 3.8],   // 1500 USD -> ~75000 PHP
    ['company' => 'Theta Enterprises', 'materials' => 'Wood, Glass', 'price' => 140000, 'rating' => 4.3],  // 2700 USD -> ~140000 PHP
    ['company' => 'Iota Ltd', 'materials' => 'Cement, Steel', 'price' => 130000, 'rating' => 4.2],  // 2600 USD -> ~130000 PHP
    ['company' => 'Kappa Industries', 'materials' => 'Aluminum, Rubber', 'price' => 150000, 'rating' => 4.5],  // 3000 USD -> ~150000 PHP
    ['company' => 'Lambda Supplies', 'materials' => 'Copper, Glass', 'price' => 105000, 'rating' => 4.0]  // 2000 USD -> ~105000 PHP
];

// Sort suppliers by price based on the 'order' parameter (ascending or descending)
$order = $_GET['order'] ?? 'asc';
usort($suppliers, function ($a, $b) use ($order) {
    return $order === 'asc' ? $a['price'] <=> $b['price'] : $b['price'] <=> $a['price'];
});

$next_order = $order === 'asc' ? 'desc' : 'asc';

// Sort suppliers by rating in descending order to get the top-rated ones
usort($suppliers, function ($a, $b) {
    return $b['rating'] <=> $a['rating'];
});

// Extract the top 6 unique suppliers by rating
$topSuppliers = array_slice($suppliers, 0, 6);

// Extract the ratings for the graphs
$labels = array_column($suppliers, 'company');
$ratings = array_column($suppliers, 'rating');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Supplier Rankings and Performance</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="./Styles/supprankings.css">

</head>
<body>
    <div class="sidebar">
        <?php include './INCLUDE/header_analytics.php'; ?>
    </div>

    <div class="content">
        <h1 class="text-center my-4">Supplier Rankings and Performance</h1>

        <!-- Updated Circular Ratings Section -->
        <div class="ratings-container">
            <div class="rating-circles">
                <?php
                $colors = ['#004c5f', '#00a8cc', '#aaffc3', '#009e7f', '#b8e986', '#a0c8a0'];
                $i = 0;
                foreach ($topSuppliers as $supplier):
                    $circleColor = $colors[$i % count($colors)];
                ?>
                    <div class="circle-rating text-center">
                        <div class="circle-label" style="font-weight: bold;"><?= htmlspecialchars($supplier['company']) ?></div>
                        <div class="circle-value" style="background-color: <?= $circleColor ?>;">
                            <?= number_format($supplier['rating'], 1) ?>
                        </div>
                        <div class="circle-label">Rating</div>
                    </div>
                <?php $i++; endforeach; ?>
            </div>
        </div>

        <div class="mb-4 d-flex justify-content-between align-items-center flex-wrap">
            <a href="?order=<?= $next_order ?>" class="sort-btn">
                <i class="fas fa-sort-amount-<?= $order === 'asc' ? 'up' : 'down' ?>"></i> Sort by Price
            </a>
        </div>

        <div class="graph-section">
            <div class="left-table">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Company</th>
                            <th>Materials</th>
                            <th>Price (PHP)</th>
                            <th>Rating</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($suppliers as $supplier): ?>
                            <tr>
                                <td><?= htmlspecialchars($supplier['company']) ?></td>
                                <td><?= htmlspecialchars($supplier['materials']) ?></td>
                                <td>₱<?= number_format($supplier['price'], 2) ?></td> <!-- Display price in PHP -->
                                <td><?= number_format($supplier['rating'], 1) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="right-graph">
                <div class="chart-container">
                    <canvas id="lineChart"></canvas>
                    <canvas id="barChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script>
        const labels = <?= json_encode(array_column($suppliers, 'company')) ?>;
        const ratings = <?= json_encode(array_column($suppliers, 'rating')) ?>;
    </script>

    <script src="./Script/supprankings.js"></script>

</body>
</html>