@extends('layouts.app')

@section('content')
<div class="container mt-4 mb-5">
    <h1 class="text-center mb-4">{{ $edit_mode ? 'Edit' : 'Create' }} Contract Agreement</h1>
    
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    
    <form method="POST" action="{{ $edit_mode ? route('contracts.update', $contract->id) : route('contracts.store') }}" enctype="multipart/form-data" id="contractForm">
        @csrf
        @if($edit_mode)
            @method('PUT')
        @endif

        <!-- Contractor Information -->
        <div class="form-section">
            <h2>Contractor Information</h2>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="contractor_name" class="form-label">Contractor Name</label>
                    <input type="text" class="form-control" id="contractor_name" name="contractor_name" 
                        value="{{ old('contractor_name', $edit_mode ? $contractor->name ?? config('contractor.name') : config('contractor.name')) }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="contractor_company" class="form-label">Company Name</label>
                    <input type="text" class="form-control" id="contractor_company" name="contractor_company" 
                        value="{{ old('contractor_company', $edit_mode ? $contractor->company_name ?? config('contractor.company') : config('contractor.company')) }}" required>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="contractor_street" class="form-label">Street Address</label>
                    <input type="text" class="form-control" id="contractor_street" name="contractor_street" 
                        value="{{ old('contractor_street', $edit_mode ? $contractor->street ?? config('contractor.street') : config('contractor.street')) }}" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="contractor_city" class="form-label">City</label>
                    <input type="text" class="form-control" id="contractor_city" name="contractor_city" 
                        value="{{ old('contractor_city', $edit_mode ? $contractor->city ?? config('contractor.city') : config('contractor.city')) }}" required>
                </div>
                <div class="col-md-2 mb-3">
                    <label for="contractor_state" class="form-label">State/Province</label>
                    <input type="text" class="form-control" id="contractor_state" name="contractor_state" 
                        value="{{ old('contractor_state', $edit_mode ? $contractor->state ?? config('contractor.state') : config('contractor.state')) }}" required>
                </div>
                <div class="col-md-1 mb-3">
                    <label for="contractor_postal" class="form-label">Postal Code</label>
                    <input type="text" class="form-control postal-code" id="contractor_postal" name="contractor_postal" 
                        value="{{ old('contractor_postal', $edit_mode ? $contractor->postal ?? config('contractor.postal') : config('contractor.postal')) }}" 
                        pattern="[0-9\-]*" title="Only numbers and hyphens are allowed" required>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="contractor_email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="contractor_email" name="contractor_email" 
                        value="{{ old('contractor_email', $edit_mode ? $contractor->email ?? config('contractor.email') : config('contractor.email')) }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="contractor_phone" class="form-label">Phone Number</label>
                    <input type="tel" class="form-control" id="contractor_phone" name="contractor_phone" 
                        value="{{ old('contractor_phone', $edit_mode ? $contractor->phone ?? config('contractor.phone') : config('contractor.phone')) }}" 
                        pattern="[0-9()\- +]*" title="Only numbers, parentheses, hyphens, and plus signs are allowed" required>
                </div>
            </div>
        </div>

        <!-- Client Information -->
        <div class="form-section">
            <h2>Client Information</h2>
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <input type="text" class="form-control" id="client_search" placeholder="Search client by name or email">
                        <button class="btn btn-outline-secondary" type="button" id="client_search_btn">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                    <div class="search-results" id="client_search_results"></div>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="company_name" class="form-label">Company Name</label>
                    <input type="text" class="form-control" id="company_name" name="company_name" 
                        value="{{ old('company_name', $edit_mode ? $client->company_name ?? '' : '') }}">
                </div>
                <div class="col-md-6">
                    <label for="contact_person" class="form-label">Contact Person</label>
                    <input type="text" class="form-control" id="contact_person" name="contact_person" 
                        value="{{ old('contact_person', $edit_mode ? ($client->entity_type === 'person' ? $client->name : '') : '') }}">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="client_street" class="form-label">Street Address</label>
                    <input type="text" class="form-control" id="client_street" name="client_street" 
                        value="{{ old('client_street', $edit_mode ? $client->street ?? '' : '') }}" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="client_city" class="form-label">City</label>
                    <input type="text" class="form-control" id="client_city" name="client_city" 
                        value="{{ old('client_city', $edit_mode ? $client->city ?? '' : '') }}" required>
                </div>
                <div class="col-md-2 mb-3">
                    <label for="client_state" class="form-label">State/Province</label>
                    <input type="text" class="form-control" id="client_state" name="client_state" 
                        value="{{ old('client_state', $edit_mode ? $client->state ?? '' : '') }}" required>
                </div>
                <div class="col-md-1 mb-3">
                    <label for="client_postal" class="form-label">Postal Code</label>
                    <input type="text" class="form-control postal-code" id="client_postal" name="client_postal" 
                        value="{{ old('client_postal', $edit_mode ? $client->postal ?? '' : '') }}" 
                        pattern="[0-9\-]*" title="Only numbers and hyphens are allowed" required>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="client_email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="client_email" name="client_email" 
                        value="{{ old('client_email', $edit_mode ? $client->email ?? '' : '') }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="client_phone" class="form-label">Phone Number</label>
                    <input type="tel" class="form-control" id="client_phone" name="client_phone" 
                        value="{{ old('client_phone', $edit_mode ? $client->phone ?? '' : '') }}" 
                        pattern="[0-9()\- +]*" title="Only numbers, parentheses, hyphens, and plus signs are allowed" required>
                </div>
            </div>
        </div>

        <!-- Property Information -->
        <div class="form-section">
            <h2>Property Information</h2>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="property_street" class="form-label">Street Address</label>
                    <input type="text" class="form-control" id="property_street" name="property_street" 
                        value="{{ old('property_street', $edit_mode ? $property->street ?? '' : '') }}" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="property_city" class="form-label">City</label>
                    <input type="text" class="form-control" id="property_city" name="property_city" 
                        value="{{ old('property_city', $edit_mode ? $property->city ?? '' : '') }}" required>
                </div>
                <div class="col-md-2 mb-3">
                    <label for="property_state" class="form-label">State/Province</label>
                    <input type="text" class="form-control" id="property_state" name="property_state" 
                        value="{{ old('property_state', $edit_mode ? $property->state ?? '' : '') }}" required>
                </div>
                <div class="col-md-1 mb-3">
                    <label for="property_postal" class="form-label">Postal Code</label>
                    <input type="text" class="form-control postal-code" id="property_postal" name="property_postal" 
                        value="{{ old('property_postal', $edit_mode ? $property->postal ?? '' : '') }}" 
                        pattern="[0-9\-]*" title="Only numbers and hyphens are allowed" required>
                </div>
            </div>
        </div>

        <!-- Scope of Work -->
        <div class="form-section">
            <h2>Scope of Work</h2>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label class="form-label">Select Scope of Work:</label>
                    @php
                        $scope_works = [];
                        if ($edit_mode && !empty($contract->scope_of_work)) {
                            $scope_works = explode(', ', $contract->scope_of_work);
                        }
                    @endphp
                    <div class="form-check scope-work-option">
                        <input class="form-check-input scope-work" type="checkbox" name="scope_of_work[]" id="scope_design" value="Design Services" 
                            {{ in_array('Design Services', $scope_works) ? 'checked' : '' }}>
                        <label class="form-check-label" for="scope_design">Design Services</label>
                    </div>
                    <div class="form-check scope-work-option">
                        <input class="form-check-input scope-work" type="checkbox" name="scope_of_work[]" id="scope_construction" value="Construction" 
                            {{ in_array('Construction', $scope_works) ? 'checked' : '' }}>
                        <label class="form-check-label" for="scope_construction">Construction</label>
                    </div>
                    <div class="form-check scope-work-option">
                        <input class="form-check-input scope-work" type="checkbox" name="scope_of_work[]" id="scope_renovation" value="Renovation" 
                            {{ in_array('Renovation', $scope_works) ? 'checked' : '' }}>
                        <label class="form-check-label" for="scope_renovation">Renovation</label>
                    </div>
                    <div class="form-check scope-work-option">
                        <input class="form-check-input scope-work" type="checkbox" name="scope_of_work[]" id="scope_maintenance" value="Maintenance" 
                            {{ in_array('Maintenance', $scope_works) ? 'checked' : '' }}>
                        <label class="form-check-label" for="scope_maintenance">Maintenance</label>
                    </div>
                    <div class="form-check scope-work-option">
                        <input class="form-check-input scope-work" type="checkbox" name="scope_of_work[]" id="scope_other" value="Other" 
                            {{ (count(array_diff($scope_works, ['Design Services', 'Construction', 'Renovation', 'Maintenance'])) > 0) ? 'checked' : '' }}>
                        <label class="form-check-label" for="scope_other">Other</label>
                        <input type="text" class="form-control other-work-input mt-2" id="other_work_text" name="other_work_text" 
                            placeholder="Specify other work" value="{{ old('other_work_text', $edit_mode ? implode(', ', array_diff($scope_works, ['Design Services', 'Construction', 'Renovation', 'Maintenance'])) : '') }}"
                            style="display: {{ (count(array_diff($scope_works, ['Design Services', 'Construction', 'Renovation', 'Maintenance'])) > 0) ? 'block' : 'none' }}">
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label for="scope_description" class="form-label">Scope Description</label>
                    <textarea class="form-control" id="scope_description" name="scope_description" rows="4">{{ old('scope_description', $edit_mode ? $contract->scope_description ?? '' : '') }}</textarea>
                </div>
            </div>
        </div>

        <!-- Agreement, Service, and Project Period Sections -->
        <div class="contract-section">
            <h4>AGREEMENT</h4>
            <p id="agreementClausePreview" class="preview-text"></p>
        </div>

        <div class="contract-section">
            <h4>SERVICE</h4>
            <p id="serviceClausePreview" class="preview-text"></p>
        </div>

        <div class="contract-section">
            <h4>PROJECT PERIOD</h4>
            <p id="projectPeriodPreview" class="preview-text"></p>
            <div class="row">
                <div class="col-md-6">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" 
                        value="{{ old('start_date', $edit_mode ? $contract->start_date ?? '' : '') }}" required>
                </div>
                <div class="col-md-6">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" 
                        value="{{ old('end_date', $edit_mode ? $contract->end_date ?? '' : '') }}" required>
                </div>
            </div>
        </div>

        <!-- Amount Section -->
        <div class="form-section">
            <h2>Amount</h2>
            <div class="row">
                <div class="col-md-12">
                    <div id="item_container">
                        <div class="row mb-2">
                            <div class="col-md-4">
                                <strong>Item Description</strong>
                            </div>
                            <div class="col-md-2">
                                <strong>Qty</strong>
                            </div>
                            <div class="col-md-2">
                                <strong>Amount</strong>
                            </div>
                            <div class="col-md-2">
                                <strong>Total</strong>
                            </div>
                            <div class="col-md-2"></div>
                        </div>
                        @if($edit_mode && !empty($items))
                            @foreach($items as $index => $item)
                                <div class="row item-row mb-2">
                                    <div class="col-md-4">
                                        <input type="text" class="form-control" name="item_description[]" 
                                            value="{{ $item->description }}" required>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" class="form-control quantity" name="item_quantity[]" 
                                            value="{{ $item->quantity }}" min="0" step="0.01" required>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" class="form-control amount" name="item_amount[]" 
                                            value="{{ $item->amount }}" min="0" step="0.01" required>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="text" class="form-control total" 
                                            value="{{ number_format($item->total, 2) }}" readonly>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-danger btn-sm remove-item">×</button>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    <button type="button" class="btn btn-secondary" id="add_item">Add Item</button>
                </div>
            </div>
            
            <div class="row mt-3">
                <div class="col-md-3 offset-md-9">
                    <div class="input-group mb-3">
                        <span class="input-group-text">Total:</span>
                        <input type="text" class="form-control" id="total_amount" name="total_amount" 
                            value="{{ old('total_amount', $edit_mode ? number_format($contract->total_amount ?? 0, 2) : '0.00') }}" readonly>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contract Terms -->
        <div class="contract-section">
            <h4>CONTRACT TERMS</h4>
            <textarea class="form-control" id="contract_paragraphs" name="contract_paragraphs" rows="8" required>{{ old('contract_paragraphs', $edit_mode ? $contract->contract_terms ?? '' : "1. PAYMENT TERMS: The Client agrees to pay the Contractor the total amount specified above according to the following schedule: 50% upon signing this agreement, 40% upon completion of 50% of the work, and the remaining 10% upon final completion and acceptance of all work.\n\n2. CHANGE ORDERS: Any changes to the scope of work must be agreed upon in writing by both parties and may result in additional charges and time extensions.\n\n3. WARRANTIES: The Contractor warrants that all work will be performed in a professional manner consistent with industry standards. Materials will be of good quality unless otherwise specified.\n\n4. TERMINATION: Either party may terminate this agreement with written notice if the other party fails to cure a material breach within 14 days of receiving written notice of such breach.") }}</textarea>
        </div>
        
        <div class="contract-section">
            <h4>JURISDICTION</h4>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="jurisdiction" class="form-label">State Jurisdiction</label>
                    <input type="text" class="form-control" id="jurisdiction" name="jurisdiction" 
                        value="{{ old('jurisdiction', $edit_mode ? $contract->jurisdiction ?? '' : '') }}" required>
                </div>
            </div>
        </div>

        <!-- Signatures -->
        <div class="form-section">
            <h2>Signatures</h2>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Client Signature</label>
                    <div>
                        <input type="file" class="form-control mb-2" id="client_signature_upload" name="client_signature" accept="image/*">
                        @if($edit_mode && !empty($contract->client_signature))
                            <div class="mb-2">
                                <p class="text-muted">Current signature:</p>
                                <img src="{{ $contract->client_signature }}" class="signature-preview">
                                <input type="hidden" name="existing_client_signature" value="{{ $contract->client_signature }}">
                            </div>
                        @endif
                        <small class="text-muted">OR draw signature below:</small>
                        <div class="signature-pad">
                            <canvas id="clientSignatureCanvas"></canvas>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-secondary mt-2" onclick="clearSignature('client')">Clear</button>
                        <input type="hidden" id="client_signature_data" name="client_signature_data">
                    </div>
                    <label class="form-label mt-3">Client Name</label>
                    <input type="text" class="form-control" id="signed_client_name" readonly>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Contractor Signature</label>
                    <div>
                        <input type="file" class="form-control mb-2" id="contractor_signature_upload" name="contractor_signature" accept="image/*">
                        @if($edit_mode && !empty($contract->contractor_signature))
                            <div class="mb-2">
                                <p class="text-muted">Current signature:</p>
                                <img src="{{ $contract->contractor_signature }}" class="signature-preview">
                                <input type="hidden" name="existing_contractor_signature" value="{{ $contract->contractor_signature }}">
                            </div>
                        @endif
                        <small class="text-muted">OR draw signature below:</small>
                        <div class="signature-pad">
                            <canvas id="contractorSignatureCanvas"></canvas>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-secondary mt-2" onclick="clearSignature('contractor')">Clear</button>
                        <input type="hidden" id="contractor_signature_data" name="contractor_signature_data">
                    </div>
                    <label class="form-label mt-3">Contractor Name</label>
                    <input type="text" class="form-control" id="signed_contractor_name" readonly>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="row">
            <div class="col-md-12 text-center">
                <button type="submit" class="btn btn-primary btn-lg">{{ $edit_mode ? 'Update' : 'Submit' }} Contract</button>
                @if($edit_mode)
                    <a href="{{ route('contracts.show', $contract->id) }}" class="btn btn-secondary btn-lg ms-2">Cancel</a>
                @endif
            </div>
        </div>
    </form>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
