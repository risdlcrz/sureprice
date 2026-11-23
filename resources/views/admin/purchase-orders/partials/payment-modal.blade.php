{{-- Submit a payment to the supplier for verification (POST purchase-orders.payments.store). Param: $purchaseOrder --}}
<x-modal-dialog id="submitPaymentModal">
    <x-slot:title>Submit Payment to Supplier</x-slot:title>
    <form method="POST" action="{{ route('purchase-orders.payments.store', $purchaseOrder) }}" enctype="multipart/form-data">
        @csrf
        <div class="modal-body">
            <x-field name="amount" label="Amount (₱)" type="number" step="0.01" min="0.01" :value="$purchaseOrder->total_amount" required />
            <x-field name="payment_method" label="Payment Method" required>
                <select name="payment_method" id="payment_method" class="form-select" required>
                    @foreach(['bank_transfer' => 'Bank Transfer', 'check' => 'Check', 'cash' => 'Cash'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('payment_method') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </x-field>
            <x-field name="admin_reference_number" label="Reference Number" required />
            <x-field name="admin_paid_date" label="Date Paid" type="date" :value="now()->toDateString()" required />
            <x-field name="admin_proof" label="Proof of Payment" type="file" upload="documents" required />
            <x-field name="admin_notes" label="Notes (optional)" type="textarea" />
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Submit for Verification</button>
        </div>
    </form>
</x-modal-dialog>