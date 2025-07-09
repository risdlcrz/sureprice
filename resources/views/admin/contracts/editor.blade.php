@extends('layouts.app')

@section('content')
<div class="container contract-template" id="contract-editor-root">
    <form id="contractEditorForm" method="POST" action="{{ isset($contract) ? route('contracts.update', $contract->id) : route('contracts.store') }}">
        @csrf
        @if(isset($contract))
            @method('PUT')
        @endif
        <div class="mb-4 row">
            <div class="col-md-6">
                <label for="quotation_request_id" class="form-label"><strong>Client Quotation Request</strong> <span class="text-danger">*</span></label>
                <select name="quotation_request_id" id="quotation_request_id" class="form-control" required>
                    <option value="">Select Quotation Request</option>
                    @foreach($quotationRequests as $qr)
                        <option value="{{ $qr->id }}" data-client="{{ $qr->user->name }}" data-request-number="{{ $qr->request_number }}">QR-{{ $qr->request_number }} - {{ $qr->user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label for="constructor_id" class="form-label"><strong>Constructor</strong> <span class="text-danger">*</span></label>
                <select name="constructor_id" id="constructor_id" class="form-control" required>
                    <option value="">Select Constructor</option>
                    @foreach($contractors as $c)
                        <option value="{{ $c->id }}"
                            data-first_name="{{ $c->first_name }}"
                            data-last_name="{{ $c->last_name }}"
                            data-company_name="{{ $c->company_name }}"
                            data-street="{{ $c->street }}"
                            data-barangay="{{ $c->barangay }}"
                            data-city="{{ $c->city }}"
                            data-state="{{ $c->state }}"
                            data-postal="{{ $c->postal }}"
                            data-email="{{ $c->email }}"
                            data-phone="{{ $c->phone }}"
                        >
                            {{ $c->first_name }} {{ $c->last_name }}@if($c->company_name) - {{ $c->company_name }}@endif
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="project_start_date" class="form-label">Project Start Date <span class="text-danger">*</span></label>
                <input type="date" id="project_start_date" name="project_start_date" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label for="project_end_date" class="form-label">Project End Date <span class="text-danger">*</span></label>
                <input type="date" id="project_end_date" name="project_end_date" class="form-control" required>
            </div>
        </div>
        <div class="contract-border p-4 bg-white">
            <div class="text-center mb-4">
                <h2 class="contract-title">CONSTRUCTION CONTRACT AGREEMENT</h2>
            </div>
            <div class="section mb-3">
                <strong>PARTIES</strong>
                <p class="mt-2">
                    This Construction Contract Agreement (the <b>"Agreement"</b>) is entered into on <span class="contract-blank" id="effective_date">{{ date('F d, Y') }}</span> (the <b>"Effective Date"</b>), by and between
                    <span class="contract-blank" id="contractor_name_display"></span>, with an address of <span class="contract-blank" id="contractor_address_display"></span> (the <b>"Constructor"</b>), and
                    <span class="contract-blank" id="client_name_display"></span>, with an address of <span class="contract-blank" id="client_address_display"></span> (the <b>"Client"</b>), (collectively referred to as the <b>"Parties"</b>).
                </p>
            </div>
            <div class="section mb-3">
                <strong>CONSTRUCTION PROPERTY</strong>
                <p class="mt-2">
                    The Property that is to be constructed is located at the following address:<br>
                    <span class="contract-blank" id="property_address_display"></span>
                </p>
            </div>
            <div class="section mb-3">
                <strong>SCOPE OF WORK</strong>
                <p class="mt-2 mb-1">The Constructor agrees to perform the following work as per the purchase order:</p>
                <ol id="scope-list" class="contract-scope-list mb-2"></ol>
                <div class="row">
                    <div class="col-md-6">
                        <span class="fw-bold">Total Materials Cost:</span> <span class="contract-blank" id="materials_total_display"></span>
                    </div>
                    <div class="col-md-6">
                        <span class="fw-bold">Labor Fee (15%):</span> <span class="contract-blank" id="labor_fee_display"></span>
                    </div>
                </div>
            </div>
            <div class="section mb-3">
                <strong>PROJECT TIMELINE</strong>
                <p class="mt-2">
                    <span class="fw-bold">Start Date:</span> <span class="contract-blank" id="timeline_start_display"></span>
                    <span class="fw-bold ms-3">End Date:</span> <span class="contract-blank" id="timeline_end_display"></span>
                </p>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <span class="fw-bold">Grand Total:</span> <span class="contract-blank" id="grand_total_display"></span>
                </div>
            </div>
            <div class="section mb-3">
                <strong>PAYMENT TERMS</strong>
                <div class="row mb-2">
                    <div class="col-md-6">
                        <label for="payment_method" class="form-label">Payment Method <span class="text-danger">*</span></label>
                        <select name="payment_method" id="payment_method" class="form-control" required>
                            <option value="">Select Method</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="check">Check</option>
                            <option value="cash">Cash</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="payment_plan" class="form-label">Payment Plan <span class="text-danger">*</span></label>
                        <select name="payment_plan" id="payment_plan" class="form-control mb-2">
                            <option value="">Select Plan</option>
                            <option value="30% down, 40% halfway, 30% on completion">30% down, 40% halfway, 30% on completion</option>
                            <option value="50/50">50% down, 50% on completion</option>
                            <option value="Full upon completion">Full upon completion</option>
                            <option value="custom">Other (specify below)</option>
                        </select>
                        <input type="text" name="payment_plan_custom" id="payment_plan_custom" class="form-control mt-1" placeholder="Enter custom payment plan..." style="display:none;">
                    </div>
                </div>
                <p class="mt-2"><span class="contract-blank" id="payment_terms_display"></span></p>
            </div>
            <div class="section mb-3">
                <strong>WARRANTY TERMS</strong>
                <p class="mt-2"><span class="contract-blank" id="warranty_terms_display">Contractor warrants all work for a period of one (1) year from completion against defects in workmanship and materials. Warranty excludes damage due to misuse, neglect, or natural disasters.</span></p>
            </div>
            <div class="section mb-3">
                <strong>CANCELLATION TERMS</strong>
                <p class="mt-2"><span class="contract-blank" id="cancellation_terms_display">Either party may cancel this contract with written notice. If cancelled after materials are ordered, the client agrees to pay for all materials and work completed up to the date of cancellation.</span></p>
            </div>
            <div class="section mb-3">
                <strong>ADDITIONAL TERMS</strong>
                <p class="mt-2"><span class="contract-blank" id="additional_terms_display">All changes to the scope of work must be agreed upon in writing. The contractor will comply with all applicable laws and regulations.</span></p>
            </div>
            <div class="section mb-3">
                <strong>SIGNATURES</strong>
                <div class="row mt-4">
                    <div class="col-md-6 text-center">
                        <p><b>Contractor Signature:</b></p>
                        <canvas id="contractor-signature-pad" width="300" height="100" style="border:1px solid #000;"></canvas>
                        <input type="hidden" name="contractor_signature" id="contractor_signature">
                        <div class="mt-2">
                            <button type="button" class="btn btn-secondary btn-sm" onclick="clearSignature('contractor')">Clear</button>
                        </div>
                        <p class="mt-2"><b>Name:</b> <span class="contract-blank" id="contractor_name_signed_display"></span></p>
                        <p><b>Date:</b> <span class="contract-blank" id="contractor_date_signed_display"></span></p>
                    </div>
                    <div class="col-md-6 text-center">
                        <p><b>Client Signature:</b></p>
                        <canvas id="client-signature-pad" width="300" height="100" style="border:1px solid #000;"></canvas>
                        <input type="hidden" name="client_signature" id="client_signature">
                        <div class="mt-2">
                            <button type="button" class="btn btn-secondary btn-sm" onclick="clearSignature('client')">Clear</button>
                        </div>
                        <p class="mt-2"><b>Name:</b> <span class="contract-blank" id="client_name_signed_display"></span></p>
                        <p><b>Date:</b> <span class="contract-blank" id="client_date_signed_display"></span></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-4 d-flex justify-content-between">
            <button type="submit" class="btn btn-success" id="save-btn" disabled>Save Contract</button>
            <button type="button" class="btn btn-info" id="download-pdf">Download/Print</button>
        </div>
        <!-- Hidden fields for backend submission -->
        <input type="hidden" name="contractor[name]" id="contractor_name" />
        <input type="hidden" name="contractor[street]" id="contractor_street" />
        <input type="hidden" name="contractor[city]" id="contractor_city" />
        <input type="hidden" name="contractor[state]" id="contractor_state" />
        <input type="hidden" name="contractor[postal]" id="contractor_postal" />
        <input type="hidden" name="contractor[email]" id="contractor_email" />
        <input type="hidden" name="contractor[phone]" id="contractor_phone" />
        <input type="hidden" name="client[name]" id="client_name" />
        <input type="hidden" name="client[street]" id="client_street" />
        <input type="hidden" name="client[city]" id="client_city" />
        <input type="hidden" name="client[state]" id="client_state" />
        <input type="hidden" name="client[postal]" id="client_postal" />
        <input type="hidden" name="client[email]" id="client_email" />
        <input type="hidden" name="client[phone]" id="client_phone" />
        <input type="hidden" name="property[street]" id="property_street" />
        <input type="hidden" name="property[city]" id="property_city" />
        <input type="hidden" name="property[state]" id="property_state" />
        <input type="hidden" name="property[postal]" id="property_postal" />
        <input type="hidden" name="contract[scope_of_work]" id="scope_of_work" />
        <input type="hidden" name="contract[scope_description]" id="scope_description" />
        <input type="hidden" name="contract[payment_terms]" id="payment_terms" />
        <input type="hidden" name="contract[warranty_terms]" id="warranty_terms" />
        <input type="hidden" name="contract[cancellation_terms]" id="cancellation_terms" />
        <input type="hidden" name="contract[additional_terms]" id="additional_terms" />
        <input type="hidden" name="materials_total" id="materials_total" />
        <input type="hidden" name="labor_fee" id="labor_fee" />
        <input type="hidden" name="grand_total" id="grand_total" />
    </form>
</div>
@endsection

@push('styles')
<style>
.contract-template {
    max-width: 950px;
    margin: 32px auto 32px auto;
    padding-left: 0;
    padding-right: 0;
}
.contract-border {
    border: 1.5px solid #e0e0e0;
    border-radius: 8px;
    box-shadow: 0 2px 16px rgba(0,0,0,0.06);
    background: #fff;
    padding: 2.5rem 2.5rem 2rem 2.5rem;
    margin-left: 0;
    margin-right: 0;
}
.contract-title {
    font-size: 2.2rem;
    font-weight: bold;
    letter-spacing: 0.04em;
    margin-bottom: 0.5em;
}
.section strong {
    font-size: 1.08rem;
    text-transform: uppercase;
    letter-spacing: 0.01em;
}
.contract-blank {
    display: inline-block;
    min-width: 110px;
    border-bottom: 1.5px solid #222;
    padding: 0 0.4em;
    margin: 0 0.15em;
    font-weight: 500;
    color: #222;
    background: #f8fafc;
}
.contract-scope-list {
    margin-left: 1.2em;
    margin-bottom: 0.5em;
}
.contract-scope-list li {
    border-bottom: 1px dotted #bbb;
    margin-bottom: 0.2em;
    padding-bottom: 0.15em;
    font-size: 1.04em;
}
@media (max-width: 900px) {
    .contract-template { max-width: 100vw; padding-left: 0.5rem; padding-right: 0.5rem; }
    .contract-border { padding: 1.2rem 0.5rem; }
}
@media print {
    .contract-template, .contract-border { box-shadow: none !important; border-color: #000 !important; }
    .btn, #po-search, #po-search-results, #save-btn, #download-pdf { display: none !important; }
    body { background: #fff !important; }
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<script>
let contractorPad, clientPad;
document.addEventListener('DOMContentLoaded', function() {
    // --- Quotation Request Autofill ---
    const qrSelect = document.getElementById('quotation_request_id');
    const saveBtn = document.getElementById('save-btn');
    qrSelect.addEventListener('change', function() {
        const qrId = this.value;
        if (!qrId) return;
        fetch(`/api/quotation-requests/${qrId}`)
            .then(res => res.json())
            .then(data => {
                // Parties
                document.getElementById('client_name_display').innerText = data.client?.name || '';
                document.getElementById('client_address_display').innerText = data.client?.address || '';
                // Property
                document.getElementById('property_address_display').innerText = data.property?.address || '';
                // Scope of Work
                const scopeList = document.getElementById('scope-list');
                scopeList.innerHTML = '';
                let total = 0, labor = 0;
                (data.items || []).forEach((item, idx) => {
                    const qty = parseFloat(item.quantity) || 0;
                    const amt = parseFloat(item.unit_price || item.amount) || 0;
                    const laborCost = 0.15 * amt * qty;
                    total += qty * amt;
                    labor += laborCost;
                    const li = document.createElement('li');
                    li.innerText = `${item.material?.name || ''} - Qty: ${qty}, Unit Price: ₱${amt.toFixed(2)}, Total: ₱${(qty*amt).toFixed(2)}`;
                    scopeList.appendChild(li);
                });
                document.getElementById('materials_total_display').innerText = '₱' + total.toFixed(2);
                document.getElementById('labor_fee_display').innerText = '₱' + labor.toFixed(2);
                document.getElementById('grand_total_display').innerText = '₱' + (total + labor).toFixed(2);
                document.getElementById('grand_total').value = (total + labor).toFixed(2);
                // Terms
                document.getElementById('payment_terms_display').innerText = data.payment_terms || '';
                document.getElementById('warranty_terms_display').innerText = data.warranty_terms || '';
                document.getElementById('cancellation_terms_display').innerText = data.cancellation_terms || '';
                document.getElementById('additional_terms_display').innerText = data.additional_terms || '';
                // Signatures (clear for new contract)
                document.getElementById('contractor_name_signed_display').innerText = '';
                document.getElementById('client_name_signed_display').innerText = data.client?.name || '';
                document.getElementById('contractor_date_signed_display').innerText = '';
                document.getElementById('client_date_signed_display').innerText = '';
                // Hidden fields for backend
                document.getElementById('client_name').value = data.client?.name || '';
                document.getElementById('client_street').value = data.client?.street || '';
                document.getElementById('client_city').value = data.client?.city || '';
                document.getElementById('client_state').value = data.client?.state || '';
                document.getElementById('client_postal').value = data.client?.postal || '';
                document.getElementById('client_email').value = data.client?.email || '';
                document.getElementById('client_phone').value = data.client?.phone || '';
                document.getElementById('property_street').value = data.property?.street || '';
                document.getElementById('property_city').value = data.property?.city || '';
                document.getElementById('property_state').value = data.property?.state || '';
                document.getElementById('property_postal').value = data.property?.postal || '';
                document.getElementById('scope_of_work').value = data.scope_of_work || '';
                document.getElementById('scope_description').value = data.scope_description || '';
                document.getElementById('payment_terms').value = data.payment_terms || '';
                document.getElementById('warranty_terms').value = data.warranty_terms || '';
                document.getElementById('cancellation_terms').value = data.cancellation_terms || '';
                document.getElementById('additional_terms').value = data.additional_terms || '';
                document.getElementById('materials_total').value = total.toFixed(2);
                document.getElementById('labor_fee').value = labor.toFixed(2);
                // Clear timeline fields
                document.getElementById('project_start_date').value = '';
                document.getElementById('project_end_date').value = '';
                document.getElementById('timeline_start_display').innerText = '';
                document.getElementById('timeline_end_display').innerText = '';
                saveBtn.disabled = false;
            });
    });
    // --- End Quotation Request Autofill ---

    // Project Timeline logic
    function updateTimeline() {
      const start = document.getElementById('project_start_date').value;
      const end = document.getElementById('project_end_date').value;
      document.getElementById('timeline_start_display').innerText = start;
      document.getElementById('timeline_end_display').innerText = end;
      // Only enable save if both dates are filled
      const saveBtn = document.getElementById('save-btn');
      if (start && end) {
        saveBtn.disabled = false;
      } else {
        saveBtn.disabled = true;
      }
    }
    document.getElementById('project_start_date').addEventListener('change', updateTimeline);
    document.getElementById('project_end_date').addEventListener('change', updateTimeline);

    // Constructor autofill logic
    const constructorSelect = document.getElementById('constructor_id');
    constructorSelect.addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        if (!selected || !selected.value) return;
        // Compose name
        const name = (selected.dataset.first_name || '') + ' ' + (selected.dataset.last_name || '');
        const company = selected.dataset.company_name || '';
        const fullName = company ? (name + ' - ' + company) : name;
        // Address
        const address = [selected.dataset.street, selected.dataset.barangay, selected.dataset.city, selected.dataset.state, selected.dataset.postal].filter(Boolean).join(', ');
        // Fill contract blanks
        document.getElementById('contractor_name_display').innerText = fullName;
        document.getElementById('contractor_address_display').innerText = address;
        document.getElementById('contractor_name_signed_display').innerText = fullName;
        // Fill hidden fields
        document.getElementById('contractor_name').value = fullName;
        document.getElementById('contractor_street').value = selected.dataset.street || '';
        document.getElementById('contractor_city').value = selected.dataset.city || '';
        document.getElementById('contractor_state').value = selected.dataset.state || '';
        document.getElementById('contractor_postal').value = selected.dataset.postal || '';
        document.getElementById('contractor_email').value = selected.dataset.email || '';
        document.getElementById('contractor_phone').value = selected.dataset.phone || '';
    });

    // Signature pad logic
    contractorPad = new SignaturePad(document.getElementById('contractor-signature-pad'));
    clientPad = new SignaturePad(document.getElementById('client-signature-pad'));
    document.getElementById('contractEditorForm').addEventListener('submit', function(e) {
        document.getElementById('contractor_signature').value = contractorPad.isEmpty() ? '' : contractorPad.toDataURL();
        document.getElementById('client_signature').value = clientPad.isEmpty() ? '' : clientPad.toDataURL();
    });
    document.getElementById('download-pdf').addEventListener('click', function() {
        window.print();
    });

    // Payment plan custom input toggle
    const planSelect = document.getElementById('payment_plan');
    const planCustom = document.getElementById('payment_plan_custom');
    planSelect.addEventListener('change', function() {
        if (this.value === 'custom') {
            planCustom.style.display = '';
            planCustom.required = true;
        } else {
            planCustom.style.display = 'none';
            planCustom.required = false;
        }
    });
});
function clearSignature(type) {
    if(type === 'contractor') contractorPad.clear();
    else clientPad.clear();
}
</script>
@endpush 