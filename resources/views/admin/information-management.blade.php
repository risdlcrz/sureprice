@extends('layouts.app')

@section('content')
<div class="page-content">
    <div class="page-header">
        <h1 class="h3 mb-4 text-center page-title">Information Management</h1>
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
            <a href="{{ route('information-management.index', ['role' => 'contractor'] + request()->except('role')) }}" 
               class="btn {{ $role === 'contractor' ? 'btn-primary' : 'btn-outline-secondary' }}">
                <i class="fas fa-user-tie me-1"></i> Contractor
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
                
                <!-- New Import CSV button to trigger modal -->
                <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#importCsvModal" title="Import CSV">
                    <i class="fas fa-file-csv me-1"></i> Import CSV
                </button>
                
                <!-- Original Download Template button -->
                <a href="{{ route('information-management.template') }}" class="btn btn-sm btn-outline-success" title="Download Template">
                    <i class="fas fa-download"></i>
                </a>

            </div>
            @endif
        </div>
    </div>

    @include('admin.information-management.import-modal')

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
                                        <button type="button" class="btn btn-sm btn-info" 
                                           data-bs-toggle="modal" 
                                           data-bs-target="#viewModal{{ $item->id }}"
                                           title="View">
                                            <i class="fas fa-eye"></i>
                                        </button>
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
                                    <span class="role-badge role-{{ strtolower($item->designation) }}">
                                        {{ ucfirst($item->designation) }}
                                    </span>
                                </td>
                                <td>{{ $item->contact_person }}</td>
                                <td>{{ $item->email }}</td>
                                <td>{{ $item->mobile_number }}</td>
                                <td>
                                    <x-status-badge :status="$item->status" />
                                </td>
                                <td class="actions">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('admin.companies.show', $item->id) }}"
                                           class="btn btn-sm btn-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($item->status === 'pending')
                                            <!-- Approve Button triggers modal -->
                                            <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#approveModal{{ $item->id }}" title="Approve">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <x-confirm-modal id="approveModal{{ $item->id }}" title="Approve {{ $item->company_name }}"
                                                :action="route('admin.companies.approve', $item->id)" confirm="Approve" tone="success">
                                                Are you sure you want to approve this company?
                                            </x-confirm-modal>

                                            <!-- Reject Button triggers modal -->
                                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $item->id }}" title="Reject">
                                                <i class="fas fa-times"></i>
                                            </button>
                                            <x-confirm-modal id="rejectModal{{ $item->id }}" title="Reject {{ $item->company_name }}"
                                                :action="route('admin.companies.reject', $item->id)" confirm="Reject" tone="danger">
                                                Are you sure you want to reject this company?
                                            </x-confirm-modal>
                                        @endif
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <x-empty-row :cols="$type === 'employee' ? 6 : 7">No records found.</x-empty-row>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {{ $items->appends(['type' => $type, 'role' => $role, 'search' => $search])->links() }}
    </div>
</div>

@if($type === 'employee')
    @foreach($items as $item)
        @include('admin.information-management.employee-modal')
    @endforeach
@endif

@if($type === 'company')
    @foreach($items as $company)
        @include('admin.information-management.company-modal')
    @endforeach
@endif

@push('styles')
@vite('resources/css/pages/admin-information-management.css')
@endpush
@endsection