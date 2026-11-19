@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Purchase Request</h3>
                    <div class="card-tools">
                        <a href="{{ route('purchase-requests.show', $purchaseRequest) }}" class="btn btn-default">
                            <i class="fas fa-arrow-left"></i> Back to Details
                        </a>
                    </div>
                </div>
                <form action="{{ route('purchase-requests.update', $purchaseRequest) }}" method="POST" id="purchaseRequestForm">
                    @csrf
                    @method('PUT')
                    <div class="card-body">

                        <!-- Request Type Selection -->
                        @if($purchaseRequest->contract_id)
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Request Type</label>
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input type="radio" id="project_related" name="is_project_related" value="1" class="custom-control-input" checked disabled>
                                            <label class="custom-control-label" for="project_related">Project Related</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="is_project_related" value="1">
                            <input type="hidden" name="contract_id" value="{{ $purchaseRequest->contract_id }}">
                        @else
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Request Type</label>
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input type="radio" id="standalone" name="is_project_related" value="0" class="custom-control-input" checked disabled>
                                            <label class="custom-control-label" for="standalone">Standalone Request</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="is_project_related" value="0">
                        @endif

                        <!-- Project/Contract Selection -->
                        @if($purchaseRequest->contract_id && $purchaseRequest->contract)
                            <div class="row mb-4" id="projectRelatedFields" style="display: flex;">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="contract_id">Contract</label>
                                        <input type="text" class="form-control bg-light" value="{{ $purchaseRequest->contract->contract_number . ' - ' . ($purchaseRequest->contract->name ?? $purchaseRequest->contract->title ?? '') }}" readonly>
                                        <input type="hidden" name="contract_id" value="{{ $purchaseRequest->contract_id }}">
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Items Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h4>Request Items</h4>
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
                                                <th>Notes</th>
                                                <th>Actions</th>
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
                                                        <script>console.log('Blade Debug: Item Index {{ $index }}, Preferred Supplier ID: {{ $item->preferred_supplier_id }}');</script>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="items[{{ $index }}][notes]" class="form-control" value="{{ $item->notes }}">
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-danger btn-sm remove-row">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
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
                                    <textarea name="notes" id="notes" class="form-control" rows="3">{{ $purchaseRequest->notes }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Purchase Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>window.__page = Object.assign(window.__page || {}, { edit1_0: {{ Js::from($purchaseRequest->items->count()) }} });</script>
@vite('resources/js/pages/admin-purchase-requests-edit.js')
@endpush
@endsection 