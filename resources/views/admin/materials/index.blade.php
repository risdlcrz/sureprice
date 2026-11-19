@extends('layouts.app')

@push('styles')
@vite('resources/css/pages/admin-materials-index.css')
@endpush

@section('content')
<h1 class="h3 mb-4 text-gray-800 text-center" style="font-weight:700;color:#198754;letter-spacing:0.01em;">Materials</h1>
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow materials-fadein" id="materialsFadeinCard">
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
                                <tr class="material-row-animate">
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
                                        <div class="d-flex flex-column align-items-start">
                                            <span>₱{{ number_format($material->srp_price, 2) }}</span>
                                            @php
                                                $prevSrp = optional($material->priceHistories()->latest('date')->first())->price;
                                            @endphp
                                            @if($prevSrp)
                                                <small class="text-muted">Previous SRP: ₱{{ number_format($prevSrp, 2) }}</small>
                                            @endif
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
<x-modal-dialog id="deleteModal">
    <x-slot:title>Delete Material</x-slot:title>
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
</x-modal-dialog>

<!-- Bulk SRP Update Modal -->
<x-modal-dialog id="bulkSrpModal" dialog-class="modal-xl">
    <x-slot:title>Set SRP Prices</x-slot:title>
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
                                <th>Previous SRP</th>
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
</x-modal-dialog>

<!-- Supplier Prices Modal -->
<x-modal-dialog id="supplierPricesModal" dialog-class="modal-lg">
    <x-slot:title>Supplier Prices</x-slot:title>
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
</x-modal-dialog>

@push('scripts')
<script>window.__page = Object.assign(window.__page || {}, { index0_0: {{ Js::from((string) (route('materials.destroy', ':id'))) }}, index0_1: @json((string) (route("api.materials.all"))), index0_2: @json((string) (route("materials.update-srp"))), index0_3: @json((string) (url('api/materials'))) });</script>
@vite('resources/js/pages/admin-materials-index.js')
@endpush 