<!-- Verify Payment Modal -->
<div class="modal fade" id="verifyPaymentModal{{ $payment->id }}" tabindex="-1" aria-labelledby="verifyPaymentModalLabel{{ $payment->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('payments.verify', $payment) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="verifyPaymentModalLabel{{ $payment->id }}">
                        <i class="fas fa-check-circle text-success"></i> 
                        Verify Payment - {{ $payment->payment_number }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Payment Summary -->
                    <div class="alert alert-info">
                        <h6 class="fw-bold"><i class="fas fa-info-circle"></i> Payment Summary</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Client:</strong> {{ $payment->contract->client->name ?? 'N/A' }}<br>
                                <strong>Contract:</strong> {{ $payment->contract->contract_number ?? 'Contract #' . $payment->contract_id }}<br>
                                <strong>Payment Type:</strong> {{ ucfirst($payment->payment_type) }}
                            </div>
                            <div class="col-md-6">
                                <strong>Amount Due:</strong> ₱{{ number_format($payment->amount, 2) }}<br>
                                <strong>Amount Paid:</strong> ₱{{ number_format($payment->client_paid_amount, 2) }}<br>
                                <strong>Payment Method:</strong> {{ ucfirst(str_replace('_', ' ', $payment->client_payment_method)) }}
                            </div>
                        </div>
                    </div>

                    <!-- Verification Details -->
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="fw-bold text-primary">Client Submission</h6>
                            <p><strong>Reference Number:</strong> {{ $payment->client_reference_number }}</p>
                            <p><strong>Payment Date:</strong> {{ $payment->client_paid_date ? $payment->client_paid_date->format('M d, Y') : 'N/A' }}</p>
                            @if($payment->client_notes)
                                <p><strong>Client Notes:</strong> {{ $payment->client_notes }}</p>
                            @endif
                            @if($payment->client_payment_proof)
                                <p><strong>Proof:</strong> 
                                    <a href="{{ route('payments.proof', $payment) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold text-primary">Finance Verification</h6>
                            
                            <!-- Payment Method Verification -->
                            <div class="mb-3">
                                <label for="admin_payment_method_{{ $payment->id }}" class="form-label fw-bold">
                                    <i class="fas fa-credit-card text-primary"></i> Payment Method (Client Submission)
                                </label>
                                <input type="text" name="admin_payment_method" id="admin_payment_method_{{ $payment->id }}" 
                                       class="form-control bg-light" 
                                       value="{{ ucfirst(str_replace('_', ' ', $payment->client_payment_method)) }}" 
                                       readonly>
                                <div class="form-text">
                                    <i class="fas fa-info-circle text-info"></i> 
                                    This reflects the payment method the client actually used. Cannot be changed during verification.
                                </div>
                            </div>

                            <!-- Reference Number Verification -->
                            <div class="mb-3">
                                <label for="admin_reference_number_{{ $payment->id }}" class="form-label fw-bold">
                                    <i class="fas fa-hashtag text-primary"></i> Reference Number
                                </label>
                                <input type="text" name="admin_reference_number" id="admin_reference_number_{{ $payment->id }}" 
                                       class="form-control" value="{{ $payment->client_reference_number }}" required>
                                <div class="form-text">Verify the reference number matches client submission</div>
                            </div>

                            <!-- Amount Verification -->
                            <div class="mb-3">
                                <label for="admin_received_amount_{{ $payment->id }}" class="form-label fw-bold">
                                    <i class="fas fa-money-bill-wave text-primary"></i> Amount Received
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">₱</span>
                                    <input type="number" name="admin_received_amount" id="admin_received_amount_{{ $payment->id }}" 
                                           class="form-control" value="{{ $payment->client_paid_amount }}" step="0.01" required>
                                </div>
                                <div class="form-text">Verify the amount received matches client payment</div>
                            </div>

                            <!-- Received Date -->
                            <div class="mb-3">
                                <label for="admin_received_date_{{ $payment->id }}" class="form-label fw-bold">
                                    <i class="fas fa-calendar text-primary"></i> Date Received
                                </label>
                                <input type="date" name="admin_received_date" id="admin_received_date_{{ $payment->id }}" 
                                       class="form-control" value="{{ date('Y-m-d') }}" required>
                                <div class="form-text">Date when payment was actually received</div>
                            </div>

                            <!-- Finance Notes -->
                            <div class="mb-3">
                                <label for="admin_notes_{{ $payment->id }}" class="form-label fw-bold">
                                    <i class="fas fa-sticky-note text-primary"></i> Verification Notes
                                </label>
                                <textarea name="admin_notes" id="admin_notes_{{ $payment->id }}" 
                                          class="form-control" rows="3" 
                                          placeholder="Add verification notes or comments..."></textarea>
                                <div class="form-text">Optional notes about the verification process</div>
                            </div>

                            <!-- Upload Finance Proof -->
                            <div class="mb-3">
                                <label for="admin_payment_proof_{{ $payment->id }}" class="form-label fw-bold">
                                    <i class="fas fa-upload text-primary"></i> Upload Finance Proof (Optional)
                                </label>
                                <input type="file" name="admin_payment_proof" id="admin_payment_proof_{{ $payment->id }}" 
                                       class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                                <div class="form-text">Upload bank statement, receipt, or other proof of payment receipt</div>
                            </div>
                        </div>
                    </div>

                    <!-- Verification Checklist -->
                    <div class="alert alert-warning">
                        <h6 class="fw-bold"><i class="fas fa-clipboard-check"></i> Verification Checklist</h6>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="check_amount_{{ $payment->id }}" required>
                            <label class="form-check-label" for="check_amount_{{ $payment->id }}">
                                Amount received matches the due amount (₱{{ number_format($payment->amount, 2) }})
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="check_reference_{{ $payment->id }}" required>
                            <label class="form-check-label" for="check_reference_{{ $payment->id }}">
                                Reference number is valid and matches client submission
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="check_proof_{{ $payment->id }}" required>
                            <label class="form-check-label" for="check_proof_{{ $payment->id }}">
                                Payment proof has been reviewed and is valid
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="check_method_{{ $payment->id }}" required>
                            <label class="form-check-label" for="check_method_{{ $payment->id }}">
                                Payment method is correct and payment has been received
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check"></i> Verify & Mark as Paid
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-fill received date with today's date
    const receivedDateInput = document.getElementById('admin_received_date_{{ $payment->id }}');
    if (receivedDateInput && !receivedDateInput.value) {
        receivedDateInput.value = new Date().toISOString().split('T')[0];
    }

    // Validate amount received matches due amount
    const amountInput = document.getElementById('admin_received_amount_{{ $payment->id }}');
    const dueAmount = {{ $payment->amount }};
    
    amountInput.addEventListener('change', function() {
        const receivedAmount = parseFloat(this.value);
        if (Math.abs(receivedAmount - dueAmount) > 0.01) {
            this.classList.add('is-invalid');
            this.nextElementSibling.innerHTML = 'Amount received should match the due amount (₱' + dueAmount.toFixed(2) + ')';
        } else {
            this.classList.remove('is-invalid');
            this.nextElementSibling.innerHTML = '';
        }
    });

    // File size validation
    const fileInput = document.getElementById('admin_payment_proof_{{ $payment->id }}');
    if (fileInput) {
        fileInput.addEventListener('change', function() {
            const file = this.files[0];
            const maxSize = 5 * 1024 * 1024; // 5MB
            
            if (file && file.size > maxSize) {
                alert('File size must be less than 5MB');
                this.value = '';
            }
        });
    }
});
</script> 