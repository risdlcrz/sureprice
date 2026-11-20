@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Suppliers</h4>
                    <div>
                        <a href="{{ route('supplier-invitations.create') }}" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> Invite Supplier
                        </a>
                        <a href="{{ route('suppliers.index') }}" class="btn btn-outline-primary ms-2">
                            <i class="fas fa-eye"></i> View Suppliers
                        </a>
                        <a href="{{ route('supplier-invitations.index') }}" class="btn btn-outline-secondary ms-2">
                            <i class="fas fa-list"></i> View Invitations
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Search and Filters -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="search">Search</label>
                                <input type="text" class="form-control" id="search" 
                                    placeholder="Search by company name, contact person, or email...">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="sort">Sort By</label>
                                <select class="form-control" id="sort">
                                    <option value="company_name">Company Name</option>
                                    <option value="contact_person">Contact Person</option>
                                    <option value="materials_count">Number of Materials</option>
                                    <option value="created_at">Date Added</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="perPage">Per Page</label>
                                <select class="form-control" id="perPage">
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Suppliers Table -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Company Name</th>
                                    <th>Contact Person</th>
                                    <th>Contact Info</th>
                                    <th>Address</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="suppliersTableBody">
                                @forelse($suppliers as $supplier)
                                @php $company = $supplier->company; @endphp
                                <tr>
                                    <td>
                                        <strong>{{ $supplier->company_name }}</strong>
                                    </td>
                                    <td>{{ $supplier->contact_person }}</td>
                                    <td>
                                        <div class="small">
                                            <i class="fas fa-envelope text-muted me-1"></i>{{ $supplier->email }}<br>
                                            <i class="fas fa-phone text-muted me-1"></i>{{ $supplier->phone ?: '—' }}
                                        </div>
                                    </td>
                                    <td class="small">{{ $supplier->address ?: '—' }}</td>
                                    <td>
                                        <x-status-badge :status="$supplier->status" />
                                        @if($company)
                                            <div class="small text-muted mt-1">Account: <x-status-badge :status="$company->status" /></div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex flex-nowrap gap-1 align-items-center">
                                            <a href="{{ route('suppliers.show', $supplier) }}" class="btn btn-sm btn-outline-secondary" title="View supplier">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-sm btn-outline-primary" title="Edit supplier">
                                                <i class="fas fa-pen"></i>
                                            </a>
                                            {{-- Account approval acts on the supplier's own company account (never on a company that merely shares the ID). --}}
                                            @if($company)
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                        Account
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        <li>
                                                            <form action="{{ route('admin.companies.approve', $company) }}" method="POST">
                                                                @csrf
                                                                <button type="submit" class="dropdown-item" @disabled($company->status === 'approved')><i class="fas fa-check text-success me-2"></i>Approve</button>
                                                            </form>
                                                        </li>
                                                        <li>
                                                            <form action="{{ route('admin.companies.update', $company) }}" method="POST">
                                                                @csrf
                                                                @method('PATCH')
                                                                <input type="hidden" name="status" value="pending">
                                                                <button type="submit" class="dropdown-item" @disabled($company->status === 'pending')><i class="fas fa-hourglass-half text-warning me-2"></i>Set to Pending</button>
                                                            </form>
                                                        </li>
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <form action="{{ route('admin.companies.reject', $company) }}" method="POST">
                                                                @csrf
                                                                <input type="hidden" name="rejection_reason" value="Manual admin rejection">
                                                                <button type="submit" class="dropdown-item text-danger" @disabled($company->status === 'rejected')><i class="fas fa-ban me-2"></i>Reject</button>
                                                            </form>
                                                        </li>
                                                    </ul>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <x-empty-row cols="6">No suppliers found.</x-empty-row>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div>
                            Showing {{ $suppliers->firstItem() }} to {{ $suppliers->lastItem() }} of {{ $suppliers->total() }} suppliers
                        </div>
                        {{ $suppliers->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Supplier</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this supplier? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline-block;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Search and filter functionality
    let searchTimeout;
    const search = document.getElementById('search');
    const sort = document.getElementById('sort');
    const perPage = document.getElementById('perPage');

    function updateSuppliers() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            const params = new URLSearchParams({
                search: search.value,
                sort: sort.value,
                per_page: perPage.value
            });

            window.location.href = `${window.location.pathname}?${params.toString()}`;
        }, 500);
    }

    search.addEventListener('input', updateSuppliers);
    sort.addEventListener('change', updateSuppliers);
    perPage.addEventListener('change', updateSuppliers);

    // Set initial values from URL params
    const urlParams = new URLSearchParams(window.location.search);
    search.value = urlParams.get('search') || '';
    sort.value = urlParams.get('sort') || 'company_name';
    perPage.value = urlParams.get('per_page') || '10';

    // Delete supplier functionality
    const deleteModal = document.getElementById('deleteModal');
    const deleteForm = document.getElementById('deleteForm');
    const deleteBtns = document.querySelectorAll('.delete-supplier');

    deleteBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const supplierId = this.dataset.id;
            deleteForm.action = `/suppliers/${supplierId}`;
            $(deleteModal).modal('show');
        });
    });
});
</script>
@endpush

@push('styles')
@vite('resources/css/pages/admin-suppliers-index.css')
@endpush
@endsection 