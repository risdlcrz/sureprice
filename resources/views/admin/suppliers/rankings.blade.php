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
                    
                    <!-- Star Rating Fields -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label d-block">Delivery Speed Score</label>
                            <div class="rating">
                                @for($i = 5; $i >= 1; $i--)
                                <input type="radio" id="delivery_speed_{{ $i }}" name="delivery_speed_score" value="{{ $i }}" />
                                <label for="delivery_speed_{{ $i }}">
                                    <svg viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                                </label>
                                @endfor
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label d-block">Quality Score</label>
                            <div class="rating">
                                @for($i = 5; $i >= 1; $i--)
                                <input type="radio" id="quality_{{ $i }}" name="quality_score" value="{{ $i }}" />
                                <label for="quality_{{ $i }}">
                                    <svg viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                                </label>
                                @endfor
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label d-block">Cost Variance Score</label>
                            <div class="rating">
                                @for($i = 5; $i >= 1; $i--)
                                <input type="radio" id="cost_{{ $i }}" name="cost_variance_score" value="{{ $i }}" />
                                <label for="cost_{{ $i }}">
                                    <svg viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                                </label>
                                @endfor
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label d-block">Performance Score</label>
                            <div class="rating">
                                @for($i = 5; $i >= 1; $i--)
                                <input type="radio" id="performance_{{ $i }}" name="performance_score" value="{{ $i }}" />
                                <label for="performance_{{ $i }}">
                                    <svg viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                                </label>
                                @endfor
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label d-block">Engagement Score</label>
                            <div class="rating">
                                @for($i = 5; $i >= 1; $i--)
                                <input type="radio" id="engagement_{{ $i }}" name="engagement_score" value="{{ $i }}" />
                                <label for="engagement_{{ $i }}">
                                    <svg viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                                </label>
                                @endfor
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label d-block">Sustainability Score</label>
                            <div class="rating">
                                @for($i = 5; $i >= 1; $i--)
                                <input type="radio" id="sustainability_{{ $i }}" name="sustainability_score" value="{{ $i }}" />
                                <label for="sustainability_{{ $i }}">
                                    <svg viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                                </label>
                                @endfor
                            </div>
                        </div>
                    </div>

                    <!-- Metrics Section -->
                    <div class="metrics-section">
                        <h6 class="metrics-title">Purchase Order Metrics</h6>
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <div class="metric-value" id="ontime_deliveries_display">0</div>
                                <div class="metric-label">On-time Deliveries</div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="metric-value" id="total_deliveries_display">0</div>
                                <div class="metric-label">Total Deliveries</div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="metric-value" id="defective_units_display">0</div>
                                <div class="metric-label">Defective Units</div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="metric-value" id="total_units_display">0</div>
                                <div class="metric-label">Total Units</div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="metric-value" id="actual_cost_display">₱0.00</div>
                                <div class="metric-label">Actual Cost</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="metric-value" id="estimated_cost_display">₱0.00</div>
                                <div class="metric-label">Estimated Cost</div>
                            </div>
                        </div>
                    </div>

                    <!-- Hidden Metrics Fields -->
                    <input type="hidden" name="ontime_deliveries" id="ontime_deliveries">
                    <input type="hidden" name="total_deliveries" id="total_deliveries">
                    <input type="hidden" name="defective_units" id="defective_units">
                    <input type="hidden" name="total_units" id="total_units">
                    <input type="hidden" name="actual_cost" id="actual_cost">
                    <input type="hidden" name="estimated_cost" id="estimated_cost">
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
            // Load evaluation data
            const evalResponse = await fetch(`/admin/suppliers/${supplierId}/latest-evaluation`);
            const evalData = await evalResponse.json();

            // Set star ratings
            if (evalData.evaluation) {
                document.querySelector(`input[name="delivery_speed_score"][value="${Math.round(evalData.evaluation.delivery_speed_score)}"]`)?.checked = true;
                document.querySelector(`input[name="quality_score"][value="${Math.round(evalData.evaluation.quality_score)}"]`)?.checked = true;
                document.querySelector(`input[name="cost_variance_score"][value="${Math.round(evalData.evaluation.cost_variance_score)}"]`)?.checked = true;
                document.querySelector(`input[name="performance_score"][value="${Math.round(evalData.evaluation.performance_score)}"]`)?.checked = true;
                document.querySelector(`input[name="engagement_score"][value="${Math.round(evalData.evaluation.engagement_score)}"]`)?.checked = true;
                document.querySelector(`input[name="sustainability_score"][value="${Math.round(evalData.evaluation.sustainability_score)}"]`)?.checked = true;
            }

            // Load purchase order metrics
            const metricsResponse = await fetch(`/admin/suppliers/${supplierId}/purchase-order-metrics`);
            const metricsData = await metricsResponse.json();

            // Update metrics display and hidden fields
            document.getElementById('ontime_deliveries_display').textContent = metricsData.ontime_deliveries;
            document.getElementById('total_deliveries_display').textContent = metricsData.total_deliveries;
            document.getElementById('defective_units_display').textContent = metricsData.defective_units;
            document.getElementById('total_units_display').textContent = metricsData.total_units;
            document.getElementById('actual_cost_display').textContent = `₱${metricsData.actual_cost.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
            document.getElementById('estimated_cost_display').textContent = `₱${metricsData.estimated_cost.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;

            // Set hidden fields
            document.getElementById('ontime_deliveries').value = metricsData.ontime_deliveries;
            document.getElementById('total_deliveries').value = metricsData.total_deliveries;
            document.getElementById('defective_units').value = metricsData.defective_units;
            document.getElementById('total_units').value = metricsData.total_units;
            document.getElementById('actual_cost').value = metricsData.actual_cost;
            document.getElementById('estimated_cost').value = metricsData.estimated_cost;

        } catch (error) {
            console.error('Error loading data:', error);
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
            
            // Save evaluation and metrics in parallel
            const [evalResponse, metricsResponse] = await Promise.all([
                fetch(`/admin/suppliers/${supplierId}/evaluations`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                }),
                fetch(`/admin/suppliers/${supplierId}/metrics`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
            ]);
            
            // Check if both requests were successful
            if (!evalResponse.ok) throw new Error('Failed to save evaluation');
            if (!metricsResponse.ok) throw new Error('Failed to save metrics');

            // Show success message
            successMessage.classList.remove('d-none');
            
            // Reload page after a delay to ensure server processing is complete
            setTimeout(() => {
                window.location.reload();
            }, 2500);

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

.rating {
    display: inline-flex;
    flex-direction: row-reverse;
    gap: 0.3rem;
    --stroke: #666;
    --fill: #ffc73a;
}

.rating input {
    appearance: unset;
}

.rating label {
    cursor: pointer;
}

.rating svg {
    width: 2rem;
    height: 2rem;
    overflow: visible;
    fill: transparent;
    stroke: var(--stroke);
    stroke-linejoin: bevel;
    stroke-width: 2px;
    transition: 0.2s;
}

.rating input:checked ~ label svg {
    fill: var(--fill);
    stroke: var(--fill);
}

.rating input:hover ~ label svg {
    fill: var(--fill);
    stroke: var(--fill);
}

.metrics-section {
    background-color: #f8f9fa;
    padding: 1rem;
    border-radius: 0.5rem;
    margin-top: 1rem;
}

.metrics-title {
    font-size: 1.1rem;
    font-weight: 500;
    margin-bottom: 1rem;
    color: #495057;
}

.metric-value {
    font-size: 1.2rem;
    font-weight: 600;
}

.metric-label {
    font-size: 0.9rem;
    color: #6c757d;
}
</style>
@endpush 