@extends('layouts.app')

@section('content')
<div class="container-fluid req-page">
    <x-page-header title="New Material Request" icon="dolly" subtitle="Stock is reserved first; anything short can go straight to a purchase request.">
        <a href="{{ route('material-requests.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Back</a>
    </x-page-header>

    <form action="{{ route('material-requests.store') }}" method="POST" id="materialRequestForm">
        @csrf
        @if(isset($quotation_id))
            <input type="hidden" name="quotation_request_id" value="{{ $quotation_id }}">
        @endif
        @if(isset($contract_id))
            <input type="hidden" name="contract_id" value="{{ $contract_id }}">
        @endif

        @if(isset($selectedSuppliers) && count($selectedSuppliers) > 0)
            <div class="req-callout req-callout--success mb-4">
                <div class="req-callout__text">
                    <i class="fas fa-check-circle" aria-hidden="true"></i>
                    <div><strong>Suppliers already chosen.</strong> The client picked suppliers for these materials during the quotation; they'll be used for procurement.</div>
                </div>
            </div>
        @endif

        <div class="card">
            <div class="card-header"><h3 class="req-section-title"><i class="fas fa-boxes"></i> Items</h3></div>
            <div class="table-responsive">
                                    <table class="table req-form-table" id="itemsTable">
                                        <thead>
                                            <tr>
                                                <th>Material</th>
                                                <th>Unit</th>
                                                <th>In stock</th>
                                                <th>Quantity</th>
                                                <th>Supplier</th>
                                                <th><span class="visually-hidden">Actions</span></th>
                                            </tr>
                                        </thead>
                                        <tbody id="items-container">
                                            @if(!empty($items))
                                                @foreach($items as $index => $item)
                                                    <tr class="item-row">
                                                        <td>
                                                            {{ $item['name'] }}
                                                            <input type="hidden" name="items[{{ $index }}][material_id]" value="{{ $item['material_id'] }}">
                                                        </td>
                                                        <td class="unit">{{ $item['unit'] }}</td>
                                                        <td>
                                                            <span class="badge bg-{{ $item['available'] > 0 ? 'success' : 'danger' }}">
                                                                {{ number_format($item['available'], 2) }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <input type="number" name="items[{{ $index }}][quantity]" class="form-control quantity" step="0.01" value="{{ $item['quantity'] }}" required>
                                                        </td>
                                                        <td>
                                                            @if(isset($item['selected_supplier_id']) && $item['selected_supplier_id'])
                                                                @php
                                                                    $selectedSupplier = \App\Models\Supplier::find($item['selected_supplier_id']);
                                                                @endphp
                                                                <input type="hidden" name="items[{{ $index }}][preferred_supplier_id]" value="{{ $item['selected_supplier_id'] }}">
                                                                <div class="d-flex align-items-center">
                                                                    <i class="fas fa-check-circle text-success me-2"></i>
                                                                    <div>
                                                                        <span class="text-success fw-semibold">{{ $selectedSupplier ? $selectedSupplier->company_name : 'Client Selected Supplier' }}</span>
                                                                        <small class="text-muted d-block">Client's choice</small>
                                                                    </div>
                                                                </div>
                                                            @else
                                                                <select name="items[{{ $index }}][preferred_supplier_id]" class="form-control supplier-select">
                                                                    <option value="">Select Supplier</option>
                                                                    @php
                                                                        $material = \App\Models\Material::find($item['material_id']);
                                                                        $suppliers = $material ? $material->suppliers : collect();
                                                                    @endphp
                                                                    @foreach($suppliers as $supplier)
                                                                        <option value="{{ $supplier->id }}">{{ $supplier->company_name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <button type="button" class="btn btn-danger btn-sm remove-row" title="Remove">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
            </div>
            <div class="card-body pt-3">
                <button type="button" class="btn btn-outline-success" id="addRow"><i class="fas fa-plus me-1"></i> Add item</button>

                <div class="row mt-4">
                    <div class="col-lg-7 mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea name="notes" id="notes" class="form-control" rows="3" placeholder="Delivery instructions, site contact, …"></textarea>
                    </div>
                    <div class="col-lg-5 mb-3">
                        <div class="form-label">Out-of-stock items</div>
                        <div class="req-note">
                            <div class="form-check mb-1">
                                <input type="checkbox" class="form-check-input" id="create_purchase_request" name="create_purchase_request" value="1" @if($anyShort) checked disabled @endif>
                                <label class="form-check-label" for="create_purchase_request">Create a purchase request for items not in stock</label>
                            </div>
                            <div class="form-text mb-0">
                                @if($anyShort)
                                    Some items exceed available stock, so a purchase request will be created for the shortfall.
                                @else
                                    Anything that can't be filled from current stock is sent to purchasing automatically.
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="req-form-footer">
                <a href="{{ route('material-requests.index') }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-success"><i class="fas fa-paper-plane me-1"></i> Create material request</button>
            </div>
        </div>
    </form>
</div>

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
<script>window.__page = Object.assign(window.__page || {}, { create0_0: {{ Js::from(!empty($items) ? count($items) : 0) }}, create0_1: {{ Js::from($materials) }}, create0_2: {{ Js::from((string) (route("material-requests.create"))) }}, create0_3: {{ Js::from((string) (route("purchase-requests.recommend-suppliers"))) }}, create0_4: {{ Js::from((string) (url('admin/materials'))) }}, create0_5: {{ Js::from((string) (url('admin/materials'))) }} });</script>
@vite('resources/js/pages/admin-material-requests-create.js')
@endpush

@push('styles')
@vite('resources/css/pages/requests.css')
@endpush
@endsection