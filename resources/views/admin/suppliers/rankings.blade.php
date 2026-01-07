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

@push('styles')
    @vite(['resources/css/admin/suppliers/rankings.css'])
@endpush

@push('scripts')
    @vite(['resources/js/admin/suppliers/rankings.js'])
    <script>
        // Pass rankings data to JavaScript
        window.rankingsData = @json($rankings);
    </script>
@endpush 