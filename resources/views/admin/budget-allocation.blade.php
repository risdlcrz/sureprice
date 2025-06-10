@extends('layouts.app')

@section('content')


<div class="content">
    <h1 class="text-center my-4">Project Budget and Expenditures</h1>

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
                                        <p class="text-muted mb-1">Available Budget</p>
                                        <h5>₱{{ number_format($selectedContract->total_amount - $totalSpent, 2) }}</h5>
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
                </div>
            </div>

            <!-- Budget Overview -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="mb-0">Budget Overview</h5>
                        </div>
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <canvas id="budgetDonut" width="200" height="200"></canvas>
                                </div>
                                <div class="col-md-6">
                                    @php
                                        $totalBudget = $selectedContract->total_amount;
                                        $remaining = max(0, $totalBudget - $totalSpent);
                                        $percentUsed = $totalBudget > 0 ? ($totalSpent / $totalBudget) * 100 : 0;
                                    @endphp
                                    <h6 class="mb-3">Budget Status</h6>
                                    <div class="progress mb-3" style="height: 10px;">
                                        <div class="progress-bar bg-{{ $percentUsed > 90 ? 'danger' : ($percentUsed > 70 ? 'warning' : 'success') }}"
                                             role="progressbar"
                                             style="width: {{ min(100, $percentUsed) }}%">
                                        </div>
                                    </div>
                                    <p class="mb-1">
                                        <strong>Total Budget:</strong><br>
                                        ₱{{ number_format($totalBudget, 2) }}
                                    </p>
                                    <p class="mb-1">
                                        <strong>Total Spent:</strong><br>
                                        ₱{{ number_format($totalSpent, 2) }}
                                    </p>
                                    <p class="mb-1">
                                        <strong>Remaining:</strong><br>
                                        ₱{{ number_format($remaining, 2) }}
                                    </p>
                                    <p class="mb-0">
                                        <strong>Utilization:</strong><br>
                                        {{ number_format($percentUsed, 1) }}%
                                    </p>
                                </div>
                            </div>
                            @if($percentUsed > 90)
                                <div class="alert alert-danger d-flex align-items-center mt-3">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <div>
                                        <strong>Critical Budget Alert!</strong><br>
                                        Budget is nearly depleted
                                    </div>
                                </div>
                            @elseif($percentUsed > 80)
                                <div class="alert alert-warning d-flex align-items-center mt-3">
                                    <i class="fas fa-exclamation-circle me-2"></i>
                                    <div>
                                        <strong>Budget Alert!</strong><br>
                                        Only ₱{{ number_format($remaining, 2) }} remaining
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-success d-flex align-items-center mt-3">
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
