@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">Edit My Information</h1>
    <form method="POST" action="{{ route('supplier.profile.update') }}">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="company_name" class="form-label">Company Name</label>
            <input type="text" class="form-control" id="company_name" name="company_name" value="{{ old('company_name', $supplier->company_name) }}" required>
        </div>
        <div class="mb-3">
            <label for="contact_person" class="form-label">Contact Person</label>
            <input type="text" class="form-control" id="contact_person" name="contact_person" value="{{ old('contact_person', $supplier->contact_person) }}" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $supplier->email) }}" required>
        </div>
        <div class="mb-3">
            <label for="phone" class="form-label">Phone</label>
            <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $supplier->phone) }}" required>
        </div>
        <div class="mb-3">
            <label for="address" class="form-label">Address</label>
            <input type="text" class="form-control" id="address" name="address" value="{{ old('address', $supplier->address) }}" required>
        </div>
        <div class="mb-3">
            <label for="tax_number" class="form-label">Tax Number</label>
            <input type="text" class="form-control" id="tax_number" name="tax_number" value="{{ old('tax_number', $supplier->tax_number) }}">
        </div>
        <div class="mb-3">
            <label for="registration_number" class="form-label">Registration Number</label>
            <input type="text" class="form-control" id="registration_number" name="registration_number" value="{{ old('registration_number', $supplier->registration_number) }}">
        </div>
        <button type="submit" class="btn btn-primary">Submit for Approval</button>
        <a href="{{ route('supplier.dashboard') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection 