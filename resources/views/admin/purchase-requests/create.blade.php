@extends('layouts.app')

@section('content')

<h1 class="h3 mb-4 text-gray-800 text-center" style="font-weight:700;color:#198754;letter-spacing:0.01em;">Create Purchase Request</h1>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-end align-items-center">
                    <div class="card-tools">
                        <a href="{{ route('purchase-requests.index') }}" class="btn btn-default">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <form action="{{ route('purchase-requests.store') }}" method="POST" id="purchaseRequestForm">
                    @csrf
                    <div class="card-body">

                        <!-- Request Type Selection -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Request Type</label>
                                    <div class="custom-control custom-radio custom-control-inline">
                                        <input type="radio" id="standalone" name="is_project_related" value="0" class="custom-control-input" {{ request('contract_id') ? '' : 'checked' }}>
                                        <label class="custom-control-label" for="standalone">Standalone Request</label>
                                    </div>
                                    <div class="custom-control custom-radio custom-control-inline">
                                        <input type="radio" id="project_related" name="is_project_related" value="1" class="custom-control-input" {{ request('contract_id') ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="project_related">Contract Related</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Contract Selection (always visible for project-related) -->
                        <div class="row mb-4" id="projectRelatedFields">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="contract_search">Search Contract</label>
                                    <input type="text" id="contract_search" class="form-control" placeholder="Search contracts by number or name...">
                                    <div id="contract_search_results" class="list-group position-absolute w-100" style="z-index: 2000; max-height: 200px; overflow-y: auto;"></div>
                                    <input type="hidden" name="contract_id" id="contract_id" value="{{ request('contract_id') }}">
                                    <div id="selected_contract_info" class="mt-2 p-2 bg-light rounded" style="display: none;">
                                        <strong>Selected Contract:</strong> <span id="contract_display"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Items Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h4>Request Items</h4>
                                <div class="mb-2">
                                    <input type="text" id="materialMasterSearch" class="form-control" placeholder="Search for material in purchase request...">
                                    <div id="materialMasterSearchResults" class="list-group position-absolute w-100" style="z-index: 2000;"></div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="itemsTable">
                                        <thead>
                                            <tr>
                                                <th>Material</th>
                                                <th>Description</th>
                                                <th>Quantity</th>
                                                <th>Unit</th>
                                                <th>Estimated Unit Price</th>
                                                <th>Total Amount</th>
                                                <th>Preferred Brand</th>
                                                <th>Preferred Supplier</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="items-container">
                                            @if(isset($prefillItems) && count($prefillItems) > 0)

                                                @foreach($prefillItems as $index => $item)
                                                    @php
                                                        $materialObj = $item['material_obj'];
                                                    @endphp
                                                    <tr class="item-row">
                                                        <td>
                                                            {{ $item['material_name'] }}
                                                            <input type="hidden" name="items[{{ $index }}][material_id]" value="{{ $item['material_id'] }}">
                                                        </td>
                                                        <td>
                                                            <input type="text" name="items[{{ $index }}][description]" class="form-control" value="{{ $item['description'] }}" placeholder="Enter description (required)">
                                                        </td>
                                                        <td>
                                                            <input type="number" name="items[{{ $index }}][quantity]" class="form-control quantity" step="0.01" value="{{ $item['quantity'] }}" required>
                                                        </td>
                                                        <td>
                                                            <input type="text" name="items[{{ $index }}][unit]" class="form-control unit" value="{{ $item['unit'] }}" readonly>
                                                        </td>
                                                        <td>
                                                            <input type="number" name="items[{{ $index }}][estimated_unit_price]" class="form-control unit-price" step="0.01" value="{{ $item['estimated_unit_price'] }}" required readonly>
                                                        </td>
                                                        <td>
                                                            <input type="number" name="items[{{ $index }}][total_amount]" class="form-control total-amount" value="{{ $item['total_amount'] }}" readonly>
                                                        </td>
                                                        <td>
                                                            <input type="text" name="items[{{ $index }}][preferred_brand]" class="form-control" value="{{ $item['preferred_brand'] }}">
                                                        </td>
                                                        <td>
                                                            <select name="items[{{ $index }}][preferred_supplier_id]" class="form-control supplier-select">
                                                                <option value="">Select Supplier</option>
                                                                @php
                                                                    // Only show suppliers linked to this material
                                                                    $suppliersForDropdown = ($materialObj) ? $materialObj->suppliers : collect();
                                                                @endphp
                                                                @foreach($suppliersForDropdown as $supplier)
                                                                    <option value="{{ $supplier->id }}" data-price="{{ $supplier->pivot->price ?? $supplier->price ?? '' }}" {{ (isset($item['preferred_supplier_id']) && $item['preferred_supplier_id'] == $supplier->id) ? 'selected' : '' }}>{{ $supplier->company_name ?? $supplier->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <button type="button" class="btn btn-info btn-sm recommend-supplier-btn" data-material-id="{{ $item['material_id'] ?? '' }}" data-material-name="{{ $item['material_name'] ?? '' }}">
                                                                <i class="fas fa-lightbulb"></i> Recommend Supplier
                                                            </button>
                                                            <button type="button" class="btn btn-outline-secondary btn-sm replace-material" title="Replace Material">
                                                                <i class="fas fa-exchange-alt"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-danger btn-sm remove-row" title="Remove">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                            <tr class="item-row">
                                                <td></td>
                                                <td>
                                                    <input type="text" name="items[0][description]" class="form-control">
                                                </td>
                                                <td>
                                                    <input type="number" name="items[0][quantity]" class="form-control quantity" step="0.01" required>
                                                </td>
                                                <td>
                                                    <input type="text" name="items[0][unit]" class="form-control unit" readonly>
                                                </td>
                                                <td>
                                                    <input type="number" name="items[0][estimated_unit_price]" class="form-control unit-price" step="0.01" required>
                                                </td>
                                                <td>
                                                    <input type="number" name="items[0][total_amount]" class="form-control total-amount" readonly>
                                                </td>
                                                <td>
                                                    <input type="text" name="items[0][preferred_brand]" class="form-control">
                                                </td>
                                                <td>
                                                        <select name="items[0][preferred_supplier_id]" class="form-control supplier-select" required>
                                                        <option value="">Select Supplier</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-info btn-sm recommend-supplier-btn" data-material-id="{{ $item['material_id'] ?? '' }}" data-material-name="{{ $item['material_name'] ?? '' }}">
                                                        <i class="fas fa-lightbulb"></i> Recommend Supplier
                                                    </button>
                                                    <button type="button" class="btn btn-outline-secondary btn-sm replace-material" title="Replace Material">
                                                        <i class="fas fa-exchange-alt"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-sm remove-row">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                                <button type="button" class="btn btn-success" id="addRow">
                                    <i class="fas fa-plus"></i> Add Item
                                </button>
                            </div>
                        </div>

                        <!-- Notes Section -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="notes">Notes</label>
                                    <textarea name="notes" id="notes" class="form-control" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Create Purchase Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Material Search Modal -->
<x-modal-dialog id="materialSearchModal" dialog-class="modal-dialog-centered">
    <x-slot:title>Select Material</x-slot:title>
      <div class="modal-body">
        <input type="text" id="modalMaterialSearchInput" class="form-control mb-2" placeholder="Search materials...">
        <div id="modalMaterialSearchResults" class="list-group" style="max-height: 300px; overflow-y: auto;"></div>
        <div id="modalMaterialSearchWarning" class="alert alert-warning mt-2 d-none"></div>
      </div>
</x-modal-dialog>

<!-- Material Replace Modal -->
<x-modal-dialog id="replaceMaterialModal" dialog-class="modal-dialog-centered">
    <x-slot:title>Replace Material</x-slot:title>
      <div class="modal-body">
        <input type="text" id="replaceMaterialSearchInput" class="form-control mb-2" placeholder="Search materials...">
        <div id="replaceMaterialSearchResults" class="list-group" style="max-height: 300px; overflow-y: auto;"></div>
        <div id="replaceMaterialWarning" class="alert alert-warning mt-2 d-none"></div>
        <div id="replaceMaterialConfirmSection" class="d-none mt-3">
            <h6>Confirm Replacement</h6>
            <div id="replaceMaterialSummary"></div>
            <button type="button" class="btn btn-primary mt-2" id="confirmReplaceMaterialBtn">Confirm Replace</button>
        </div>
      </div>
</x-modal-dialog>

<!-- Supplier Recommendation Modal -->
<x-modal-dialog id="supplierRecommendationModal">
    <x-slot:title>Supplier Recommendation for <span id="modalMaterialName"></span></x-slot:title>
            <div class="modal-body">
                <form id="supplier-recommendation-form" class="row g-3 mb-3">
                    <input type="hidden" name="material_id" id="modalMaterialId">
                    <div class="col-12">
                        <label for="rec_mode" class="form-label">Recommendation Mode</label>
                        <select class="form-select" id="rec_mode" name="mode">
                            <option value="best_score">Best Overall Score</option>
                            <option value="on_time_delivery">Best On-Time Delivery</option>
                            <option value="lowest_defect">Lowest Defect Rate</option>
                            <option value="lowest_cost">Lowest Cost Variance</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary w-100">Show Recommendations</button>
                    </div>
                </form>
                <div id="supplier-recommendation-results"></div>
            </div>
</x-modal-dialog>

@push('scripts')
<script>window.__page = Object.assign(window.__page || {}, { create0_0: @json((string) (url("/"))), create0_1: @json($suppliers ?? []), create0_2: @json($materials ?? []), create0_3: @json($contracts ?? []), create0_4: @json($bestSuppliers ?? []), create0_5: @json((string) (route("purchase-requests.recommend-suppliers"))) });</script>
@vite('resources/js/pages/admin-purchase-requests-create.js')
@endpush
@push('styles')
@vite('resources/css/pages/admin-purchase-requests-create.css')
@endpush
@endsection