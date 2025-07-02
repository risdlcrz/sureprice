@extends('layouts.app')

@section('content')
    @include('include.header_analytics')
    <h1 class="text-center my-4">Price Trend Analysis</h1>
    <div class="dashboard-box">
        <div class="price-trend-container mb-4">
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
                        <th>Forecasted Price (Next Period)</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($materials as $material)
                    @php
                        $history = $material->price_history_for_analysis ?? collect();
                        $historyArr = $history->all();
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Example Chart.js usage (optional, for demo)
        const ctx = document.getElementById('priceTrendChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May'],
                datasets: [{
                    label: 'Price',
                    data: [500, 480, 470, 460, 450],
                    borderColor: 'rgba(30, 136, 229, 1)',
                    backgroundColor: 'rgba(30, 136, 229, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                }
            }
        });
    </script>
@endsection
