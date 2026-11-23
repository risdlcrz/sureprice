@extends('layouts.app')

@section('content')
<div class="container-fluid req-page">
    <x-page-header :title="'Edit ' . $purchaseRequest->request_number" icon="file-invoice" subtitle="Only pending requests can be edited.">
        <a href="{{ route('purchase-requests.show', $purchaseRequest) }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Back to details</a>
    </x-page-header>

    <form action="{{ route('purchase-requests.update', $purchaseRequest) }}" method="POST" id="purchaseRequestForm">
        @csrf
        @method('PUT')
        <input type="hidden" name="is_project_related" value="{{ $purchaseRequest->contract_id ? 1 : 0 }}">
        @if($purchaseRequest->contract_id)
            <input type="hidden" name="contract_id" value="{{ $purchaseRequest->contract_id }}">
        @endif

        <div class="card mb-4">
            <div class="card-header"><h3 class="req-section-title"><i class="fas fa-sliders-h"></i> Request details</h3></div>
            <div class="card-body">
                <dl class="req-meta mb-3">
                    <div>
                        <dt>Request type</dt>
                        <dd>{{ $purchaseRequest->contract_id ? 'Contract related' : 'Standalone' }}</dd>
                    </div>
                    @if($purchaseRequest->contract_id && $purchaseRequest->contract)
                        <div id="projectRelatedFields">
                            <dt>Contract</dt>
                            <dd>{{ $purchaseRequest->contract->contract_number }}<span class="req-sub">{{ $purchaseRequest->contract->name ?? $purchaseRequest->contract->title ?? '' }}</span></dd>
                        </div>
                    @endif
                    <div>
                        <dt>Status</dt>
                        <dd><x-status-badge :status="$purchaseRequest->status" /></dd>
                    </div>
                </dl>
                <label for="notes" class="form-label">Notes</label>
                <textarea name="notes" id="notes" class="form-control" rows="2">{{ $purchaseRequest->notes }}</textarea>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h3 class="req-section-title"><i class="fas fa-boxes"></i> Items</h3></div>
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
                                                <th>Notes</th>
                                                <th><span class="visually-hidden">Actions</span></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($purchaseRequest->items as $index => $item)
                                                <tr class="item-row">
                                                    <td>
                                                        <select name="items[{{ $index }}][material_id]" class="form-control material-select" required>
                                                            <option value="">Select Material</option>
                                                            @foreach($materials as $material)
                                                                <option value="{{ $material->id }}"
                                                                    data-unit="{{ $material->unit }}"
                                                                    data-suppliers='{{ Js::from($material->suppliers->map(fn($s) => ["id"=>$s->id,"name"=>$s->company_name])) }}'
                                                                    {{ $item->material_id == $material->id ? 'selected' : '' }}>
                                                                    {{ $material->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="items[{{ $index }}][description]" class="form-control" value="{{ $item->description }}" required>
                                                    </td>
                                                    <td>
                                                        <input type="number" name="items[{{ $index }}][quantity]" class="form-control quantity" step="0.01" value="{{ $item->quantity }}" required>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="items[{{ $index }}][unit]" class="form-control unit" value="{{ $item->unit }}" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="number" name="items[{{ $index }}][estimated_unit_price]" class="form-control unit-price" step="0.01" value="{{ $item->estimated_unit_price }}" required>
                                                    </td>
                                                    <td>
                                                        <input type="number" name="items[{{ $index }}][total_amount]" class="form-control total-amount" value="{{ $item->total_amount }}" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="items[{{ $index }}][preferred_brand]" class="form-control" value="{{ $item->preferred_brand }}">
                                                    </td>
                                                    <td>
                                                        <select name="items[{{ $index }}][preferred_supplier_id]" class="form-control supplier-select" required data-selected-supplier="{{ $item->preferred_supplier_id }}">
                                                            <option value="">Select Supplier</option>
                                                            <!-- Options will be dynamically populated based on selected material -->
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="items[{{ $index }}][notes]" class="form-control" value="{{ $item->notes }}">
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-outline-danger btn-sm remove-row" title="Remove">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
            </div>
            <div class="card-body pt-3">
                <button type="button" class="btn btn-outline-success" id="addRow"><i class="fas fa-plus me-1"></i> Add item</button>
            </div>
            <div class="req-form-footer">
                <a href="{{ route('purchase-requests.show', $purchaseRequest) }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-success"><i class="fas fa-save me-1"></i> Save changes</button>
            </div>
        </div>
    </form>
</div>
@push('scripts')
<script>window.__page = Object.assign(window.__page || {}, { edit1_0: {{ Js::from($purchaseRequest->items->count()) }} });</script>
@vite('resources/js/pages/admin-purchase-requests-edit.js')
@endpush
@push('styles')
@vite('resources/css/pages/requests.css')
@endpush
@endsection 