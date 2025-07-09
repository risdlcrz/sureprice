@extends('layouts.app')

@push('styles')
<style>
body {
    background: linear-gradient(135deg, #f8fafc 0%, #e9ecef 100%) !important;
}
/* Prevent content overlap with sidebar and fit content to screen */
.container-fluid {
    margin-left: 250px;
    max-width: calc(100vw - 250px);
    width: 100%;
    padding-left: 0;
    padding-right: 0;
    transition: margin-left 0.3s, max-width 0.3s;
}
@media (max-width: 1199.98px) {
    .container-fluid {
        margin-left: 200px;
        max-width: calc(100vw - 200px);
    }
}
@media (max-width: 991.98px) {
    .container-fluid {
        margin-left: 0;
        max-width: 100vw;
        padding-left: 10px;
        padding-right: 10px;
    }
}
.card {
    border-radius: 1.25rem;
    box-shadow: 0 4px 24px rgba(44,62,80,0.08), 0 1.5px 6px rgba(44,62,80,0.04);
    border: none;
    margin-bottom: 2rem;
    max-width: 100%;
    overflow-x: auto;
}
.card-header {
    background: #fff;
    border-radius: 1.25rem 1.25rem 0 0;
    border-bottom: none;
    padding: 1.5rem 2rem 1rem 2rem;
}
.btn-primary, .btn-info {
    border-radius: 2rem;
    font-weight: 600;
    font-size: 1.08rem;
    border: none;
    box-shadow: 0 2px 8px #38b6ff33;
    transition: background 0.2s, color 0.2s, box-shadow 0.2s;
}
.btn-primary {
    background: linear-gradient(90deg, #38b6ff 0%, #2563eb 100%);
}
.btn-info {
    background: linear-gradient(90deg, #00c6fb 0%, #005bea 100%);
    color: #fff;
}
.btn-primary:hover, .btn-info:hover {
    filter: brightness(1.08);
    box-shadow: 0 4px 16px #38b6ff33;
}
.btn-secondary {
    border-radius: 2rem;
    font-weight: 600;
    font-size: 1.08rem;
    background: #e9ecef;
    color: #495057;
    border: none;
    margin-left: 0.5rem;
    transition: background 0.2s, color 0.2s;
}
.btn-secondary:hover {
    background: #d1d5db;
    color: #222;
}
.btn-group .btn {
    margin-right: 6px;
    border-radius: 1.5rem !important;
    font-size: 0.85rem;
    min-width: 28px;
    padding: 0.25rem 0.45rem;
    height: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.btn-group .btn:last-child {
    margin-right: 0;
}
.table-responsive {
    border-radius: 1.1rem;
    overflow-x: auto;
    box-shadow: 0 4px 24px rgba(44,62,80,0.08), 0 1.5px 6px rgba(44,62,80,0.04);
    background: #fff;
    max-width: 100%;
}
.table {
    margin-bottom: 0;
    background: #fff;
    border-radius: 1.1rem;
    overflow: hidden;
    font-size: 0.89rem;
    table-layout: fixed;
}
.table th, .table td {
    vertical-align: middle;
    padding: 0.32rem 0.15rem;
    border: none;
    background: #f8fafc;
    text-align: center;
    word-break: break-word;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.table thead th {
    background: #f1f5f9;
    font-weight: 700;
    color: #198754;
    border-bottom: 2px solid #e3e3e3;
    text-align: center;
    font-size: 0.93rem;
    padding: 0.32rem 0.15rem;
}
.table-hover tbody tr:hover {
    background: #e3f2fd44;
}
.table th, .table td {
    min-width: 60px;
    max-width: 110px;
}
.table th:nth-child(2), .table td:nth-child(2) {
    min-width: 90px;
    max-width: 120px;
}
.table th:nth-child(3), .table td:nth-child(3) {
    min-width: 80px;
    max-width: 100px;
}
.table th:nth-child(5), .table td:nth-child(5),
.table th:nth-child(6), .table td:nth-child(6) {
    min-width: 80px;
    max-width: 100px;
}
.table th:nth-child(1), .table td:nth-child(1) {
    min-width: 80px;
    max-width: 90px;
}
/* Responsive column hiding */
.hide-col {
    display: table-cell;
}
@media (max-width: 1400px) {
    .table {
        font-size: 0.85rem;
    }
    .table th, .table td {
        padding: 0.25rem 0.08rem;
        font-size: 0.85rem;
        min-width: 50px;
        max-width: 90px;
    }
    .table th:nth-child(2), .table td:nth-child(2) {
        min-width: 70px;
        max-width: 90px;
    }
    .table th:nth-child(3), .table td:nth-child(3) {
        min-width: 60px;
        max-width: 80px;
    }
    /* Hide Warranty and Suppliers columns */
    .hide-col-1 { display: none !important; }
    .hide-col-2 { display: none !important; }
}
@media (max-width: 1200px) {
    .table {
        font-size: 0.81rem;
    }
    .table th, .table td {
        padding: 0.18rem 0.05rem;
        font-size: 0.81rem;
        min-width: 40px;
        max-width: 70px;
    }
    .table th:nth-child(2), .table td:nth-child(2) {
        min-width: 50px;
        max-width: 70px;
    }
    .table th:nth-child(3), .table td:nth-child(3) {
        min-width: 40px;
        max-width: 60px;
    }
    /* Hide Base Price and SRP columns */
    .hide-col-3 { display: none !important; }
    .hide-col-4 { display: none !important; }
}
.badge {
    font-size: 0.95em;
    font-weight: 600;
    letter-spacing: 0.01em;
    box-shadow: 0 1px 4px #ffc10722;
    border-radius: 0.7em;
    padding: 0.5em 1em;
}
.badge.bg-success {
    background-color: #28a745 !important;
    color: #fff;
}
.badge.bg-danger {
    background-color: #dc3545 !important;
    color: #fff;
}
.badge.bg-secondary {
    background-color: #6c757d !important;
    color: #fff;
}
input.form-control, select.form-control {
    border-radius: 1.2rem;
    border: 1px solid #d1d5db;
    background: #f8fafc;
    font-size: 1.08rem;
    padding: 0.85rem 1.1rem;
    transition: border-color 0.2s, box-shadow 0.2s;
}
input.form-control:focus, select.form-control:focus {
    border-color: #38b6ff;
    box-shadow: 0 0 0 2px #38b6ff33;
    background: #fff;
}
::-webkit-input-placeholder { color: #b0b3b8; }
::-moz-placeholder { color: #b0b3b8; }
:-ms-input-placeholder { color: #b0b3b8; }
::placeholder { color: #b0b3b8; }
</style>
@endpush

@section('content')
<h1 class="h3 mb-4 text-gray-800 text-center" style="font-weight:700;color:#198754;letter-spacing:0.01em;">Materials</h1>
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
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
                                    <th>Status</th>
                                    <th>Suppliers</th>
                                    <th>Warranty</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="materialsTableBody">
                                @foreach($materials as $material)
                                <tr>
                                    <td>{{ $material->code }}</td>
                                    <td>
                                        <strong>{{ $material->name }}</strong>
                                        @if($material->description)
                                            <br>
                                            <small class="text-muted">{{ Str::limit($material->description, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $material->category->name ?? 'N/A' }}
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
                                        @php
                                            $status = 'In Stock';
                                            $color = 'success';
                                            if ($material->current_stock <= 0) {
                                                $status = 'Out of Stock';
                                                $color = 'danger';
                                            } elseif ($material->current_stock < $material->minimum_stock) {
                                                $status = 'Low Stock';
                                                $color = 'warning';
                                            }
                                        @endphp
                                        <span class="badge bg-{{ $color }}">{{ $status }}</span>
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
                                        </button>
                                    </td>
                                    <td>{{ $material->warranty_period ? $material->warranty_period . ' months' : 'No warranty' }}</td>
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
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this material? This action cannot be undone.</p>
                <form id="deleteMaterialForm" method="POST" style="display: none;">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
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
    const deleteBtns = document.querySelectorAll('.delete-material');
    const deleteForm = document.getElementById('deleteMaterialForm');
    let materialToDeleteId = null;

    deleteBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            materialToDeleteId = this.dataset.id;
            deleteForm.action = "{{ route('materials.destroy', ':id') }}".replace(':id', materialToDeleteId);
            const modal = new bootstrap.Modal(deleteModal);
            modal.show();
        });
    });

    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
    if (confirmDeleteBtn) {
        confirmDeleteBtn.addEventListener('click', function() {
            deleteForm.submit();
        });
    }

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