<div class="modal fade" id="clientPayModal{{ $payment->id }}" tabindex="-1" aria-labelledby="clientPayModalLabel{{ $payment->id }}" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="{{ route('payments.submitClientProof', $payment) }}" enctype="multipart/form-data">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="clientPayModalLabel{{ $payment->id }}">Submit Payment Proof</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="client_payment_proof_{{ $payment->id }}" class="form-label">Upload Proof of Payment</label>
            <input type="file" class="form-control" name="client_payment_proof" id="client_payment_proof_{{ $payment->id }}" accept=".jpg,.jpeg,.png,.pdf" required>
          </div>
          <div class="mb-3">
            <label for="client_payment_method_{{ $payment->id }}" class="form-label">Payment Method</label>
            <select class="form-select" name="client_payment_method" id="client_payment_method_{{ $payment->id }}" required>
              <option value="">Select Method</option>
              <option value="bank_transfer" @if($payment->payment_method=='bank_transfer') selected @endif>Bank Transfer</option>
              <option value="check" @if($payment->payment_method=='check') selected @endif>Check</option>
              <option value="cash" @if($payment->payment_method=='cash') selected @endif>Cash</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="client_reference_number_{{ $payment->id }}" class="form-label">Reference Number</label>
            <input type="text" class="form-control" name="client_reference_number" id="client_reference_number_{{ $payment->id }}" required>
          </div>
          <div class="mb-3">
            <label for="client_paid_amount_{{ $payment->id }}" class="form-label">Amount Paid</label>
            <input type="number" step="0.01" class="form-control" name="client_paid_amount" id="client_paid_amount_{{ $payment->id }}" value="{{ $payment->amount }}" required>
          </div>
          <div class="mb-3">
            <label for="client_paid_date_{{ $payment->id }}" class="form-label">Date Paid</label>
            <input type="date" class="form-control" name="client_paid_date" id="client_paid_date_{{ $payment->id }}" value="{{ now()->toDateString() }}" required>
          </div>
          <div class="mb-3">
            <label for="client_notes_{{ $payment->id }}" class="form-label">Notes (optional)</label>
            <textarea class="form-control" name="client_notes" id="client_notes_{{ $payment->id }}" rows="2"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Submit for Verification</button>
        </div>
      </form>
    </div>
  </div>
</div> 