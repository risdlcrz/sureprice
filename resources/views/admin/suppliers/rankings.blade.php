@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">

                    <h1 class="h3 mb-4 text-gray-800 text-center" style="font-weight:700;color:#198754;letter-spacing:0.01em;">Supplier Rankings</a></h1>

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
                        <a href="{{ route('suppliers.template.download') }}" class="btn btn-gradient-blue me-2">
                            <i class="fas fa-download"></i> Download Template
                        </a>
                        <a href="{{ route('suppliers.materials.template.download') }}" class="btn btn-gradient-green">
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
                    
                    <!-- Rating Fields -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Delivery Speed Score</label>
                            <div class="score-input">
                                <input type="number" 
                                    class="form-control form-control-sm" 
                                    min="0" 
                                    max="5" 
                                    step="0.5" 
                                    value="0.0"
                                    name="delivery_speed_score">
                                <div class="score-controls">
                                    <button type="button" class="btn-increment" data-action="increment">
                                        <i class="fas fa-chevron-up"></i>
                                    </button>
                                    <button type="button" class="btn-increment" data-action="decrement">
                                        <i class="fas fa-chevron-down"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Quality Score</label>
                            <div class="score-input">
                                <input type="number" 
                                    class="form-control form-control-sm" 
                                    min="0" 
                                    max="5" 
                                    step="0.5" 
                                    value="0.0"
                                    name="quality_score">
                                <div class="score-controls">
                                    <button type="button" class="btn-increment" data-action="increment">
                                        <i class="fas fa-chevron-up"></i>
                                    </button>
                                    <button type="button" class="btn-increment" data-action="decrement">
                                        <i class="fas fa-chevron-down"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Cost Variance Score</label>
                            <div class="score-input">
                                <input type="number" 
                                    class="form-control form-control-sm" 
                                    min="0" 
                                    max="5" 
                                    step="0.5" 
                                    value="0.0"
                                    name="cost_variance_score">
                                <div class="score-controls">
                                    <button type="button" class="btn-increment" data-action="increment">
                                        <i class="fas fa-chevron-up"></i>
                                    </button>
                                    <button type="button" class="btn-increment" data-action="decrement">
                                        <i class="fas fa-chevron-down"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Performance Score</label>
                            <div class="score-input">
                                <input type="number" 
                                    class="form-control form-control-sm" 
                                    min="0" 
                                    max="5" 
                                    step="0.5" 
                                    value="0.0"
                                    name="performance_score">
                                <div class="score-controls">
                                    <button type="button" class="btn-increment" data-action="increment">
                                        <i class="fas fa-chevron-up"></i>
                                    </button>
                                    <button type="button" class="btn-increment" data-action="decrement">
                                        <i class="fas fa-chevron-down"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Engagement Score</label>
                            <div class="score-input">
                                <input type="number" 
                                    class="form-control form-control-sm" 
                                    min="0" 
                                    max="5" 
                                    step="0.5" 
                                    value="0.0"
                                    name="engagement_score">
                                <div class="score-controls">
                                    <button type="button" class="btn-increment" data-action="increment">
                                        <i class="fas fa-chevron-up"></i>
                                    </button>
                                    <button type="button" class="btn-increment" data-action="decrement">
                                        <i class="fas fa-chevron-down"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Sustainability Score</label>
                            <div class="score-input">
                                <input type="number" 
                                    class="form-control form-control-sm" 
                                    min="0" 
                                    max="5" 
                                    step="0.5" 
                                    value="0.0"
                                    name="sustainability_score">
                                <div class="score-controls">
                                    <button type="button" class="btn-increment" data-action="increment">
                                        <i class="fas fa-chevron-up"></i>
                                    </button>
                                    <button type="button" class="btn-increment" data-action="decrement">
                                        <i class="fas fa-chevron-down"></i>
                                    </button>
                                </div>
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

    // Handle score inputs
    const scoreInputs = document.querySelectorAll('.score-input');
    
    scoreInputs.forEach(container => {
        const input = container.querySelector('input[type="number"]');
        const incrementBtns = container.querySelectorAll('.btn-increment');
        
        // Handle input changes
        input.addEventListener('change', function() {
            let value = parseFloat(this.value);
            
            // Enforce min/max bounds
            if (value < 0) value = 0;
            if (value > 5) value = 5;
            
            // Round to nearest 0.5
            value = Math.round(value * 2) / 2;
            
            // Update input value
            this.value = value.toFixed(1);
        });

        // Handle increment/decrement buttons
        incrementBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                let value = parseFloat(input.value);
                if (this.dataset.action === 'increment') {
                    value = Math.min(5, value + 0.5);
                } else {
                    value = Math.max(0, value - 0.5);
                }
                input.value = value.toFixed(1);
                input.dispatchEvent(new Event('change'));
            });
        });
    });

    // Load existing ratings when modal opens
    const evaluationModal = document.getElementById('evaluationModal');
    evaluationModal.addEventListener('show.bs.modal', async function(event) {
        const button = event.relatedTarget;
        const supplierId = button.getAttribute('data-supplier-id');
        document.getElementById('supplier_id').value = supplierId;

        try {
            const response = await fetch(`/admin/suppliers/${supplierId}/latest-evaluation`);
            const data = await response.json();

            if (data.evaluation) {
                Object.entries(data.evaluation).forEach(([key, value]) => {
                    if (key.endsWith('_score')) {
                        const input = document.querySelector(`[name="${key}"]`);
                        if (input) {
                            input.value = value.toFixed(1);
                            input.dispatchEvent(new Event('change'));
                        }
                    }
                });
            }

            // Update metrics display
            const metricsResponse = await fetch(`/admin/suppliers/${supplierId}/purchase-order-metrics`);
            const metricsData = await metricsResponse.json();
            
            document.getElementById('ontime_deliveries_display').textContent = metricsData.ontime_deliveries;
            document.getElementById('total_deliveries_display').textContent = metricsData.total_deliveries;
            document.getElementById('defective_units_display').textContent = metricsData.defective_units;
            document.getElementById('total_units_display').textContent = metricsData.total_units;
            document.getElementById('actual_cost_display').textContent = `₱${metricsData.actual_cost.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
            document.getElementById('estimated_cost_display').textContent = `₱${metricsData.estimated_cost.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
        } catch (error) {
            console.error('Error loading data:', error);
        }
    });

    // Handle form submission
    const evaluationForm = document.getElementById('evaluationForm');
    evaluationForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const supplierId = document.getElementById('supplier_id').value;
        
        try {
            const response = await fetch(`/admin/suppliers/${supplierId}/evaluations`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            if (response.ok) {
                const successMessage = document.getElementById('successMessage');
                successMessage.classList.remove('d-none');
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                throw new Error('Failed to save evaluation');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Failed to save evaluation. Please try again.');
        }
    });
});
</script>
@endpush

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
<style>
body, .container-fluid {
    background: linear-gradient(135deg, #f8fafc 0%, #e9ecef 100%) !important;
    font-family: 'Inter', 'Segoe UI', Arial, sans-serif;
}
.card {
    border: none;
    border-radius: 1.25rem;
    box-shadow: 0 8px 32px 0 rgba(44,62,80,0.10), 0 1.5px 6px rgba(44,62,80,0.04);
    margin-bottom: 1.5rem;
    background: rgba(255,255,255,0.92);
    backdrop-filter: blur(8px);
    transition: box-shadow 0.2s, background 0.2s;
}
.card:hover {
    box-shadow: 0 16px 48px 0 rgba(44,62,80,0.16), 0 2px 8px rgba(44,62,80,0.08);
    background: rgba(255,255,255,0.97);
}
.card-header {
    border-radius: 1.25rem 1.25rem 0 0;
    border-bottom: 1px solid #e9ecef;
    background: rgba(255,255,255,0.96);
    font-family: 'Inter', Arial, sans-serif;
    font-weight: 700;
    font-size: 1.2rem;
    color: #198754;
    letter-spacing: 0.01em;
    box-shadow: 0 2px 8px 0 rgba(44,62,80,0.04);
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.h3, h1, h4, h5, h6 {
    font-family: 'Inter', Arial, sans-serif;
    font-weight: 700;
    letter-spacing: 0.01em;
}
.table th {
    font-weight: 600;
    color: #495057;
    background: #f8fafc;
    border-top: none;
}
.table-hover tbody tr:hover {
    background: #f4faff;
    transition: background 0.2s;
}
.table td, .table th {
    vertical-align: middle;
}
.btn-gradient-blue {
    background: linear-gradient(90deg, #2196f3 0%, #21cbf3 100%);
    color: #fff !important;
    border: none;
    font-weight: 600;
    border-radius: 2rem;
    padding: 0.5em 1.5em;
    font-size: 1.08em;
    letter-spacing: 0.01em;
    box-shadow: 0 2px 8px #2196f322;
    transition: background 0.2s, color 0.2s, box-shadow 0.2s;
}
.btn-gradient-blue:hover {
    background: linear-gradient(90deg, #21cbf3 0%, #2196f3 100%);
    color: #fff;
    box-shadow: 0 4px 16px #2196f344;
}
.btn-gradient-green {
    background: linear-gradient(90deg, #43e97b 0%, #38f9d7 100%);
    color: #fff !important;
    border: none;
    font-weight: 600;
    border-radius: 2rem;
    padding: 0.5em 1.5em;
    font-size: 1.08em;
    letter-spacing: 0.01em;
    box-shadow: 0 2px 8px #43e97b22;
    transition: background 0.2s, color 0.2s, box-shadow 0.2s;
}
.btn-gradient-green:hover {
    background: linear-gradient(90deg, #38f9d7 0%, #43e97b 100%);
    color: #fff;
    box-shadow: 0 4px 16px #43e97b44;
}
.btn-outline-primary, .btn-outline-success {
    font-weight: 600;
    border-radius: 2rem;
    padding: 0.5em 1.5em;
    font-size: 1.08em;
    letter-spacing: 0.01em;
    transition: background 0.2s, color 0.2s, box-shadow 0.2s;
}
.btn-outline-primary:hover {
    background: #2196f3;
    color: #fff;
    border-color: #2196f3;
    box-shadow: 0 2px 8px #2196f322;
}
.btn-outline-success:hover {
    background: #43e97b;
    color: #fff;
    border-color: #43e97b;
    box-shadow: 0 2px 8px #43e97b22;
}
</style>
@endpush 