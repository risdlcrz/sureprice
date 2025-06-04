@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Suppliers</h4>
                    <div>
                        <button type="button" class="btn btn-secondary mr-2" data-toggle="modal" data-target="#importModal">
                            <i class="fas fa-file-import"></i> Import Suppliers
                        </button>
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addSupplierModal">
                            <i class="fas fa-plus"></i> Add New Supplier
                        </button>
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
                                    <th>Materials</th>
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
                                            <i class="fas fa-phone"></i> {{ $supplier->phone }}
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            {{ $supplier->city }}, {{ $supplier->state }}<br>
                                            <small class="text-muted">{{ $supplier->address }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-secondary text-dark">
                                            {{ $supplier->materials_count ?? 0 }} materials
                                        </span>
                                        @if($supplier->materials && $supplier->materials->count() > 0)
                                            <div class="mt-1">
                                                <small class="text-muted">
                                                    {{ $supplier->materials->take(3)->pluck('name')->implode(', ') }}
                                                    @if($supplier->materials->count() > 3)
                                                        +{{ $supplier->materials->count() - 3 }} more
                                                    @endif
                                                </small>
                                            </div>
                                            <div class="mt-1">
                                                <small class="text-muted">
                                                    Top categories: 
                                                    {{ $supplier->materials->groupBy('category.name')->take(2)->map(function($items, $cat) {
                                                        return $cat;
                                                    })->implode(', ') }}
                                                </small>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('admin.suppliers.show', $supplier->id) }}" 
                                                class="btn btn-sm btn-info" 
                                                title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.suppliers.edit', $supplier->id) }}" 
                                                class="btn btn-sm btn-primary" 
                                                title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" 
                                                class="btn btn-sm btn-danger delete-supplier" 
                                                data-id="{{ $supplier->id }}"
                                                title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
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

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import Suppliers</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.suppliers.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="import_file">Select Excel File</label>
                        <input type="file" class="form-control-file" id="import_file" name="import_file" required>
                        <small class="form-text text-muted">
                            Please download the template file first to ensure correct formatting.
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="{{ route('admin.suppliers.template') }}" class="btn btn-secondary">
                        <i class="fas fa-download"></i> Download Template
                    </a>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Import</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Supplier Modal -->
<div class="modal fade" id="addSupplierModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Supplier</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.suppliers.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="company_name">Company Name</label>
                        <input type="text" class="form-control" id="company_name" name="company_name" required>
                    </div>
                    <div class="form-group">
                        <label for="contact_person">Contact Person</label>
                        <input type="text" class="form-control" id="contact_person" name="contact_person" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="tel" class="form-control" id="phone" name="phone" required>
                    </div>
                    <div class="form-group">
                        <label for="address">Address</label>
                        <textarea class="form-control" id="address" name="address" rows="2" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="city">City</label>
                                <input type="text" class="form-control" id="city" name="city" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="state">State</label>
                                <input type="text" class="form-control" id="state" name="state" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Supplier</button>
                </div>
            </form>
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
            deleteForm.action = `/admin/suppliers/${supplierId}`;
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