@extends('layouts.app')

@section('content')
<div class="content">
    <div class="page-header">
        <h1 class="page-title">Information Management</h1>
    </div>

    <div class="top-controls d-flex justify-content-between align-items-center flex-wrap mb-4 gap-3">
        <!-- Type Filter -->
        <div class="btn-group" role="group">
            <a href="{{ route('information-management.index', ['type' => 'employee'] + request()->except('type')) }}" 
               class="btn {{ $type === 'employee' ? 'btn-primary' : 'btn-outline-secondary' }}">
                <i class="fas fa-users me-1"></i> Employees
            </a>
            <a href="{{ route('information-management.index', ['type' => 'company'] + request()->except('type')) }}" 
               class="btn {{ $type === 'company' ? 'btn-primary' : 'btn-outline-secondary' }}">
                <i class="fas fa-building me-1"></i> Companies
            </a>
        </div>

        <!-- Role Filter Buttons (Only show for employees) -->
        @if($type === 'employee')
        <div class="filter-buttons">
            <a href="{{ route('information-management.index', ['role' => 'all'] + request()->except('role')) }}" 
               class="btn {{ $role === 'all' ? 'btn-primary' : 'btn-outline-secondary' }}">
                <i class="fas fa-th-list me-1"></i> All
            </a>
            <a href="{{ route('information-management.index', ['role' => 'procurement'] + request()->except('role')) }}" 
               class="btn {{ $role === 'procurement' ? 'btn-primary' : 'btn-outline-secondary' }}">
                <i class="fas fa-box me-1"></i> Procurement
            </a>
            <a href="{{ route('information-management.index', ['role' => 'warehousing'] + request()->except('role')) }}" 
               class="btn {{ $role === 'warehousing' ? 'btn-primary' : 'btn-outline-secondary' }}">
                <i class="fas fa-warehouse me-1"></i> Warehousing
            </a>
        </div>
        @endif

        <!-- Right side controls -->
        <div class="right-controls d-flex align-items-center flex-wrap gap-3">
            <!-- Search -->
            <form method="GET" action="{{ route('information-management.index') }}" class="search-form d-flex">
                <input type="hidden" name="type" value="{{ $type }}">
                <input type="hidden" name="role" value="{{ $role }}">
                <input type="search" class="form-control form-control-sm" name="search" 
                       placeholder="Search..." value="{{ $search }}">
                <button class="btn btn-sm btn-primary ms-2" type="submit">
                    <i class="fas fa-search"></i>
                </button>
            </form>

            <!-- Add Button -->
            @if($type === 'employee')
            <div class="d-flex gap-2">
                <a href="{{ route('information-management.create') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-user-plus me-1"></i> Add Employee
                </a>
                <form action="{{ route('information-management.import') }}" method="POST" enctype="multipart/form-data" class="d-inline">
                    @csrf
                    <div class="btn-group">
                        <label class="btn btn-sm btn-success" title="Upload CSV">
                            <i class="fas fa-file-csv me-1"></i> Import CSV
                            <input type="file" name="csv_file" class="d-none" accept=".csv" onchange="this.form.submit()">
                        </label>
                        <a href="{{ route('information-management.template') }}" class="btn btn-sm btn-outline-success" title="Download Template">
                            <i class="fas fa-download"></i>
                        </a>
                    </div>
                </form>
            </div>
            @endif
        </div>
    </div>

    <div class="data-card">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        @if($type === 'employee')
                            <th>Role</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Username</th>
                            <th>Email</th>
                        @else
                            <th>Company Name</th>
                            <th>Designation</th>
                            <th>Contact Person</th>
                            <th>Email</th>
                            <th>Mobile</th>
                            <th>Status</th>
                        @endif
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        <tr>
                            @if($type === 'employee')
                                <td>
                                    <span class="role-badge role-{{ strtolower($item->role) }}">
                                        {{ ucfirst($item->role) }}
                                    </span>
                                </td>
                                <td>{{ $item->first_name }}</td>
                                <td>{{ $item->last_name }}</td>
                                <td>{{ $item->username }}</td>
                                <td>{{ $item->email }}</td>
                                <td class="actions">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="#" class="btn btn-sm btn-info" 
                                           data-bs-toggle="modal" 
                                           data-bs-target="#viewModal{{ $item->id }}"
                                           title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('information-management.edit', $item->id) }}" 
                                           class="btn btn-sm btn-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('information-management.destroy', $item->id) }}" 
                                              method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" 
                                                    onclick="return confirm('Are you sure you want to delete this employee?')" 
                                                    title="Delete">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            @else
                                <td>{{ $item->company_name }}</td>
                                <td>
                                    <span class="badge bg-{{ $item->designation === 'client' ? 'info' : 'primary' }}">
                                        <i class="fas fa-{{ $item->designation === 'client' ? 'user-tie' : 'truck' }} me-1"></i>
                                        {{ ucfirst($item->designation) }}
                                    </span>
                                </td>
                                <td>{{ $item->contact_person }}</td>
                                <td>{{ $item->email }}</td>
                                <td>{{ $item->mobile_number }}</td>
                                <td>
                                    <span class="badge bg-{{ $item->status === 'approved' ? 'success' : ($item->status === 'pending' ? 'warning' : 'danger') }}">
                                        {{ ucfirst($item->status) }}
                                    </span>
                                </td>
                                <td class="actions">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('admin.companies.show', $item->id) }}" 
                                           class="btn btn-sm btn-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($item->status === 'pending')
                                        <form action="{{ route('admin.companies.approve', $item->id) }}" 
                                              method="POST" 
                                              class="d-inline">
                                            @csrf
                                            <button type="submit" 
                                                    class="btn btn-sm btn-success" 
                                                    title="Approve"
                                                    onclick="return confirm('Are you sure you want to approve this company?')">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                        <button type="button" 
                                                class="btn btn-sm btn-danger" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#rejectModal{{ $item->id }}" 
                                                title="Reject">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $type === 'employee' ? 6 : 6 }}" class="text-center">
                                No records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {{ $items->links() }}
    </div>
