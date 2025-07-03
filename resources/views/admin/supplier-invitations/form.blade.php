@extends('layouts.app')

@section('content')
<h1 class="h3 mb-4 text-gray-800 text-center" style="font-weight:700;color:#198754;letter-spacing:0.01em;">{{ isset($invitation) ? 'Edit Supplier Invitation' : 'New Supplier Invitation' }}</h1>
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-white py-3">
                    <!-- Heading moved above -->
                </div>
                <div class="card-body">
                    <form method="POST" 
                          action="{{ isset($invitation) ? route('supplier-invitations.update', $invitation) : route('supplier-invitations.store') }}">
                        @csrf
                        @if(isset($invitation))
                            @method('PUT')
                        @endif

                        <!-- Contract Selection -->
                        <div class="mb-4">
                            <label for="contract_id" class="form-label">Contract (optional)</label>
                            <select name="contract_id" id="contract_id" class="form-control @error('contract_id') is-invalid @enderror">
                                <option value="">Select Contract</option>
                                @foreach($contracts as $contract)
                                    <option value="{{ $contract->id }}" 
                                            {{ old('contract_id', $invitation->contract_id ?? '') == $contract->id ? 'selected' : '' }}>
                                        {{ $contract->contract_number }} - {{ $contract->title }}
                                    </option>
                                @endforeach
                            </select>
                            @error('contract_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Company Information (auto-filled) -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="company_name" class="form-label">Company Name</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="company_name" 
                                           name="company_name" 
                                           value="{{ old('company_name', isset($invitation) ? $invitation->company_name : '') }}" 
                                           readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="contact_name" class="form-label">Contact Person</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="contact_name" 
                                           name="contact_name" 
                                           value="{{ old('contact_name', isset($invitation) ? $invitation->contact_name : '') }}" 
                                           readonly>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Information (auto-filled) -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" 
                                           class="form-control" 
                                           id="email" 
                                           name="email" 
                                           value="{{ old('email', isset($invitation) ? $invitation->email : '') }}" 
                                           readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Phone</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="phone" 
                                           name="phone" 
                                           value="{{ old('phone', isset($invitation) ? $invitation->phone : '') }}" 
                                           readonly>
                                </div>
                            </div>
                        </div>

                        <!-- Materials Selection -->
                        <div class="mb-4">
                            <label class="form-label">Materials</label>
                            <div class="row">
                                @foreach($materials as $material)
                                    <div class="col-md-4">
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" 
                                                   type="checkbox" 
                                                   name="materials[]" 
                                                   value="{{ $material->id }}" 
                                                   id="material_{{ $material->id }}"
                                                   {{ (old('materials') && in_array($material->id, old('materials'))) || 
                                                      (isset($invitation) && $invitation->materials->contains($material->id)) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="material_{{ $material->id }}">
                                                {{ $material->name }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('materials')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Message and Due Date -->
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="message" class="form-label">Message</label>
                                    <textarea class="form-control @error('message') is-invalid @enderror" 
                                              id="message" 
                                              name="message" 
                                              rows="4">{{ old('message', isset($invitation) ? $invitation->message : '') }}</textarea>
                                    @error('message')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="due_date" class="form-label">Due Date</label>
                                    <input type="date" 
                                           class="form-control @error('due_date') is-invalid @enderror" 
                                           id="due_date" 
                                           name="due_date" 
                                           value="{{ old('due_date', isset($invitation) ? $invitation->due_date->format('Y-m-d') : '') }}" 
                                           required>
                                    @error('due_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex justify-content-end mt-4">
                            <a href="{{ route('supplier-invitations.index') }}" class="btn btn-secondary me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                {{ isset($invitation) ? 'Update Invitation' : 'Send Invitation' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const supplierSelect = document.getElementById('supplier_id');
    const companyNameInput = document.getElementById('company_name');
    const contactNameInput = document.getElementById('contact_name');
    const emailInput = document.getElementById('email');
    const phoneInput = document.getElementById('phone');

    supplierSelect.addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        companyNameInput.value = selected.getAttribute('data-company-name') || '';
        contactNameInput.value = selected.getAttribute('data-contact-name') || '';
        emailInput.value = selected.getAttribute('data-email') || '';
        phoneInput.value = selected.getAttribute('data-phone') || '';
    });

    // If editing, trigger change to auto-fill fields
    if (supplierSelect.value) {
        const event = new Event('change');
        supplierSelect.dispatchEvent(event);
    }
});
</script>
@endpush

@push('styles')
<style>
body {
    background: linear-gradient(135deg, #f8fafc 0%, #e9ecef 100%) !important;
}
.card {
    border-radius: 1.25rem;
    box-shadow: 0 4px 24px rgba(44,62,80,0.08), 0 1.5px 6px rgba(44,62,80,0.04);
    border: none;
    margin-bottom: 2rem;
}
.card-header {
    background: #fff;
    border-radius: 1.25rem 1.25rem 0 0;
    border-bottom: none;
    padding: 1.5rem 2rem 1rem 2rem;
}
.form-label {
    color: #198754;
    font-weight: 600;
    font-size: 1.05rem;
    margin-bottom: 0.4rem;
}
.form-control, .form-select {
    border-radius: 1.2rem;
    border: 1px solid #d1d5db;
    background: #f8fafc;
    font-size: 1.08rem;
    padding: 0.85rem 1.1rem;
    transition: border-color 0.2s, box-shadow 0.2s;
}
.form-control:focus, .form-select:focus {
    border-color: #38b6ff;
    box-shadow: 0 0 0 2px #38b6ff33;
    background: #fff;
}
.btn-primary {
    border-radius: 2rem;
    font-weight: 600;
    font-size: 1.1rem;
    background: linear-gradient(90deg, #38b6ff 0%, #2563eb 100%);
    border: none;
    box-shadow: 0 2px 8px #38b6ff33;
    transition: background 0.2s, color 0.2s, box-shadow 0.2s;
}
.btn-primary:hover {
    filter: brightness(1.08);
    box-shadow: 0 4px 16px #38b6ff33;
}
.btn-secondary {
    border-radius: 2rem;
    font-weight: 600;
    font-size: 1.1rem;
    background: #e9ecef;
    color: #495057;
    border: none;
    margin-left: 0.5rem;
    transition: background 0.2s, color 0.2s;
}
.btn-secondary:hover {
    background: #d1d5db;
    color: #222;
}
.form-check-input[type="checkbox"] {
    border-radius: 0.5em;
    border: 1.5px solid #38b6ff;
    width: 1.1em;
    height: 1.1em;
    margin-top: 0.2em;
    margin-right: 0.5em;
    transition: border-color 0.2s, box-shadow 0.2s;
}
.form-check-input:checked {
    background-color: #38b6ff;
    border-color: #2563eb;
}
.form-check-label {
    font-size: 1.01rem;
    color: #222;
}
textarea.form-control {
    min-height: 120px;
}
@media (max-width: 991.98px) {
    .card-header {
        padding: 1rem 0.5rem 0.5rem 0.5rem;
    }
    .card {
        padding: 0.5rem;
    }
}
</style>
@endpush 