<div class="modal fade" id="adminVerifyModal{{ $payment->id }}" tabindex="-1" aria-labelledby="adminVerifyModalLabel{{ $payment->id }}" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="{{ route('payments.submitAdminProof', $payment) }}" enctype="multipart/form-data">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="adminVerifyModalLabel{{ $payment->id }}">Verify Payment</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <h6>Client Submission</h6>
          <div class="mb-2">
            <label class="form-label">Proof of Payment:</label>
            @if($payment->client_payment_proof)
              <a href="{{ asset('storage/' . $payment->client_payment_proof) }}" target="_blank" class="btn btn-link btn-sm">View</a>
            @else
              <span class="text-muted">No file</span>
            @endif
          </div>
          <div class="mb-2"><strong>Method:</strong> {{ $payment->client_payment_method ?? '-' }}</div>
          <div class="mb-2"><strong>Reference #:</strong> {{ $payment->client_reference_number ?? '-' }}</div>
          <div class="mb-2"><strong>Amount:</strong> ₱{{ number_format($payment->client_paid_amount, 2) ?? '-' }}</div>
          <div class="mb-2"><strong>Date:</strong> {{ $payment->client_paid_date ?? '-' }}</div>
          <div class="mb-2"><strong>Notes:</strong> {{ $payment->client_notes ?? '-' }}</div>
          <hr>
          <h6>Admin Verification</h6>
          <div class="mb-3">
            <label for="admin_payment_proof_{{ $payment->id }}" class="form-label">Upload Proof of Receipt</label>
            <input type="file" class="form-control" name="admin_payment_proof" id="admin_payment_proof_{{ $payment->id }}" accept=".jpg,.jpeg,.png,.pdf" required>
          </div>
          <div class="mb-3">
            <label for="admin_payment_method_{{ $payment->id }}" class="form-label">Payment Method</label>
            <select class="form-select" name="admin_payment_method" id="admin_payment_method_{{ $payment->id }}" required>
              <option value="">Select Method</option>
              <option value="bank_transfer" @if($payment->payment_method=='bank_transfer') selected @endif>Bank Transfer</option>
              <option value="check" @if($payment->payment_method=='check') selected @endif>Check</option>
              <option value="cash" @if($payment->payment_method=='cash') selected @endif>Cash</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="admin_reference_number_{{ $payment->id }}" class="form-label">Reference Number</label>
            <input type="text" class="form-control" name="admin_reference_number" id="admin_reference_number_{{ $payment->id }}" required>
            <div class="invalid-feedback" id="reference_number_error_{{ $payment->id }}" style="display: none;">
              Reference number does not match the client's submission.
            </div>
          </div>
          <div class="mb-3">
            <label for="admin_received_amount_{{ $payment->id }}" class="form-label">Amount Received</label>
            <input type="number" step="0.01" class="form-control" name="admin_received_amount" id="admin_received_amount_{{ $payment->id }}" value="{{ $payment->amount }}" required>
          </div>
          <div class="mb-3">
            <label for="admin_received_date_{{ $payment->id }}" class="form-label">Date Received</label>
            <input type="date" class="form-control" name="admin_received_date" id="admin_received_date_{{ $payment->id }}" value="{{ now()->toDateString() }}" required>
          </div>
          <div class="mb-3">
            <label for="admin_notes_{{ $payment->id }}" class="form-label">Notes (optional)</label>
            <textarea class="form-control" name="admin_notes" id="admin_notes_{{ $payment->id }}" rows="2"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success" onclick="return validateReferenceNumber({{ $payment->id }})">Submit Verification</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
function validateReferenceNumber(paymentId) {
  const clientReference = '{{ $payment->client_reference_number }}';
  const adminReference = document.getElementById('admin_reference_number_' + paymentId).value;
  const errorDiv = document.getElementById('reference_number_error_' + paymentId);
  
  if (clientReference && adminReference && clientReference !== adminReference) {
    errorDiv.style.display = 'block';
    return false;
  }
  
  errorDiv.style.display = 'none';
  return true;
}
</script> 