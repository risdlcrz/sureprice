@extends('layouts.app')

@section('content')
<div class="content">
    <div class="container-fluid ps-0">
        <!-- Standalone Page Header -->
        <h1 class="h3 mb-4 text-gray-800 text-center" style="font-weight:700;color:#198754;letter-spacing:0.01em;">Project Cost Management</h1>
        <!-- Page Actions -->
        <div class="d-flex justify-content-end align-items-center mb-4">
            <div class="d-flex gap-2">
                <button class="btn btn-outline-primary" onclick="window.print()">
                    <i class="fas fa-print me-2"></i>Print Report
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
                                $totalContractValue = isset($selectedContract) && $selectedContract ? $selectedContract->total_amount : 0;
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
                                        <h5 class="mb-0">₱{{ number_format($totalContractValue - $totalSpent, 2) }}</h5>
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
                                            <div class="flex-grow-1">
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        @if($transaction->type === 'purchase_order')
                                                            <div class="bg-primary bg-opacity-10 p-2 rounded">
                                                                <i class="fas fa-shopping-cart text-primary"></i>
                                                            </div>
                                                        @else
                                                            <div class="bg-success bg-opacity-10 p-2 rounded">
                                                                <i class="fas fa-exchange-alt text-success"></i>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <div class="d-flex align-items-center">
                                                            <div class="fw-bold">{{ Carbon\Carbon::parse($transaction->date)->format('M d, Y') }}</div>
                                                            <div class="ms-2">
                                                                @if($transaction->payment_status === 'pending')
                                                                    <span class="badge bg-warning">Pending</span>
                                                                @elseif($transaction->payment_status === 'completed')
                                                                    <span class="badge bg-success">Paid</span>
                                                                @elseif($transaction->payment_status === 'overdue')
                                                                    <span class="badge bg-danger">Overdue</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-center gap-2">
                                                            <small class="text-muted">{{ $transaction->description }}</small>
                                                            @if($transaction->type === 'purchase_order')
                                                                <span class="badge bg-info">Purchase Order</span>
                                                            @endif
                                                        </div>
                                                        @if($transaction->notes)
                                                            <div class="mt-1">
                                                                <small class="text-muted"><i class="fas fa-sticky-note me-1"></i>{{ $transaction->notes }}</small>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="text-end">
                                                <div class="d-flex align-items-center justify-content-end gap-2">
                                                    <div>
                                                        <div class="fw-bold">₱{{ number_format($transaction->amount, 2) }}</div>
                                                        @php
                                                            $budgetImpact = ($selectedContract && $selectedContract->total_amount > 0) ? ($transaction->amount / $selectedContract->total_amount) * 100 : 0;
                                                        @endphp
                                                        <small class="text-muted">{{ number_format($budgetImpact, 1) }}% of budget</small>
                                                    </div>
                                                    <button class="btn btn-sm btn-link p-0" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#transactionModal{{ $transaction->id }}">
                                                        <i class="fas fa-info-circle"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Transaction Details Modal -->
                                    <div class="modal fade" id="transactionModal{{ $transaction->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">
                                                        @if($transaction->type === 'purchase_order')
                                                            Purchase Order Details
                                                        @else
                                                            Transaction Details
                                                        @endif
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <h6>Basic Information</h6>
                                                        <p class="mb-1"><strong>Date:</strong> {{ Carbon\Carbon::parse($transaction->date)->format('M d, Y') }}</p>
                                                        <p class="mb-1"><strong>Type:</strong> {{ ucfirst($transaction->type) }}</p>
                                                        <p class="mb-1"><strong>Amount:</strong> ₱{{ number_format($transaction->amount, 2) }}</p>
                                                        <p class="mb-1"><strong>Budget Impact:</strong> {{ number_format($budgetImpact, 1) }}% of total budget</p>
                                                        <p class="mb-1"><strong>Status:</strong> {{ ucfirst($transaction->status) }}</p>
                                                        <p class="mb-1"><strong>Payment Status:</strong> {{ ucfirst($transaction->payment_status) }}</p>
                                                    </div>

                                                    @if($transaction->type === 'purchase_order')
                                                        <div class="mb-3">
                                                            <h6>Purchase Order Details</h6>
                                                            <p class="mb-1"><strong>PO Number:</strong> {{ $transaction->description }}</p>
                                                            <p class="mb-1"><strong>Supplier:</strong> {{ $transaction->supplier->company_name ?? 'N/A' }}</p>
                                                            @if($transaction->items)
                                                                <div class="mt-2">
                                                                    <h6>Items</h6>
                                                                    <ul class="list-unstyled">
                                                                        @foreach($transaction->items as $item)
                                                                            <li class="mb-1">
                                                                                {{ $item->material->name ?? 'N/A' }} - 
                                                                                {{ $item->quantity }} x ₱{{ number_format($item->unit_price, 2) }}
                                                                            </li>
                                                                        @endforeach
                                                                    </ul>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @endif

                                                    @if($transaction->notes)
                                                        <div class="mb-3">
                                                            <h6>Notes</h6>
                                                            <p class="mb-0">{{ $transaction->notes }}</p>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                </div>
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

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
<link href="{{ asset('resources/css/admin-budget-allocation.css') }}" rel="stylesheet" />
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@if($selectedContract)
<script>
window.budgetMonthlyData = {!! json_encode($monthlyData) !!};
window.budgetWeeklyData = {!! json_encode($weeklyData) !!};
window.budgetCategoryData = {!! json_encode($categoryData) !!};
window.budgetSupplierData = {!! json_encode($supplierData) !!};
window.budgetTotalSpent = {!! json_encode($totalSpent) !!};
window.budgetRemaining = {!! json_encode(isset($selectedContract) && $selectedContract ? max(0, $selectedContract->total_amount - $totalSpent) : 0) !!};
</script>
<script src="{{ asset('resources/js/admin-budget-allocation.js') }}"></script>
@endif
@endpush
@endsection

