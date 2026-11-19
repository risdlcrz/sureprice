{{-- Admin-only supplier evaluation modal (resources/js/admin/suppliers/rankings.js) --}}

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
                    <div class="alert alert-danger d-none" id="errorMessage"></div>
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
