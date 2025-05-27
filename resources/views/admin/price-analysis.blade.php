<?php
// You can connect to your database here if needed
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Price Trend Analysis</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="stylesheet" href="./Styles/price.css">

</head>
<body>
  <div class="sidebar">
    <?php include './INCLUDE/header_analytics.php'; ?>
  </div>

  <div class="content">
    <h1 class="text-center my-4">Price Trend Analysis</h1>

    <div class="dashboard-box">
      <div class="price-trend-container">
        <h5>Price Trend (PHP)</h5>
        <canvas id="priceTrendChart" height="100"></canvas>
      </div>

      <div class="table-container">
        <h5>Product Prices</h5>
        <table class="table table-bordered bg-white">
          <thead>
            <tr>
              <th>Product</th>
              <th>Last Price (PHP)</th>
              <th>Updated Price (PHP)</th>
              <th>Price Change</th>
            </tr>
          </thead>
          <tbody id="productPriceTable">
            <!-- Simulated data -->
            <tr>
              <td>Paint - White</td>
              <td>500</td>
              <td>450</td>
              <td class="price-change" style="color: green;">-50</td>
            </tr>
            <tr>
              <td>Brush - Large</td>
              <td>250</td>
              <td>270</td>
              <td class="price-change" style="color: red;">+20</td>
            </tr>
            <tr>
              <td>Tape - 1in</td>
              <td>150</td>
              <td>140</td>
              <td class="price-change" style="color: green;">-10</td>
            </tr>
            <tr>
              <td>Paint - Blue</td>
              <td>600</td>
              <td>620</td>
              <td class="price-change" style="color: red;">+20</td>
            </tr>
            <tr>
              <td>Brush - Small</td>
              <td>200</td>
              <td>190</td>
              <td class="price-change" style="color: green;">-10</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <script src="./Script/price.js"></script>

</body>
</html>
