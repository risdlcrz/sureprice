@extends('layouts.app')

@section('content')
@php
    $isClient = auth()->check() && auth()->user()->user_type === 'company' && auth()->user()->company && auth()->user()->company->designation === 'client';
@endphp
<div class="container contract-template {{ $isClient ? 'client-user' : '' }}" id="contract-editor-root">
    <form id="contractEditorForm" method="POST" action="{{ isset($contract) ? route('contracts.update', $contract->id) : route('contracts.store') }}">
        @csrf
        <input type="hidden" name="contractor[name]" id="contractor_name" />
        <input type="hidden" name="contractor[email]" id="contractor_email" />
        <!-- Contractor Address Fields -->
        <input type="hidden" name="contractor[street]" id="contractor_street" />
        <input type="hidden" name="contractor[barangay]" id="contractor_barangay" />
        <input type="hidden" name="contractor[city]" id="contractor_city" />
        <input type="hidden" name="contractor[state]" id="contractor_state" />
        <input type="hidden" name="contractor[postal]" id="contractor_postal" />
        <!-- Removed contractor phone field -->
        <input type="hidden" name="client[email]" id="client_email" />
        <!-- Client Address Fields -->
        <input type="hidden" name="client[street]" id="client_street" />
        <input type="hidden" name="client[barangay]" id="client_barangay" />
        <input type="hidden" name="client[city]" id="client_city" />
        <input type="hidden" name="client[state]" id="client_state" />
        <input type="hidden" name="client[postal]" id="client_postal" />
        <!-- Removed client phone field -->
        @if(isset($contract))
            @method('PUT')
        @endif
        <!-- Hidden fields for backend validation (NO DUPLICATES, ONLY THIS SET) -->
        <input type="hidden" name="client[name]" id="client_name" />
        <input type="hidden" name="property[street]" id="property_street" />
        <input type="hidden" name="property[city]" id="property_city" />
        <input type="hidden" name="property[state]" id="property_state" />
        <input type="hidden" name="property[postal]" id="property_postal" />
        <input type="hidden" name="property[barangay]" id="property_barangay" />
        <input type="hidden" name="contract[scope_of_work]" id="contract_scope_of_work" />
        <input type="hidden" name="contract[scope_description]" id="contract_scope_description" />
        <input type="hidden" name="contract[payment_terms]" id="contract_payment_terms" />
        <input type="hidden" name="contract[warranty_terms]" id="contract_warranty_terms" />
        <input type="hidden" name="contract[cancellation_terms]" id="contract_cancellation_terms" />
        <input type="hidden" name="contract[additional_terms]" id="contract_additional_terms" />
        <input type="hidden" name="materials_total" id="materials_total" />
        <input type="hidden" name="labor_fee" id="labor_fee" />
        <input type="hidden" name="grand_total" id="grand_total" />
        <div class="mb-4 row align-items-end justify-content-between">
            <div class="col-md-6">
                <label for="quotation_request_id" class="form-label"><strong>Client Quotation Request</strong> <span class="text-danger">*</span></label>
                <select name="quotation_request_id" id="quotation_request_id" class="form-control" required>
                    <option value="">Select Quotation Request</option>
                    @foreach($quotationRequests as $qr)
                        <option value="{{ $qr->id }}" data-client="{{ $qr->user->name }}" data-request-number="{{ $qr->request_number }}" data-payment-plan="{{ $qr->payment_plan }}">QR-{{ $qr->request_number }} - {{ $qr->user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 d-flex justify-content-end gap-2">
                <button type="submit" class="btn btn-success" id="save-btn" disabled>Save Contract</button>
                <button type="button" class="btn btn-info" id="download-pdf">Download/Print</button>
            </div>
        </div>
        <!-- Remove the old input row for constructor and dates -->
        <!-- Inline contract document starts here -->
        <div class="contract-border p-4 bg-white" style="margin-top: 1.5rem;">
            <div class="text-center mb-4">
                <h2 class="contract-title mb-3">CONSTRUCTION CONTRACT AGREEMENT</h2>
            </div>
            <div class="section mb-3">
                <strong>PARTIES</strong>
                <p class="mt-2">
                    This Construction Contract Agreement (the <b>"Agreement"</b>) is entered into on 
                    <input type="date" id="effective_date" name="effective_date" class="contract-inline-input" value="{{ date('Y-m-d') }}" required>
                    (the <b>"Effective Date"</b>), by and between
                    <select name="constructor_id" id="constructor_id" class="contract-inline-input" required>
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
                    , with an address of <span class="contract-blank" id="contractor_address_display"></span> (the <b>"Constructor"</b>), <span class="contract-blank" id="contractor_email_display"></span>, and
                    <span class="contract-blank" id="client_name_display"></span>, with an address of <span class="contract-blank" id="client_address_display"></span> (the <b>"Client"</b>), (collectively referred to as the <b>"Parties"</b>).
                </p>
            </div>
            <div class="section mb-3">
                <strong>CONSTRUCTION PROPERTY</strong>
                <p class="mt-2">
                    The Property that is to be constructed is located at the following address:<br>
                    <input type="text" name="property_address" id="property_address" class="form-control mb-2" value="{{ old('property_address', $contract->property_address ?? '') }}" placeholder="Enter property address...">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="same_as_client_address" id="same_as_client_address" value="1" {{ old('same_as_client_address', $contract->same_as_client_address ?? false) ? 'checked' : '' }}>
                        <label class="form-check-label" for="same_as_client_address">
                            Same as client address
                        </label>
                    </div>
                    <span class="contract-blank" id="property_address_display"></span>
                </p>
            </div>
            <div class="section mb-3">
                <strong>SCOPE OF WORK</strong>
                <div class="mb-3" id="selected-scopes-section">
                    <label class="form-label fw-bold" style="font-size:1.1rem;">Selected Scope(s) of Work</label>
                    <div id="selected-scopes" class="selected-scopes-list">
                        <span class="text-muted">No scope selected yet.</span>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold" style="font-size:1.1rem;">Scope Summary</label>
                    <div class="table-responsive">
                        <table class="table table-bordered" id="scope-summary-table">
                            <thead>
                                <tr>
                                    <th>Room</th>
                                    <th>Scope</th>
                                    <th>Material</th>
                                    <th>Quantity</th>
                                    <th>Unit</th>
                                    <th>Chosen Supplier</th>
                                    <th>Unit Price</th>
                                    <th>Total Price</th>
                                </tr>
                            </thead>
                            <tbody id="scope-summary-table-body">
                                <tr><td colspan="8" class="text-center text-muted">No data yet.</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <p class="mt-2">
                    The Constructor agrees to perform the following work as per the purchase order:
                </p>
                <div class="row mb-2">
                    <div class="col-md-6">
                        <label class="form-label">Total Materials Cost:</label>
                        <input type="text" class="form-control" id="materials_total_display" readonly>
                        <div id="discount-summary" class="mt-2"></div>
                        <style>
                        #discount-summary {
                            font-size: 1rem;
                        }
                        #discount-summary .discount-original {
                            text-decoration: line-through;
                            color: #888;
                        }
                        #discount-summary .discount-label {
                            font-weight: bold;
                            margin-top: 0.5em;
                            display: block;
                        }
                        #discount-summary .discount-value {
                            font-size: 1.1em;
                            color: #1a7f37;
                            font-weight: bold;
                        }
                        #discount-summary .discount-type {
                            color: #0d6efd;
                            font-weight: 500;
                        }
                        </style>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Labor Fee:</label>
                        <input type="text" class="form-control" id="labor_fee_display" readonly>
                    </div>
                </div>
            </div>
            <div class="section mb-3">
                <strong>PROJECT TIMELINE</strong>
                <p class="mt-2">
                    <span class="fw-bold">Start Date:</span> <input type="date" id="project_start_date" name="project_start_date" class="contract-inline-input" required>
                    <span class="fw-bold ms-3">End Date:</span> <input type="date" id="project_end_date" name="project_end_date" class="contract-inline-input" required>
                    <!-- Removed Estimated Days display -->
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
                        <select name="payment_plan" id="payment_plan" class="form-control mb-2" required>
                            <option value="">Select Plan</option>
                            <option value="30% down, 40% halfway, 30% on completion">30% down, 40% halfway, 30% on completion</option>
                            <option value="50/50">50% down, 50% on completion</option>
                            <option value="Full upon completion">Full upon completion</option>
                            <option value="milestone">Milestone-based (20% down, 20% after foundation, 30% after structure, 30% on completion)</option>
                            <option value="monthly3">Monthly for 3 months (equal payments)</option>
                            <option value="monthly6">Monthly for 6 months (equal payments)</option>
                            <option value="monthly12">Monthly for 12 months (equal payments)</option>
                        </select>
                        <input type="text" name="payment_plan_custom" id="payment_plan_custom" class="form-control mt-1" placeholder="Enter custom payment plan..." style="display:none;">
                    </div>
                </div>
                <div id="payment-breakdown" class="mt-3"></div>
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
            <div class="section mb-3 signature-section">
                <strong>SIGNATURES</strong>
                <div class="row mt-4">
                    <div class="col-md-6 text-center contractor-signature-section">
                        <p><b>Contractor Signature:</b></p>
                        <canvas id="contractor-signature-pad" width="300" height="100" class="signature-pad"></canvas>
                        <input type="hidden" name="contractor_signature" id="contractor_signature">
                        <input type="hidden" name="contractor_date_signed" id="contractor_date_signed">
                        <div class="mt-2">
                            <button type="button" class="btn btn-secondary btn-sm" onclick="clearSignature('contractor')">Clear</button>
                        </div>
                        <p class="mt-2"><b>Name:</b> <span class="contract-blank" id="contractor_name_signed_display"></span></p>
                        <p><b>Date:</b> <span class="contract-blank" id="contractor_date_signed_display"></span></p>
                    </div>
                    <div class="col-md-6 text-center client-signature-section">
                        <p><b>Client Signature:</b></p>
                        <canvas id="client-signature-pad" width="300" height="100" class="signature-pad"></canvas>
                        <input type="hidden" name="client_signature" id="client_signature">
                        <input type="hidden" name="client_date_signed" id="client_date_signed">
                        <div class="mt-2">
                            <button type="button" class="btn btn-secondary btn-sm" onclick="clearSignature('client')">Clear</button>
                        </div>
                        <p class="mt-2"><b>Name:</b> <span class="contract-blank" id="client_name_signed_display"></span></p>
                        <p><b>Date:</b> <span class="contract-blank" id="client_date_signed_display"></span></p>
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-12 text-end">
                    <button type="button" class="btn btn-primary" id="submit-approval-btn" style="display:none;">Submit for Approval</button>
                </div>
            </div>
        </div>
        
  
