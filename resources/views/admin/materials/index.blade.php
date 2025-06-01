@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Materials</h4>
                    <a href="{{ route('materials.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add New Material
                    </a>
                </div>
                <div class="card-body">
                    <!-- Search and Filters -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="search">Search</label>
                                <input type="text" class="form-control" id="search" placeholder="Search materials...">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="category">Category</label>
                                <select class="form-control" id="category">
                                    <option value="">All Categories</option>
                                    <option value="construction">Construction</option>
                                    <option value="electrical">Electrical</option>
                                    <option value="plumbing">Plumbing</option>
                                    <option value="finishing">Finishing</option>
                                    <option value="tools">Tools</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="sort">Sort By</label>
                                <select class="form-control" id="sort">
                                    <option value="name">Name</option>
                                    <option value="code">Code</option>
                                    <option value="price">Price</option>
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

                    <!-- Materials Table -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Unit</th>
                                    <th>Base Price</th>
                                    <th>Suppliers</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="materialsTableBody">
                                @foreach($materials as $material)
                                <tr>
                                    <td>{{ $material->code }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($material->images && count($material->images) > 0)
                                                <img src="{{ Storage::url($material->images[0]) }}" 
                                                    alt="{{ $material->name }}" 
                                                    class="img-thumbnail mr-2" 
                                                    style="width: 50px; height: 50px; object-fit: cover;">
                                            @endif
                                            <div>
                                                <strong>{{ $material->name }}</strong>
                                                @if($material->description)
                                                    <br>
                                                    <small class="text-muted">{{ Str::limit($material->description, 50) }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ ucfirst($material->category) }}</td>
                                    <td>{{ $material->unit }}</td>
                                    <td>₱{{ number_format($material->base_price, 2) }}</td>
                                    <td>
                                        <span class="badge badge-info">
                                            {{ $material->suppliers_count ?? 0 }} suppliers
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('materials.show', $material->id) }}" 
                                                class="btn btn-sm btn-info" 
                                                title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('materials.edit', $material->id) }}" 
                                                class="btn btn-sm btn-primary" 
                                                title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" 
                                                class="btn btn-sm btn-danger delete-material" 
                                                data-id="{{ $material->id }}"
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
                            Showing {{ $materials->firstItem() }} to {{ $materials->lastItem() }} of {{ $materials->total() }} materials
                        </div>
                        {{ $materials->links() }}
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
                <h5 class="modal-title">Delete Material</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this material? This action cannot be undone.</p>
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
    const category = document.getElementById('category');
    const sort = document.getElementById('sort');
    const perPage = document.getElementById('perPage');

    function updateMaterials() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            const params = new URLSearchParams({
                search: search.value,
                category: category.value,
                sort: sort.value,
                per_page: perPage.value
            });

            window.location.href = `${window.location.pathname}?${params.toString()}`;
        }, 500);
    }

    search.addEventListener('input', updateMaterials);
    category.addEventListener('change', updateMaterials);
    sort.addEventListener('change', updateMaterials);
    perPage.addEventListener('change', updateMaterials);

    // Set initial values from URL params
    const urlParams = new URLSearchParams(window.location.search);
    search.value = urlParams.get('search') || '';
    category.value = urlParams.get('category') || '';
    sort.value = urlParams.get('sort') || 'name';
    perPage.value = urlParams.get('per_page') || '10';

    // Delete material functionality
    const deleteModal = document.getElementById('deleteModal');
    const deleteForm = document.getElementById('deleteForm');
    const deleteBtns = document.querySelectorAll('.delete-material');

    deleteBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const materialId = this.dataset.id;
            deleteForm.action = `/materials/${materialId}`;
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
</style>
@endpush
@endsection 