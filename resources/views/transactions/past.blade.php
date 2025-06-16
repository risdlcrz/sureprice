@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2>Past Transactions</h2>
    <div class="row mb-4">
        <div class="col-md-6">
            <canvas id="frequencyChart"></canvas>
        </div>
        <div class="col-md-6">
            <canvas id="monthlyChart"></canvas>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header">Top 5 Suppliers by Transaction Count</div>
                <div class="card-body">
                    <ul class="list-group">
                        @foreach($topSuppliers as $s)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $s['supplier'] }}
                                <span class="badge bg-primary rounded-pill">{{ $s['count'] }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header">Category Distribution</div>
                <div class="card-body">
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">All Past Transactions</h5>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Material/Service</th>
                        <th>Supplier</th>
                        <th>Quantity</th>
                        <th>Unit Price</th>
                        <th>Total</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                        <tr>
                            <td>{{ $item->created_at->format('Y-m-d') }}</td>
                            <td>{{ $item->material->name ?? 'Unknown' }}</td>
                            <td>{{ $item->supplier->company_name ?? 'Unknown' }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>₱{{ number_format($item->estimated_unit_price, 2) }}</td>
                            <td>₱{{ number_format($item->total_amount, 2) }}</td>
                            <td>{{ $item->notes }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const freqLabels = @json($frequency->pluck('material'));
const freqData = @json($frequency->pluck('count'));
const monthLabels = @json($monthly->pluck('month'));
const monthData = @json($monthly->pluck('count'));

const ctx1 = document.getElementById('frequencyChart').getContext('2d');
new Chart(ctx1, {
    type: 'bar',
    data: {
        labels: freqLabels,
        datasets: [{
            label: 'Frequency of Purchases/Services',
            data: freqData,
            backgroundColor: 'rgba(54, 162, 235, 0.6)'
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } }
    }
});

const ctx2 = document.getElementById('monthlyChart').getContext('2d');
new Chart(ctx2, {
    type: 'line',
    data: {
        labels: monthLabels,
        datasets: [{
            label: 'Monthly Purchases/Services',
            data: monthData,
            fill: false,
            borderColor: 'rgba(255, 99, 132, 0.8)',
            backgroundColor: 'rgba(255, 99, 132, 0.4)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true
    }
});

const catLabels = @json($categoryDist->pluck('category'));
const catData = @json($categoryDist->pluck('count'));
const ctx3 = document.getElementById('categoryChart').getContext('2d');
new Chart(ctx3, {
    type: 'pie',
    data: {
        labels: catLabels,
        datasets: [{
            label: 'Category Distribution',
            data: catData,
            backgroundColor: [
                'rgba(255, 99, 132, 0.6)',
                'rgba(54, 162, 235, 0.6)',
                'rgba(255, 206, 86, 0.6)',
                'rgba(75, 192, 192, 0.6)',
                'rgba(153, 102, 255, 0.6)',
                'rgba(255, 159, 64, 0.6)'
            ]
        }]
    },
    options: {
        responsive: true
    }
});
</script>
@endpush 