</div>

@if($type === 'employee')
    @foreach($items as $item)
        <!-- View Employee Modal -->
        <div class="modal fade" id="viewModal{{ $item->id }}" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Employee Information</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <h6 class="text-primary">Personal Information</h6>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <tr>
                                                <th width="40%">Full Name</th>
                                                <td>{{ $item->first_name }} {{ $item->last_name }}</td>
                                            </tr>
                                            <tr>
                                                <th>Username</th>
                                                <td>{{ $item->username }}</td>
                                            </tr>
                                            <tr>
                                                <th>Email</th>
                                                <td>{{ $item->email }}</td>
                                            </tr>
                                            <tr>
                                                <th>Role</th>
                                                <td>
                                                    <span class="role-badge role-{{ strtolower($item->role) }}">
                                                        {{ ucfirst($item->role) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <h6 class="text-primary">Activity Information</h6>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <tr>
                                                <th width="40%">Last Login</th>
                                                <td>{{ $item->last_login_at ? $item->last_login_at->diffForHumans() : 'Never' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Email Verified</th>
                                                <td>
                                                    @if($item->email_verified_at)
                                                        <span class="text-success">
                                                            <i class="fas fa-check-circle"></i> Verified
                                                        </span>
                                                    @else
                                                        <span class="text-danger">
                                                            <i class="fas fa-times-circle"></i> Not Verified
                                                        </span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Created At</th>
                                                <td>{{ $item->created_at->format('M d, Y H:i A') }}</td>
                                            </tr>
                                            <tr>
                                                <th>Updated At</th>
                                                <td>{{ $item->updated_at->format('M d, Y H:i A') }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endif

@if($type === 'company')
    @foreach($items as $company)
        @if($company->status === 'pending')
            <!-- Reject Modal -->
            <div class="modal fade" id="rejectModal{{ $company->id }}" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Reject Company Registration</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form action="{{ route('admin.companies.reject', $company->id) }}" method="POST">
                            @csrf
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="rejection_reason" class="form-label">Reason for Rejection</label>
                                    <textarea class="form-control" name="rejection_reason" required></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-danger">Reject</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
@endif

@push('styles')
<style>
.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: none;
    margin-bottom: 1rem;
}

.role-badge {
    padding: 0.25em 0.5em;
    border-radius: 0.25rem;
    font-size: 0.875em;
    font-weight: 600;
}

.role-procurement {
    background-color: #0d6efd;
    color: white;
}

.role-warehousing {
    background-color: #198754;
    color: white;
}

.modal-body .table th {
    background-color: #f8f9fa;
}
</style>
@endpush
@endsection