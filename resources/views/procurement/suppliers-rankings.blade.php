@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('procurement.analytics') }}">Analytics Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ request('from') === 'history' ? 'Supplier Performance Records' : 'Supplier Rankings' }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Top 3 Suppliers Overview -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Top Performing Suppliers</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <canvas id="topSuppliersChart" height="200"></canvas>
                        </div>
                        <div class="col-md-4">
                            <div class="top-suppliers-legend">
                                <h6 class="text-muted mb-3">Performance Metrics</h6>
                                <div id="topSuppliersLegend"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Supplier Rankings</h4>
                    <div>
                        <a href="#" class="btn btn-outline-primary me-2" disabled>
                            <i class="fas fa-download"></i> Download Template
                        </a>
                        <a href="#" class="btn btn-outline-primary" disabled>
                            <i class="fas fa-download"></i> Materials Template
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Rank</th>
                                    <th>Company Name</th>
                                    <th>Score</th>
                                    <th>Delivery</th>
                                    <th>Quality</th>
                                    <th>Cost</th>
                                    <th>Performance</th>
                                    <th>Engagement</th>
                                    <th>Sustainability</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rankings as $ranking)
                                <tr>
                                    <td>{{ $ranking['rank'] ?? 'N/A' }}</td>
                                    <td>{{ $ranking['supplier']->company_name }}</td>
                                    <td>{{ number_format($ranking['score'], 2) }}</td>
                                    <td>
                                        @if($ranking['supplier']->evaluations->isNotEmpty())
                                            {{ number_format($ranking['supplier']->evaluations->last()->delivery_speed_score, 2) }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        @if($ranking['supplier']->evaluations->isNotEmpty())
                                            {{ number_format($ranking['supplier']->evaluations->last()->quality_score, 2) }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        @if($ranking['supplier']->evaluations->isNotEmpty())
                                            {{ number_format($ranking['supplier']->evaluations->last()->cost_variance_score, 2) }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        @if($ranking['supplier']->evaluations->isNotEmpty())
                                            {{ number_format($ranking['supplier']->evaluations->last()->performance_score, 2) }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        @if($ranking['supplier']->evaluations->isNotEmpty())
                                            {{ number_format($ranking['supplier']->evaluations->last()->engagement_score, 2) }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        @if($ranking['supplier']->evaluations->isNotEmpty())
                                            {{ number_format($ranking['supplier']->evaluations->last()->sustainability_score, 2) }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-primary" disabled>
                                            <i class="fas fa-star"></i> Evaluate
                                        </button>
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
<a href="{{ url()->previous() }}" class="btn btn-secondary mt-3">Back</a>
@endsection

@push('styles')
<style>
.top-suppliers-legend {
    padding: 1rem;
    border-left: 1px solid #dee2e6;
    height: 100%;
}
@media (max-width: 768px) {
    .top-suppliers-legend {
        border-left: none;
        border-top: 1px solid #dee2e6;
        margin-top: 1rem;
        padding-top: 1rem;
    }
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Dummy data for chart and legend
const rankings = @json($rankings);
const topSuppliers = rankings.slice(0, 3);
const ctx = document.getElementById('topSuppliersChart').getContext('2d');
const chart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: topSuppliers.map(s => s.supplier.company_name),
        datasets: [{
            label: 'Overall Score',
            data: topSuppliers.map(s => s.score),
            backgroundColor: ['#FFD700', '#C0C0C0', '#CD7F32'],
            borderColor: ['#FFD700', '#C0C0C0', '#CD7F32'],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                max: 5
            }
        },
        plugins: {
            legend: {
                display: false
            }
        }
    }
});
// Create custom legend
const legendContainer = document.getElementById('topSuppliersLegend');
const medals = ['🥇', '🥈', '🥉'];
topSuppliers.forEach((supplier, index) => {
    const div = document.createElement('div');
    div.className = 'mb-3';
    div.innerHTML = `
        <div class="d-flex align-items-center">
            <span class="me-2">${medals[index]}</span>
            <div>
                <h6 class="mb-0">${supplier.supplier.company_name}</h6>
                <small class="text-muted">Score: ${supplier.score.toFixed(2)}</small>
            </div>
        </div>
    `;
    legendContainer.appendChild(div);
});
</script>
@endpush 