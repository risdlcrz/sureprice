<!-- Request More Info Modal -->
<div class="modal fade" id="requestMoreInfoModal{{ $payment->id }}" tabindex="-1" aria-labelledby="requestMoreInfoModalLabel{{ $payment->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('payments.request-more-info', $payment) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="requestMoreInfoModalLabel{{ $payment->id }}">
                        <i class="fas fa-question-circle text-info"></i> 
                        Request More Information - {{ $payment->payment_number }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Payment Summary -->
                    <div class="alert alert-info">
                        <h6 class="fw-bold"><i class="fas fa-info-circle"></i> Payment Details</h6>
                        <p><strong>Client:</strong> {{ $payment->contract->client->name ?? 'N/A' }}</p>
                        <p><strong>Amount:</strong> ₱{{ number_format($payment->amount, 2) }}</p>
                        <p><strong>Payment Method:</strong> {{ ucfirst(str_replace('_', ' ', $payment->client_payment_method)) }}</p>
                        <p><strong>Reference Number:</strong> {{ $payment->client_reference_number }}</p>
                    </div>

                    <!-- Information Request Type -->
                    <div class="mb-3">
                        <label for="info_request_type_{{ $payment->id }}" class="form-label fw-bold">
                            <i class="fas fa-list text-info"></i> Type of Information Needed
                        </label>
                        <select name="info_request_type" id="info_request_type_{{ $payment->id }}" class="form-select" required>
                            <option value="">Select information type</option>
                            <option value="additional_proof">Additional Payment Proof</option>
                            <option value="bank_statement">Bank Statement</option>
                            <option value="transaction_details">Transaction Details</option>
                            <option value="payment_confirmation">Payment Confirmation</option>
                            <option value="receipt_copy">Receipt Copy</option>
                            <option value="account_details">Account Details</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <!-- Specific Request -->
                    <div class="mb-3">
                        <label for="specific_request_{{ $payment->id }}" class="form-label fw-bold">
                            <i class="fas fa-edit text-info"></i> Specific Information Request
                        </label>
                        <textarea name="specific_request" id="specific_request_{{ $payment->id }}" 
                                  class="form-control" rows="4" 
                                  placeholder="Please specify what additional information is needed..." required></textarea>
                        <div class="form-text">Be specific about what information is required</div>
                    </div>

                    <!-- Deadline -->
                    <div class="mb-3">
                        <label for="response_deadline_{{ $payment->id }}" class="form-label fw-bold">
                            <i class="fas fa-calendar text-info"></i> Response Deadline
                        </label>
                        <input type="date" name="response_deadline" id="response_deadline_{{ $payment->id }}" 
                               class="form-control" value="{{ date('Y-m-d', strtotime('+7 days')) }}" required>
                        <div class="form-text">When should the client provide this information?</div>
                    </div>

                    <!-- Priority Level -->
                    <div class="mb-3">
                        <label for="priority_level_{{ $payment->id }}" class="form-label fw-bold">
                            <i class="fas fa-flag text-info"></i> Priority Level
                        </label>
                        <select name="priority_level" id="priority_level_{{ $payment->id }}" class="form-select" required>
                            <option value="low">Low Priority</option>
                            <option value="medium" selected>Medium Priority</option>
                            <option value="high">High Priority</option>
                            <option value="urgent">Urgent</option>
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

                    <!-- Information -->
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Note:</strong> This will keep the payment in "For Verification" status and send a notification to the client requesting additional information.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-info">
                        <i class="fas fa-paper-plane"></i> Send Information Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-fill specific request based on selected type
    const typeSelect = document.getElementById('info_request_type_{{ $payment->id }}');
    const requestTextarea = document.getElementById('specific_request_{{ $payment->id }}');
    
    typeSelect.addEventListener('change', function() {
        const type = this.value;
        let request = '';
        
        switch(type) {
            case 'additional_proof':
                request = 'Please provide additional payment proof such as a screenshot of the transaction, bank transfer confirmation, or receipt.';
                break;
            case 'bank_statement':
                request = 'Please provide a copy of your bank statement showing the transaction details for this payment.';
                break;
            case 'transaction_details':
                request = 'Please provide detailed transaction information including the exact time, date, and any transaction IDs or reference numbers.';
                break;
            case 'payment_confirmation':
                request = 'Please provide a payment confirmation email, SMS, or any official confirmation from your payment method.';
                break;
            case 'receipt_copy':
                request = 'Please provide a clear, legible copy of the payment receipt or proof of payment.';
                break;
            case 'account_details':
                request = 'Please provide the account details used for this payment (account number, bank name, etc.).';
                break;
            case 'other':
                request = 'Please provide the specific information requested by our finance team.';
                break;
        }
        
        requestTextarea.value = request;
    });

    // Set default deadline to 7 days from now
    const deadlineInput = document.getElementById('response_deadline_{{ $payment->id }}');
    if (deadlineInput && !deadlineInput.value) {
        const sevenDaysFromNow = new Date();
        sevenDaysFromNow.setDate(sevenDaysFromNow.getDate() + 7);
        deadlineInput.value = sevenDaysFromNow.toISOString().split('T')[0];
    }
});
</script> 