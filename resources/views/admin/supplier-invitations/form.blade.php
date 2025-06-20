@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-white py-3">
                    <h4 class="card-title mb-0">{{ isset($invitation) ? 'Edit Supplier Invitation' : 'New Supplier Invitation' }}</h4>
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