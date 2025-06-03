@extends('layouts.app')

@section('content')


<div class="content">
    <h1 class="text-center my-4">Budget Allocation and Expenditures</h1>

    <div class="container-fluid">
        <!-- Top Row with Spending Chart and Recent Transactions -->
        <div class="row mb-4">
            <!-- Spending Chart -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h4>Spending This Month</h4>
                        <div style="height: 300px;">
                            <canvas id="spendingChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Transactions -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h4>Recent Transactions</h4>
                        <div class="list-group">
                            @forelse($recentTransactions ?? [] as $transaction)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-bold">{{ $transaction->date->format('Y-m-d') }}</div>
                                        <div>{{ $transaction->description }}</div>
                                    </div>
                                    <span class="badge bg-primary rounded-pill">₱{{ number_format($transaction->amount, 2) }}</span>
                                </div>
                            @empty
                                <div class="list-group-item">No recent transactions found.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Row with Cost Breakdown and Budget Tracking -->
        <div class="row">
            <!-- Cost Breakdown Chart -->
            <div class="col-md-8 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h4>Cost Breakdown</h4>
                        <div style="height: 300px;">
                            <canvas id="costBreakdownChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Budget Tracking -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h4>Budget Tracking</h4>
                        <div class="mt-3">
                            @php
                                $percentUsed = $totalBudget > 0 ? ($totalSpent / $totalBudget) * 100 : 0;
                                $remaining = $totalBudget - $totalSpent;
                                $statusClass = $remaining < 0 ? 'danger' : ($percentUsed > 80 ? 'warning' : 'success');
                            @endphp

                            <p><strong>{{ number_format($percentUsed, 1) }}% of Budget Used</strong></p>
                            <div class="progress mb-3">
                                <div class="progress-bar bg-{{ $statusClass }}" 
                                     role="progressbar" 
                                     style="width: {{ min($percentUsed, 100) }}%"
                                     aria-valuenow="{{ $percentUsed }}" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100">
                                    {{ number_format($percentUsed, 1) }}%
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mb-3">
                                <div>
                                    <strong>Total Budget:</strong><br>
                                    ₱{{ number_format($totalBudget, 2) }}
                                </div>
                                <div>
                                    <strong>Total Spent:</strong><br>
                                    ₱{{ number_format($totalSpent, 2) }}
                                </div>
                                <div>
                                    <strong>Remaining:</strong><br>
                                    ₱{{ number_format($remaining, 2) }}
                                </div>
                            </div>

                            @if($remaining < 0)
                                <div class="alert alert-danger">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    Over Budget by ₱{{ number_format(abs($remaining), 2) }}
                                </div>
                            @elseif($percentUsed > 80)
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-circle"></i>
                                    Approaching budget limit
                                </div>
                            @else
                                <div class="alert alert-success">
                                    <i class="fas fa-check-circle"></i>
                                    Budget on track
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Spending Chart
    const spendingCtx = document.getElementById('spendingChart').getContext('2d');
    new Chart(spendingCtx, {
        type: 'line',
        data: {
            labels: @json($spendingChartData->labels ?? []),
            datasets: [{
                label: 'PHP Spent',
                data: @json($spendingChartData->values ?? []),
                borderColor: 'rgba(75, 192, 192, 1)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '₱' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Cost Breakdown Chart
    const breakdownCtx = document.getElementById('costBreakdownChart').getContext('2d');
    new Chart(breakdownCtx, {
        type: 'pie',
        data: {
            labels: @json($costBreakdownData->labels ?? []),
            datasets: [{
                data: @json($costBreakdownData->values ?? []),
                backgroundColor: [
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(255, 206, 86, 0.8)',
                    'rgba(75, 192, 192, 0.8)'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right'
                }
            }
        }
    });
});
</script>
@endpush

@push('styles')
<style>
.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: none;
    margin-bottom: 1rem;
}

.progress {
    height: 1.5rem;
}

.progress-bar {
    font-size: 0.875rem;
    line-height: 1.5rem;
}

.list-group-item {
    border-left: none;
    border-right: none;
}

.alert {
    margin-bottom: 0;
}

.alert i {
    margin-right: 0.5rem;
}
</style>
@endpush
@endsection
