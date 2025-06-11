@extends('layouts.app')

@section('content')
<div class="content">
    <div class="page-header">
        <h1 class="page-title">Edit Employee</h1>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('information-management.update', $employee->id) }}" class="needs-validation" novalidate>
                @csrf
                @method('PUT')

                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="first_name" class="form-label">First Name</label>
                            <input type="text" class="form-control @error('first_name') is-invalid @enderror" 
                                   id="first_name" name="first_name" value="{{ old('first_name', $employee->first_name) }}" required>
                            @error('first_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="last_name" class="form-label">Last Name</label>
                            <input type="text" class="form-control @error('last_name') is-invalid @enderror" 
                                   id="last_name" name="last_name" value="{{ old('last_name', $employee->last_name) }}" required>
                            @error('last_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email', $employee->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control @error('username') is-invalid @enderror" 
                                   id="username" name="username" value="{{ old('username', $employee->username) }}" required>
                            @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="role" class="form-label">Role</label>
                            <select class="form-select @error('role') is-invalid @enderror" 
                                    id="role" name="role" required>
                                <option value="">Select Role</option>
                                <option value="procurement" {{ old('role', $employee->role) == 'procurement' ? 'selected' : '' }}>Procurement</option>
                                <option value="warehousing" {{ old('role', $employee->role) == 'warehousing' ? 'selected' : '' }}>Warehousing</option>
                                <option value="contractor" {{ old('role', $employee->role) == 'contractor' ? 'selected' : '' }}>Contractor</option>
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div id="contractor-fields" style="display: none;">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="company_name" class="form-label">Company Name (Optional)</label>
                                <input type="text" class="form-control" id="company_name" name="company_name" value="{{ old('company_name', $employee->company_name) }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $employee->phone) }}">
                            </div>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="street" class="form-label">Street Address</label>
                                <input type="text" class="form-control" id="street" name="street" value="{{ old('street', $employee->street) }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="barangay" class="form-label">Barangay</label>
                                <input type="text" class="form-control" id="barangay" name="barangay" value="{{ old('barangay', $employee->barangay) }}">
                            </div>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="city" class="form-label">City/Municipality</label>
                                <input type="text" class="form-control" id="city" name="city" value="{{ old('city', $employee->city) }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="state" class="form-label">Province/State</label>
                                <input type="text" class="form-control" id="state" name="state" value="{{ old('state', $employee->state) }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="postal" class="form-label">Postal Code</label>
                                <input type="text" class="form-control" id="postal" name="postal" value="{{ old('postal', $employee->postal) }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('information-management.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Employee
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.getElementById('role');
    const contractorFields = document.getElementById('contractor-fields');
    function toggleContractorFields() {
        if (roleSelect.value === 'contractor') {
            contractorFields.style.display = '';
            // Make contractor fields required
            ['street','barangay','city','state','postal','phone'].forEach(id => {
                document.getElementById(id).setAttribute('required', 'required');
            });
        } else {
            contractorFields.style.display = 'none';
            // Remove required from contractor fields
            ['street','barangay','city','state','postal','phone'].forEach(id => {
                document.getElementById(id).removeAttribute('required');
            });
        }
    }
    roleSelect.addEventListener('change', toggleContractorFields);
    toggleContractorFields(); // Initial
});
</script>
@endpush 