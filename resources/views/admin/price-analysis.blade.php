@extends('layouts.app')

@section('content')
    @include('include.header_analytics')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <div class="container-fluid bg-light py-4 min-vh-100">
        <h2 class="fw-bold text-center mb-4 text-secondary text-uppercase">Price Analytics</h2>
        <div class="row justify-content-center mb-4">
            <div class="col-lg-10">
                <div class="card shadow-lg rounded p-4">
                    <div class="d-flex flex-wrap align-items-center mb-3 gap-3">
                        <label for="materialSelect" class="form-label mb-0 me-2 fw-bold">Select Material:</label>
                        <select id="materialSelect" class="form-select" style="width: 300px;">
                            @foreach($materials as $material)
                                <option value="{{ $material->id }}">{{ $material->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <h5 class="mb-3 fw-bold text-primary">Price Trend (PHP)</h5>
                    <canvas id="priceTrendChart" height="120"></canvas>
                </div>
            </div>
        </div>
        <hr class="my-4">
        <h3 class="fw-bold text-center mb-4 text-secondary text-uppercase">Descriptive Analytics</h3>
        <div class="row g-4 mb-4 justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card shadow h-100 rounded p-3">
                    <h6 class="card-title fw-bold text-info">Orders Per Month</h6>
                    <canvas id="ordersPerMonthChart" height="180"></canvas>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card shadow h-100 rounded p-3">
                    <h6 class="card-title fw-bold text-info">Most Used Materials Annually</h6>
                    <canvas id="mostUsedMaterialsPie" height="180"></canvas>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card shadow h-100 rounded p-3">
                    <h6 class="card-title fw-bold text-info">Most Consumed Material This Month</h6>
                    <canvas id="mostConsumedThisMonthBar" height="180"></canvas>
                </div>
            </div>
        </div>
        <hr class="my-4">
        <h3 class="fw-bold text-center mb-4 text-secondary text-uppercase">Product Prices</h3>
        <div class="row justify-content-center mb-4">
            <div class="col-lg-10">
                <div class="card shadow rounded p-3">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm bg-white align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Product</th>
                                    <th>Last Price (PHP)</th>
                                    <th>Updated Price (PHP)</th>
                                    <th>Price Change</th>
                                    <th>Forecasted Price (Next Period)</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($materials as $material)
                                @php
                                    $history = $material->price_history_for_analysis ?? collect();
                                    $historyArr = array_values($history->all());
                                    $lastPrice = count($historyArr) > 1 ? $historyArr[count($historyArr)-2] : '-';
                                    $updatedPrice = count($historyArr) ? $historyArr[count($historyArr)-1] : '-';
                                    $priceChange = is_numeric($lastPrice) && is_numeric($updatedPrice) ? $updatedPrice - $lastPrice : null;
                                @endphp
                                <tr>
                                    <td>{{ $material->name }}</td>
                                    <td>{{ $lastPrice }}</td>
                                    <td>{{ $updatedPrice }}</td>
                                    <td class="price-change" style="color: {{ $priceChange < 0 ? 'green' : ($priceChange > 0 ? 'red' : 'black') }};">
                                        {{ $priceChange > 0 ? '+' : '' }}{{ $priceChange ?? '-' }}
                                    </td>
                                    <td><strong>{{ $material->forecasted_price ?? 'N/A' }}</strong></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            $('#materialSelect').select2({
                placeholder: 'Select a material',
                allowClear: true
            });

            // Always treat IDs as strings for comparison
            const materials = @json($materials);
            let selectedMaterialId = $('#materialSelect').val() || (materials[0] ? String(materials[0].id) : null);

            function rollingForecasts(prices) {
                // prices: array of numbers (actuals)
                let forecasts = [null, null]; // first two points can't be forecasted
                for (let i = 2; i < prices.length; i++) {
                    // Use only data up to i-1 to forecast i
                    const x = Array.from({length: i}, (_, k) => k + 1);
                    const y = prices.slice(0, i);
                    const n = x.length;
                    const sumX = x.reduce((a, b) => a + b, 0);
                    const sumY = y.reduce((a, b) => a + b, 0);
                    let sumXY = 0, sumX2 = 0;
                    for (let j = 0; j < n; j++) {
                        sumXY += x[j] * y[j];
                        sumX2 += x[j] * x[j];
                    }
                    const slope = (n * sumXY - sumX * sumY) / (n * sumX2 - sumX * sumX);
                    const intercept = (sumY - slope * sumX) / n;
                    const nextX = i + 1;
                    const forecast = slope * nextX + intercept;
                    forecasts.push(forecast);
                }
                return forecasts;
            }

            function renderPriceChart(materialId) {
                const material = materials.find(m => String(m.id) === String(materialId));
                if (!material) return;
                const history = material.price_history_for_analysis ? Object.entries(material.price_history_for_analysis) : [];
                const chartLabels = history.map(([date, price]) => date);
                const chartData = history.map(([date, price]) => price);
                const forecasted = material.forecasted_price;
                let forecastLabels = [...chartLabels];
                let forecastData = [...chartData];
                // Rolling forecast line
                let rollingForecastData = rollingForecasts(chartData);
                // Optionally add the next period forecast as a single point
                let forecastDataset = Array(chartData.length).fill(null);
                if (forecasted !== null && chartLabels.length > 0) {
                    const lastDate = new Date(chartLabels[chartLabels.length-1]);
                    const nextDate = new Date(lastDate);
                    nextDate.setMonth(lastDate.getMonth() + 1);
                    const forecastLabel = nextDate.toISOString().slice(0, 7);
                    forecastLabels.push(forecastLabel);
                    forecastData.push(null); // keep history line from connecting to forecast
                    rollingForecastData.push(null); // keep forecast line from connecting to next period
                    forecastDataset.push(forecasted);
                }

                if (window.priceTrendChartInstance) window.priceTrendChartInstance.destroy();
                const ctx = document.getElementById('priceTrendChart').getContext('2d');
                window.priceTrendChartInstance = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: forecastLabels,
                        datasets: [
                            {
                                label: material.name + ' Price History',
                                data: forecastData,
                                borderColor: 'rgba(30, 136, 229, 1)',
                                backgroundColor: 'rgba(30, 136, 229, 0.1)',
                                fill: true,
                                tension: 0.4,
                                spanGaps: false
                            },
                            {
                                label: material.name + ' Rolling Forecast',
                                data: rollingForecastData,
                                borderColor: 'rgba(255, 99, 132, 1)',
                                backgroundColor: 'rgba(255, 99, 132, 0.1)',
                                borderDash: [5, 5],
                                pointRadius: 4,
                                fill: false,
                                tension: 0.4,
                                spanGaps: false
                            },
                            {
                                label: material.name + ' Next Period Forecast',
                                data: forecastDataset,
                                borderColor: 'rgba(255, 99, 132, 1)',
                                backgroundColor: 'rgba(255, 99, 132, 0.1)',
                                borderDash: [2, 2],
                                pointRadius: 6,
                                fill: false,
                                tension: 0.4,
                                spanGaps: false
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { display: true }
                        }
                    }
                });
            }

            // Initial render
            renderPriceChart(selectedMaterialId);

            $('#materialSelect').on('change', function() {
                selectedMaterialId = $(this).val();
                renderPriceChart(selectedMaterialId);
            });
        });
        // --- Sample Data for Additional Charts ---
        // Replace with real data from your backend as needed
        const ordersPerMonthLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
        const ordersPerMonthData = [12, 19, 3, 5, 2, 3];
        new Chart(document.getElementById('ordersPerMonthChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: ordersPerMonthLabels,
                datasets: [{
                    label: 'Orders',
                    data: ordersPerMonthData,
                    backgroundColor: 'rgba(54, 162, 235, 0.7)'
                }]
            },
            options: { responsive: true }
        });
        const mostUsedMaterialsLabels = ['Adhesive', 'Caulk', 'Conduit', 'Drywall tape'];
        const mostUsedMaterialsData = [300, 250, 200, 150];
        new Chart(document.getElementById('mostUsedMaterialsPie').getContext('2d'), {
            type: 'pie',
            data: {
                labels: mostUsedMaterialsLabels,
                datasets: [{
                    data: mostUsedMaterialsData,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.7)',
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(255, 206, 86, 0.7)',
                        'rgba(75, 192, 192, 0.7)'
                    ]
                }]
            },
            options: { responsive: true }
        });
        const mostConsumedThisMonthLabels = ['Adhesive', 'Caulk', 'Conduit', 'Drywall tape'];
        const mostConsumedThisMonthData = [50, 40, 30, 20];
        new Chart(document.getElementById('mostConsumedThisMonthBar').getContext('2d'), {
            type: 'bar',
            data: {
                labels: mostConsumedThisMonthLabels,
                datasets: [{
                    label: 'Quantity Consumed',
                    data: mostConsumedThisMonthData,
                    backgroundColor: 'rgba(255, 159, 64, 0.7)'
                }]
            },
            options: { responsive: true }
        });
    </script>
@endsection
