@extends('layouts.app')

@section('content')
@php
    $isClient = auth()->check() && auth()->user()->user_type === 'company' && auth()->user()->company && auth()->user()->company->designation === 'client';
    $contractNumber = $contract->contract_number ?? ($nextContractNumber ?? null);
@endphp
<div class="container contract-template contract-editor {{ $isClient ? 'client-user' : '' }}" id="contract-editor-root">
    <form id="contractEditorForm" method="POST" action="{{ isset($contract) ? route('contracts.update', $contract->id) : route('contracts.store') }}">
        @csrf
        @if(isset($contract))
            @method('PUT')
        @endif
        {{-- Hidden fields for backend validation; filled by resources/js/admin-contracts-editor.js --}}
        <input type="hidden" name="contractor[name]" id="contractor_name" />
        <input type="hidden" name="contractor[email]" id="contractor_email" />
        <input type="hidden" name="contractor[street]" id="contractor_street" />
        <input type="hidden" name="contractor[barangay]" id="contractor_barangay" />
        <input type="hidden" name="contractor[city]" id="contractor_city" />
        <input type="hidden" name="contractor[state]" id="contractor_state" />
        <input type="hidden" name="contractor[postal]" id="contractor_postal" />
        <input type="hidden" name="client[email]" id="client_email" />
        <input type="hidden" name="client[street]" id="client_street" />
        <input type="hidden" name="client[barangay]" id="client_barangay" />
        <input type="hidden" name="client[city]" id="client_city" />
        <input type="hidden" name="client[state]" id="client_state" />
        <input type="hidden" name="client[postal]" id="client_postal" />
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

        {{-- Toolbar: source quotation request + actions --}}
        <div class="ce-toolbar">
            <div class="ce-toolbar-source">
                <label for="quotation_request_id" class="form-label">Client Quotation Request <span class="text-danger">*</span></label>
                <select name="quotation_request_id" id="quotation_request_id" class="form-select" required>
                    <option value="">Select Quotation Request</option>
                    @foreach($quotationRequests as $qr)
                        <option value="{{ $qr->id }}" data-client="{{ $qr->user->name }}" data-request-number="{{ $qr->request_number }}" data-payment-plan="{{ $qr->payment_plan }}">QR-{{ $qr->request_number }} - {{ $qr->user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="ce-toolbar-actions">
                <button type="button" class="btn btn-outline-secondary" id="download-pdf"><i class="fas fa-print me-1"></i> Print Preview</button>
                <button type="submit" class="btn btn-success" id="save-btn" disabled><i class="fas fa-save me-1"></i> Save Contract</button>
            </div>
        </div>

        {{-- Contract document --}}
        <div class="contract-border ce-card">
            <div class="ce-meta">
                <div class="ce-meta-number">
                    <span class="ce-meta-label">Contract #</span>
                    <span class="ce-number-badge">{{ $contractNumber ?? '—' }}</span>
                    @unless(isset($contract))
                        <span class="ce-meta-hint">Assigned when saved</span>
                    @endunless
                </div>
                <div class="ce-meta-date">
                    <label for="effective_date" class="ce-meta-label">Effective Date</label>
                    <input type="date" id="effective_date" name="effective_date" class="form-control ce-date" value="{{ date('Y-m-d') }}" required>
                </div>
            </div>

            <header class="ce-header">
                <h2 class="contract-title">Construction Contract Agreement</h2>
                <p class="ce-subtitle">This Agreement takes effect on the Effective Date above and binds the Constructor and the Client (together, the "Parties").</p>
            </header>

            {{-- Parties --}}
            <section class="section ce-section">
                <h3 class="ce-section-title"><span class="ce-step">1</span> Parties</h3>
                <div class="ce-parties">
                    <div class="ce-party">
                        <div class="ce-party-head">
                            <span class="ce-party-icon"><i class="fas fa-hard-hat"></i></span>
                            <div>
                                <div class="ce-party-role">Constructor</div>
                                <div class="ce-party-note">Performs the work</div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="constructor_id" class="form-label">Constructor <span class="text-danger">*</span></label>
                            <select name="constructor_id" id="constructor_id" class="form-select" required>
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
                        <div class="mb-3">
                            <label for="contractor_address_display" class="form-label">Address</label>
                            <input type="text" id="contractor_address_display" class="form-control" readonly placeholder="Filled in from the selected constructor">
                        </div>
                        <div>
                            <label for="contractor_contact_display" class="form-label">Contact</label>
                            <input type="text" id="contractor_contact_display" class="form-control" readonly placeholder="Email and phone">
                        </div>
                    </div>

                    <div class="ce-party">
                        <div class="ce-party-head">
                            <span class="ce-party-icon"><i class="fas fa-user-tie"></i></span>
                            <div>
                                <div class="ce-party-role">Client</div>
                                <div class="ce-party-note">Owner of the project</div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="client_name" class="form-label">Client Name <span class="text-danger">*</span></label>
                            <input type="text" name="client[name]" id="client_name" class="form-control" placeholder="Filled in from the quotation request">
                        </div>
                        <div>
                            <label for="client_address_display" class="form-label">Client Address</label>
                            <input type="text" id="client_address_display" class="form-control" readonly placeholder="Filled in from the quotation request">
                        </div>
                    </div>
                </div>
            </section>

            {{-- Construction property --}}
            <section class="section ce-section">
                <h3 class="ce-section-title"><span class="ce-step">2</span> Construction Property</h3>
                <div class="ce-inset">
                    <label for="property_address" class="form-label">Property Address</label>
                    <input type="text" name="property_address" id="property_address" class="form-control" value="{{ old('property_address', $contract->property_address ?? '') }}" placeholder="Where the construction will take place">
                    <div class="form-check form-switch mt-3 mb-0">
                        <input class="form-check-input" type="checkbox" role="switch" name="same_as_client_address" id="same_as_client_address" value="1" {{ old('same_as_client_address', $contract->same_as_client_address ?? false) ? 'checked' : '' }}>
                        <label class="form-check-label" for="same_as_client_address">Same as client address</label>
                    </div>
                </div>
            </section>

            {{-- Scope of work --}}
            <section class="section ce-section">
                <h3 class="ce-section-title"><span class="ce-step">3</span> Scope of Work</h3>
                <p class="ce-lead">The Constructor agrees to perform the following work as per the purchase order.</p>

                <div class="mb-4" id="selected-scopes-section">
                    <div class="ce-subheading">Selected Scope(s) of Work</div>
                    <div id="selected-scopes" class="selected-scopes-list">
                        <span class="text-muted">No scope selected yet.</span>
                    </div>
                </div>

                <div class="ce-subheading">Scope Summary</div>
                <div class="table-responsive ce-table-wrap">
                    <table class="table ce-table mb-0" id="scope-summary-table">
                        <thead>
                            <tr>
                                <th>Room</th>
                                <th>Scope</th>
                                <th>Material</th>
                                <th class="text-end">Quantity</th>
                                <th>Unit</th>
                                <th>Chosen Supplier</th>
                                <th class="text-end">Unit Price</th>
                                <th class="text-end">Total Price</th>
                            </tr>
                        </thead>
                        <tbody id="scope-summary-table-body">
                            <tr class="ce-empty">
                                <td colspan="8">
                                    <i class="fas fa-clipboard-list"></i>
                                    <div class="ce-empty-title">No scope selected yet</div>
                                    <div class="ce-empty-text">Choose a client quotation request above to load its rooms, materials and pricing.</div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="ce-costs">
                    <div>
                        <label for="materials_total_display" class="form-label">Total Materials Cost</label>
                        <input type="text" class="form-control ce-num" id="materials_total_display" readonly>
                        <div id="discount-summary" class="mt-2"></div>
                    </div>
                    <div>
                        <label for="labor_fee_display" class="form-label">Labor Fee</label>
                        <input type="text" class="form-control ce-num" id="labor_fee_display" readonly>
                    </div>
                </div>
            </section>

            <section class="section ce-section">
                <h3 class="ce-section-title"><span class="ce-step">4</span> Project Timeline</h3>
                <div class="ce-costs">
                    <div>
                        <label for="project_start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                        <input type="date" id="project_start_date" name="project_start_date" class="form-control" required>
                    </div>
                    <div>
                        <label for="project_end_date" class="form-label">End Date <span class="text-danger">*</span></label>
                        <input type="date" id="project_end_date" name="project_end_date" class="form-control" required>
                    </div>
                </div>
                <div class="ce-grand-total">
                    <span>Grand Total</span>
                    <span class="ce-num" id="grand_total_display">—</span>
                </div>
            </section>

            <section class="section ce-section">
                <h3 class="ce-section-title"><span class="ce-step">5</span> Payment Terms</h3>
                <div class="ce-costs">
                    <div>
                        <label for="payment_method" class="form-label">Payment Method <span class="text-danger">*</span></label>
                        <select name="payment_method" id="payment_method" class="form-select" required>
                            <option value="">Select Method</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="check">Check</option>
                            <option value="cash">Cash</option>
                        </select>
                    </div>
                    <div>
                        <label for="payment_plan" class="form-label">Payment Plan <span class="text-danger">*</span></label>
                        <select name="payment_plan" id="payment_plan" class="form-select" required>
                            <option value="">Select Plan</option>
                            <option value="30% down, 40% halfway, 30% on completion">30% down, 40% halfway, 30% on completion</option>
                            <option value="50/50">50% down, 50% on completion</option>
                            <option value="Full upon completion">Full upon completion</option>
                            <option value="milestone">Milestone-based (20% down, 20% after foundation, 30% after structure, 30% on completion)</option>
                            <option value="monthly3">Monthly for 3 months (equal payments)</option>
                            <option value="monthly6">Monthly for 6 months (equal payments)</option>
                            <option value="monthly12">Monthly for 12 months (equal payments)</option>
                        </select>
                        <input type="text" name="payment_plan_custom" id="payment_plan_custom" class="form-control mt-2" placeholder="Enter custom payment plan..." style="display:none;">
                    </div>
                </div>
                <div id="payment-breakdown" class="mt-3"></div>
                <p class="mt-2"><span id="payment_terms_display"></span></p>
            </section>

            <section class="section ce-section">
                <h3 class="ce-section-title"><span class="ce-step">6</span> Warranty Terms</h3>
                <p class="ce-terms" id="warranty_terms_display">Contractor warrants all work for a period of one (1) year from completion against defects in workmanship and materials. Warranty excludes damage due to misuse, neglect, or natural disasters.</p>
            </section>

            <section class="section ce-section">
                <h3 class="ce-section-title"><span class="ce-step">7</span> Cancellation Terms</h3>
                <p class="ce-terms" id="cancellation_terms_display">Either party may cancel this contract with written notice. If cancelled after materials are ordered, the client agrees to pay for all materials and work completed up to the date of cancellation.</p>
            </section>

            <section class="section ce-section">
                <h3 class="ce-section-title"><span class="ce-step">8</span> Additional Terms</h3>
                <p class="ce-terms" id="additional_terms_display">All changes to the scope of work must be agreed upon in writing. The contractor will comply with all applicable laws and regulations.</p>
            </section>

            <section class="section ce-section signature-section">
                <h3 class="ce-section-title"><span class="ce-step">9</span> Signatures</h3>
                <div class="ce-parties">
                    <div class="ce-signature contractor-signature-section">
                        <div class="ce-subheading">Contractor Signature</div>
                        <canvas id="contractor-signature-pad" width="300" height="100" class="signature-pad"></canvas>
                        <input type="hidden" name="contractor_signature" id="contractor_signature">
                        <input type="hidden" name="contractor_date_signed" id="contractor_date_signed">
                        <div class="mt-2">
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="clearSignature('contractor')">Clear</button>
                        </div>
                        <p class="mt-2 mb-1"><b>Name:</b> <span id="contractor_name_signed_display"></span></p>
                        <p class="mb-0"><b>Date:</b> <span id="contractor_date_signed_display"></span></p>
                    </div>
                    <div class="ce-signature client-signature-section">
                        <div class="ce-subheading">Client Signature</div>
                        <canvas id="client-signature-pad" width="300" height="100" class="signature-pad"></canvas>
                        <input type="hidden" name="client_signature" id="client_signature">
                        <input type="hidden" name="client_date_signed" id="client_date_signed">
                        <div class="mt-2">
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="clearSignature('client')">Clear</button>
                        </div>
                        <p class="mt-2 mb-1"><b>Name:</b> <span id="client_name_signed_display"></span></p>
                        <p class="mb-0"><b>Date:</b> <span id="client_date_signed_display"></span></p>
                    </div>
                </div>
            </section>

            <div class="text-end mt-4">
                <button type="button" class="btn btn-primary" id="submit-approval-btn" style="display:none;">Submit for Approval</button>
            </div>
        </div>
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
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('contractEditorForm');
    const planSelect = document.getElementById('payment_plan');
    const customPlan = document.getElementById('payment_plan_custom');

    // Custom plan: show the free-text field when "custom" (empty value) is chosen
    planSelect?.addEventListener('change', function() {
        if (customPlan) customPlan.style.display = planSelect.value === '' ? 'block' : 'none';
    });

    form?.addEventListener('submit', function() {
        // contractor_date_signed follows the effective date
        const effective = document.getElementById('effective_date');
        const signed = document.getElementById('contractor_date_signed');
        if (effective && signed) signed.value = effective.value;
        // a filled custom plan replaces the empty select value
        if (planSelect && planSelect.value === '' && customPlan?.value) planSelect.value = customPlan.value;
    });
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
