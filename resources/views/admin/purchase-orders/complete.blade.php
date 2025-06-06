<!-- Purchase Order Completion Modal -->
<div class="modal fade" id="completePurchaseOrderModal" tabindex="-1" aria-labelledby="completePurchaseOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="completePurchaseOrderModalLabel">Complete Purchase Order</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="completePurchaseOrderForm">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <div class="alert alert-success d-none" id="completeSuccessMessage">
                        Purchase order completed successfully!
                    </div>

                    <!-- Delivery Information -->
                    <div class="mb-4">
                        <h6 class="mb-3">Delivery Information</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="delivery_date" class="form-label">Delivery Date</label>
                                    <input type="date" class="form-control" id="delivery_date" name="delivery_date" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label d-block">Delivery Status</label>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="is_on_time" id="ontime" value="1" checked>
                                        <label class="form-check-label" for="ontime">On Time</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="is_on_time" id="delayed" value="0">
                                        <label class="form-check-label" for="delayed">Delayed</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quality Assessment -->
                    <div class="mb-4">
                        <h6 class="mb-3">Quality Assessment</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="total_units" class="form-label">Total Units Received</label>
                                    <input type="number" class="form-control" id="total_units" name="total_units" min="0" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="defective_units" class="form-label">Defective Units</label>
                                    <input type="number" class="form-control" id="defective_units" name="defective_units" min="0" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="quality_notes" class="form-label">Quality Notes</label>
                            <textarea class="form-control" id="quality_notes" name="quality_notes" rows="3"></textarea>
                        </div>
                    </div>

                    <!-- Cost Information -->
                    <div class="mb-4">
                        <h6 class="mb-3">Cost Information</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="estimated_cost" class="form-label">Estimated Cost</label>
                                    <div class="input-group">
                                        <span class="input-group-text">₱</span>
                                        <input type="number" class="form-control" id="estimated_cost" name="estimated_cost" min="0" step="0.01" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="actual_cost" class="form-label">Actual Cost</label>
                                    <div class="input-group">
                                        <span class="input-group-text">₱</span>
                                        <input type="number" class="form-control" id="actual_cost" name="actual_cost" min="0" step="0.01" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success" id="completeButton">
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        Complete Purchase Order
                    </button>
                </div>
            </form>
        </div>
    </div>
</div> 