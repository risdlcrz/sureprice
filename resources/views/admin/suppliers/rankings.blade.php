@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.analytics') }}">Analytics Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Supplier Rankings</li>
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
                        <a href="{{ route('suppliers.template.download') }}" class="btn btn-outline-primary me-2">
                            <i class="fas fa-download"></i> Download Template
                        </a>
                        <a href="{{ route('suppliers.materials.template.download') }}" class="btn btn-outline-primary">
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
                                        <button type="button" class="btn btn-sm btn-primary" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#evaluationModal" 
                                                data-supplier-id="{{ $ranking['supplier']->id }}">
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

<!-- Evaluation Modal -->
<div class="modal fade" id="evaluationModal" tabindex="-1" aria-labelledby="evaluationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="evaluationModalLabel">Supplier Evaluation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="evaluationForm">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-success d-none" id="successMessage">
                        Evaluation saved successfully!
                    </div>
                    <input type="hidden" name="supplier_id" id="supplier_id">
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="delivery_speed_score" class="form-label">Delivery Speed Score (0-5)</label>
                            <input type="number" class="form-control" id="delivery_speed_score" name="delivery_speed_score" 
                                   min="0" max="5" step="0.1" required>
                        </div>
                        <div class="col-md-6">
                            <label for="quality_score" class="form-label">Quality Score (0-5)</label>
                            <input type="number" class="form-control" id="quality_score" name="quality_score" 
                                   min="0" max="5" step="0.1" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="cost_variance_score" class="form-label">Cost Variance Score (0-5)</label>
                            <input type="number" class="form-control" id="cost_variance_score" name="cost_variance_score" 
                                   min="0" max="5" step="0.1" required>
                        </div>
                        <div class="col-md-6">
                            <label for="performance_score" class="form-label">Performance Score (0-5)</label>
                            <input type="number" class="form-control" id="performance_score" name="performance_score" 
                                   min="0" max="5" step="0.1" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="engagement_score" class="form-label">Engagement Score (0-5)</label>
                            <input type="number" class="form-control" id="engagement_score" name="engagement_score" 
                                   min="0" max="5" step="0.1" required>
                        </div>
                        <div class="col-md-6">
                            <label for="sustainability_score" class="form-label">Sustainability Score (0-5)</label>
                            <input type="number" class="form-control" id="sustainability_score" name="sustainability_score" 
                                   min="0" max="5" step="0.1" required>
                        </div>
                    </div>

                    <hr>

                    <h6>Metrics</h6>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="ontime_deliveries" class="form-label">On-time Deliveries</label>
                            <input type="number" class="form-control" id="ontime_deliveries" name="ontime_deliveries" 
                                   min="0" required>
                        </div>
                        <div class="col-md-6">
                            <label for="total_deliveries" class="form-label">Total Deliveries</label>
                            <input type="number" class="form-control" id="total_deliveries" name="total_deliveries" 
                                   min="0" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="defective_units" class="form-label">Defective Units</label>
                            <input type="number" class="form-control" id="defective_units" name="defective_units" 
                                   min="0" required>
                        </div>
                        <div class="col-md-6">
                            <label for="total_units" class="form-label">Total Units</label>
                            <input type="number" class="form-control" id="total_units" name="total_units" 
                                   min="0" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="actual_cost" class="form-label">Actual Cost</label>
                            <input type="number" class="form-control" id="actual_cost" name="actual_cost" 
                                   min="0" step="0.01" required>
                        </div>
                        <div class="col-md-6">
                            <label for="estimated_cost" class="form-label">Estimated Cost</label>
                            <input type="number" class="form-control" id="estimated_cost" name="estimated_cost" 
                                   min="0" step="0.01" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="saveButton">
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        Save Evaluation
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Fetch top suppliers data
    fetch('{{ route("admin.supplier-rankings.top") }}')
        .then(response => response.json())
        .then(data => {
            const ctx = document.getElementById('topSuppliersChart').getContext('2d');
            const chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.map(s => s.company_name),
                    datasets: [{
                        label: 'Overall Score',
                        data: data.map(s => s.score),
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
            data.forEach((supplier, index) => {
                const medals = ['🥇', '🥈', '🥉'];
                const div = document.createElement('div');
                div.className = 'mb-3';
                div.innerHTML = `
                    <div class="d-flex align-items-center">
                        <span class="me-2">${medals[index]}</span>
                        <div>
                            <h6 class="mb-0">${supplier.company_name}</h6>
                            <small class="text-muted">Score: ${supplier.score.toFixed(2)}</small>
                        </div>
                    </div>
                `;
                legendContainer.appendChild(div);
            });
        });

    const evaluationModal = document.getElementById('evaluationModal');
    evaluationModal.addEventListener('show.bs.modal', async function(event) {
        const button = event.relatedTarget;
        const supplierId = button.getAttribute('data-supplier-id');
        document.getElementById('supplier_id').value = supplierId;

        try {
            const response = await fetch(`/admin/suppliers/${supplierId}/latest-evaluation`);
            const data = await response.json();

            if (data.evaluation) {
                document.getElementById('delivery_speed_score').value = data.evaluation.delivery_speed_score;
                document.getElementById('quality_score').value = data.evaluation.quality_score;
                document.getElementById('cost_variance_score').value = data.evaluation.cost_variance_score;
                document.getElementById('engagement_score').value = data.evaluation.engagement_score;
                document.getElementById('performance_score').value = data.evaluation.performance_score;
                document.getElementById('sustainability_score').value = data.evaluation.sustainability_score;
            }

            if (data.metrics) {
                document.getElementById('total_deliveries').value = data.metrics.total_deliveries;
                document.getElementById('ontime_deliveries').value = data.metrics.ontime_deliveries;
                document.getElementById('total_units').value = data.metrics.total_units;
                document.getElementById('defective_units').value = data.metrics.defective_units;
                document.getElementById('estimated_cost').value = data.metrics.estimated_cost;
                document.getElementById('actual_cost').value = data.metrics.actual_cost;
            }
        } catch (error) {
            console.error('Error loading evaluation data:', error);
        }
    });

    const evaluationForm = document.getElementById('evaluationForm');
    const saveButton = document.getElementById('saveButton');
    const spinner = saveButton.querySelector('.spinner-border');
    const successMessage = document.getElementById('successMessage');

    evaluationForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const supplierId = document.getElementById('supplier_id').value;
        const formData = new FormData(evaluationForm);
        
        try {
            // Show loading state
            saveButton.disabled = true;
            spinner.classList.remove('d-none');
            successMessage.classList.add('d-none');
            
            // Save evaluation
            const evalResponse = await fetch(`/admin/suppliers/${supplierId}/evaluations`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            
            if (!evalResponse.ok) throw new Error('Failed to save evaluation');

            // Save metrics
            const metricsResponse = await fetch(`/admin/suppliers/${supplierId}/metrics`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            
            if (!metricsResponse.ok) throw new Error('Failed to save metrics');

            // Show success message
            successMessage.classList.remove('d-none');
            
            // Reload page after a short delay
            setTimeout(() => {
                window.location.reload();
            }, 1500);

        } catch (error) {
            console.error('Error:', error);
            alert('Failed to save evaluation. Please try again.');
        } finally {
            // Reset loading state
            saveButton.disabled = false;
            spinner.classList.add('d-none');
        }
    });
});
</script>
@endpush

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