<!-- Reject Payment Modal -->
<div class="modal fade" id="rejectPaymentModal{{ $payment->id }}" tabindex="-1" aria-labelledby="rejectPaymentModalLabel{{ $payment->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('payments.reject', $payment) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectPaymentModalLabel{{ $payment->id }}">
                        <i class="fas fa-times-circle text-danger"></i> 
                        Reject Payment - {{ $payment->payment_number }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Payment Summary -->
                    <div class="alert alert-warning">
                        <h6 class="fw-bold"><i class="fas fa-exclamation-triangle"></i> Payment Details</h6>
                        <p><strong>Client:</strong> {{ $payment->contract->client->name ?? 'N/A' }}</p>
                        <p><strong>Amount:</strong> ₱{{ number_format($payment->amount, 2) }}</p>
                        <p><strong>Payment Method:</strong> {{ ucfirst(str_replace('_', ' ', $payment->client_payment_method)) }}</p>
                        <p><strong>Reference Number:</strong> {{ $payment->client_reference_number }}</p>
                    </div>

                    <!-- Rejection Reason -->
                    <div class="mb-3">
                        <label for="rejection_reason_{{ $payment->id }}" class="form-label fw-bold">
                            <i class="fas fa-comment text-danger"></i> Reason for Rejection
                        </label>
                        <select name="rejection_reason" id="rejection_reason_{{ $payment->id }}" class="form-select" required>
                            <option value="">Select a reason</option>
                            <option value="invalid_reference">Invalid Reference Number</option>
                            <option value="amount_mismatch">Amount Mismatch</option>
                            <option value="payment_not_received">Payment Not Received</option>
                            <option value="invalid_proof">Invalid or Unclear Payment Proof</option>
                            <option value="wrong_payment_method">Wrong Payment Method</option>
                            <option value="duplicate_payment">Duplicate Payment</option>
                            <option value="late_payment">Payment Received After Due Date</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <!-- Detailed Explanation -->
                    <div class="mb-3">
                        <label for="rejection_details_{{ $payment->id }}" class="form-label fw-bold">
                            <i class="fas fa-edit text-danger"></i> Detailed Explanation
                        </label>
                        <textarea name="rejection_details" id="rejection_details_{{ $payment->id }}" 
                                  class="form-control" rows="4" 
                                  placeholder="Please provide detailed explanation for the rejection..." required></textarea>
                        <div class="form-text">This explanation will be sent to the client</div>
                    </div>

                    <!-- Action Required -->
                    <div class="mb-3">
                        <label for="action_required_{{ $payment->id }}" class="form-label fw-bold">
                            <i class="fas fa-tasks text-danger"></i> Action Required from Client
                        </label>
                        <select name="action_required" id="action_required_{{ $payment->id }}" class="form-select" required>
                            <option value="">Select required action</option>
                            <option value="resubmit_proof">Resubmit Payment Proof</option>
                            <option value="correct_amount">Pay Correct Amount</option>
                            <option value="use_correct_method">Use Correct Payment Method</option>
                            <option value="provide_valid_reference">Provide Valid Reference Number</option>
                            <option value="contact_finance">Contact Finance Department</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <!-- Finance Notes -->
                    <div class="mb-3">
                        <label for="finance_notes_{{ $payment->id }}" class="form-label fw-bold">
                            <i class="fas fa-sticky-note text-primary"></i> Internal Notes (Optional)
                        </label>
                        <textarea name="finance_notes" id="finance_notes_{{ $payment->id }}" 
                                  class="form-control" rows="3" 
                                  placeholder="Internal notes for finance team..."></textarea>
                        <div class="form-text">These notes will not be shared with the client</div>
                    </div>

                    <!-- Warning -->
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Warning:</strong> Rejecting this payment will return it to "Pending" status and notify the client. 
                        The client will need to resubmit the payment with the correct information.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times-circle"></i> Reject Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-fill detailed explanation based on selected reason
    const reasonSelect = document.getElementById('rejection_reason_{{ $payment->id }}');
    const detailsTextarea = document.getElementById('rejection_details_{{ $payment->id }}');
    
    reasonSelect.addEventListener('change', function() {
        const reason = this.value;
        let explanation = '';
        
        switch(reason) {
            case 'invalid_reference':
                explanation = 'The reference number provided does not match our records or is invalid. Please provide a valid transaction reference number.';
                break;
            case 'amount_mismatch':
                explanation = 'The amount paid does not match the required payment amount. Please ensure you pay the exact amount due.';
                break;
            case 'payment_not_received':
                explanation = 'We have not received the payment in our system. Please verify that the payment was completed successfully.';
                break;
            case 'invalid_proof':
                explanation = 'The payment proof provided is unclear or invalid. Please provide a clear, legible copy of the payment receipt.';
                break;
            case 'wrong_payment_method':
                explanation = 'The payment method used is not accepted for this transaction. Please use an approved payment method.';
                break;
            case 'duplicate_payment':
                explanation = 'This appears to be a duplicate payment. Please verify that you have not already submitted this payment.';
                break;
            case 'late_payment':
                explanation = 'The payment was received after the due date. Please contact us to discuss late payment arrangements.';
                break;
            case 'other':
                explanation = 'Please provide specific details about the issue with this payment submission.';
                break;
        }
        
        detailsTextarea.value = explanation;
    });
});
</script> 