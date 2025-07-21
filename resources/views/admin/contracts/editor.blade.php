@extends('layouts.app')

@section('content')
@php
    $isClient = auth()->check() && auth()->user()->user_type === 'company' && auth()->user()->company && auth()->user()->company->designation === 'client';
@endphp
<div class="container contract-template {{ $isClient ? 'client-user' : '' }}" id="contract-editor-root">
    <form id="contractEditorForm" method="POST" action="{{ isset($contract) ? route('contracts.update', $contract->id) : route('contracts.store') }}">
        @csrf
        @if(isset($contract))
            @method('PUT')
        @endif
        <div class="mb-4 row align-items-end justify-content-between">
            <div class="col-md-6">
                <label for="quotation_request_id" class="form-label"><strong>Client Quotation Request</strong> <span class="text-danger">*</span></label>
                <select name="quotation_request_id" id="quotation_request_id" class="form-control" required>
                    <option value="">Select Quotation Request</option>
                    @foreach($quotationRequests as $qr)
                        <option value="{{ $qr->id }}" data-client="{{ $qr->user->name }}" data-request-number="{{ $qr->request_number }}">QR-{{ $qr->request_number }} - {{ $qr->user->name }}</option>
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
                    , with an address of <span class="contract-blank" id="contractor_address_display"></span> (the <b>"Constructor"</b>), and
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
                <p class="mt-2">
                    The Constructor agrees to perform the following work as per the purchase order:
                </p>
                <div class="row mb-2">
                    <div class="col-md-6">
                        <label class="form-label">Total Materials Cost:</label>
                        <input type="text" class="form-control" id="materials_total_display" readonly>
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
@vite(['resources/css/admin-contracts-editor.css'])
@endpush

@push('scripts')
<script>
    window.quotationRequestApiUrl = '{{ url('api/quotation-requests') }}';
</script>
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
@vite(['resources/js/admin-contracts-editor.js'])
@endpush 