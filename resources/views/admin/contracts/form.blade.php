@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ isset($contract) ? 'Edit Contract' : 'Create New Contract' }}</h4>
                </div>
                <div class="card-body">
                    <form id="contractForm" method="POST" action="{{ isset($contract) ? route('contracts.update', $contract->id) : route('contracts.store') }}" enctype="multipart/form-data">
                        @csrf
                        @if(isset($contract))
                            @method('PUT')
                        @endif

                        <!-- Contractor Information Section -->
                        <div class="section-container" id="contractorSection">
                            <h5 class="section-title">Contractor Information</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="contractor_name">Contractor Name</label>
                                        <input type="text" class="form-control" id="contractor_name" name="contractor_name" 
                                            value="{{ old('contractor_name', $contract->contractor_name ?? '') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="contractor_address">Address</label>
                                        <input type="text" class="form-control" id="contractor_address" name="contractor_address" 
                                            value="{{ old('contractor_address', $contract->contractor_address ?? '') }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="contractor_email">Email</label>
                                        <input type="email" class="form-control" id="contractor_email" name="contractor_email" 
                                            value="{{ old('contractor_email', $contract->contractor_email ?? '') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="contractor_phone">Phone Number</label>
                                        <input type="tel" class="form-control" id="contractor_phone" name="contractor_phone" 
                                            value="{{ old('contractor_phone', $contract->contractor_phone ?? '') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="contractor_license">License Number</label>
                                        <input type="text" class="form-control" id="contractor_license" name="contractor_license" 
                                            value="{{ old('contractor_license', $contract->contractor_license ?? '') }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Client Information Section -->
                        <div class="section-container" id="clientSection">
                            <h5 class="section-title">Client Information</h5>
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="client_search">Search Client</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="client_search" placeholder="Search by name, email, or phone...">
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary" type="button" id="searchClientBtn">
                                                    <i class="fas fa-search"></i> Search
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="clientSearchResults" class="mt-2" style="display: none;">
                                        <!-- Search results will be populated here -->
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="client_name">Client Name</label>
                                        <input type="text" class="form-control" id="client_name" name="client_name" 
                                            value="{{ old('client_name', $contract->client_name ?? '') }}" required>
                                        <input type="hidden" id="client_id" name="client_id" 
                                            value="{{ old('client_id', $contract->client_id ?? '') }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="client_address">Address</label>
                                        <input type="text" class="form-control" id="client_address" name="client_address" 
                                            value="{{ old('client_address', $contract->client_address ?? '') }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="client_email">Email</label>
                                        <input type="email" class="form-control" id="client_email" name="client_email" 
                                            value="{{ old('client_email', $contract->client_email ?? '') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="client_phone">Phone Number</label>
                                        <input type="tel" class="form-control" id="client_phone" name="client_phone" 
                                            value="{{ old('client_phone', $contract->client_phone ?? '') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="client_type">Client Type</label>
                                        <select class="form-control" id="client_type" name="client_type" required>
                                            <option value="">Select Type</option>
                                            <option value="individual" {{ (old('client_type', $contract->client_type ?? '') == 'individual') ? 'selected' : '' }}>Individual</option>
                                            <option value="company" {{ (old('client_type', $contract->client_type ?? '') == 'company') ? 'selected' : '' }}>Company</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Property Information Section -->
                        <div class="section-container" id="propertySection">
                            <h5 class="section-title">Property Information</h5>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="property_address">Property Address</label>
                                        <input type="text" class="form-control" id="property_address" name="property_address" 
                                            value="{{ old('property_address', $contract->property_address ?? '') }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="property_city">City</label>
                                        <input type="text" class="form-control" id="property_city" name="property_city" 
                                            value="{{ old('property_city', $contract->property_city ?? '') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="property_state">State</label>
                                        <input type="text" class="form-control" id="property_state" name="property_state" 
                                            value="{{ old('property_state', $contract->property_state ?? '') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="property_zip">ZIP Code</label>
                                        <input type="text" class="form-control" id="property_zip" name="property_zip" 
                                            value="{{ old('property_zip', $contract->property_zip ?? '') }}" required 
                                            pattern="[0-9]{5}(-[0-9]{4})?" title="Five digit zip code">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="property_type">Property Type</label>
                                        <select class="form-control" id="property_type" name="property_type" required>
                                            <option value="">Select Type</option>
                                            <option value="residential" {{ (old('property_type', $contract->property_type ?? '') == 'residential') ? 'selected' : '' }}>Residential</option>
                                            <option value="commercial" {{ (old('property_type', $contract->property_type ?? '') == 'commercial') ? 'selected' : '' }}>Commercial</option>
                                            <option value="industrial" {{ (old('property_type', $contract->property_type ?? '') == 'industrial') ? 'selected' : '' }}>Industrial</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="property_size">Property Size (sq ft)</label>
                                        <input type="number" class="form-control" id="property_size" name="property_size" 
                                            value="{{ old('property_size', $contract->property_size ?? '') }}" min="0" step="1">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Scope of Work Section -->
                        <div class="section-container" id="scopeSection">
                            <h5 class="section-title">Scope of Work</h5>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="project_name">Project Name</label>
                                        <input type="text" class="form-control" id="project_name" name="project_name" 
                                            value="{{ old('project_name', $contract->project_name ?? '') }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="start_date">Start Date</label>
                                        <input type="date" class="form-control" id="start_date" name="start_date" 
                                            value="{{ old('start_date', $contract->start_date ?? '') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="completion_date">Expected Completion Date</label>
                                        <input type="date" class="form-control" id="completion_date" name="completion_date" 
                                            value="{{ old('completion_date', $contract->completion_date ?? '') }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="work_description">Detailed Work Description</label>
                                        <textarea id="work_description" name="work_description" class="form-control" rows="10">{{ old('work_description', $contract->work_description ?? '') }}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="special_instructions">Special Instructions</label>
                                        <textarea class="form-control" id="special_instructions" name="special_instructions" rows="4">{{ old('special_instructions', $contract->special_instructions ?? '') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Items Section -->
                        <div class="section-container" id="itemsSection">
                            <h5 class="section-title">Items and Materials</h5>
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <button type="button" class="btn btn-primary" id="addItemBtn">
                                        <i class="fas fa-plus"></i> Add Item
                                    </button>
                                </div>
                            </div>
                            <div id="itemsList">
                                <!-- Items will be dynamically added here -->
                            </div>
                            <template id="itemTemplate">
                                <div class="item-container mb-4 border p-3 rounded">
                                    <div class="row">
                                        <div class="col-md-11">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Material Search</label>
                                                        <div class="input-group">
                                                            <input type="text" class="form-control material-search" placeholder="Search materials...">
                                                            <div class="input-group-append">
                                                                <button class="btn btn-outline-secondary search-material-btn" type="button">
                                                                    <i class="fas fa-search"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <div class="material-search-results mt-2" style="display: none;"></div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Material Name</label>
                                                        <input type="text" class="form-control material-name" name="items[INDEX][material_name]" required readonly>
                                                        <input type="hidden" class="material-id" name="items[INDEX][material_id]">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Quantity</label>
                                                        <input type="number" class="form-control item-quantity" name="items[INDEX][quantity]" min="1" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Unit</label>
                                                        <input type="text" class="form-control item-unit" name="items[INDEX][unit]" required readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Unit Price</label>
                                                        <input type="number" class="form-control item-price" name="items[INDEX][unit_price]" min="0" step="0.01" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Total</label>
                                                        <input type="text" class="form-control item-total" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label>Preferred Suppliers</label>
                                                        <div class="suppliers-container">
                                                            <!-- Suppliers will be dynamically loaded here -->
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            <button type="button" class="btn btn-danger remove-item-btn mt-4">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- Contract Clauses Section -->
                        <div class="section-container" id="clausesSection">
                            <h5 class="section-title">Contract Clauses</h5>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="payment_terms">Payment Terms</label>
                                        <textarea class="form-control" id="payment_terms" name="payment_terms" rows="4" required>{{ old('payment_terms', $contract->payment_terms ?? "1. Initial deposit of 30% upon contract signing\n2. 40% upon completion of 50% of work\n3. Remaining 30% upon final inspection and completion") }}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="warranty_terms">Warranty Terms</label>
                                        <textarea class="form-control" id="warranty_terms" name="warranty_terms" rows="4" required>{{ old('warranty_terms', $contract->warranty_terms ?? "1. Workmanship warranty for 1 year from completion date\n2. Materials warranty as per manufacturer specifications\n3. Warranty excludes damage from misuse or natural disasters") }}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="cancellation_terms">Cancellation Terms</label>
                                        <textarea class="form-control" id="cancellation_terms" name="cancellation_terms" rows="4" required>{{ old('cancellation_terms', $contract->cancellation_terms ?? "1. Client may cancel within 3 business days for full refund\n2. Cancellation after materials ordered subject to 25% fee\n3. Contractor may terminate if client breaches payment terms") }}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="additional_terms">Additional Terms and Conditions</label>
                                        <textarea class="form-control" id="additional_terms" name="additional_terms" rows="6">{{ old('additional_terms', $contract->additional_terms ?? '') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Signature Section -->
                        <div class="section-container" id="signatureSection">
                            <h5 class="section-title">Signatures</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Contractor Signature</label>
                                        <div class="signature-pad-container">
                                            <canvas id="contractorSignature" class="signature-pad"></canvas>
                                            <input type="hidden" name="contractor_signature" id="contractorSignatureData">
                                        </div>
                                        <div class="mt-2">
                                            <button type="button" class="btn btn-sm btn-secondary clear-signature" data-pad="contractor">Clear</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Client Signature</label>
                                        <div class="signature-pad-container">
                                            <canvas id="clientSignature" class="signature-pad"></canvas>
                                            <input type="hidden" name="client_signature" id="clientSignatureData">
                                        </div>
                                        <div class="mt-2">
                                            <button type="button" class="btn btn-sm btn-secondary clear-signature" data-pad="client">Clear</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="contractor_name_signed">Contractor Name (Print)</label>
                                        <input type="text" class="form-control" id="contractor_name_signed" name="contractor_name_signed" 
                                            value="{{ old('contractor_name_signed', $contract->contractor_name ?? '') }}" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="contractor_date_signed">Date Signed</label>
                                        <input type="date" class="form-control" id="contractor_date_signed" name="contractor_date_signed" 
                                            value="{{ old('contractor_date_signed', $contract->contractor_date_signed ?? date('Y-m-d')) }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="client_name_signed">Client Name (Print)</label>
                                        <input type="text" class="form-control" id="client_name_signed" name="client_name_signed" 
                                            value="{{ old('client_name_signed', $contract->client_name ?? '') }}" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="client_date_signed">Date Signed</label>
                                        <input type="date" class="form-control" id="client_date_signed" name="client_date_signed" 
                                            value="{{ old('client_date_signed', $contract->client_date_signed ?? date('Y-m-d')) }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary">Save Contract</button>
                            <a href="{{ route('contracts.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .section-container {
        margin-bottom: 2rem;
        padding: 1.5rem;
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
    }
    .section-title {
        margin-bottom: 1.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #007bff;
    }
    .form-group {
        margin-bottom: 1rem;
    }
    /* Rich text editor styles */
    .tox-tinymce {
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
    }
    .material-search-results {
        position: absolute;
        width: 100%;
        z-index: 1000;
        background: white;
        border: 1px solid #ddd;
        border-radius: 4px;
        max-height: 200px;
        overflow-y: auto;
    }
    .material-result {
        padding: 8px 12px;
        cursor: pointer;
    }
    .material-result:hover {
        background-color: #f8f9fa;
    }
    .suppliers-container {
        border: 1px solid #ddd;
        padding: 10px;
        border-radius: 4px;
        max-height: 150px;
        overflow-y: auto;
    }
    .supplier-checkbox {
        margin-right: 10px;
    }
    .signature-pad-container {
        border: 1px solid #dee2e6;
        border-radius: 4px;
        background-color: #fff;
    }
    .signature-pad {
        width: 100%;
        height: 200px;
        border-radius: 4px;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize form validation
    const form = document.getElementById('contractForm');
    form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        form.classList.add('was-validated');
    });

    // Client search functionality
    const clientSearchInput = document.getElementById('client_search');
    const clientSearchResults = document.getElementById('clientSearchResults');
    const searchClientBtn = document.getElementById('searchClientBtn');
    let searchTimeout;

    function performClientSearch() {
        const searchTerm = clientSearchInput.value.trim();
        if (searchTerm.length < 2) {
            clientSearchResults.style.display = 'none';
            return;
        }

        fetch(`{{ route('clients.search') }}?query=${encodeURIComponent(searchTerm)}`)
            .then(response => response.json())
            .then(data => {
                if (data.length > 0) {
                    const resultsHtml = data.map(client => `
                        <div class="client-result p-2 border-bottom" style="cursor: pointer;" 
                             onclick="selectClient(${JSON.stringify(client).replace(/"/g, '&quot;')})">
                            <strong>${client.name}</strong><br>
                            <small>${client.email} | ${client.phone}</small>
                        </div>
                    `).join('');
                    
                    clientSearchResults.innerHTML = `
                        <div class="card">
                            <div class="card-body p-0">
                                ${resultsHtml}
                            </div>
                        </div>
                    `;
                    clientSearchResults.style.display = 'block';
                } else {
                    clientSearchResults.innerHTML = '<div class="alert alert-info">No clients found</div>';
                    clientSearchResults.style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Error searching clients:', error);
                clientSearchResults.innerHTML = '<div class="alert alert-danger">Error searching clients</div>';
                clientSearchResults.style.display = 'block';
            });
    }

    function selectClient(client) {
        document.getElementById('client_id').value = client.id;
        document.getElementById('client_name').value = client.name;
        document.getElementById('client_email').value = client.email;
        document.getElementById('client_phone').value = client.phone;
        document.getElementById('client_address').value = client.address;
        document.getElementById('client_type').value = client.type || 'individual';
        
        clientSearchResults.style.display = 'none';
        clientSearchInput.value = '';
    }

    clientSearchInput.addEventListener('input', () => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(performClientSearch, 300);
    });

    searchClientBtn.addEventListener('click', performClientSearch);

    // Close search results when clicking outside
    document.addEventListener('click', (e) => {
        if (!clientSearchResults.contains(e.target) && e.target !== clientSearchInput && e.target !== searchClientBtn) {
            clientSearchResults.style.display = 'none';
        }
    });

    // Initialize TinyMCE
    tinymce.init({
        selector: '#work_description',
        height: 300,
        menubar: false,
        plugins: [
            'advlist autolink lists link image charmap print preview anchor',
            'searchreplace visualblocks code fullscreen',
            'insertdatetime media table paste code help wordcount'
        ],
        toolbar: 'undo redo | formatselect | ' +
            'bold italic backcolor | alignleft aligncenter ' +
            'alignright alignjustify | bullist numlist outdent indent | ' +
            'removeformat | help',
        content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; font-size: 14px; }'
    });

    // Date validation
    const startDateInput = document.getElementById('start_date');
    const completionDateInput = document.getElementById('completion_date');

    startDateInput.addEventListener('change', validateDates);
    completionDateInput.addEventListener('change', validateDates);

    function validateDates() {
        const startDate = new Date(startDateInput.value);
        const completionDate = new Date(completionDateInput.value);
        
        if (completionDate < startDate) {
            completionDateInput.setCustomValidity('Completion date must be after start date');
        } else {
            completionDateInput.setCustomValidity('');
        }
    }

    // Items Management
    const itemsList = document.getElementById('itemsList');
    const itemTemplate = document.getElementById('itemTemplate');
    const addItemBtn = document.getElementById('addItemBtn');
    let itemCount = 0;

    function addItem() {
        const newItem = document.importNode(itemTemplate.content, true);
        
        // Replace INDEX placeholder with actual index
        newItem.querySelectorAll('[name*="INDEX"]').forEach(element => {
            element.name = element.name.replace('INDEX', itemCount);
        });

        // Add event listeners for the new item
        setupItemEventListeners(newItem);
        
        itemsList.appendChild(newItem);
        itemCount++;
    }

    function setupItemEventListeners(itemElement) {
        const container = itemElement.querySelector('.item-container');
        const searchInput = container.querySelector('.material-search');
        const searchBtn = container.querySelector('.search-material-btn');
        const searchResults = container.querySelector('.material-search-results');
        const quantityInput = container.querySelector('.item-quantity');
        const priceInput = container.querySelector('.item-price');
        const totalInput = container.querySelector('.item-total');
        const removeBtn = container.querySelector('.remove-item-btn');
        let searchTimeout;

        // Material search
        function performMaterialSearch() {
            const searchTerm = searchInput.value.trim();
            if (searchTerm.length < 2) {
                searchResults.style.display = 'none';
                return;
            }

            fetch(`{{ route('materials.search') }}?query=${encodeURIComponent(searchTerm)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.length > 0) {
                        const resultsHtml = data.map(material => `
                            <div class="material-result" data-material='${JSON.stringify(material)}'>
                                <strong>${material.name}</strong><br>
                                <small>${material.description || ''}</small>
                            </div>
                        `).join('');
                        searchResults.innerHTML = resultsHtml;
                        searchResults.style.display = 'block';

                        // Add click handlers for results
                        searchResults.querySelectorAll('.material-result').forEach(result => {
                            result.addEventListener('click', () => selectMaterial(result, container));
                        });
                    } else {
                        searchResults.innerHTML = '<div class="p-2">No materials found</div>';
                        searchResults.style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('Error searching materials:', error);
                    searchResults.innerHTML = '<div class="p-2 text-danger">Error searching materials</div>';
                    searchResults.style.display = 'block';
                });
        }

        function selectMaterial(resultElement, itemContainer) {
            const material = JSON.parse(resultElement.dataset.material);
            
            itemContainer.querySelector('.material-id').value = material.id;
            itemContainer.querySelector('.material-name').value = material.name;
            itemContainer.querySelector('.item-unit').value = material.unit;
            
            searchResults.style.display = 'none';
            searchInput.value = '';

            // Load suppliers for this material
            loadSuppliers(material.id, itemContainer);
        }

        function loadSuppliers(materialId, itemContainer) {
            const suppliersContainer = itemContainer.querySelector('.suppliers-container');
            
            fetch(`/materials/${materialId}/suppliers`)
                .then(response => response.json())
                .then(suppliers => {
                    if (suppliers.length > 0) {
                        const suppliersHtml = suppliers.map(supplier => `
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input supplier-checkbox" 
                                    name="items[${itemCount}][suppliers][]" 
                                    value="${supplier.id}" 
                                    id="supplier_${itemCount}_${supplier.id}">
                                <label class="form-check-label" for="supplier_${itemCount}_${supplier.id}">
                                    ${supplier.name} - ${supplier.price_range || 'Price not available'}
                                </label>
                            </div>
                        `).join('');
                        suppliersContainer.innerHTML = suppliersHtml;
                    } else {
                        suppliersContainer.innerHTML = '<p class="text-muted">No suppliers available for this material</p>';
                    }
                })
                .catch(error => {
                    console.error('Error loading suppliers:', error);
                    suppliersContainer.innerHTML = '<p class="text-danger">Error loading suppliers</p>';
                });
        }

        function calculateTotal() {
            const quantity = parseFloat(quantityInput.value) || 0;
            const price = parseFloat(priceInput.value) || 0;
            totalInput.value = (quantity * price).toFixed(2);
        }

        // Event listeners
        searchInput.addEventListener('input', () => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(performMaterialSearch, 300);
        });

        searchBtn.addEventListener('click', performMaterialSearch);

        quantityInput.addEventListener('input', calculateTotal);
        priceInput.addEventListener('input', calculateTotal);

        removeBtn.addEventListener('click', () => {
            container.remove();
        });

        // Close search results when clicking outside
        document.addEventListener('click', (e) => {
            if (!searchResults.contains(e.target) && e.target !== searchInput && e.target !== searchBtn) {
                searchResults.style.display = 'none';
            }
        });
    }

    addItemBtn.addEventListener('click', addItem);

    // Add initial item if none exists
    if (itemCount === 0) {
        addItem();
    }

    // Initialize TinyMCE for contract clauses
    ['payment_terms', 'warranty_terms', 'cancellation_terms', 'additional_terms'].forEach(id => {
        tinymce.init({
            selector: `#${id}`,
            height: 200,
            menubar: false,
            plugins: [
                'lists link paste help wordcount'
            ],
            toolbar: 'undo redo | formatselect | bold italic | ' +
                    'bullist numlist | removeformat | help',
            content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; font-size: 14px; }'
        });
    });

    // Initialize signature pads
    const contractorPad = new SignaturePad(document.getElementById('contractorSignature'), {
        backgroundColor: 'rgb(255, 255, 255)',
        penColor: 'rgb(0, 0, 0)'
    });

    const clientPad = new SignaturePad(document.getElementById('clientSignature'), {
        backgroundColor: 'rgb(255, 255, 255)',
        penColor: 'rgb(0, 0, 0)'
    });

    // Clear signature buttons
    document.querySelectorAll('.clear-signature').forEach(button => {
        button.addEventListener('click', () => {
            const padType = button.dataset.pad;
            if (padType === 'contractor') {
                contractorPad.clear();
                document.getElementById('contractorSignatureData').value = '';
            } else {
                clientPad.clear();
                document.getElementById('clientSignatureData').value = '';
            }
        });
    });

    // Save signatures on form submit
    document.getElementById('contractForm').addEventListener('submit', function(e) {
        if (!contractorPad.isEmpty()) {
            document.getElementById('contractorSignatureData').value = contractorPad.toDataURL();
        }
        if (!clientPad.isEmpty()) {
            document.getElementById('clientSignatureData').value = clientPad.toDataURL();
        }
    });

    // Resize signature pads on window resize
    function resizeSignaturePads() {
        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        ['contractorSignature', 'clientSignature'].forEach(id => {
            const canvas = document.getElementById(id);
            const container = canvas.parentElement;
            
            canvas.width = container.offsetWidth * ratio;
            canvas.height = container.offsetHeight * ratio;
            canvas.getContext("2d").scale(ratio, ratio);
            canvas.style.width = "100%";
            canvas.style.height = "200px";
        });
    }

    window.addEventListener('resize', resizeSignaturePads);
    resizeSignaturePads();
});
</script>
@endpush
@endsection 