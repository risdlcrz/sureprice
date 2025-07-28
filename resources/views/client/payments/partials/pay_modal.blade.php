<!-- Payment Modal for Client -->
<div class="modal fade" id="payModal{{ $payment->id }}" tabindex="-1" aria-labelledby="payModalLabel{{ $payment->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('payments.submitClientProof', $payment->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="payModalLabel{{ $payment->id }}">
                        <i class="fas fa-credit-card text-success"></i> 
                        Submit Payment for {{ $payment->payment_type }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Payment Details -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-primary">Payment Information</h6>
                            <p><strong>Payment Number:</strong> {{ $payment->payment_number }}</p>
                            <p><strong>Amount Due:</strong> <span class="text-success fw-bold">₱{{ number_format($payment->amount, 2) }}</span></p>
                            <p><strong>Due Date:</strong> {{ $payment->due_date->format('M d, Y') }}</p>
                            <p><strong>Contract:</strong> {{ $payment->contract->contract_number ?? 'Contract #' . $payment->contract_id }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-primary">Contract Details</h6>
                            <p><strong>Client:</strong> {{ $payment->contract->client->name ?? 'N/A' }}</p>
                            <p><strong>Contractor:</strong> {{ $payment->contract->contractor->name ?? 'N/A' }}</p>
                            <p><strong>Payment Type:</strong> {{ ucfirst($payment->payment_type) }}</p>
                        </div>
                    </div>

                    <hr>

                    <!-- Payment Form -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="client_payment_method_{{ $payment->id }}" class="form-label">
                                    <i class="fas fa-money-bill-wave"></i> Payment Method <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="client_payment_method_{{ $payment->id }}" name="client_payment_method" required>
                                    <option value="">Select payment method</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                    <option value="gcash">GCash</option>
                                    <option value="paymaya">PayMaya</option>
                                    <option value="credit_card">Credit Card</option>
                                    <option value="debit_card">Debit Card</option>
                                    <option value="cash">Cash</option>
                                    <option value="check">Check</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="client_reference_number_{{ $payment->id }}" class="form-label">
                                    <i class="fas fa-hashtag"></i> Reference Number <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="client_reference_number_{{ $payment->id }}" 
                                       name="client_reference_number" placeholder="Enter reference number" required>
                                <div class="form-text">Transaction ID, receipt number, or any reference from your payment</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="client_paid_amount_{{ $payment->id }}" class="form-label">
                                    <i class="fas fa-coins"></i> Amount Paid <span class="text-danger">*</span>
                                </label>
                                <input type="number" step="0.01" min="0" class="form-control" 
                                       id="client_paid_amount_{{ $payment->id }}" name="client_paid_amount" 
                                       value="{{ $payment->amount }}" required>
                                <div class="form-text">Should match the amount due: ₱{{ number_format($payment->amount, 2) }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="client_paid_date_{{ $payment->id }}" class="form-label">
                                    <i class="fas fa-calendar"></i> Payment Date <span class="text-danger">*</span>
                                </label>
                                <input type="date" class="form-control" id="client_paid_date_{{ $payment->id }}" 
                                       name="client_paid_date" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="client_payment_proof_{{ $payment->id }}" class="form-label">
                            <i class="fas fa-file-upload"></i> Upload Payment Proof <span class="text-danger">*</span>
                        </label>
                        <input type="file" class="form-control" id="client_payment_proof_{{ $payment->id }}" 
                               name="client_payment_proof" accept=".jpg,.jpeg,.png,.pdf" required>
                        <div class="form-text">
                            Upload screenshot, receipt, or proof of payment (JPG, PNG, or PDF, max 5MB)
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="client_notes_{{ $payment->id }}" class="form-label">
                            <i class="fas fa-sticky-note"></i> Additional Notes
                        </label>
                        <textarea class="form-control" id="client_notes_{{ $payment->id }}" name="client_notes" 
                                  rows="3" placeholder="Any additional information about your payment..."></textarea>
                    </div>

                    <!-- Payment Instructions -->
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle"></i> Payment Instructions:</h6>
                        <ul class="mb-0">
                            <li>Ensure the amount paid matches the amount due</li>
                            <li>Upload clear proof of payment (receipt, screenshot, etc.)</li>
                            <li>Provide accurate reference number from your payment transaction</li>
                            <li>Your payment will be reviewed by our finance team</li>
                            <li>You'll receive confirmation once payment is verified</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-paper-plane"></i> Submit Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div> 