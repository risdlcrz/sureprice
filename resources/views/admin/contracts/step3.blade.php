@extends('layouts.app')

@push('styles')
<style>
    .content-wrapper {
        margin-left: 0;
        padding: 20px;
        min-height: 100vh;
        background-color: #f8f9fa;
    }

    .section-container {
        margin-bottom: 2rem;
        padding: 1.25rem;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        background-color: #fff;
    }

    .section-title {
        margin-bottom: 1.25rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #0d6efd;
        color: #344767;
        font-weight: 600;
        font-size: 1.1rem;
    }

    .form-group {
        margin-bottom: 1rem;
    }

    .form-label {
        font-weight: 500;
        margin-bottom: 0.5rem;
        color: #344767;
    }

    .progress {
        height: 0.5rem;
        margin-bottom: 2rem;
    }

    .step-indicator {
        display: flex;
        justify-content: space-between;
        margin-bottom: 2rem;
    }

    .step {
        text-align: center;
        flex: 1;
        position: relative;
    }

    .step:not(:last-child):after {
        content: '';
        position: absolute;
        top: 50%;
        right: 0;
        width: 100%;
        height: 2px;
        background: #dee2e6;
        z-index: 1;
    }

    .step.active:not(:last-child):after {
        background: #0d6efd;
    }

    .step.completed:not(:last-child):after {
        background: #198754;
    }

    .step-number {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: #dee2e6;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 0.5rem;
        position: relative;
        z-index: 2;
    }

    .step.active .step-number {
        background: #0d6efd;
    }

    .step.completed .step-number {
        background: #198754;
    }

    .step-label {
        font-size: 0.875rem;
        color: #6c757d;
    }

    .step.active .step-label {
        color: #0d6efd;
        font-weight: 600;
    }

    .step.completed .step-label {
        color: #198754;
        font-weight: 600;
    }

    .signature-pad {
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        margin-bottom: 1rem;
    }

    .signature-pad canvas {
        width: 100%;
        height: 200px;
    }
</style>
@endpush

