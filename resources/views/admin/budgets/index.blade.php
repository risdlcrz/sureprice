@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Budget Tracking</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Contract Number</th>
                                    <th>Client</th>
                                    <th>Total Budget</th>
                                    <th>Total Spent</th>
                                    <th>Remaining</th>
                                    <th>Utilization</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($budgetData as $data)
                                <tr>
                                    <td>{{ $data['contract']->contract_number }}</td>
                                    <td>{{ $data['contract']->client->name }}</td>
                                    <td>{{ number_format($data['budget']['total_budget'], 2) }}</td>
                                    <td>{{ number_format($data['budget']['total_spent'], 2) }}</td>
                                    <td>{{ number_format($data['budget']['remaining_budget'], 2) }}</td>
                                    <td>
                                        <div class="progress">
                                            <div class="progress-bar {{ $data['budget']['budget_utilization'] >= 90 ? 'bg-danger' : ($data['budget']['budget_utilization'] >= 75 ? 'bg-warning' : 'bg-success') }}"
                                                role="progressbar"
                                                style="width: {{ $data['budget']['budget_utilization'] }}%"
                                                aria-valuenow="{{ $data['budget']['budget_utilization'] }}"
                                                aria-valuemin="0"
                                                aria-valuemax="100">
                                                {{ number_format($data['budget']['budget_utilization'], 1) }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($data['alert'])
                                            <span class="badge badge-{{ $data['alert']['type'] }}">
                                                {{ $data['alert']['message'] }}
                                            </span>
                                        @else
                                            <span class="badge badge-success">On Track</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.budgets.show', $data['contract']) }}" 
                                           class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> View Details
                                        </a>
                                        <a href="{{ route('admin.budgets.export', $data['contract']) }}" 
                                           class="btn btn-sm btn-secondary">
                                            <i class="fas fa-file-pdf"></i> Export
                                        </a>
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

@push('scripts')
<script>
    // Refresh budget alerts every 5 minutes
    setInterval(function() {
        $.get('{{ route("admin.budgets.alerts") }}', function(alerts) {
            // Update alerts in the table
            alerts.forEach(function(alert) {
                const row = $(`tr:contains('${alert.contract}')`);
                const statusCell = row.find('td:eq(6)');
                statusCell.html(`
                    <span class="badge badge-${alert.alert.type}">
                        ${alert.alert.message}
                    </span>
                `);
            });
        });
    }, 300000);
</script>
@endpush
@endsection 