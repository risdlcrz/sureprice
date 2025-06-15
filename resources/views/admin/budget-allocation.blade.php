@extends('layouts.app')

@section('content')
<div class="content">
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Project Cost Management</h1>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-primary" onclick="window.print()">
                    <i class="fas fa-print me-2"></i>Print Report
                </button>
                <button class="btn btn-outline-success" id="exportExcel">
                    <i class="fas fa-file-excel me-2"></i>Export to Excel
                </button>
            </div>
        </div>

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
                                            {{ $contract->contract_number }} - {{ optional($contract->client)->name }} (₱{{ number_format($contract->total_amount, 2) }})
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
                                        <h5 class="mb-3">{{ $selectedContract->contract_number }}</h5>
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
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <small class="text-muted">Labor Cost</small>
                                            <span>₱{{ number_format($selectedContract->labor_cost, 2) }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <small class="text-muted">Materials Cost</small>
                                            <span>₱{{ number_format($selectedContract->materials_cost, 2) }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">Total Spent</small>
                                            <span class="fw-bold">₱{{ number_format($totalSpent, 2) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Row -->
            <div class="row">
                <!-- Left Column: Charts and Tables -->
                <div class="col-md-8">
                    <!-- Spending Trends -->
                    <div class="card mb-4">
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

                    <!-- Contract Items -->
                    <div class="card mb-4">
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
                                            <th>% of Contract</th>
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

                    <!-- Cost Distribution -->
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

                <!-- Right Column: Cost Tracking and Recent Transactions -->
                <div class="col-md-4">
                    <!-- Cost Tracking -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h4 class="mb-0">Cost Tracking</h4>
                        </div>
                        <div class="card-body">
                            @php
                                $totalContractValue = $selectedContract->total_amount;
                                $remaining = max(0, $totalContractValue - $totalSpent);
                                $percentUsed = $totalContractValue > 0 ? ($totalSpent / $totalContractValue) * 100 : 0;
                            @endphp

                            <div class="text-center mb-4">
                                <div class="position-relative d-inline-block">
                                    <canvas id="budgetDonut" width="150" height="150"></canvas>
                                    <div class="position-absolute top-50 start-50 translate-middle">
                                        <h3 class="mb-0">{{ number_format($percentUsed, 1) }}%</h3>
                                        <small class="text-muted">Spent</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-6">
                                    <div class="border-start border-4 border-primary ps-3">
                                        <small class="text-muted">Contract Value</small>
                                        <h5 class="mb-0">₱{{ number_format($totalContractValue, 2) }}</h5>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="border-start border-4 border-success ps-3">
                                        <small class="text-muted">Total Spent</small>
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

                            <div class="mb-4">
                                <h6 class="text-muted mb-3">Cost Summary</h6>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <small>Labor</small>
                                    <div class="d-flex align-items-center">
                                        <span class="me-2">₱{{ number_format($selectedContract->labor_cost, 2) }}</span>
                                        <small class="text-muted">({{ number_format(($selectedContract->labor_cost / $totalContractValue) * 100, 1) }}%)</small>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small>Materials</small>
                                    <div class="d-flex align-items-center">
                                        <span class="me-2">₱{{ number_format($selectedContract->materials_cost, 2) }}</span>
                                        <small class="text-muted">({{ number_format(($selectedContract->materials_cost / $totalContractValue) * 100, 1) }}%)</small>
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-info d-flex align-items-center">
                                <i class="fas fa-info-circle me-2"></i>
                                <div>
                                    <strong>Contract Status:</strong><br>
                                    Total Spent: ₱{{ number_format($totalSpent, 2) }} ({{ number_format($percentUsed, 1) }}% of contract value)
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Transactions -->
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
        // Initialize charts
        initSpendingChart();
        initBreakdownChart();
        initBudgetDonut();

        // Export to Excel functionality
        document.getElementById('exportExcel').addEventListener('click', function() {
            // Add Excel export logic here
            alert('Excel export functionality will be implemented here');
        });
    @endif
});

// Chart initialization functions
function initSpendingChart() {
    const ctx = document.getElementById('spendingChart').getContext('2d');
    const monthlyData = @json($monthlyData);
    const weeklyData = @json($weeklyData);

    window.spendingChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: monthlyData.labels,
            datasets: [{
                label: 'Monthly Spending',
                data: monthlyData.values,
                borderColor: 'rgba(75, 192, 192, 1)',
                backgroundColor: 'rgba(75, 192, 192, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
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
}

function initBreakdownChart() {
    const ctx = document.getElementById('costBreakdownChart').getContext('2d');
    const categoryData = @json($categoryData);
    const supplierData = @json($supplierData);

    window.breakdownChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: categoryData.labels,
            datasets: [{
                data: categoryData.values,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(255, 206, 86, 0.8)',
                    'rgba(75, 192, 192, 0.8)',
                    'rgba(153, 102, 255, 0.8)'
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
}

function initBudgetDonut() {
    const ctx = document.getElementById('budgetDonut').getContext('2d');
    const totalSpent = {{ $totalSpent }};
    const remaining = {{ max(0, $selectedContract->total_amount - $totalSpent) }};

    window.budgetDonut = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Spent', 'Remaining'],
            datasets: [{
                data: [totalSpent, remaining],
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
                }
            }
        }
    });
}

// Toggle functions
function toggleChartView(type) {
    const monthlyData = @json($monthlyData);
    const weeklyData = @json($weeklyData);
    const data = type === 'monthly' ? monthlyData : weeklyData;
    
    window.spendingChart.data.labels = data.labels;
    window.spendingChart.data.datasets[0].data = data.values;
    window.spendingChart.data.datasets[0].label = type === 'monthly' ? 'Monthly Spending' : 'Weekly Spending';
    window.spendingChart.update();

    // Update button states
    document.querySelectorAll('.btn-group button').forEach(btn => {
        btn.classList.remove('active');
        if (btn.textContent.toLowerCase().includes(type)) {
            btn.classList.add('active');
        }
    });
}

function toggleBreakdownView(type) {
    const categoryData = @json($categoryData);
    const supplierData = @json($supplierData);
    const data = type === 'category' ? categoryData : supplierData;
    
    window.breakdownChart.data.labels = data.labels;
    window.breakdownChart.data.datasets[0].data = data.values;
    window.breakdownChart.update();

    // Update button states
    const buttons = document.querySelectorAll('.card-header .btn-group button');
    buttons.forEach(btn => {
        btn.classList.remove('active');
        if (btn.textContent.toLowerCase().includes(type)) {
            btn.classList.add('active');
        }
    });
}
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

@media print {
    .btn, .form-select {
        display: none !important;
    }
    
    .card {
        break-inside: avoid;
    }
}
</style>
@endpush
@endsection

