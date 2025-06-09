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

                        <form method="POST" action="{{ route('contracts.store.step3') }}" id="step3Form">
                            @csrf

                            <!-- Contract Clauses Section -->
                            <div class="section-container" id="clausesSection">
                                <h5 class="section-title">Contract Clauses</h5>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="payment_terms">Payment Terms</label>
                                            <textarea class="form-control" id="payment_terms" name="payment_terms" rows="4" required>{{ old('payment_terms', session('contract_step3.payment_terms', "1. Initial deposit of 30% upon contract signing\n2. 40% upon completion of 50% of work\n3. Remaining 30% upon final inspection and completion")) }}</textarea>
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
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mt-4">
                                <a href="{{ route('contracts.step2') }}" class="btn btn-secondary">Previous Step</a>
                                <button type="submit" class="btn btn-primary">Next Step</button>
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
    // Initialize signature pads
    const contractorCanvas = document.getElementById('contractorSignaturePad');
    const clientCanvas = document.getElementById('clientSignaturePad');
    
    if (contractorCanvas && clientCanvas) {
        window.contractorPad = new SignaturePad(contractorCanvas);
        window.clientPad = new SignaturePad(clientCanvas);

        // Clear signature buttons
        document.getElementById('clearContractorSignature')?.addEventListener('click', () => window.contractorPad.clear());
        document.getElementById('clearClientSignature')?.addEventListener('click', () => window.clientPad.clear());

        // Handle canvas resize
        function resizeCanvas() {
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            [contractorCanvas, clientCanvas].forEach(canvas => {
                canvas.width = canvas.offsetWidth * ratio;
                canvas.height = canvas.offsetHeight * ratio;
                canvas.getContext("2d").scale(ratio, ratio);
            });
            if (window.contractorPad) window.contractorPad.clear();
            if (window.clientPad) window.clientPad.clear();
        }

        window.addEventListener('resize', resizeCanvas);
        resizeCanvas(); // Initial setup
    }

    // Form validation
    const form = document.getElementById('step3Form');
    form.addEventListener('submit', function(e) {
        if (!form.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        }

        // Check if signatures are provided
        if (window.contractorPad && window.contractorPad.isEmpty()) {
            e.preventDefault();
            alert('Please provide contractor signature');
            return;
        }

        if (window.clientPad && window.clientPad.isEmpty()) {
            e.preventDefault();
            alert('Please provide client signature');
            return;
        }

        form.classList.add('was-validated');
    });
});
</script>
@endpush 