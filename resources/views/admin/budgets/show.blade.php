@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Budget Details - {{ $contract->contract_number }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.budgets.index') }}" class="btn btn-tool">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                        <a href="{{ route('admin.budgets.export', $contract) }}" class="btn btn-tool">
                            <i class="fas fa-file-pdf"></i> Export
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($alert)
                    <div class="alert alert-{{ $alert['type'] }} alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <h5><i class="icon fas fa-exclamation-triangle"></i> Alert!</h5>
                        {{ $alert['message'] }}
                    </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-info"><i class="fas fa-file-contract"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Contract Number</span>
                                    <span class="info-box-number">{{ $report['contract']['number'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-success"><i class="fas fa-building"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Client</span>
                                    <span class="info-box-number">{{ $report['contract']['client'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3>{{ number_format($report['contract']['total_budget'], 2) }}</h3>
                                    <p>Total Budget</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-money-bill-wave"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3>{{ number_format($report['contract']['total_spent'], 2) }}</h3>
                                    <p>Total Spent</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-shopping-cart"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="small-box {{ $report['contract']['remaining_budget'] < 0 ? 'bg-danger' : 'bg-warning' }}">
                                <div class="inner">
                                    <h3>{{ number_format($report['contract']['remaining_budget'], 2) }}</h3>
                                    <p>Remaining Budget</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-wallet"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="small-box {{ $report['contract']['budget_utilization'] >= 90 ? 'bg-danger' : ($report['contract']['budget_utilization'] >= 75 ? 'bg-warning' : 'bg-success') }}">
                                <div class="inner">
                                    <h3>{{ number_format($report['contract']['budget_utilization'], 1) }}%</h3>
                                    <p>Budget Utilization</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-chart-pie"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Material Cost Breakdown</h3>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Material</th>
                                                    <th>Estimated Quantity</th>
                                                    <th>Estimated Cost</th>
                                                    <th>Actual Quantity</th>
                                                    <th>Actual Cost</th>
                                                    <th>Average Unit Cost</th>
                                                    <th>Variance</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($report['materials'] as $material)
                                                <tr>
                                                    <td>{{ $material['material'] }}</td>
                                                    <td>{{ number_format($material['estimated_quantity'], 2) }}</td>
                                                    <td>{{ number_format($material['estimated_cost'], 2) }}</td>
                                                    <td>{{ number_format($material['actual_quantity'], 2) }}</td>
                                                    <td>{{ number_format($material['actual_cost'], 2) }}</td>
                                                    <td>{{ number_format($material['average_unit_cost'], 2) }}</td>
                                                    <td class="{{ $material['variance'] > 0 ? 'text-danger' : 'text-success' }}">
                                                        {{ number_format($material['variance'], 2) }}
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Add any additional JavaScript for charts or data visualization here
</script>
@endpush
@endsection 