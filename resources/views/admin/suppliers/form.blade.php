@extends('layouts.app')

{{-- Edit a supplier's details. New suppliers are added through Supplier Invitations, not here. --}}
@section('content')
<div class="container-fluid">
    <x-page-header title="Edit Supplier" icon="truck" :subtitle="$supplier->company_name">
        <a href="{{ route('suppliers.show', $supplier) }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Supplier
        </a>
    </x-page-header>

    <div class="row g-4">
        <div class="col-lg-8">
            <form method="POST" action="{{ route('suppliers.update', $supplier) }}" class="card">
                @csrf
                @method('PUT')
                <div class="card-header">Supplier Information</div>
                <div class="card-body">
                    <div class="row">
                        <x-field col="md-6" name="company_name" label="Company Name" :value="$supplier->company_name" required maxlength="255" />
                        <x-field col="md-6" name="contact_person" label="Contact Person" :value="$supplier->contact_person" required maxlength="255" />
                        <x-field col="md-6" name="email" label="Email" type="email" :value="$supplier->email" required maxlength="255" />
                        <x-field col="md-6" name="phone" label="Phone" :value="$supplier->phone" required maxlength="20" />
                        <x-field col="12" name="address" label="Address" :value="$supplier->address" maxlength="255" />
                        <x-field col="md-6" name="tax_number" label="Tax Number (TIN)" :value="$supplier->tax_number" maxlength="50" />
                        <x-field col="md-6" name="registration_number" label="Registration Number" :value="$supplier->registration_number" maxlength="50" />
                        <x-field col="md-6" name="status" label="Supplier Status" required>
                            <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
                                @foreach(['active' => 'Active', 'inactive' => 'Inactive', 'pending' => 'Pending'] as $value => $label)
                                    <option value="{{ $value }}" @selected(old('status', $supplier->status) === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </x-field>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-end gap-2">
                    <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Save Changes</button>
                </div>
            </form>
        </div>

        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">Company Account</div>
                <div class="card-body">
                    @if($supplier->company)
                        <p class="mb-2">Account status: <x-status-badge :status="$supplier->company->status" /></p>
                        <p class="text-muted small mb-3">Approval and documents are managed on the company page.</p>
                        <a href="{{ route('admin.companies.show', $supplier->company) }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-building me-1"></i> Open Company Details
                        </a>
                    @else
                        <p class="text-muted small mb-0">This supplier has no linked company account.</p>
                    @endif
                </div>
            </div>
            <div class="card">
                <div class="card-header">Materials &amp; Prices</div>
                <div class="card-body">
                    <p class="text-muted small mb-0">
                        This supplier offers {{ $supplier->materials()->count() }} material(s). Prices and material links
                        are managed in Material Management and are not changed by this form.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
