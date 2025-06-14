@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Materials</h4>
                    <div>
                        <button type="button" class="btn btn-info me-2" data-bs-toggle="modal" data-bs-target="#bulkSrpModal">
                            <i class="fas fa-tags"></i> Set SRP Prices
                        </button>
                    <a href="{{ route('materials.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add New Material
                    </a>
                    </div>
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
                                    <option value="construction-materials">Construction Materials</option>
                                    <option value="electrical-supplies">Electrical Supplies</option>
                                    <option value="plumbing-materials">Plumbing Materials</option>
                                    <option value="windows-and-doors">Windows and Doors</option>
                                    <option value="tools-and-hardware">Tools and Hardware</option>
                                    <option value="paint-and-coatings">Paint and Coatings</option>
                                    <option value="safety-equipment">Safety Equipment</option>
                                    <option value="insulation-materials">Insulation Materials</option>
                                    <option value="structural-materials">Structural Materials</option>
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
                        <div class="col-md-12 mt-2">
                            <a href="{{ route('materials.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Clear Filters
                            </a>
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
                                    <th>SRP</th>
                                    <th>Suppliers</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="materialsTableBody">
                                @foreach($materials as $material)
                                <tr>
                                    <td>{{ $material->code }}</td>
                                    <td>
                                        <div class="d-flex align-items-center flex-md-row flex-column">
                                            @if($material->images && count($material->images) > 0)
                                                <img src="{{ asset('storage/' . $material->images[0]->path) }}"
                                                     alt="{{ $material->name }}"
                                                     class="img-thumbnail mr-2 mb-2 mb-md-0"
                                                     style="width: 150px; height: 150px; object-fit: contain; background: #fff; border: 2px solid #e0e0e0;">
                                            @endif
                                            <div class="text-center text-md-start">
                                                <strong>{{ $material->name }}</strong>
                                                @if($material->description)
                                                    <br>
                                                    <small class="text-muted">{{ Str::limit($material->description, 50) }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if(($material->category->name ?? '') === 'Other' && !empty($material->custom_category))
                                            {{ $material->custom_category }}
                                        @else
                                            {{ $material->category->name ?? '' }}
                                        @endif
                                    </td>
                                    <td>{{ $material->unit }}</td>
                                    <td>₱{{ number_format($material->base_price, 2) }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            ₱{{ number_format($material->srp_price, 2) }}
                                            @php
                                                $markup = $material->base_price > 0 
                                                    ? (($material->srp_price - $material->base_price) / $material->base_price * 100) 
                                                    : 0;
                                            @endphp
                                            <small class="ms-2 text-muted">({{ number_format($markup, 1) }}%)</small>
                                        </div>
                                    </td>
                                    <td>
                                        <button type="button" 
                                            class="btn btn-sm btn-outline-info view-suppliers" 
                                            data-material-id="{{ $material->id }}"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#supplierPricesModal">
                                            <span class="badge bg-secondary">
                                                {{ $material->suppliers->count() }}
                                        </span>
                                            View Suppliers
                                        </button>
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

<!-- Bulk SRP Update Modal -->
<div class="modal fade" id="bulkSrpModal" tabindex="-1" aria-labelledby="bulkSrpModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bulkSrpModalLabel">Set SRP Prices</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Add search field -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <div class="input-group">
                            <input type="text" class="form-control" id="srpSearchInput" placeholder="Search materials...">
                            <button class="btn btn-outline-secondary" type="button" id="clearSrpSearch">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Unit</th>
                                <th>Base Price</th>
                                <th>SRP Price</th>
                                <th>Markup %</th>
                            </tr>
                        </thead>
                        <tbody id="srpTableBody">
                            <!-- Will be populated via JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveSrpPrices">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<!-- Supplier Prices Modal -->
<div class="modal fade" id="supplierPricesModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Supplier Prices</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Supplier</th>
                                <th>Price</th>
                                <th>Lead Time</th>
                                <th>Last Updated</th>
                                <th>Variance</th>
                            </tr>
                        </thead>
                        <tbody id="supplierPricesBody">
                            <!-- Will be populated via JavaScript -->
                        </tbody>
                    </table>
                </div>
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

    // Load all materials when opening the SRP modal
    const bulkSrpModal = document.getElementById('bulkSrpModal');
    bulkSrpModal.addEventListener('show.bs.modal', async function() {
        try {
            const response = await fetch('{{ route("api.materials.all") }}');
            const materials = await response.json();
            
            const tbody = document.getElementById('srpTableBody');
            tbody.innerHTML = '';
            
            materials.forEach(material => {
                const markup = material.base_price > 0 ? 
                    ((material.srp_price - material.base_price) / material.base_price * 100) : 0;
                
                const row = `
                    <tr>
                        <td>${material.code}</td>
                        <td>${material.name}</td>
                        <td>${material.category ? material.category.name : ''}</td>
                        <td>${material.unit}</td>
                        <td>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">₱</span>
                                <input type="number" class="form-control base-price" 
                                    value="${material.base_price}" readonly>
                            </div>
                        </td>
                        <td>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">₱</span>
                                <input type="number" class="form-control srp-price" 
                                    data-material-id="${material.id}"
                                    value="${material.srp_price}"
                                    step="0.01"
                                    min="0">
                            </div>
                        </td>
                        <td>
                            <div class="input-group input-group-sm">
                                <input type="number" class="form-control markup-percent" 
                                    value="${markup.toFixed(2)}"
                                    step="0.01">
                                <span class="input-group-text">%</span>
                            </div>
                        </td>
                    </tr>
                `;
                tbody.insertAdjacentHTML('beforeend', row);
            });

            // Reattach event listeners for the newly created inputs
            attachSrpEventListeners();
        } catch (error) {
            console.error('Error loading materials:', error);
            alert('Failed to load materials. Please try again.');
        }
    });

    function attachSrpEventListeners() {
        // Handle markup percentage changes
        document.querySelectorAll('.markup-percent').forEach(input => {
            input.addEventListener('change', function() {
                const row = this.closest('tr');
                const basePrice = parseFloat(row.querySelector('.base-price').value) || 0;
                const markup = parseFloat(this.value) || 0;
                const srpPrice = basePrice * (1 + markup/100);
                row.querySelector('.srp-price').value = srpPrice.toFixed(2);
            });
        });

        // Handle SRP price changes
        document.querySelectorAll('.srp-price').forEach(input => {
            input.addEventListener('change', function() {
                const row = this.closest('tr');
                const basePrice = parseFloat(row.querySelector('.base-price').value) || 0;
                const srpPrice = parseFloat(this.value) || 0;
                const markup = basePrice > 0 ? ((srpPrice - basePrice) / basePrice * 100) : 0;
                row.querySelector('.markup-percent').value = markup.toFixed(2);
            });
        });
    }

    // Save SRP prices
    document.getElementById('saveSrpPrices').addEventListener('click', async function() {
        const updates = [];
        document.querySelectorAll('.srp-price').forEach(input => {
            const id = input.dataset.materialId;
            const srp_price = input.value;
            if (id && srp_price) {
                updates.push({ id, srp_price });
            }
        });

        if (updates.length === 0) {
            alert('No materials to update');
            return;
        }

        try {
            const response = await fetch('{{ route("materials.update-srp") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ materials: updates })
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Failed to update SRP prices');
            }

            const modal = bootstrap.Modal.getInstance(document.getElementById('bulkSrpModal'));
            modal.hide();
            
            // Show success message
            alert('SRP prices updated successfully');
            window.location.reload();

        } catch (error) {
            console.error('Error:', error);
            alert(error.message || 'Failed to update SRP prices. Please try again.');
        }
    });

    // Add SRP search functionality
    const srpSearchInput = document.getElementById('srpSearchInput');
    const clearSrpSearch = document.getElementById('clearSrpSearch');

    srpSearchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const rows = document.querySelectorAll('#srpTableBody tr');
        
        rows.forEach(row => {
            const code = row.cells[0].textContent.toLowerCase();
            const name = row.cells[1].textContent.toLowerCase();
            const category = row.cells[2].textContent.toLowerCase();
            
            if (code.includes(searchTerm) || name.includes(searchTerm) || category.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    clearSrpSearch.addEventListener('click', function() {
        srpSearchInput.value = '';
        const rows = document.querySelectorAll('#srpTableBody tr');
        rows.forEach(row => row.style.display = '');
    });

    // Add supplier prices modal functionality
    const viewSupplierButtons = document.querySelectorAll('.view-suppliers');

    // Get the correct base URL for API
    const apiBase = "{{ url('api/materials') }}";

    viewSupplierButtons.forEach(button => {
        button.addEventListener('click', async function() {
            const materialId = this.dataset.materialId;
            try {
                const response = await fetch(`${apiBase}/${materialId}/suppliers`);
                const data = await response.json();
                const suppliers = data.suppliers || [];
                const basePrice = parseFloat(data.base_price) || 0;
                const tbody = document.getElementById('supplierPricesBody');
                tbody.innerHTML = '';
                
                suppliers.forEach(supplier => {
                    const variance = calculateVariance(supplier.price, basePrice);
                    const row = `
                        <tr>
                            <td>${supplier.company_name}</td>
                            <td>₱${supplier.price ? parseFloat(supplier.price).toFixed(2) : 'N/A'}</td>
                            <td>${supplier.lead_time || 'N/A'}</td>
                            <td>${supplier.last_updated ? new Date(supplier.last_updated).toLocaleDateString() : 'N/A'}</td>
                            <td>
                                <span class="badge ${variance.class}">
                                    ${variance.percentage}%
                                </span>
                            </td>
                        </tr>
                    `;
                    tbody.insertAdjacentHTML('beforeend', row);
                });
            } catch (error) {
                console.error('Error fetching supplier prices:', error);
            }
        });
    });

    function calculateVariance(supplierPrice, basePrice) {
        if (!basePrice || basePrice === 0 || !supplierPrice) return { percentage: 0, class: 'bg-secondary' };
        const variance = ((supplierPrice - basePrice) / basePrice) * 100;
        const formattedVariance = variance.toFixed(2);
        return {
            percentage: formattedVariance,
            class: variance < 0 ? 'bg-success' : variance > 0 ? 'bg-danger' : 'bg-secondary'
        };
    }
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
    .suppliers-badge {
        min-width: 70px;
        display: inline-block;
        text-align: center;
    }
    .badge.bg-success {
        background-color: #28a745 !important;
    }
    .badge.bg-danger {
        background-color: #dc3545 !important;
    }
    .badge.bg-secondary {
        background-color: #6c757d !important;
    }
</style>
@endpush
@endsection 