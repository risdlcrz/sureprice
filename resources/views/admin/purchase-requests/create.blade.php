@extends('layouts.app')

@section('content')

<div class="container-fluid req-page">
    <x-page-header title="New Purchase Request" icon="file-invoice" subtitle="List the materials to buy; prices and suppliers fill in from each material's supplier list.">
        <a href="{{ route('purchase-requests.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Back</a>
    </x-page-header>

    <form action="{{ route('purchase-requests.store') }}" method="POST" id="purchaseRequestForm">
        @csrf
        <div class="card mb-4">
            <div class="card-header"><h3 class="req-section-title"><i class="fas fa-sliders-h"></i> Request details</h3></div>
            <div class="card-body">
                <div class="form-label">Request type</div>
                <div class="req-choice mb-3" role="radiogroup" aria-label="Request type">
                    <div class="form-check">
                        <input type="radio" id="standalone" name="is_project_related" value="0" class="form-check-input" {{ request('contract_id') ? '' : 'checked' }}>
                        <label class="form-check-label" for="standalone">Standalone request</label>
                    </div>
                    <div class="form-check">
                        <input type="radio" id="project_related" name="is_project_related" value="1" class="form-check-input" {{ request('contract_id') ? 'checked' : '' }}>
                        <label class="form-check-label" for="project_related">Contract related</label>
                    </div>
                </div>

                <div class="row" id="projectRelatedFields">
                    <div class="col-md-6 position-relative">
                        <label for="contract_search" class="form-label">Contract</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-search" aria-hidden="true"></i></span>
                            <input type="text" id="contract_search" class="form-control" placeholder="Search by contract number or name" autocomplete="off">
                        </div>
                        <div id="contract_search_results" class="list-group req-search-results"></div>
                        <input type="hidden" name="contract_id" id="contract_id" value="{{ request('contract_id') }}">
                        <div id="selected_contract_info" class="req-note mt-2" style="display: none;">
                            <i class="fas fa-file-contract text-success me-1"></i> <strong>Selected:</strong> <span id="contract_display"></span>
                        </div>
                    </div>
                </div>

                <div class="mt-3">
                    <label for="notes" class="form-label">Notes</label>
                    <textarea name="notes" id="notes" class="form-control" rows="2" placeholder="Anything the approver or supplier should know"></textarea>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                <h3 class="req-section-title"><i class="fas fa-boxes"></i> Items</h3>
                <div class="position-relative req-search">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="fas fa-filter" aria-hidden="true"></i></span>
                        <input type="text" id="materialMasterSearch" class="form-control" placeholder="Find an item in this list" autocomplete="off">
                    </div>
                    <div id="materialMasterSearchResults" class="list-group req-search-results"></div>
                </div>
            </div>
            <div class="table-responsive">
                                    <table class="table req-form-table" id="itemsTable">
                                        <thead>
                                            <tr>
                                                <th>Material</th>
                                                <th>Description</th>
                                                <th>Quantity</th>
                                                <th>Unit</th>
                                                <th>Est. unit price</th>
                                                <th>Amount</th>
                                                <th>Preferred brand</th>
                                                <th>Supplier</th>
                                                <th><span class="visually-hidden">Actions</span></th>
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
                                                            <button type="button" class="btn btn-outline-success btn-sm recommend-supplier-btn" title="Recommend a supplier" data-material-id="{{ $item['material_id'] ?? '' }}" data-material-name="{{ $item['material_name'] ?? '' }}">
                                                                <i class="fas fa-lightbulb"></i> Recommend
                                                            </button>
                                                            <button type="button" class="btn btn-outline-secondary btn-sm replace-material" title="Replace Material">
                                                                <i class="fas fa-exchange-alt"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-outline-danger btn-sm remove-row" title="Remove">
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
                                                    <button type="button" class="btn btn-outline-success btn-sm recommend-supplier-btn" title="Recommend a supplier" data-material-id="{{ $item['material_id'] ?? '' }}" data-material-name="{{ $item['material_name'] ?? '' }}">
                                                        <i class="fas fa-lightbulb"></i> Recommend
                                                    </button>
                                                    <button type="button" class="btn btn-outline-secondary btn-sm replace-material" title="Replace Material">
                                                        <i class="fas fa-exchange-alt"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-outline-danger btn-sm remove-row">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            @endif
                                        </tbody>
                                    </table>
            </div>
            <div class="card-body pt-3">
                <button type="button" class="btn btn-outline-success" id="addRow"><i class="fas fa-plus me-1"></i> Add item</button>
            </div>
            <div class="req-form-footer">
                <a href="{{ route('purchase-requests.index') }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-success"><i class="fas fa-paper-plane me-1"></i> Create purchase request</button>
            </div>
        </div>
    </form>
</div>

<!-- Material Search Modal -->
<x-modal-dialog id="materialSearchModal" dialog-class="modal-dialog-centered">
    <x-slot:title>Select Material</x-slot:title>
      <div class="modal-body">
        <input type="text" id="modalMaterialSearchInput" class="form-control mb-2" placeholder="Search materials...">
        <div id="modalMaterialSearchResults" class="list-group req-modal-results"></div>
        <div id="modalMaterialSearchWarning" class="alert alert-warning mt-2 d-none"></div>
      </div>
</x-modal-dialog>

<!-- Material Replace Modal -->
<x-modal-dialog id="replaceMaterialModal" dialog-class="modal-dialog-centered">
    <x-slot:title>Replace Material</x-slot:title>
      <div class="modal-body">
        <input type="text" id="replaceMaterialSearchInput" class="form-control mb-2" placeholder="Search materials...">
        <div id="replaceMaterialSearchResults" class="list-group req-modal-results"></div>
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
@vite('resources/css/pages/requests.css')
@endpush
@endsection