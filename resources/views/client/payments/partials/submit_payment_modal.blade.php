<!-- Submit Payment Modal for Client -->
<div class="modal fade" id="submitPaymentModal{{ $payment->id }}" tabindex="-1" aria-labelledby="submitPaymentModalLabel{{ $payment->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('client.payments.submit-client-proof', $payment) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="submitPaymentModalLabel{{ $payment->id }}">
                        Submit Payment Proof - {{ ucfirst($payment->payment_type) }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Payment Details -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="fw-bold text-primary">Payment Details</h6>
                            <p><strong>Payment #:</strong> {{ $payment->payment_number }}</p>
                            <p><strong>Amount Due:</strong> ₱{{ number_format($payment->amount, 2) }}</p>
                            <p><strong>Due Date:</strong> {{ $payment->due_date->format('M d, Y') }}</p>
                            <p><strong>Contract:</strong> {{ $payment->contract->contract_number ?? 'Contract #' . $payment->contract_id }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold text-primary">Contract Information</h6>
                            <p><strong>Contractor:</strong> {{ $payment->contract->contractor->name ?? 'N/A' }} - {{ $payment->contract->contractor->company_name ?? 'N/A' }}</p>
                            <p><strong>Client:</strong> {{ $payment->contract->client->name ?? 'N/A' }}</p>
                            <p><strong>Payment Type:</strong> {{ ucfirst($payment->payment_type) }}</p>
                        </div>
                    </div>

                    <!-- Payment Method Selection -->
                    <div class="mb-3">
                        <label for="client_payment_method_{{ $payment->id }}" class="form-label fw-bold">
                            Payment Method
                        </label>
                        <select name="client_payment_method" id="client_payment_method_{{ $payment->id }}" class="form-select" required>
                            <option value="">Select Payment Method</option>
                            <option value="bank_transfer" {{ $payment->contract->payment_method == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                            <option value="check" {{ $payment->contract->payment_method == 'check' ? 'selected' : '' }}>Check</option>
                            <option value="cash" {{ $payment->contract->payment_method == 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="online_payment" {{ $payment->contract->payment_method == 'online_payment' ? 'selected' : '' }}>Online Payment</option>
                            <option value="mobile_banking" {{ $payment->contract->payment_method == 'mobile_banking' ? 'selected' : '' }}>Mobile Banking</option>
                        </select>
                        <div class="form-text">
                            <strong>Contract Payment Method:</strong> {{ ucfirst(str_replace('_', ' ', $payment->contract->payment_method)) }}
                            <br>You can change this if you used a different payment method.
                        </div>
                    </div>

                    <!-- Reference Number -->
                    <div class="mb-3">
                        <label for="client_reference_number_{{ $payment->id }}" class="form-label fw-bold">
                            # Reference Number
                        </label>
                        <input type="text" name="client_reference_number" id="client_reference_number_{{ $payment->id }}"
                               class="form-control" placeholder="Enter transaction/reference number" required>
                        <div class="form-text">Enter the transaction number, check number, or any reference number from your payment.</div>
                    </div>

                    <!-- Amount Paid -->
                    <div class="mb-3">
                        <label for="client_paid_amount_{{ $payment->id }}" class="form-label fw-bold">
                            Amount Paid
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">₱</span>
                            <input type="number" name="client_paid_amount" id="client_paid_amount_{{ $payment->id }}"
                                   class="form-control" value="{{ $payment->amount }}" step="0.01" min="0" required>
                        </div>
                        <div class="form-text">Enter the exact amount you paid (should match the due amount).</div>
                    </div>

                    <!-- Payment Date -->
                    <div class="mb-3">
                        <label for="client_paid_date_{{ $payment->id }}" class="form-label fw-bold">
                            Payment Date
                        </label>
                        <input type="date" name="client_paid_date" id="client_paid_date_{{ $payment->id }}"
                               class="form-control" value="{{ date('Y-m-d') }}" required>
                        <div class="form-text">Select the date when you made the payment.</div>
                    </div>

                    <!-- Payment Proof Upload -->
                    <div class="mb-3">
                        <label for="client_payment_proof_{{ $payment->id }}" class="form-label fw-bold">
                            Upload Payment Proof
                        </label>
                        <input type="file" name="client_payment_proof" id="client_payment_proof_{{ $payment->id }}"
                               class="form-control" required {{ \App\Support\Uploads::attributes('documents', 1, 5120) }}>
                        <div class="form-text">
                            Upload a screenshot, photo, or scanned copy of your payment receipt, bank transfer confirmation, or check image.
                        </div>
                    </div>

                    <!-- Additional Notes -->
                    <div class="mb-3">
                        <label for="client_notes_{{ $payment->id }}" class="form-label fw-bold">
                            Additional Notes (Optional)
                        </label>
                        <textarea name="client_notes" id="client_notes_{{ $payment->id }}"
                                  class="form-control" rows="3"
                                  placeholder="Add any additional information about your payment..."></textarea>
                        <div class="form-text">Include any additional details that might help with verification.</div>
                    </div>

                    <!-- Important Notice -->
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Important:</strong> Your payment will be reviewed by our finance team.
                        You will receive a notification once the payment is verified.
                        Please ensure all information provided is accurate.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-success">
                        Submit Payment Proof
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modalId = 'submitPaymentModal{{ $payment->id }}';
    const modal = document.getElementById(modalId);

    if (!modal) {
        console.error('Modal not found:', modalId);
        return;
    }

    // Get form elements
    const form = modal.querySelector('form');
    const paymentDateInput = document.getElementById('client_paid_date_{{ $payment->id }}');
    const amountInput = document.getElementById('client_paid_amount_{{ $payment->id }}');
    const fileInput = document.getElementById('client_payment_proof_{{ $payment->id }}');
    const dueAmount = {{ $payment->amount }};

    // Set default values once when modal loads
    if (paymentDateInput && !paymentDateInput.value) {
        paymentDateInput.value = new Date().toISOString().split('T')[0];
    }

    // Set the payment method to match the contract's payment method
    const paymentMethodSelect = document.getElementById('client_payment_method_{{ $payment->id }}');
    if (paymentMethodSelect) {
        const contractPaymentMethod = '{{ $payment->contract->payment_method }}';
        if (contractPaymentMethod) {
            paymentMethodSelect.value = contractPaymentMethod;
        }
    }

    // Simple amount validation without real-time updates
    if (amountInput) {
        amountInput.addEventListener('blur', function() {
            const paidAmount = parseFloat(this.value) || 0;
            if (Math.abs(paidAmount - dueAmount) > 0.01) {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
            }
        });
    }

    // File type/size/count are checked by resources/js/upload-limits.js (data-upload-* on the input)

    // Simple form submission without complex validation
    if (form) {
        form.addEventListener('submit', function(e) {
            // Basic validation
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('is-invalid');
                    isValid = false;
                } else {
                    field.classList.remove('is-invalid');
                }
            });

            // Validate amount
            if (amountInput) {
                const paidAmount = parseFloat(amountInput.value) || 0;
                if (Math.abs(paidAmount - dueAmount) > 0.01) {
                    amountInput.classList.add('is-invalid');
                    isValid = false;
                }
            }

            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields correctly.');
                return;
            }

            // Show loading state
            const submitBtn = form.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
            submitBtn.disabled = true;
        });
    }

    // Remove all modal event listeners that might cause glitching
    // Only keep essential functionality
});
</script>