</div>
@endsection

@push('styles')
@vite(['resources/css/admin-contracts-editor.css'])
@endpush

@push('scripts')
<script>
    window.quotationRequestApiUrl = '{{ url('api/quotation-requests') }}';
</script>
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // When the form is submitted, set contractor_date_signed to effective_date
    const form = document.getElementById('contractEditorForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const effectiveDate = document.getElementById('effective_date').value;
            document.getElementById('contractor_date_signed').value = effectiveDate;
        });
    }
    // Custom plan logic
    const planSelect = document.getElementById('payment_plan');
    const customPlan = document.getElementById('payment_plan_custom');
    planSelect.addEventListener('change', function() {
        if (planSelect.value === '' && customPlan) {
            customPlan.style.display = 'block';
        } else {
            customPlan.style.display = 'none';
        }
    });
    // On submit, if custom plan is filled, copy to select
    const form = document.getElementById('contractEditorForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            if (planSelect.value === '' && customPlan && customPlan.value) {
                planSelect.value = customPlan.value;
            }
        });
    }
    // If a quotation request is selected and has a payment plan, pre-fill
    const qrSelect = document.getElementById('quotation_request_id');
    if (qrSelect) {
        qrSelect.addEventListener('change', function() {
            const selected = qrSelect.options[qrSelect.selectedIndex];
            const paymentPlan = selected.getAttribute('data-payment-plan');
            if (paymentPlan) {
                planSelect.value = paymentPlan;
                customPlan.style.display = 'none';
            }
        });
    }
});
</script>
@vite(['resources/js/admin-contracts-editor.js'])
@endpush 