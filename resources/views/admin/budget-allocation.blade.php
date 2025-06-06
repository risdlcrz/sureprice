@extends('layouts.app')

@section('content')


<div class="content">
    <h1 class="text-center my-4">Budget Allocation and Expenditures</h1>

    <div class="container-fluid">
        <!-- Contract Selection with Quick Stats -->
        <div class="row mb-4">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <form id="contractForm" method="GET" action="{{ route('admin.budget-allocation') }}" class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <label for="contract_id" class="form-label">Select Contract:</label>
                                <select name="contract_id" id="contract_id" class="form-select" onchange="this.form.submit()">
                                    @foreach($contracts as $contract)
                                        <option value="{{ $contract->id }}" {{ $selectedContract && $selectedContract->id == $contract->id ? 'selected' : '' }}>
                                            {{ $contract->contract_id }} - {{ optional($contract->client)->name }} (₱{{ number_format($contract->total_amount, 2) }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Quick Stats Cards -->
            <div class="col-md-4">
                <div class="row">
                    <div class="col-6">
                        <div class="card bg-primary text-white">
                            <div class="card-body p-3">
                                <h6 class="mb-1">Total Contracts</h6>
                                <h4 class="mb-0">{{ $contracts->count() }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card bg-success text-white">
                            <div class="card-body p-3">
                                <h6 class="mb-1">Active Projects</h6>
                                <h4 class="mb-0">{{ $contracts->where('status', 'approved')->count() }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($selectedContract)
            <!-- Contract Details -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h4 class="mb-0">
                                    <i class="fas fa-file-contract me-2"></i>
                                    Contract Information
                                </h4>
                                <span class="badge bg-{{ $selectedContract->status === 'draft' ? 'warning' : ($selectedContract->status === 'approved' ? 'success' : 'danger') }} p-2">
                                    {{ ucfirst($selectedContract->status) }}
                                </span>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="border-start border-4 border-primary ps-3">
                                        <p class="text-muted mb-1">Contract ID</p>
                                        <h5 class="mb-3">{{ $selectedContract->contract_id }}</h5>
                                        <p class="text-muted mb-1">Client</p>
                                        <h5>{{ optional($selectedContract->client)->name }}</h5>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="border-start border-4 border-success ps-3">
                                        <p class="text-muted mb-1">Duration</p>
                                        <h5 class="mb-3">
                                            {{ $selectedContract->start_date->format('M d, Y') }} - 
                                            {{ $selectedContract->end_date->format('M d, Y') }}
                                        </h5>
                                        @php
                                            $projectDuration = $selectedContract->start_date->diffInDays($selectedContract->end_date, false) + 1;
                                        @endphp
                                        <p class="text-muted mb-1">Project Duration</p>
                                        <h5>{{ $projectDuration }} days</h5>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="border-start border-4 border-info ps-3">
                                        <p class="text-muted mb-1">Total Contract Value</p>
                                        <h5 class="mb-3">₱{{ number_format($selectedContract->total_amount, 2) }}</h5>
                                        <p class="text-muted mb-1">Budget Allocation</p>
                                        <h5>₱{{ number_format($selectedContract->budget_allocation ?? $selectedContract->total_amount, 2) }}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contract Items -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <h4 class="mb-0">Contract Items</h4>
                                <div class="d-flex gap-2">
                                    <span class="badge bg-primary">
                                        Total Items: {{ $selectedContract->items->count() }}
                                    </span>
                                    <span class="badge bg-success">
                                        Total Value: ₱{{ number_format($selectedContract->items->sum('total'), 2) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Material</th>
                                            <th>Supplier</th>
                                            <th>Quantity</th>
                                            <th>Unit Price</th>
                                            <th>Total</th>
                                            <th>% of Budget</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($selectedContract->items as $item)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-box me-2 text-primary"></i>
                                                        {{ optional($item->material)->name ?? 'N/A' }}
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-building me-2 text-success"></i>
                                                        {{ optional($item->supplier)->company_name ?? $item->supplier_name ?? 'N/A' }}
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-info">
                                                        {{ number_format($item->quantity, 0) }} {{ optional($item->material)->unit ?? '' }}
                                                    </span>
                                                </td>
                                                <td>₱{{ number_format($item->amount, 2) }}</td>
                                                <td>₱{{ number_format($item->total, 2) }}</td>
                                                <td>
                                                    @php
                                                        $percentage = ($item->total / $selectedContract->total_amount) * 100;
                                                    @endphp
                                                    <div class="progress" style="height: 6px;">
                                                        <div class="progress-bar bg-{{ $percentage > 50 ? 'warning' : 'success' }}" 
                                                             role="progressbar" 
                                                             style="width: {{ $percentage }}%">
                                                        </div>
                                                    </div>
                                                    <small class="text-muted">{{ number_format($percentage, 1) }}%</small>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">No items added to this contract</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <h4 class="mb-0">Spending Trends</h4>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-outline-primary active" onclick="toggleChartView('monthly')">Monthly</button>
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="toggleChartView('weekly')">Weekly</button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div style="height: 300px;">
                                <canvas id="spendingChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Transactions -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <h4 class="mb-0">Recent Transactions</h4>
                                <span class="badge bg-primary">Last {{ $recentTransactions->count() }} transactions</span>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="list-group list-group-flush">
                                @forelse($recentTransactions as $transaction)
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <div class="d-flex align-items-center">
                                                    <i class="fas {{ $transaction->type === 'purchase_order' ? 'fa-shopping-cart' : 'fa-exchange-alt' }} me-2 
                                                              text-{{ $transaction->type === 'purchase_order' ? 'primary' : 'success' }}"></i>
                                                    <div>
                                                        <div class="fw-bold">{{ Carbon\Carbon::parse($transaction->date)->format('M d, Y') }}</div>
                                                        <small class="text-muted">{{ $transaction->description }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="text-end">
                                                <div class="fw-bold text-{{ $transaction->amount > 10000 ? 'danger' : 'success' }}">
                                                    ₱{{ number_format($transaction->amount, 2) }}
                                                </div>
                                                @if($transaction->type === 'purchase_order')
                                                    <span class="badge bg-info">Purchase Order</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="list-group-item text-center">No recent transactions found.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Row -->
            <div class="row">
                <div class="col-md-8 mb-4">
                    <div class="card">
                        <div class="card-header bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <h4 class="mb-0">Cost Distribution</h4>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-outline-primary active" onclick="toggleBreakdownView('category')">By Category</button>
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="toggleBreakdownView('supplier')">By Supplier</button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div style="height: 300px;">
                                <canvas id="costBreakdownChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Budget Overview -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h4 class="mb-0">Budget Overview</h4>
                        </div>
                        <div class="card-body">
                            @php
                                $percentUsed = $totalBudget > 0 ? round(($totalSpent / $totalBudget) * 100, 1) : 0;
                                $remaining = $totalBudget - $totalSpent;
                                $statusClass = $remaining < 0 ? 'danger' : ($percentUsed > 80 ? 'warning' : 'success');
                            @endphp

                            <div class="text-center mb-4">
                                <div class="position-relative d-inline-block">
                                    <canvas id="budgetDonut" width="150" height="150"></canvas>
                                    <div class="position-absolute top-50 start-50 translate-middle">
                                        <h3 class="mb-0">{{ number_format($percentUsed, 1) }}%</h3>
                                        <small class="text-muted">Used</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-6">
                                    <div class="border-start border-4 border-primary ps-3">
                                        <small class="text-muted">Total Budget</small>
                                        <h5 class="mb-0">₱{{ number_format($totalBudget, 2) }}</h5>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="border-start border-4 border-success ps-3">
                                        <small class="text-muted">Spent</small>
                                        <h5 class="mb-0">₱{{ number_format($totalSpent, 2) }}</h5>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="border-start border-4 border-info ps-3">
                                        <small class="text-muted">Remaining</small>
                                        <h5 class="mb-0">₱{{ number_format($remaining, 2) }}</h5>
                                    </div>
                                </div>
                            </div>

                            @if($remaining < 0)
                                <div class="alert alert-danger d-flex align-items-center">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <div>
                                        <strong>Budget Exceeded!</strong><br>
                                        Over by ₱{{ number_format(abs($remaining), 2) }}
                                    </div>
                                </div>
                            @elseif($percentUsed > 80)
                                <div class="alert alert-warning d-flex align-items-center">
                                    <i class="fas fa-exclamation-circle me-2"></i>
                                    <div>
                                        <strong>Budget Alert!</strong><br>
                                        Only ₱{{ number_format($remaining, 2) }} remaining
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-success d-flex align-items-center">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <div>
                                        <strong>On Track!</strong><br>
                                        Budget is being managed well
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                No contracts found. Please create a contract first.
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    @if($selectedContract)
        // Budget Overview Donut
        const budgetCtx = document.getElementById('budgetDonut').getContext('2d');
        new Chart(budgetCtx, {
            type: 'doughnut',
            data: {
                labels: ['Spent', 'Remaining'],
                datasets: [{
                    data: [{{ $totalSpent }}, {{ max(0, $totalBudget - $totalSpent) }}],
                    backgroundColor: [
                        'rgba(75, 192, 192, 0.8)',
                        'rgba(232, 232, 232, 0.8)'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                cutout: '80%',
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const value = context.raw;
                                return `${context.label}: ₱${value.toLocaleString(undefined, {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                })}`;
                            }
                        }
                    }
                }
            }
        });

        // Chart configurations
        let spendingChart = null;
        let breakdownChart = null;
        let currentSpendingView = 'monthly';
        let currentBreakdownView = 'category';

        // Monthly data
        const monthlyData = {
            labels: @json($monthlyData->labels),
            values: @json($monthlyData->values)
        };

        // Weekly data
        const weeklyData = {
            labels: @json($weeklyData->labels),
            values: @json($weeklyData->values)
        };

        // Category breakdown data
        const categoryData = {
            labels: @json($categoryData->labels),
            values: @json($categoryData->values)
        };

        // Supplier breakdown data
        const supplierData = {
            labels: @json($supplierData->labels),
            values: @json($supplierData->values)
        };

        // Initialize spending chart
        function initSpendingChart(type = 'monthly') {
            const ctx = document.getElementById('spendingChart').getContext('2d');
            const data = type === 'monthly' ? monthlyData : weeklyData;

            if (spendingChart) {
                spendingChart.destroy();
            }

            spendingChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: type === 'monthly' ? 'Monthly Spending' : 'Weekly Spending',
                        data: data.values,
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'rgba(75, 192, 192, 0.1)',
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: 'rgba(75, 192, 192, 1)',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                drawBorder: false
                            },
                            ticks: {
                                callback: function(value) {
                                    return '₱' + value.toLocaleString(undefined, {
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2
                                    });
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            titleColor: '#fff',
                            titleFont: {
                                size: 14,
                                weight: 'bold'
                            },
                            bodyFont: {
                                size: 13
                            },
                            callbacks: {
                                label: function(context) {
                                    return '₱' + context.raw.toLocaleString(undefined, {
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2
                                    });
                                }
                            }
                        },
                        legend: {
                            display: false
                        }
                    }
                }
            });
        }

        // Initialize breakdown chart
        function initBreakdownChart(type = 'category') {
            const ctx = document.getElementById('costBreakdownChart').getContext('2d');
            const data = type === 'category' ? categoryData : supplierData;

            if (breakdownChart) {
                breakdownChart.destroy();
            }

            breakdownChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: data.labels,
                    datasets: [{
                        data: data.values,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.8)',
                            'rgba(54, 162, 235, 0.8)',
                            'rgba(255, 206, 86, 0.8)',
                            'rgba(75, 192, 192, 0.8)',
                            'rgba(153, 102, 255, 0.8)',
                            'rgba(255, 159, 64, 0.8)'
                        ],
                        borderColor: '#fff',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '65%',
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                padding: 20,
                                usePointStyle: true,
                                pointStyle: 'circle',
                                font: {
                                    size: 12
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            titleColor: '#fff',
                            titleFont: {
                                size: 14,
                                weight: 'bold'
                            },
                            bodyFont: {
                                size: 13
                            },
                            callbacks: {
                                label: function(context) {
                                    const value = context.raw;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = ((value / total) * 100).toFixed(1);
                                    return `${context.label}: ₱${value.toLocaleString(undefined, {
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2
                                    })} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
        }

        // Initialize both charts
        initSpendingChart();
        initBreakdownChart();

        // Toggle spending chart view
        window.toggleChartView = function(type) {
            if (currentSpendingView !== type) {
                currentSpendingView = type;
                initSpendingChart(type);
                
                // Update button states
                document.querySelectorAll('.btn-group button').forEach(btn => {
                    btn.classList.remove('active');
                    if (btn.textContent.toLowerCase().includes(type)) {
                        btn.classList.add('active');
                    }
                });
            }
        };

        // Toggle breakdown chart view
        window.toggleBreakdownView = function(type) {
            if (currentBreakdownView !== type) {
                currentBreakdownView = type;
                initBreakdownChart(type);
                
                // Update button states
                const buttons = document.querySelectorAll('.card-header .btn-group button');
                buttons.forEach(btn => {
                    btn.classList.remove('active');
                    if (btn.textContent.toLowerCase().includes(type)) {
                        btn.classList.add('active');
                    }
                });
            }
        };
    @endif
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
    transition: all 0.2s ease;
}

.list-group-item:hover {
    background-color: rgba(0,0,0,0.02);
}

.alert {
    margin-bottom: 0;
}

.alert i {
    margin-right: 0.5rem;
}

.form-select {
    padding: 0.5rem;
    border-radius: 0.25rem;
    border: 1px solid #ced4da;
}

.badge {
    padding: 0.5em 0.8em;
}

.border-start.border-4 {
    transition: all 0.3s ease;
}

.border-start.border-4:hover {
    transform: translateX(5px);
}

.progress.rounded-circle {
    transform: rotate(-90deg);
}

.btn-group .btn-outline-primary {
    transition: all 0.2s ease;
}

.btn-group .btn-outline-primary:hover {
    transform: translateY(-1px);
}

.card-header {
    border-bottom: 1px solid rgba(0,0,0,0.125);
    background-color: #f8f9fa;
}

.table th {
    font-weight: 600;
    color: #495057;
}

.progress {
    overflow: visible;
}
</style>
@endpush
@endsection
