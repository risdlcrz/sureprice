@extends('layouts.app')

@section('content')
<h1 class="h3 mb-4 text-gray-800 text-center" style="font-weight:700;color:#198754;letter-spacing:0.01em;">
    {{ isset($quotation) ? 'Edit Request for Quotation' : 'Create Request for Quotation' }}
</h1>
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-white py-3">
                    <!-- Heading moved above -->
                </div>
                <div class="card-body">
                    <form id="quotationForm" method="POST" action="{{ isset($quotation) ? route('quotations.update', $quotation->id) : route('quotations.store') }}" enctype="multipart/form-data">
                        @csrf
                        @if(isset($quotation))
                            @method('PUT')
                        @endif

                        <!-- Quotation Type Toggle -->
                        <div class="section-container mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="standaloneToggle" name="is_standalone" value="1" {{ old('is_standalone', isset($quotation) && !$quotation->purchase_request_id ? 'checked' : '') }}>
                                <label class="form-check-label" for="standaloneToggle">Standalone Quotation (not linked to a Purchase Request)</label>
                            </div>
                        </div>

                        <!-- Purchase Request Information -->
                        <div class="section-container" id="purchaseRequestSection">
                            <h5 class="section-title">Purchase Request Information</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="purchase_request_id">Purchase Request</label>
                                        <select class="form-control @error('purchase_request_id') is-invalid @enderror" 
                                            id="purchase_request_id" name="purchase_request_id">
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

                        <!-- Manual Material Selection Section -->
                        <div class="section-container mt-4" id="manualMaterialSection" style="display: none;">
                            <h5 class="section-title">Select Materials</h5>
                            <div class="mb-3">
                                <label for="material_search" class="form-label">Search Materials</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="material_search" placeholder="Search by name or code">
                                    <button class="btn btn-outline-secondary" type="button" id="clear_material_search">Clear</button>
                                </div>
                                <div id="material_search_results" class="list-group mt-2" style="max-height: 200px; overflow-y: auto; display: none;"></div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered" id="selectedMaterialsTable">
                                    <thead>
                                        <tr>
                                            <th>Material</th>
                                            <th>Quantity</th>
                                            <th>Unit</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Materials will be added here dynamically -->
                                        @if(isset($quotation) && !$quotation->purchase_request_id && $quotation->materials->isNotEmpty())
                                            @foreach($quotation->materials as $material)
                                                <tr data-material-id="{{ $material->id }}">
                                                    <td>
                                                        {{ $material->name }}
                                                        <input type="hidden" name="materials[{{ $material->id }}][id]" value="{{ $material->id }}">
                                                    </td>
                                                    <td>
                                                        <input type="number" name="materials[{{ $material->id }}][quantity]" class="form-control form-control-sm" value="{{ $material->pivot->quantity }}" min="0.01" step="0.01" required>
                                                    </td>
                                                    <td>{{ $material->unit }}</td>
                                                    <td>
                                                        <button type="button" class="btn btn-danger btn-sm remove-material">Remove</button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
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
                        <div class="section-container mt-4" id="prMaterialsSection">
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
                                        @if(isset($quotation) && $quotation->purchase_request_id)
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
    @vite(['resources/css/admin/quotations/form.css'])
@endpush

@push('scripts')
    @vite(['resources/js/admin/quotations/form.js'])
@endpush
@endsection 