<style>
    body {
        background-image: url('https://images.unsplash.com/photo-1564501049412-61c2a3083791?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1932&q=80');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        background-color: rgba(255, 255, 255, 0.9);
        background-blend-mode: overlay;
    }
    .container {
        max-width: 1500px;
        background-color: rgba(255, 255, 255, 0.95);
        padding: 30px;
        padding-right: 30px;
        border-radius: 10px;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        margin-top: 30px;
        margin-bottom: 30px;
    }
    .signature-pad {
        border: 1px solid #ddd;
        background-color: white;
        margin-bottom: 10px;
    }
    canvas {
        width: 100%;
        height: 150px;
        background-color: #f8f9fa;
    }
    .form-section {
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 1px solid #eee;
    }
    .other-work-input {
        margin-top: 5px;
        display: none;
    }
    .scope-work-option {
        margin-bottom: 5px;
    }
    .postal-code {
        width: 100px;
    }
    .search-results {
        position: absolute;
        z-index: 1000;
        width: 100%;
        max-height: 200px;
        overflow-y: auto;
        background: white;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        display: none;
    }
    .search-result-item {
        padding: 8px 12px;
        cursor: pointer;
    }
    .search-result-item:hover {
        background-color: #f8f9fa;
    }
    .form-control, .form-select {
        background-color: #fff;
    }
    h1, h2, h3, h4 {
        color: #2c3e50;
    }
    .container .row {
        padding-right: 15px;
    }
    .signature-preview {
        max-width: 200px;
        max-height: 100px;
        border: 1px solid #ddd;
        margin-bottom: 10px;
    }
    .material-search-results {
        position: absolute;
        z-index: 1000;
        width: 100%;
        max-height: 200px;
        overflow-y: auto;
        background: white;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        display: none;
        margin-top: 2px;
    }
    .supplier-section {
        margin-top: 10px;
        padding-left: 15px;
    }
    .preview-text {
        padding: 10px;
        background-color: #f8f9fa;
        border-radius: 4px;
        margin-top: 10px;
    }
    .contract-section {
        margin-bottom: 30px;
        padding: 15px;
        background-color: #f8f9fa;
        border-radius: 4px;
    }
    .contract-section h4 {
        color: #2c3e50;
        border-bottom: 1px solid #dee2e6;
        padding-bottom: 5px;
        margin-bottom: 15px;
    }
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<script>
    // Initialize Signature Pads
    const clientCanvas = document.getElementById('clientSignatureCanvas');
    const contractorCanvas = document.getElementById('contractorSignatureCanvas');
    
    // Set canvas size properly
    function resizeCanvas(canvas) {
        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        canvas.width = canvas.offsetWidth * ratio;
        canvas.height = canvas.offsetHeight * ratio;
        canvas.getContext("2d").scale(ratio, ratio);
    }
    
    resizeCanvas(clientCanvas);
    resizeCanvas(contractorCanvas);
    
    const clientSignaturePad = new SignaturePad(clientCanvas, {
        backgroundColor: 'rgb(255, 255, 255)'
    });
    const contractorSignaturePad = new SignaturePad(contractorCanvas, {
        backgroundColor: 'rgb(255, 255, 255)'
    });
    
    // Handle window resize
    window.addEventListener('resize', () => {
        resizeCanvas(clientCanvas);
        resizeCanvas(contractorCanvas);
    });

    // Clear signature function
    function clearSignature(type) {
        if (type === 'client') {
            clientSignaturePad.clear();
            document.getElementById('client_signature_data').value = '';
        } else {
            contractorSignaturePad.clear();
            document.getElementById('contractor_signature_data').value = '';
        }
    }

    // Material search functionality
    function initializeMaterialSearch() {
        $('.material-search').each(function() {
            const searchInput = $(this);
            const searchResults = searchInput.closest('.input-group').siblings('.material-search-results');
            const materialIdInput = searchInput.siblings('input[name="item_material_id[]"]');
            const hasPreferredSuppliersCheckbox = searchInput.closest('.item-row').find('.has-preferred-suppliers');
            const supplierSection = searchInput.closest('.item-row').find('.supplier-section');
            const supplierSelect = supplierSection.find('.supplier-select');
            const amountInput = searchInput.closest('.item-row').find('.amount');

            searchInput.on('input', function() {
                const query = $(this).val().trim();
                if (query.length < 2) {
                    searchResults.hide();
                    return;
                }

                $.get('{{ route("materials.search") }}', { query: query })
                    .done(function(data) {
                        searchResults.empty();
                        data.forEach(function(material) {
                            const div = $('<div>')
                                .addClass('search-result-item')
                                .text(material.name)
                                .data('material', material);
                            searchResults.append(div);
                        });
                        searchResults.show();
                    });
            });

            searchResults.on('click', '.search-result-item', function() {
                const material = $(this).data('material');
                searchInput.val(material.name);
                materialIdInput.val(material.id);
                searchResults.hide();

                // Update price if available
                if (material.default_price) {
                    amountInput.val(material.default_price);
                    calculateItemTotal(amountInput);
                }

                // Show/hide preferred suppliers checkbox based on material
                hasPreferredSuppliersCheckbox.prop('checked', material.has_preferred_suppliers);
                if (material.has_preferred_suppliers) {
                    loadSuppliers(material.id, supplierSelect);
                    supplierSection.show();
                } else {
                    supplierSection.hide();
                }
            });
        });

        // Hide search results when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.material-search, .material-search-results').length) {
                $('.material-search-results').hide();
            }
        });
    }

    // Load suppliers for a material
    function loadSuppliers(materialId, supplierSelect) {
        $.get(`{{ url('materials') }}/${materialId}/suppliers`, { preferred: true })
            .done(function(suppliers) {
                supplierSelect.empty().append('<option value="">Select Supplier</option>');
                suppliers.forEach(function(supplier) {
                    supplierSelect.append(
                        $('<option>')
                            .val(supplier.id)
                            .text(supplier.name)
                            .data('price', supplier.pivot.price)
                    );
                });
            });
    }

    // Update price when supplier is selected
    $(document).on('change', '.supplier-select', function() {
        const selectedOption = $(this).find('option:selected');
        const price = selectedOption.data('price');
        if (price) {
            const amountInput = $(this).closest('.item-row').find('.amount');
            amountInput.val(price);
            calculateItemTotal(amountInput);
        }
    });

    // Add new item row
    $('#add_item').click(function() {
        const newRow = $(`
            <div class="row item-row mb-2">
                <div class="col-md-4">
                    <div class="input-group">
                        <input type="text" class="form-control material-search" placeholder="Search material">
                        <input type="hidden" name="item_material_id[]">
                    </div>
                    <div class="material-search-results"></div>
                </div>
                <div class="col-md-2">
                    <input type="number" class="form-control quantity" name="item_quantity[]" placeholder="Qty" min="0" step="0.01" required>
                </div>
                <div class="col-md-2">
                    <input type="number" class="form-control amount" name="item_amount[]" placeholder="Amount" min="0" step="0.01" required>
                </div>
                <div class="col-md-2">
                    <input type="text" class="form-control total" placeholder="Total" readonly>
                </div>
                <div class="col-md-1">
                    <div class="form-check">
                        <input class="form-check-input has-preferred-suppliers" type="checkbox">
                        <label class="form-check-label">Preferred</label>
                    </div>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger btn-sm remove-item">×</button>
                </div>
                <div class="col-12 supplier-section" style="display: none;">
                    <select class="form-select supplier-select" name="item_supplier_id[]">
                        <option value="">Select Supplier</option>
                    </select>
                </div>
            </div>
        `);
        
        $('#item_container').append(newRow);
        initializeMaterialSearch();
    });

    // Remove item row
    $(document).on('click', '.remove-item', function() {
        $(this).closest('.item-row').remove();
        calculateGrandTotal();
    });

    // Calculate item total
    function calculateItemTotal(input) {
        const row = input.closest('.item-row');
        const quantity = parseFloat(row.find('.quantity').val()) || 0;
        const amount = parseFloat(row.find('.amount').val()) || 0;
        const total = quantity * amount;
        row.find('.total').val(total.toFixed(2));
        calculateGrandTotal();
    }

    // Calculate grand total
    function calculateGrandTotal() {
        let grandTotal = 0;
        $('.item-row').each(function() {
            const total = parseFloat($(this).find('.total').val()) || 0;
            grandTotal += total;
        });
        $('#total_amount').val(grandTotal.toFixed(2));
    }

    // Handle quantity and amount changes
    $(document).on('input', '.quantity, .amount', function() {
        calculateItemTotal($(this));
    });

    // Client search functionality
    const clientSearch = $('#client_search');
    const clientSearchBtn = $('#client_search_btn');
    const clientSearchResults = $('#client_search_results');
    
    function searchClients() {
        const searchTerm = clientSearch.val().trim();
        if (searchTerm.length < 2) {
            clientSearchResults.hide();
            return;
        }
        
        $.get('{{ route("clients.search") }}', { query: searchTerm })
            .done(function(data) {
                if (data.length > 0) {
                    clientSearchResults.empty();
                    data.forEach(function(client) {
                        const item = $('<div>')
                            .addClass('search-result-item')
                            .text(`${client.name}${client.company_name ? ` (${client.company_name})` : ''} - ${client.email}`)
                            .data('client', client);
                        clientSearchResults.append(item);
                    });
                    clientSearchResults.show();
                } else {
                    clientSearchResults.hide();
                }
            });
    }
    
    clientSearchBtn.click(searchClients);
    clientSearch.on('input', searchClients);
    
    // Fill client form with selected client data
    clientSearchResults.on('click', '.search-result-item', function() {
        const client = $(this).data('client');
        $('#company_name').val(client.company_name || '');
        $('#contact_person').val(client.entity_type === 'person' ? client.name : '');
        $('#client_street').val(client.street);
        $('#client_city').val(client.city);
        $('#client_state').val(client.state);
        $('#client_postal').val(client.postal);
        $('#client_email').val(client.email);
        $('#client_phone').val(client.phone);
        clientSearchResults.hide();
        updateContractPreview();
    });

    // Handle form submission
    $('#contractForm').on('submit', function(e) {
        // Save signatures if drawn
        if (!clientSignaturePad.isEmpty()) {
            $('#client_signature_data').val(clientSignaturePad.toDataURL('image/png'));
        }
        
        if (!contractorSignaturePad.isEmpty()) {
            $('#contractor_signature_data').val(contractorSignaturePad.toDataURL('image/png'));
        }
        
        // Set signed names
        const clientCompany = $('#company_name').val();
        const clientContact = $('#contact_person').val();
        const clientName = clientCompany || clientContact;
        
        $('#signed_client_name').val(clientName);
        $('#signed_contractor_name').val($('#contractor_name').val());
        
        // Validate at least one scope of work is selected
        if (!$('input[name="scope_of_work[]"]:checked').length) {
            e.preventDefault();
            alert('Please select at least one scope of work');
            return;
        }
        
        // Validate "Other" scope has text if checked
        if ($('#scope_other').is(':checked') && !$('#other_work_text').val().trim()) {
            e.preventDefault();
            alert('Please specify the "Other" scope of work');
            return;
        }
        
        // Validate at least one item exists
        if (!$('.item-row').length) {
            e.preventDefault();
            alert('Please add at least one item');
            return;
        }
        
        // Validate dates
        const startDate = new Date($('#start_date').val());
        const endDate = new Date($('#end_date').val());
        
        if (startDate >= endDate) {
            e.preventDefault();
            alert('End date must be after start date');
            return;
        }
    });

    // Initial setup
    $(document).ready(function() {
        // Add first item row if not in edit mode or no existing items
        if (!{{ $edit_mode ? 'true' : 'false' }} || !$('.item-row').length) {
            $('#add_item').click();
        }
        
        // Initialize material search for existing rows
        initializeMaterialSearch();
        
        // Calculate grand total if in edit mode with existing items
        if ({{ $edit_mode ? 'true' : 'false' }} && $('.item-row').length) {
            calculateGrandTotal();
        }

        // Show other work input if "Other" is checked
        if ($('#scope_other').is(':checked')) {
            $('#other_work_text').show();
        }

        // Initialize contract preview
        updateContractPreview();
    });

    // Handle "Other" scope of work option
    $('#scope_other').change(function() {
        const otherInput = $('#other_work_text');
        otherInput.toggle(this.checked);
        if (!this.checked) {
            otherInput.val('');
        }
        updateContractPreview();
    });

    // Update contract preview
    function updateContractPreview() {
        const scopeWork = $('input[name="scope_of_work[]"]:checked')
            .map(function() {
                if (this.id === 'scope_other') {
                    const otherText = $('#other_work_text').val();
                    return otherText || 'Other';
                }
                return $(this).val();
            })
            .get()
            .join(', ') || '[Scope of Work]';
            
        const contractorName = $('#contractor_name').val() || '[Contractor Name]';
        const contractorCompany = $('#contractor_company').val() || '[Contractor Company]';
        const contractorAddress = `${$('#contractor_street').val() || '[Street]'}, ${$('#contractor_city').val() || '[City]'}, ${$('#contractor_state').val() || '[State]'} ${$('#contractor_postal').val() || '[Postal]'}`;
        
        const clientCompany = $('#company_name').val();
        const clientContact = $('#contact_person').val();
        const clientName = clientCompany || clientContact || '[Client Name]';
        const clientAddress = `${$('#client_street').val() || '[Street]'}, ${$('#client_city').val() || '[City]'}, ${$('#client_state').val() || '[State]'} ${$('#client_postal').val() || '[Postal]'}`;
        
        const propertyAddress = `${$('#property_street').val() || '[Street]'}, ${$('#property_city').val() || '[City]'}, ${$('#property_state').val() || '[State]'} ${$('#property_postal').val() || '[Postal]'}`;
        
        $('#agreementClausePreview').text(
            `This ${scopeWork} is executed by and between ${contractorName} (${contractorCompany}) with address at ${contractorAddress} hereafter known as "Contractor" and ${clientName} with address at ${clientAddress} hereafter known as "Client".`
        );
            
        $('#serviceClausePreview').text(
            `The Contractor agrees to provide and perform ${scopeWork} for the Client's property with address located at ${propertyAddress}.`
        );
            
        $('#projectPeriodPreview').text(
            "This project shall commence and is scheduled to be completed on the following date periods unless otherwise reasonable delays would arise where such delay or interference is not caused by the Contractor, such as but not limited to cause by third party, inclement weather, fortuitous events, including acts of God:"
        );
    }

    // Add event listeners to all relevant fields for contract preview updates
    const fieldsToWatch = [
        'contractor_name', 'contractor_company', 'contractor_street', 'contractor_city', 'contractor_state', 'contractor_postal',
        'company_name', 'contact_person', 'client_street', 'client_city', 'client_state', 'client_postal',
        'property_street', 'property_city', 'property_state', 'property_postal'
    ];
    
    fieldsToWatch.forEach(id => {
        $(`#${id}`).on('input', updateContractPreview);
    });
    
    $('input[name="scope_of_work[]"]').change(updateContractPreview);
    $('#other_work_text').on('input', updateContractPreview);

    // Auto-fill client address same as property address
    $('#property_street').change(function() {
        if (confirm('Is the client address the same as the property address?')) {
            $('#client_street').val($(this).val());
            $('#client_city').val($('#property_city').val());
            $('#client_state').val($('#property_state').val());
            $('#client_postal').val($('#property_postal').val());
            updateContractPreview();
        }
    });
</script>
@endpush 