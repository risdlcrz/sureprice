@extends('layouts.app')

@section('content')
<h1 class="h3 mb-4 text-gray-800 text-center" style="font-weight:700;color:#198754;letter-spacing:0.01em;">{{ isset($inquiry) ? 'Edit Request for Inquiry' : 'Create Request for Inquiry' }}</h1>
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-white py-3"></div>
                <div class="card-body">
                    <form id="inquiryForm" method="POST" action="{{ isset($inquiry) ? route('inquiries.update', $inquiry->id) : route('inquiries.store') }}" enctype="multipart/form-data">
                        @csrf
                        @if(isset($inquiry))
                            @method('PUT')
                        @endif

                        <!-- Project Information -->
                        <div class="section-container">
                            <h5 class="section-title">Project Information</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="contract_id">Contract</label>
                                        <select class="form-control @error('contract_id') is-invalid @enderror" 
                                            id="contract_id" name="contract_id" required>
                                            <option value="">Select Contract</option>
                                            @foreach($contracts as $contract)
                                                <option value="{{ $contract->id }}" 
                                                    {{ old('contract_id', $inquiry->contract_id ?? '') == $contract->id ? 'selected' : '' }}>
                                                    {{ $contract->contract_id }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('contract_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="priority">Priority Level</label>
                                        <select class="form-control @error('priority') is-invalid @enderror" 
                                            id="priority" name="priority" required>
                                            <option value="low" {{ old('priority', $inquiry->priority ?? '') == 'low' ? 'selected' : '' }}>Low</option>
                                            <option value="medium" {{ old('priority', $inquiry->priority ?? '') == 'medium' ? 'selected' : '' }}>Medium</option>
                                            <option value="high" {{ old('priority', $inquiry->priority ?? '') == 'high' ? 'selected' : '' }}>High</option>
                                            <option value="urgent" {{ old('priority', $inquiry->priority ?? '') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                                        </select>
                                        @error('priority')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Inquiry Details -->
                        <div class="section-container mt-4">
                            <h5 class="section-title">Inquiry Details</h5>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="subject">Subject</label>
                                        <input type="text" class="form-control @error('subject') is-invalid @enderror" 
                                            id="subject" name="subject" 
                                            value="{{ old('subject', $inquiry->subject ?? '') }}" required>
                                        @error('subject')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="description">Description</label>
                                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                            id="description" name="description" rows="4" required>{{ old('description', $inquiry->description ?? '') }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="required_date">Required Date</label>
                                        <input type="date" class="form-control @error('required_date') is-invalid @enderror" 
                                            id="required_date" name="required_date" 
                                            value="{{ old('required_date', $inquiry->required_date ?? '') }}" required>
                                        @error('required_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="department">Department</label>
                                        <input type="text" class="form-control @error('department') is-invalid @enderror" 
                                            id="department" name="department" 
                                            value="{{ old('department', $inquiry->department ?? '') }}" required>
                                        @error('department')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Materials Needed -->
                        <div class="section-container mt-4">
                            <h5 class="section-title">Materials Needed</h5>
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Search and Add Materials</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="materialSearch" 
                                                placeholder="Search for materials...">
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary" type="button" id="searchMaterialBtn">
                                                    <i class="fas fa-search"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div id="materialSearchResults" class="mt-2" style="display: none;"></div>
                                    </div>
                                </div>
                            </div>

                            <div id="selectedMaterials">
                                @if(isset($inquiry) && $inquiry->materials)
                                    @foreach($inquiry->materials as $material)
                                    <div class="material-item card mb-2">
                                        <div class="card-body py-2">
                                            <div class="row align-items-center">
                                                <div class="col-md-4">
                                                    <strong>{{ $material->name }}</strong>
                                                    <input type="hidden" name="materials[{{ $material->id }}][id]" value="{{ $material->id }}">
                                                </div>
                                                <div class="col-md-3">
                                                    <input type="number" class="form-control form-control-sm" 
                                                        name="materials[{{ $material->id }}][quantity]" 
                                                        value="{{ $material->pivot->quantity }}" 
                                                        placeholder="Quantity" min="1" required>
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="text" class="form-control form-control-sm" 
                                                        name="materials[{{ $material->id }}][notes]" 
                                                        value="{{ $material->pivot->notes }}" 
                                                        placeholder="Specifications/Notes">
                                                </div>
                                                <div class="col-md-1">
                                                    <button type="button" class="btn btn-danger btn-sm remove-material">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                @endif
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
                                        @error('attachments')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            @if(isset($inquiry) && $inquiry->attachments)
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <div class="existing-attachments">
                                        @foreach($inquiry->attachments as $attachment)
                                        <div class="attachment-item d-inline-block position-relative m-2">
                                            <div class="card">
                                                <div class="card-body p-2">
                                                    <i class="fas fa-file mr-2"></i>
                                                    <span>{{ $attachment->original_name }}</span>
                                                    <button type="button" class="btn btn-danger btn-sm ml-2"
                                                        onclick="removeAttachment('{{ $attachment->id }}')">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary">Submit Inquiry</button>
                            <a href="{{ route('inquiries.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
@vite(['resources/css/admin-inquiries-form.css'])
@endpush

@push('scripts')
@vite(['resources/js/admin-inquiries-form.js'])
@endpush
@endsection