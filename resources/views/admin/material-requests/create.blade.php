@extends('layouts.app')

@section('content')
<h1 class="h3 mb-4 text-center page-title">Create Material Request</h1>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-tools">
                        <a href="{{ route('material-requests.index') }}" class="btn btn-default">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <form action="{{ route('material-requests.store') }}" method="POST" id="materialRequestForm">
                    @csrf
                    @if(isset($quotation_id))
                        <input type="hidden" name="quotation_request_id" value="{{ $quotation_id }}">
                    @endif
                    @if(isset($contract_id))
                        <input type="hidden" name="contract_id" value="{{ $contract_id }}">
                    @endif
                    <div class="card-body">

                        <!-- Items Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h4>Request Items</h4>
                                @if(isset($selectedSuppliers) && count($selectedSuppliers) > 0)
                                    <div class="alert alert-success">
                                        <i class="fas fa-check-circle"></i>
                                        <strong>Client Supplier Selection:</strong> The client has already selected suppliers for these materials during the quotation process. 
                                        The selected suppliers will be automatically used for procurement.
                                    </div>
                                @endif
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="itemsTable">
                                        <thead>
                                            <tr>
                                                <th>Material</th>
                                                <th>Unit</th>
                                                <th>Available Stock</th>
                                                <th>Requested Quantity</th>
                                                <th>Supplier</th>
                                                <th>Actions</th>
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
                                                                        <span class="text-success font-weight-bold">{{ $selectedSupplier ? $selectedSupplier->company_name : 'Client Selected Supplier' }}</span>
                                                                        <small class="text-muted d-block">(Client's choice)</small>
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

                        <!-- Purchase Request Options -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="create_purchase_request" name="create_purchase_request" value="1" @if($anyShort) checked disabled @endif>
                                        <label class="custom-control-label" for="create_purchase_request">
                                            Create purchase request for items not available in stock
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">
                                        If checked, a purchase request will be automatically created for any items that cannot be fulfilled from current stock.
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Create Material Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
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
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
@vite('resources/css/pages/admin-material-requests-create.css')
@endpush
@endsection