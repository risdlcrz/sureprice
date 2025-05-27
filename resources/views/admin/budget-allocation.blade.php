<?php
// Include your header file and other necessary logic here
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Budget Allocation and Expenditures</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./Styles/budget.css">


    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 sidebar">
            <?php include './Include/header_analytics.php'; ?>
        </div>

        <!-- Main Content -->
        <div class="col-md-9 col-lg-10 content">
            <h1 class="text-center my-4">Budget Allocation and Expenditures</h1>

            <div class="row mb-4">
                <div class="col-md-8">
                    <h4>Spending This Month</h4>
                    <div style="height: 300px;">
                        <canvas id="spendingChart"></canvas>
                    </div>
                </div>
                <div class="col-md-4">
                    <h4>Recent Transactions</h4>
                    <div class="list-group">
                        <?php
                        $transactions = [
                            ['2025-04-01', 'Office Supplies', 5000],
                            ['2025-04-05', 'Transportation', 3200],
                            ['2025-04-10', 'Utilities', 4500],
                            ['2025-04-15', 'Miscellaneous', 2750],
                            ['2025-04-20', 'Office Supplies', 6000]
                        ];
                        foreach ($transactions as [$date, $desc, $amount]) {
                            echo "<div class='list-group-item d-flex justify-content-between'>
                                    <div><strong>$date</strong><br>$desc</div>
                                    <div>₱" . number_format($amount, 0) . "</div>
                                  </div>";
                        }
                        ?>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Cost Breakdown Chart -->
                <div class="col-md-8 mb-4">
                    <h4>Cost Breakdown</h4>
                    <div style="height: 300px;">
                        <canvas id="costBreakdownChart"></canvas>
                    </div>
                </div>

                <!-- Budget Tracking -->
                <div class="col-md-4">
                    <h4>Budget Tracking</h4>
                    <div class="card p-3">
                        <?php
                        $totalBudget = 50000;
                        $totalSpent = 27500;
                        $remaining = $totalBudget - $totalSpent;
                        $spentPercent = round(($totalSpent / $totalBudget) * 100);
                        ?>
                        <!-- Budget Used -->
                        <p><strong><?= $spentPercent ?>% of Budget Used</strong></p>
                        <div class="progress mb-3">
                            <div class="progress-bar bg-success" role="progressbar" style="width: <?= $spentPercent ?>%;" aria-valuenow="<?= $spentPercent ?>" aria-valuemin="0" aria-valuemax="100">
                                <?= $spentPercent ?>%
                            </div>
                        </div>
                        
                        <!-- Budget Breakdown -->
                        <div class="d-flex justify-content-between">
                            <p><strong>Total Budget:</strong> ₱<?= number_format($totalBudget, 0) ?></p>
                            <p><strong>Total Spent:</strong> ₱<?= number_format($totalSpent, 0) ?></p>
                            <p><strong>Remaining:</strong> ₱<?= number_format($remaining, 0) ?></p>
                        </div>
                        
                        <!-- Status -->
                        <?php if ($totalSpent > $totalBudget): ?>
                            <div class="alert alert-danger mt-3" role="alert">
                                <strong>Over Budget!</strong> You have exceeded your budget by ₱<?= number_format($totalSpent - $totalBudget, 0) ?>.
                            </div>
                        <?php elseif ($remaining > 0): ?>
                            <div class="alert alert-success mt-3" role="alert">
                                <strong>Under Budget!</strong> You still have ₱<?= number_format($remaining, 0) ?> remaining.
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning mt-3" role="alert">
                                <strong>Budget Balanced!</strong> You have exactly used up your budget.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div> <!-- End of Content -->
    </div> <!-- End of Row -->
</div> <!-- End of Container -->


<script src="./Script/budget.js"></script>


</body>
</html>
