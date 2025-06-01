@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-white py-3">
                    <h4 class="card-title mb-0">{{ isset($invitation) ? 'Edit Supplier Invitation' : 'Send Supplier Invitation' }}</h4>
                </div>
                <div class="card-body">
                    <form id="invitationForm" method="POST" action="{{ isset($invitation) ? route('invitations.update', $invitation->id) : route('invitations.store') }}">
                        @csrf
                        @if(isset($invitation))
                            @method('PUT')
                        @endif

                        <!-- Company Information -->
                        <div class="section-container">
                            <h5 class="section-title">Company Information</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="company_name">Company Name</label>
                                        <input type="text" class="form-control @error('company_name') is-invalid @enderror" 
                                            id="company_name" name="company_name" 
                                            value="{{ old('company_name', $invitation->company_name ?? '') }}" required>
                                        @error('company_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="business_type">Business Type</label>
                                        <select class="form-control @error('business_type') is-invalid @enderror" 
                                            id="business_type" name="business_type" required>
                                            <option value="">Select Business Type</option>
                                            <option value="corporation" {{ old('business_type', $invitation->business_type ?? '') == 'corporation' ? 'selected' : '' }}>Corporation</option>
                                            <option value="partnership" {{ old('business_type', $invitation->business_type ?? '') == 'partnership' ? 'selected' : '' }}>Partnership</option>
                                            <option value="sole_proprietorship" {{ old('business_type', $invitation->business_type ?? '') == 'sole_proprietorship' ? 'selected' : '' }}>Sole Proprietorship</option>
                                            <option value="other" {{ old('business_type', $invitation->business_type ?? '') == 'other' ? 'selected' : '' }}>Other</option>
                                        </select>
                                        @error('business_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="section-container mt-4">
                            <h5 class="section-title">Contact Information</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="contact_person">Contact Person</label>
                                        <input type="text" class="form-control @error('contact_person') is-invalid @enderror" 
                                            id="contact_person" name="contact_person" 
                                            value="{{ old('contact_person', $invitation->contact_person ?? '') }}" required>
                                        @error('contact_person')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="position">Position</label>
                                        <input type="text" class="form-control @error('position') is-invalid @enderror" 
                                            id="position" name="position" 
                                            value="{{ old('position', $invitation->position ?? '') }}" required>
                                        @error('position')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email">Email Address</label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                            id="email" name="email" 
                                            value="{{ old('email', $invitation->email ?? '') }}" required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="phone">Phone Number</label>
                                        <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                            id="phone" name="phone" 
                                            value="{{ old('phone', $invitation->phone ?? '') }}" required>
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Material Categories -->
                        <div class="section-container mt-4">
                            <h5 class="section-title">Material Categories</h5>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Select Categories</label>
                                        <div class="category-checkboxes">
                                            @foreach($categories as $category)
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" 
                                                    id="category_{{ $category->id }}" 
                                                    name="categories[]" 
                                                    value="{{ $category->id }}"
                                                    {{ in_array($category->id, old('categories', $invitation->categories ?? [])) ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="category_{{ $category->id }}">
                                                    {{ $category->name }}
                                                </label>
                                            </div>
                                            @endforeach
                                        </div>
                                        @error('categories')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
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
                                        <label for="notes">Notes</label>
                                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                                            id="notes" name="notes" rows="4">{{ old('notes', $invitation->notes ?? '') }}</textarea>
                                        @error('notes')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary">Send Invitation</button>
                            <a href="{{ route('invitations.index') }}" class="btn btn-secondary">Cancel</a>
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
    .category-checkboxes {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 1rem;
    }
    .custom-control {
        padding-left: 2rem;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize form validation
    const form = document.getElementById('invitationForm');
    if (form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');

            // Check if at least one category is selected
            const categories = document.querySelectorAll('input[name="categories[]"]:checked');
            if (categories.length === 0) {
                event.preventDefault();
                alert('Please select at least one material category');
            }
        });
    }
});
</script>
@endpush
@endsection 