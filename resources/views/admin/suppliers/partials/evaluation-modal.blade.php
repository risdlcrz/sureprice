<div class="modal fade" id="evaluateSupplierModal" tabindex="-1" aria-labelledby="evaluateSupplierModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="evaluateSupplierModalLabel">Evaluate Supplier</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="evaluateSupplierForm" method="POST" action="{{ route('admin.suppliers.evaluate', ['supplier' => ':id']) }}">
                    @csrf
                    
                    <!-- Quality Score -->
                    <div class="mb-3">
                        <label for="quality_score" class="form-label">Quality Score (1-5) *</label>
                        <input type="number" class="form-control" id="quality_score" name="quality_score" min="1" max="5" required>
                        <div class="form-text">Rate the quality of products/services provided</div>
                    </div>

                    <!-- Delivery Score -->
                    <div class="mb-3">
                        <label for="delivery_score" class="form-label">Delivery Score (1-5) *</label>
                        <input type="number" class="form-control" id="delivery_score" name="delivery_score" min="1" max="5" required>
                        <div class="form-text">Rate the timeliness and reliability of deliveries</div>
                    </div>

                    <!-- Price Score -->
                    <div class="mb-3">
                        <label for="price_score" class="form-label">Price Score (1-5) *</label>
                        <input type="number" class="form-control" id="price_score" name="price_score" min="1" max="5" required>
                        <div class="form-text">Rate the competitiveness of pricing</div>
                    </div>

                    <!-- Communication Score -->
                    <div class="mb-3">
                        <label for="communication_score" class="form-label">Communication Score (1-5) *</label>
                        <input type="number" class="form-control" id="communication_score" name="communication_score" min="1" max="5" required>
                        <div class="form-text">Rate the effectiveness of communication</div>
                    </div>

                    <!-- Comments -->
                    <div class="mb-3">
                        <label for="comments" class="form-label">Comments</label>
                        <textarea class="form-control" id="comments" name="comments" rows="3"></textarea>
                        <div class="form-text">Additional feedback or observations</div>
                    </div>

                    <!-- Evaluation Date -->
                    <div class="mb-3">
                        <label for="evaluation_date" class="form-label">Evaluation Date *</label>
                        <input type="date" class="form-control" id="evaluation_date" name="evaluation_date" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="evaluateSupplierForm" class="btn btn-primary">Submit Evaluation</button>
            </div>
        </div>
    </div>
</div> 