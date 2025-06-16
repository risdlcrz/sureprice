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
                                @foreach($suppliers as $supplier)
                                <tr>
                                    <td>
                                        <strong>{{ $supplier->company_name }}</strong>
                                    </td>
                                    <td>{{ $supplier->contact_person }}</td>
                                    <td>
                                        <div>
                                            <i class="fas fa-envelope"></i> {{ $supplier->email }}<br>
                                            <i class="fas fa-phone"></i> {{ $supplier->mobile_number }}
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            {{ $supplier->city }}, {{ $supplier->state }}<br>
                                            <small class="text-muted">{{ $supplier->street }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $supplier->status === 'approved' ? 'success' : ($supplier->status === 'pending' ? 'warning' : 'danger') }}">
                                            {{ ucfirst($supplier->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('admin.companies.show', $supplier->id) }}" 
                                                class="btn btn-sm btn-info" 
                                                title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
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
<style>
    .table td {
        vertical-align: middle;
    }
    .badge {
        font-size: 0.9em;
    }
    .btn-group .btn {
        margin-right: 2px;
    }
    .fas {
        width: 16px;
        text-align: center;
        margin-right: 5px;
    }
</style>
@endpush
@endsection 