@section('content')
<div class="content-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header bg-white py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Create New Contract - Step 3</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Progress Steps -->
                        <div class="step-indicator">
                            <div class="step completed">
                                <div class="step-number">1</div>
                                <div class="step-label">Basic Information</div>
                            </div>
                            <div class="step completed">
                                <div class="step-number">2</div>
                                <div class="step-label">Scope & Materials</div>
                            </div>
                            <div class="step active">
                                <div class="step-number">3</div>
                                <div class="step-label">Terms & Conditions</div>
                            </div>
                            <div class="step">
                                <div class="step-number">4</div>
                                <div class="step-label">Payment & Review</div>
                            </div>
                        </div>

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div id="formErrorMessage" class="alert alert-danger d-none"></div>

                        <form method="POST" action="{{ route('contracts.store.step3') }}" id="step3Form">
                            @csrf
                            
                            <!-- Hidden fields for signatures -->
                            <input type="hidden" name="contractor_signature" id="contractor_signature">
                            <input type="hidden" name="client_signature" id="client_signature">

                            <!-- Contract Clauses Section -->
                            <div class="section-container" id="clausesSection">
                                <h5 class="section-title">Contract Clauses</h5>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="payment_terms">Payment Terms</label>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="radio" name="payment_type" id="payAllIn" value="pay_all_in" checked>
                                                <label class="form-check-label" for="payAllIn">Pay All In</label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="radio" name="payment_type" id="installmentPlan" value="installment">
                                                <label class="form-check-label" for="installmentPlan">Installment Plan</label>
                                            </div>
                                            <div id="installmentOptions" style="display: none;">
                                                <div class="mb-2">
                                                    <label for="downpayment">Downpayment:</label>
                                                    <select class="form-control" id="downpayment" name="downpayment">
                                                        <option value="10">10%</option>
                                                        <option value="20">20%</option>
                                                        <option value="30">30%</option>
                                                        <option value="40">40%</option>
                                                        <option value="50">50%</option>
                                                    </select>
                                                </div>
                                                <div class="mb-2">
                                                    <label for="installmentPeriod">Installment Period (months):</label>
                                                    <select class="form-control" id="installmentPeriod" name="installmentPeriod">
                                                        <option value="3">3 months</option>
                                                        <option value="6">6 months</option>
                                                        <option value="12">12 months</option>
                                                        <option value="24">24 months</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="radio" name="payment_type" id="progressPayment" value="progress_payment">
                                                <label class="form-check-label" for="progressPayment">Progress Payment</label>
                                            </div>
                                            <div id="progressPaymentOptions" style="display: none;">
                                                <div class="mb-2">
                                                    <label for="progressPaymentType">Payment Schedule:</label>
                                                    <select class="form-control" id="progressPaymentType" name="progressPaymentType">
                                                        <option value="70_completion">Payment after 70% completion</option>
                                                        <option value="completion">Payment after completion</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <input type="hidden" id="payment_terms" name="payment_terms" value="{{ old('payment_terms', session('contract_step3.payment_terms', 'Pay All In')) }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="warranty_terms">Warranty Terms</label>
                                            <textarea class="form-control" id="warranty_terms" name="warranty_terms" rows="4" required>{{ old('warranty_terms', session('contract_step3.warranty_terms', "1. Workmanship warranty for 1 year from completion date\n2. Materials warranty as per manufacturer specifications\n3. Warranty excludes damage from misuse or natural disasters")) }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="cancellation_terms">Cancellation Terms</label>
                                            <textarea class="form-control" id="cancellation_terms" name="cancellation_terms" rows="4" required>{{ old('cancellation_terms', session('contract_step3.cancellation_terms', "1. Client may cancel within 3 business days for full refund\n2. Cancellation after materials ordered subject to 25% fee\n3. Contractor may terminate if client breaches payment terms")) }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="additional_terms">Additional Terms and Conditions</label>
                                            <textarea class="form-control" id="additional_terms" name="additional_terms" rows="6">{{ old('additional_terms', session('contract_step3.additional_terms')) }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Signature Section -->
                            <div class="section-container" id="signaturesSection">
                                <h5 class="section-title">Signatures</h5>
                                
                                <!-- Contractor Signature -->
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <label>Contractor Signature</label>
                                        <div class="signature-pad">
                                            <canvas id="contractorSignaturePad"></canvas>
                                        </div>
                                        <div class="signature-buttons">
                                            <button type="button" class="btn btn-secondary btn-sm" id="clearContractorSignature">Clear</button>
                                        </div>
                                        @if(session('contract_step3.contractor_signature'))
                                        <div class="mt-2">
                                            <img src="{{ session('contract_step3.contractor_signature') }}" alt="Saved Contractor Signature" class="img-fluid" style="max-height: 100px;">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="keep_contractor_signature" id="keepContractorSignature" value="1" checked>
                                                <label class="form-check-label" for="keepContractorSignature">
                                                    Keep saved signature
                                                </label>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                
                                    <!-- Client Signature -->
                                    <div class="col-md-6">
                                        <label>Client Signature</label>
                                        <div class="signature-pad">
                                            <canvas id="clientSignaturePad"></canvas>
                                        </div>
                                        <div class="signature-buttons">
                                            <button type="button" class="btn btn-secondary btn-sm" id="clearClientSignature">Clear</button>
                                        </div>
                                        @if(session('contract_step3.client_signature'))
                                        <div class="mt-2">
                                            <img src="{{ session('contract_step3.client_signature') }}" alt="Saved Client Signature" class="img-fluid" style="max-height: 100px;">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="keep_client_signature" id="keepClientSignature" value="1" checked>
                                                <label class="form-check-label" for="keepClientSignature">
                                                    Keep saved signature
                                                </label>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Add a hidden input to store project duration in months -->
                            <input type="hidden" id="projectDurationMonths" value="{{ $projectDurationMonths ?? 0 }}">

                            <div class="form-group mt-4">
                                <button type="button" class="btn btn-secondary nav-btn" onclick="handlePreviousStep()">Previous Step</button>
                                <button type="submit" class="btn btn-primary">Next Step</button>
                                <a href="{{ route('contracts.clear-session') }}" class="btn btn-danger" onclick="return confirm('Are you sure you want to cancel? All entered data will be lost.')">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Payment terms handling
    const payAllIn = document.getElementById('payAllIn');
    const installmentPlan = document.getElementById('installmentPlan');
    const progressPayment = document.getElementById('progressPayment');
    const installmentOptions = document.getElementById('installmentOptions');
    const progressPaymentOptions = document.getElementById('progressPaymentOptions');
    const downpayment = document.getElementById('downpayment');
    const installmentPeriod = document.getElementById('installmentPeriod');
    const progressPaymentType = document.getElementById('progressPaymentType');
    const paymentTermsInput = document.getElementById('payment_terms');

    // Immediately show/hide options based on selection
    function toggleOptions() {
        if (payAllIn.checked) {
            installmentOptions.style.display = 'none';
            progressPaymentOptions.style.display = 'none';
        } else if (installmentPlan.checked) {
            installmentOptions.style.display = 'block';
            progressPaymentOptions.style.display = 'none';
        } else if (progressPayment.checked) {
            installmentOptions.style.display = 'none';
            progressPaymentOptions.style.display = 'block';
        }
    }

    function updatePaymentTerms() {
        let terms = '';
        if (payAllIn.checked) {
            terms = 'Pay All In';
        } else if (installmentPlan.checked) {
            const dp = downpayment.value || 30;
            const period = installmentPeriod.value || 3;
            terms = `Installment Plan: ${dp}% downpayment, ${period} months`;
        } else if (progressPayment.checked) {
            const type = progressPaymentType.value;
            terms = `Progress Payment: ${type === '70_completion' ? '70% completion' : 'completion only'}`;
        }
        paymentTermsInput.value = terms;

        // Debounce the save operation
        if (window.saveTimeout) {
            clearTimeout(window.saveTimeout);
        }
        window.saveTimeout = setTimeout(saveFormData, 300);
    }

    // Add change event listeners for immediate UI updates
    payAllIn.addEventListener('change', function() {
        toggleOptions();
        updatePaymentTerms();
    });

    installmentPlan.addEventListener('change', function() {
        toggleOptions();
        updatePaymentTerms();
    });

    progressPayment.addEventListener('change', function() {
        toggleOptions();
        updatePaymentTerms();
    });

    // Add input event listeners for numeric fields to ensure immediate feedback
    downpayment.addEventListener('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
        if (parseInt(this.value) > 100) this.value = '100';
        updatePaymentTerms();
    });

    installmentPeriod.addEventListener('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
        if (parseInt(this.value) > 24) this.value = '24';
        if (parseInt(this.value) < 1) this.value = '1';
        updatePaymentTerms();
    });

    progressPaymentType.addEventListener('change', updatePaymentTerms);

    // Initialize from saved session data
    const savedPaymentTerms = paymentTermsInput.value;
    if (savedPaymentTerms) {
        if (savedPaymentTerms.startsWith('Installment Plan:')) {
            installmentPlan.checked = true;
            const match = savedPaymentTerms.match(/(\d+)% downpayment, (\d+) months/);
            if (match) {
                downpayment.value = match[1];
                installmentPeriod.value = match[2];
            }
        } else if (savedPaymentTerms.startsWith('Progress Payment:')) {
            progressPayment.checked = true;
            if (savedPaymentTerms.includes('70% completion')) {
                progressPaymentType.value = '70_completion';
            } else {
                progressPaymentType.value = 'completion';
            }
        } else {
            payAllIn.checked = true;
        }
    } else {
        payAllIn.checked = true;
    }

    // Initial UI setup
    toggleOptions();
    updatePaymentTerms();

    // Optimize form saving
    let saveFormTimeout;
    function saveFormData() {
        const formData = new FormData(document.getElementById('step3Form'));
        const data = {
            payment_terms: formData.get('payment_terms'),
            warranty_terms: formData.get('warranty_terms'),
            cancellation_terms: formData.get('cancellation_terms'),
            additional_terms: formData.get('additional_terms'),
            contractor_signature: document.getElementById('contractor_signature').value || 
                (document.getElementById('keepContractorSignature')?.checked ? @json(session('contract_step3.contractor_signature')) : null),
            client_signature: document.getElementById('client_signature').value || 
                (document.getElementById('keepClientSignature')?.checked ? @json(session('contract_step3.client_signature')) : null)
        };

        console.log('Auto-saving form data:', data);

        return fetch('{{ route("contracts.save.step3") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        }).then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        }).then(data => {
            console.log('Form data saved successfully:', data);
        }).catch(error => {
            console.error('Error saving form data:', error);
        });
    }

    // Initialize signature pads and restore saved signatures
    const contractorCanvas = document.getElementById('contractorSignaturePad');
    const clientCanvas = document.getElementById('clientSignaturePad');
    
    if (contractorCanvas && clientCanvas) {
        window.contractorPad = new SignaturePad(contractorCanvas);
        window.clientPad = new SignaturePad(clientCanvas);

        // Restore saved signatures if they exist
        const savedContractorSignature = @json(session('contract_step3.contractor_signature'));
        const savedClientSignature = @json(session('contract_step3.client_signature'));

        if (savedContractorSignature) {
            // Create a temporary image to load the signature
            const contractorImg = new Image();
            contractorImg.onload = function() {
                const ctx = contractorCanvas.getContext('2d');
                ctx.drawImage(contractorImg, 0, 0);
                // Update the hidden input
                document.getElementById('contractor_signature').value = savedContractorSignature;
            };
            contractorImg.src = savedContractorSignature;
        }

        if (savedClientSignature) {
            const clientImg = new Image();
            clientImg.onload = function() {
                const ctx = clientCanvas.getContext('2d');
                ctx.drawImage(clientImg, 0, 0);
                // Update the hidden input
                document.getElementById('client_signature').value = savedClientSignature;
            };
            clientImg.src = savedClientSignature;
        }

        // Clear signature buttons
        document.getElementById('clearContractorSignature')?.addEventListener('click', () => {
            window.contractorPad.clear();
            document.getElementById('contractor_signature').value = '';
            if (document.getElementById('keepContractorSignature')) {
                document.getElementById('keepContractorSignature').checked = false;
            }
            saveFormData(); // Save the cleared state
        });
        
        document.getElementById('clearClientSignature')?.addEventListener('click', () => {
            window.clientPad.clear();
            document.getElementById('client_signature').value = '';
            if (document.getElementById('keepClientSignature')) {
                document.getElementById('keepClientSignature').checked = false;
            }
            saveFormData(); // Save the cleared state
        });

        // Handle canvas resize
        function resizeCanvas() {
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            [contractorCanvas, clientCanvas].forEach(canvas => {
                canvas.width = canvas.offsetWidth * ratio;
                canvas.height = canvas.offsetHeight * ratio;
                canvas.getContext("2d").scale(ratio, ratio);
            });
        }

        window.addEventListener('resize', resizeCanvas);
        resizeCanvas(); // Initial setup

        // Auto-save signatures when drawn
        [contractorCanvas, clientCanvas].forEach(canvas => {
            canvas.addEventListener('mouseup', () => {
                const pad = canvas === contractorCanvas ? window.contractorPad : window.clientPad;
                const type = canvas === contractorCanvas ? 'contractor' : 'client';
                const hiddenInput = document.getElementById(`${type}_signature`);
                
                if (!pad.isEmpty()) {
                    const dataURL = pad.toDataURL();
                    hiddenInput.value = dataURL;
                    saveFormData();
                }
            });
        });
    }

    // Add event listeners for auto-save
    const form = document.getElementById('step3Form');
    const textareas = form ? form.querySelectorAll('textarea') : [];
    const signaturePads = document.querySelectorAll('.signature-pad');

    // Auto-save when textareas change
    textareas.forEach(textarea => {
        textarea.addEventListener('change', saveFormData);
        textarea.addEventListener('input', debounce(saveFormData, 1000));
    });

    // Auto-save when signatures change
    signaturePads.forEach(pad => {
        pad.addEventListener('endStroke', debounce(saveFormData, 1000));
    });

    // Debounce function to prevent too many saves
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Make handlePreviousStep globally available
    window.handlePreviousStep = function() {
        console.log('Previous Step button clicked');
        // First save any form data including signatures
        const formData = new FormData(document.getElementById('step3Form'));
        const data = {
            payment_terms: formData.get('payment_terms'),
            warranty_terms: formData.get('warranty_terms'),
            cancellation_terms: formData.get('cancellation_terms'),
            additional_terms: formData.get('additional_terms'),
            contractor_signature: document.getElementById('contractor_signature').value || 
                (document.getElementById('keepContractorSignature')?.checked ? @json(session('contract_step3.contractor_signature')) : null),
            client_signature: document.getElementById('client_signature').value || 
                (document.getElementById('keepClientSignature')?.checked ? @json(session('contract_step3.client_signature')) : null)
        };
        // Log the data being saved
        console.log('Saving step 3 data before navigation:', data);
        // Save the data and then navigate
        fetch('{{ route("contracts.save.step3") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        }).then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        }).then(data => {
            console.log('Step 3 data saved successfully:', data);
            // Only navigate after successful save
            window.location.href = '{{ route("contracts.step2") }}';
        }).catch(error => {
            console.error('Error saving step 3 data:', error);
            // Still navigate even if save fails
            window.location.href = '{{ route("contracts.step2") }}';
        });
    }

    // Form validation and submission
    const formSubmit = document.getElementById('step3Form');
    if (formSubmit) {
        let isSubmitting = false;
        formSubmit.addEventListener('submit', function(e) {
            if (isSubmitting) return;
            e.preventDefault();
            let errorMessage = '';
            const errorDiv = document.getElementById('formErrorMessage');
            if (errorDiv) {
                errorDiv.classList.add('d-none');
                errorDiv.textContent = '';
            }
            console.log('Form submit triggered');

            if (!formSubmit.checkValidity()) {
                e.stopPropagation();
                formSubmit.classList.add('was-validated');
                errorMessage = 'Please fill in all required fields.';
                if (errorDiv) {
                    errorDiv.textContent = errorMessage;
                    errorDiv.classList.remove('d-none');
                }
                console.log('Form validation failed');
                return;
            }

            // Check if signatures are provided or kept
            const keepContractorSignature = document.getElementById('keepContractorSignature')?.checked;
            const keepClientSignature = document.getElementById('keepClientSignature')?.checked;
            console.log('keepContractorSignature:', keepContractorSignature, 'keepClientSignature:', keepClientSignature);

            // Always set hidden fields before submit
            if (keepContractorSignature) {
                document.getElementById('contractor_signature').value = @json(session('contract_step3.contractor_signature')) || '';
            } else if (window.contractorPad && !window.contractorPad.isEmpty()) {
                document.getElementById('contractor_signature').value = window.contractorPad.toDataURL();
            }

            if (keepClientSignature) {
                document.getElementById('client_signature').value = @json(session('contract_step3.client_signature')) || '';
            } else if (window.clientPad && !window.clientPad.isEmpty()) {
                document.getElementById('client_signature').value = window.clientPad.toDataURL();
            }

            if (!document.getElementById('contractor_signature').value) {
                errorMessage = 'Please provide contractor signature.';
                if (errorDiv) {
                    errorDiv.textContent = errorMessage;
                    errorDiv.classList.remove('d-none');
                }
                console.log('Contractor signature missing');
                return;
            }

            if (!document.getElementById('client_signature').value) {
                errorMessage = 'Please provide client signature.';
                if (errorDiv) {
                    errorDiv.textContent = errorMessage;
                    errorDiv.classList.remove('d-none');
                }
                console.log('Client signature missing');
                return;
            }

            // Submit the form
            console.log('All validations passed, submitting form');
            isSubmitting = true;
            formSubmit.submit();
        });
    }
});
</script>
@endpush 