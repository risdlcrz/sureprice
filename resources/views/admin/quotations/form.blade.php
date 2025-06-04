@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-white py-3">
                    <h4 class="card-title mb-0">{{ isset($quotation) ? 'Edit Request for Quotation' : 'Create Request for Quotation' }}</h4>
                </div>
                <div class="card-body">
                    <form id="quotationForm" method="POST" action="{{ isset($quotation) ? route('quotations.update', $quotation->id) : route('quotations.store') }}" enctype="multipart/form-data">
                        @csrf
                        @if(isset($quotation))
                            @method('PUT')
                        @endif

                        <!-- Project Information -->
                        <div class="section-container">
                            <h5 class="section-title">Purchase Request Information</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="purchase_request_id">Purchase Request</label>
                                        <select class="form-control @error('purchase_request_id') is-invalid @enderror" 
                                            id="purchase_request_id" name="purchase_request_id" required>
                                            <option value="">Select Purchase Request</option>
                                            @foreach($purchaseRequests as $pr)
                                                <option value="{{ $pr->id }}" 
                                                    {{ old('purchase_request_id', $quotation->purchase_request_id ?? '') == $pr->id ? 'selected' : '' }}
                                                    data-materials='@json($pr->items)'>
                                                    PR-{{ $pr->id }} ({{ $pr->department }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('purchase_request_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="due_date">Due Date</label>
                                        <input type="date" class="form-control @error('due_date') is-invalid @enderror" 
                                            id="due_date" name="due_date" 
                                            value="{{ old('due_date', $quotation->due_date ?? '') }}" required>
                                        @error('due_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Supplier Selection -->
                        <div class="section-container mt-4">
                            <h5 class="section-title">Supplier Selection</h5>
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="suppliers">Suppliers</label>
                                        <select class="form-control" id="suppliers" name="suppliers[]" multiple required>
                                            @foreach($suppliers ?? [] as $supplier)
                                                <option value="{{ $supplier->id }}"
                                                    @if(isset($quotation) && $quotation->suppliers->contains($supplier->id)) selected @endif>
                                                    {{ $supplier->company_name ?? $supplier->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div id="selectedSuppliersNotes">
                                @if(isset($quotation) && $quotation->suppliers)
                                    @foreach($quotation->suppliers as $supplier)
                                    <div class="supplier-notes-item card mb-2" data-supplier-id="{{ $supplier->id }}">
                                        <div class="card-body py-2">
                                            <div class="row align-items-center">
                                                <div class="col-md-6">
                                                    <strong>{{ $supplier->company_name }}</strong>
                                                    <input type="hidden" name="suppliers[]" value="{{ $supplier->id }}">
                                                    <br>
                                                    <small class="text-muted">{{ $supplier->email }} | {{ $supplier->phone }}</small>
                                                </div>
                                                <div class="col-md-6">
                                                    <input type="text" class="form-control form-control-sm" 
                                                        name="supplier_notes[{{ $supplier->id }}]" 
                                                        value="{{ $supplier->pivot->notes ?? '' }}" 
                                                        placeholder="Notes for this supplier">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>

                        <!-- Materials from Purchase Request -->
                        <div class="section-container mt-4">
                            <h5 class="section-title">Materials from Purchase Request</h5>
                            <div id="prMaterials" class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Material</th>
                                            <th>Quantity</th>
                                            <th>Unit</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(isset($quotation))
                                            @foreach($quotation->purchaseRequest->items as $item)
                                            <tr>
                                                <td>{{ $item->material->name }}</td>
                                                <td>{{ $item->quantity }}</td>
                                                <td>{{ $item->material->unit }}</td>
                                                <td>
                                                    @if(isset($item->total_amount))
                                                        {{ $item->total_amount }}
                                                    @elseif(isset($item->quantity) && isset($item->estimated_unit_price))
                                                        {{ number_format($item->quantity * $item->estimated_unit_price, 2) }}
                                                    @else
                                                        &mdash;
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Terms and Conditions -->
                        <div class="section-container mt-4">
                            <h5 class="section-title">Terms and Conditions</h5>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="payment_terms">Payment Terms</label>
                                        <input type="text" class="form-control @error('payment_terms') is-invalid @enderror" 
                                            id="payment_terms" name="payment_terms" 
                                            value="{{ old('payment_terms', $quotation->payment_terms ?? '') }}"
                                            placeholder="e.g., Net 30, COD">
                                        @error('payment_terms')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                            </div>
                                        </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="delivery_terms">Delivery Terms</label>
                                        <input type="text" class="form-control @error('delivery_terms') is-invalid @enderror" 
                                            id="delivery_terms" name="delivery_terms" 
                                            value="{{ old('delivery_terms', $quotation->delivery_terms ?? '') }}"
                                            placeholder="e.g., FOB Destination">
                                        @error('delivery_terms')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="validity_period">Validity Period</label>
                                        <input type="text" class="form-control @error('validity_period') is-invalid @enderror" 
                                            id="validity_period" name="validity_period" 
                                            value="{{ old('validity_period', $quotation->validity_period ?? '') }}"
                                            placeholder="e.g., 30 days">
                                        @error('validity_period')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Information -->
                        <div class="section-container mt-4">
                            <h5 class="section-title">Additional Information</h5>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="notes">Notes and Special Instructions</label>
                                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                                            id="notes" name="notes" rows="4"
                                            placeholder="Enter any additional notes or special instructions for suppliers">{{ old('notes', $quotation->notes ?? '') }}</textarea>
                                        @error('notes')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Attachments -->
                        <div class="section-container mt-4">
                            <h5 class="section-title">Attachments</h5>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="attachments">Upload Files</label>
                                        <input type="file" class="form-control-file @error('attachments') is-invalid @enderror" 
                                            id="attachments" name="attachments[]" multiple>
                                        <small class="form-text text-muted">
                                            You can upload multiple files. Maximum size per file: 10MB.
                                            Supported formats: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG
                                        </small>
                                        @error('attachments')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            @if(isset($quotation) && $quotation->attachments->count() > 0)
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <h6>Current Attachments</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>File Name</th>
                                                    <th>Size</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                        @foreach($quotation->attachments as $attachment)
                                                <tr>
                                                    <td>{{ $attachment->original_name }}</td>
                                                    <td>{{ $attachment->formatted_size }}</td>
                                                    <td>
                                                        <div class="btn-group">
                                                            <a href="{{ route('quotations.attachment.download', $attachment->id) }}" 
                                                                class="btn btn-sm btn-info">
                                                                <i class="fas fa-download"></i>
                                                            </a>
                                                            <button type="button" 
                                                                class="btn btn-sm btn-danger remove-attachment"
                                                                data-quotation="{{ $quotation->id }}"
                                                                data-attachment="{{ $attachment->id }}">
                                                                <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                                    </td>
                                                </tr>
                                        @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>

                        <!-- Add after the supplier selection, only if editing and status is approved or being approved -->
                        @if(isset($quotation) && ($quotation->status === 'approved' || $quotation->status === 'responded'))
                            <div class="mb-3">
                                <label for="awarded_supplier_id" class="form-label">Awarded Supplier</label>
                                <select name="awarded_supplier_id" id="awarded_supplier_id" class="form-select">
                                    <option value="">Select supplier</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" @if(old('awarded_supplier_id', $quotation->awarded_supplier_id ?? null) == $supplier->id) selected @endif>
                                            {{ $supplier->company_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="awarded_amount" class="form-label">Awarded Amount</label>
                                <input type="number" step="0.01" name="awarded_amount" id="awarded_amount" class="form-control" value="{{ old('awarded_amount', $quotation->awarded_amount ?? '') }}">
                            </div>
                        @endif

                        <!-- Form Actions -->
                        <div class="form-actions mt-4">
                            <button type="submit" class="btn btn-primary">
                                {{ isset($quotation) ? 'Update' : 'Create' }} RFQ
                            </button>
                            <a href="{{ route('quotations.index') }}" class="btn btn-secondary">Cancel</a>
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
        background-color: #fff;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    .section-title {
        margin-bottom: 1.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #007bff;
        color: #2c3e50;
        font-weight: 600;
    }
    #supplierSearchResults,
    #materialSearchResults {
        position: absolute;
        width: 100%;
        max-height: 200px;
        overflow-y: auto;
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
        z-index: 1000;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .supplier-result,
    .material-result {
        padding: 0.5rem;
        cursor: pointer;
        border-bottom: 1px solid #dee2e6;
    }
    .supplier-result:hover,
    .material-result:hover {
        background-color: #f8f9fa;
    }
</style>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    $('#suppliers').select2({
        placeholder: 'Select suppliers',
        allowClear: true,
        width: '100%'
    });
    // Show notes field for each selected supplier
    $('#suppliers').on('change', function() {
        const selected = $(this).val() || [];
        const allSuppliers = @json($suppliers);
        const notesDiv = $('#selectedSuppliersNotes');
        notesDiv.html('');
        selected.forEach(function(supplierId) {
            const supplier = allSuppliers.find(s => s.id == supplierId);
            if (!supplier) return;
            notesDiv.append(`
                <div class="supplier-notes-item card mb-2" data-supplier-id="${supplier.id}">
                <div class="card-body py-2">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                                <strong>${supplier.company_name ?? supplier.name}</strong>
                            <input type="hidden" name="suppliers[]" value="${supplier.id}">
                            <br>
                                <small class="text-muted">${supplier.email} | ${supplier.phone ?? ''}</small>
                        </div>
                            <div class="col-md-6">
                            <input type="text" class="form-control form-control-sm" 
                                name="supplier_notes[${supplier.id}]" 
                                placeholder="Notes for this supplier">
                        </div>
                        </div>
                    </div>
                </div>
            `);
        });
    });
    // Trigger change on page load if editing
    $('#suppliers').trigger('change');
});

document.addEventListener('DOMContentLoaded', function() {
    // Handle Purchase Request Selection
    const prSelect = document.getElementById('purchase_request_id');
    const prMaterialsTable = document.querySelector('#prMaterials tbody');

    prSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const materials = JSON.parse(selectedOption.dataset.materials || '[]');
        
        prMaterialsTable.innerHTML = materials.map(item => `
            <tr>
                <td>${item.material.name}</td>
                <td>${item.quantity}</td>
                <td>${item.material.unit}</td>
                <td>
                    ${typeof item.total_amount !== 'undefined' ? item.total_amount : (typeof item.quantity !== 'undefined' && typeof item.estimated_unit_price !== 'undefined' ? (item.quantity * item.estimated_unit_price).toFixed(2) : '')}
                </td>
            </tr>
        `).join('');
    });

    // Remove supplier search input event handler and debounce
    // Only use the button click handler below

    // Add supplier when selected from search results (handled in button click handler)
    // Remove supplier
    const selectedSuppliers = document.getElementById('selectedSuppliers');
    selectedSuppliers.addEventListener('click', function(e) {
        if (e.target.closest('.remove-supplier')) {
            e.target.closest('.supplier-item').remove();
        }
    });

    // Remove attachment
    document.querySelectorAll('.remove-attachment').forEach(btn => {
        btn.addEventListener('click', function() {
            if (!confirm('Are you sure you want to remove this attachment?')) return;

            const quotationId = this.dataset.quotation;
            const attachmentId = this.dataset.attachment;

            fetch('/api/quotations/remove-attachment', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    quotation_id: quotationId,
                    attachment_id: attachmentId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.closest('tr').remove();
                } else {
                    alert('Failed to remove attachment');
                }
            });
        });
    });
});

document.getElementById('searchSupplierBtn').addEventListener('click', function() {
    const query = document.getElementById('supplierSearch').value;
    fetch(`/api/suppliers/search?q=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(data => {
            const resultsDiv = document.getElementById('supplierSearchResults');
            resultsDiv.innerHTML = '';
            if (data.length === 0) {
                resultsDiv.innerHTML = '<div class="alert alert-warning">No suppliers found.</div>';
            } else {
                data.forEach(supplier => {
                    const div = document.createElement('div');
                    div.className = 'list-group-item list-group-item-action';
                    div.style.cursor = 'pointer';
                    div.innerHTML = `<strong>${supplier.company_name}</strong> <br>
                        <small>${supplier.email} | ${supplier.phone || ''}</small>`;
                    div.onclick = function() {
                        addSupplierToList(supplier);
                        resultsDiv.style.display = 'none';
                    };
                    resultsDiv.appendChild(div);
                });
            }
            resultsDiv.style.display = 'block';
        });
});

function addSupplierToList(supplier) {
    // Prevent duplicates
    if (document.querySelector(`#selectedSuppliers input[value='${supplier.id}']`)) return;
    const container = document.getElementById('selectedSuppliers');
    const card = document.createElement('div');
    card.className = 'supplier-item card mb-2';
    card.innerHTML = `
        <div class="card-body py-2">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <strong>${supplier.company_name}</strong>
                    <input type="hidden" name="suppliers[]" value="${supplier.id}">
                    <br>
                    <small class="text-muted">${supplier.email} | ${supplier.phone || ''}</small>
                </div>
                <div class="col-md-5">
                    <input type="text" class="form-control form-control-sm"
                        name="supplier_notes[${supplier.id}]"
                        placeholder="Notes for this supplier">
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger btn-sm remove-supplier">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>
    `;
    card.querySelector('.remove-supplier').onclick = function() {
        card.remove();
    };
    container.appendChild(card);
}
</script>
@endpush
@endsection 