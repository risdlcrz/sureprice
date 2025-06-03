@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-white py-3">
                    <h4 class="card-title mb-0">{{ isset($inquiry) ? 'Edit Request for Inquiry' : 'Create Request for Inquiry' }}</h4>
                </div>
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
    .material-result {
        padding: 0.5rem;
        cursor: pointer;
        border-bottom: 1px solid #dee2e6;
    }
    .material-result:hover {
        background-color: #f8f9fa;
    }
</style>
@endpush

@push('scripts')
<script>
    window.materialSearchUrl = "{{ url('/materials/search') }}";

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize form validation
        const form = document.getElementById('inquiryForm');
        if (form) {
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            });
        }

        // Material search functionality
        const materialSearch = document.getElementById('materialSearch');
        const searchMaterialBtn = document.getElementById('searchMaterialBtn');
        const materialSearchResults = document.getElementById('materialSearchResults');
        const selectedMaterials = document.getElementById('selectedMaterials');
        let searchTimeout;

        if (materialSearch && searchMaterialBtn && materialSearchResults) {
            // Show materials when input is focused
            materialSearch.addEventListener('focus', () => {
                searchMaterials();
            });

            materialSearch.addEventListener('input', () => {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(searchMaterials, 300);
            });

            searchMaterialBtn.addEventListener('click', searchMaterials);
        }

        function searchMaterials() {
            const query = materialSearch.value.trim();
            
            materialSearchResults.innerHTML = '<div class="p-2">Loading materials...</div>';
            materialSearchResults.style.display = 'block';

            fetch(`${window.materialSearchUrl}?query=${encodeURIComponent(query)}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.length > 0) {
                    materialSearchResults.innerHTML = data.map(material => `
                        <div class="material-result" data-material='${JSON.stringify(material)}'>
                            <strong>${material.name}</strong><br>
                            <small>${material.description || ''} - ${material.unit}</small>
                        </div>
                    `).join('');

                    // Add click handlers
                    materialSearchResults.querySelectorAll('.material-result').forEach(result => {
                        result.addEventListener('click', () => addMaterial(JSON.parse(result.dataset.material)));
                    });
                } else {
                    materialSearchResults.innerHTML = '<div class="p-2">No materials found</div>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                materialSearchResults.innerHTML = '<div class="p-2 text-danger">Error searching materials</div>';
            });
        }

        function addMaterial(material) {
            // Check if material already exists
            if (document.querySelector(`input[name="materials[${material.id}][id]"]`)) {
                alert('This material is already added');
                return;
            }

            const materialHtml = `
                <div class="material-item card mb-2">
                    <div class="card-body py-2">
                        <div class="row align-items-center">
                            <div class="col-md-4">
                                <strong>${material.name}</strong>
                                <input type="hidden" name="materials[${material.id}][id]" value="${material.id}">
                            </div>
                            <div class="col-md-3">
                                <input type="number" class="form-control form-control-sm" 
                                    name="materials[${material.id}][quantity]" 
                                    placeholder="Quantity" min="1" required>
                            </div>
                            <div class="col-md-4">
                                <input type="text" class="form-control form-control-sm" 
                                    name="materials[${material.id}][notes]" 
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
            `;

            selectedMaterials.insertAdjacentHTML('beforeend', materialHtml);
            materialSearch.value = '';
            materialSearchResults.style.display = 'none';

            // Add remove functionality to the new material
            const newMaterial = selectedMaterials.lastElementChild;
            newMaterial.querySelector('.remove-material').addEventListener('click', function() {
                newMaterial.remove();
            });
        }

        // Add remove functionality to existing materials
        document.querySelectorAll('.remove-material').forEach(button => {
            button.addEventListener('click', function() {
                this.closest('.material-item').remove();
            });
        });

        // Close search results when clicking outside
        document.addEventListener('click', (e) => {
            if (!materialSearchResults.contains(e.target) && e.target !== materialSearch && e.target !== searchMaterialBtn) {
                materialSearchResults.style.display = 'none';
            }
        });

        // Attachment removal function
        window.removeAttachment = function(attachmentId) {
            if (confirm('Are you sure you want to remove this attachment?')) {
                fetch(`/api/inquiries/remove-attachment`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        inquiry_id: '{{ $inquiry->id ?? "" }}',
                        attachment_id: attachmentId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const attachmentElement = document.querySelector(`[onclick="removeAttachment('${attachmentId}')"]`).closest('.attachment-item');
                        attachmentElement.remove();
                    } else {
                        alert('Failed to remove attachment');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error removing attachment');
                });
            }
        };
    });
</script>
@endpush
@endsection