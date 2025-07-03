@extends('layouts.app')

@section('content')
<h4 class="h3 mb-4 text-gray-800 text-center" style="font-weight:700;color:#198754;letter-spacing:0.01em;">Create New Project</h4>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <!-- Heading moved outside the container -->
                </div>
                <div class="card-body">
                    <form action="{{ route('projects.store') }}" method="POST">
                        @csrf
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="contract_id" class="form-label">Contract</label>
                                <select name="contract_id" id="contract_id" class="form-select @error('contract_id') is-invalid @enderror" required>
                                    <option value="">Select Contract</option>
                                    @foreach($contracts as $contract)
                                        <option value="{{ $contract->id }}" {{ old('contract_id') == $contract->id ? 'selected' : '' }}>
                                            {{ $contract->contract_number }} - {{ $contract->title }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('contract_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="name" class="form-label">Project Name</label>
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name') }}" 
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" 
                                       class="form-control @error('start_date') is-invalid @enderror" 
                                       id="start_date" 
                                       name="start_date" 
                                       value="{{ old('start_date') }}" 
                                       required>
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="date" 
                                       class="form-control @error('end_date') is-invalid @enderror" 
                                       id="end_date" 
                                       name="end_date" 
                                       value="{{ old('end_date') }}" 
                                       required>
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="project_manager_id" class="form-label">Project Manager</label>
                                <select name="project_manager_id" 
                                        id="project_manager_id" 
                                        class="form-select @error('project_manager_id') is-invalid @enderror" 
                                        required>
                                    <option value="">Select Project Manager</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('project_manager_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('project_manager_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="client_representative_id" class="form-label">Client Representative</label>
                                <select name="client_representative_id" 
                                        id="client_representative_id" 
                                        class="form-select @error('client_representative_id') is-invalid @enderror" 
                                        required>
                                    <option value="">Select Client Representative</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('client_representative_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('client_representative_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="budget" class="form-label">Budget</label>
                                <div class="input-group">
                                    <span class="input-group-text">₱</span>
                                    <input type="number" 
                                           class="form-control @error('budget') is-invalid @enderror" 
                                           id="budget" 
                                           name="budget" 
                                           value="{{ old('budget') }}" 
                                           step="0.01" 
                                           min="0" 
                                           required>
                                </div>
                                @error('budget')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" 
                                      name="notes" 
                                      rows="3">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('projects.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Create Project</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Date validation
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');

    startDateInput.addEventListener('change', function() {
        endDateInput.min = this.value;
    });

    endDateInput.addEventListener('change', function() {
        startDateInput.max = this.value;
    });
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
    border-top: none !important;
    box-shadow: 0 4px 24px rgba(44,62,80,0.08), 0 1.5px 6px rgba(44,62,80,0.04);
}
.card-body {
    background: #fff;
    border-radius: 1.1rem;
    padding: 2.5rem 2rem 2rem 2rem;
}
.form-label {
    font-weight: 600;
    color: #198754;
    font-size: 1.08rem;
    margin-bottom: 0.4rem;
}
.form-control, .form-select {
    border-radius: 0.8rem;
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
.input-group-text {
    border-radius: 0.8rem 0 0 0.8rem;
    background: #f1f5f9;
    border: 1px solid #d1d5db;
    font-size: 1.08rem;
}
.btn-primary {
    border-radius: 2rem;
    font-weight: 600;
    font-size: 1.1rem;
    background: linear-gradient(90deg, #38b6ff 0%, #198754 100%);
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
    background: #6c757d;
    color: #fff;
    border: none;
    transition: background 0.2s, color 0.2s;
}
.btn-secondary:hover {
    background: #495057;
    color: #fff;
}
</style>
@endpush
@endsection