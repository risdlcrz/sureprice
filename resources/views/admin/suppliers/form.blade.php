@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">{{ isset($supplier) ? 'Edit Supplier' : 'Create Supplier' }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ isset($supplier) ? route('suppliers.update', $supplier->id) : route('suppliers.store') }}" 
                          method="POST">
                        @csrf
                        @if(isset($supplier))
                            @method('PUT')
                        @endif

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Contact Name</label>
                                    <input type="text" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name', $supplier->name ?? '') }}" 
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="company_name" class="form-label">Company Name</label>
                                    <input type="text" 
                                           class="form-control @error('company_name') is-invalid @enderror" 
                                           id="company_name" 
                                           name="company_name" 
                                           value="{{ old('company_name', $supplier->company_name ?? '') }}" 
                                           required>
                                    @error('company_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           id="email" 
                                           name="email" 
                                           value="{{ old('email', $supplier->email ?? '') }}" 
                                           required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Phone</label>
                                    <input type="tel" 
                                           class="form-control @error('phone') is-invalid @enderror" 
                                           id="phone" 
                                           name="phone" 
                                           value="{{ old('phone', $supplier->phone ?? '') }}" 
                                           required>
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="street" class="form-label">Street Address</label>
                            <input type="text" 
                                   class="form-control @error('street') is-invalid @enderror" 
                                   id="street" 
                                   name="street" 
                                   value="{{ old('street', $supplier->street ?? '') }}" 
                                   required>
                            @error('street')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="city" class="form-label">City</label>
                                    <input type="text" 
                                           class="form-control @error('city') is-invalid @enderror" 
                                           id="city" 
                                           name="city" 
                                           value="{{ old('city', $supplier->city ?? '') }}" 
                                           required>
                                    @error('city')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="state" class="form-label">State</label>
                                    <input type="text" 
                                           class="form-control @error('state') is-invalid @enderror" 
                                           id="state" 
                                           name="state" 
                                           value="{{ old('state', $supplier->state ?? '') }}" 
                                           required>
                                    @error('state')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="postal" class="form-label">Postal Code</label>
                                    <input type="text" 
                                           class="form-control @error('postal') is-invalid @enderror" 
                                           id="postal" 
                                           name="postal" 
                                           value="{{ old('postal', $supplier->postal ?? '') }}" 
                                           required>
                                    @error('postal')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tax_id" class="form-label">Tax ID</label>
                                    <input type="text" 
                                           class="form-control @error('tax_id') is-invalid @enderror" 
                                           id="tax_id" 
                                           name="tax_id" 
                                           value="{{ old('tax_id', $supplier->tax_id ?? '') }}" 
                                           required>
                                    @error('tax_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select @error('status') is-invalid @enderror" 
                                            id="status" 
                                            name="status" 
                                            required>
                                        <option value="active" {{ (old('status', $supplier->status ?? '') === 'active') ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ (old('status', $supplier->status ?? '') === 'inactive') ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                {{ isset($supplier) ? 'Update Supplier' : 'Create Supplier' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 