@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>{{ isset($purchaseRequest) ? 'Edit Purchase Request' : 'Create Purchase Request' }}</h1>
            <a href="{{ route('purchase-requests.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="{{ isset($purchaseRequest) ? route('purchase-requests.update', $purchaseRequest) : route('purchase-requests.store') }}" 
                      method="POST" enctype="multipart/form-data">
                    @csrf
                    @if(isset($purchaseRequest))
                        @method('PUT')
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="contract_id">Contract <span class="text-muted">(Optional)</span></label>
                                <select class="form-select @error('contract_id') is-invalid @enderror" 
                                        id="contract_id" name="contract_id">
                                    <option value="">Select Contract (Optional)</option>
                                    @foreach($contracts as $contract)
                                        <option value="{{ $contract->id }}" 
                                            {{ (old('contract_id', $purchaseRequest->contract_id ?? '') == $contract->id) ? 'selected' : '' }}>
                                            {{ $contract->contract_id }} - Client: {{ $contract->client->company_name ?? $contract->client->name ?? '' }} | Contractor: {{ $contract->contractor->company_name ?? $contract->contractor->name ?? '' }} | Status: {{ ucfirst($contract->status) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('contract_id')
                                    <div class="invalid-feedback" style="text-decoration: underline wavy red;">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="department">Department</label>
                                <input type="text" class="form-control @error('department') is-invalid @enderror" 
                                       id="department" name="department" 
                                       value="{{ old('department', $purchaseRequest->department ?? '') }}" required>
                                @error('department')
                                    <div class="invalid-feedback" style="text-decoration: underline wavy red;">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="required_date">Required Date</label>
                                <input type="date" class="form-control @error('required_date') is-invalid @enderror" 
                                       id="required_date" name="required_date" 
                                       value="{{ old('required_date', isset($purchaseRequest) ? $purchaseRequest->required_date->format('Y-m-d') : '') }}" 
                                       required>
                                @error('required_date')
                                    <div class="invalid-feedback" style="text-decoration: underline wavy red;">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="purpose">Purpose</label>
                                <textarea class="form-control @error('purpose') is-invalid @enderror" 
                                          id="purpose" name="purpose" rows="3" required>{{ old('purpose', $purchaseRequest->purpose ?? '') }}</textarea>
                                @error('purpose')
                                    <div class="invalid-feedback" style="text-decoration: underline wavy red;">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="mb-0">Items</h5>
                        </div>
                        <div class="card-body">
                            <div id="items-container">
                                @if(isset($purchaseRequest))
                                    @foreach($purchaseRequest->items as $index => $item)
                                        <div class="item-row mb-4">
                                            <div class="row">
                                                <div class="col-md-2">
                                                        <label>Material</label>
                                                        <select class="form-select material-select" name="items[{{ $index }}][material_id]" required>
                                                            <option value="">Select Material</option>
                                                            @foreach($materials as $material)
                                                                <option value="{{ $material->id }}">{{ $material->name }}</option>
                                                            @endforeach
                                                        </select>
                                                        <input type="hidden" name="items[{{ $index }}][supplier_id]" class="supplier-id" />
                                                        <div class="supplier-name text-info small mt-1"></div>
                                                    </div>
                                                <div class="col-md-2">
                                                    <label>Supplier</label>
                                                    <div class="dropdown">
                                                        <button class="btn btn-outline-secondary dropdown-toggle w-100 supplier-dropdown-btn" type="button" id="dropdownMenu{{ $index }}" data-bs-toggle="dropdown" aria-expanded="false">
                                                            Select Supplier
                                                        </button>
                                                        <ul class="dropdown-menu w-100" aria-labelledby="dropdownMenu{{ $index }}">
                                                            <!-- Dynamically filled by JS based on material selection -->
                                                        </ul>
                                                        <div class="badge-list mt-1"></div>
                                                    </div>
                                                    <input type="hidden" class="selected-supplier-input" name="items[{{ $index }}][supplier_id]" value="">
                                                </div>
                                                <div class="col-md-2">
                                                    <label>Supplier Total Cost</label>
                                                    <input type="text" class="form-control supplier-total-cost" name="items[{{ $index }}][supplier_total_cost]" value="" readonly>
                                                </div>
                                                <div class="col-md-2">
                                                    <label>Description</label>
                                                    <input type="text" class="form-control" name="items[{{ $index }}][description]" value="{{ $item->description }}" required>
                                                </div>
                                                <div class="col-md-1">
                                                    <label>Qty</label>
                                                    <input type="number" class="form-control item-quantity" name="items[{{ $index }}][quantity]" value="{{ $item->quantity }}" min="0.01" step="0.01" required>
                                                </div>
                                                <div class="col-md-1">
                                                    <label>Unit</label>
                                                    <input type="text" class="form-control" name="items[{{ $index }}][unit]" value="{{ $item->unit }}" required>
                                                </div>
                                                <div class="col-md-2">
                                                    <label>Unit Price</label>
                                                    <input type="number" class="form-control item-unit-price" name="items[{{ $index }}][estimated_unit_price]" value="{{ $item->estimated_unit_price }}" min="0.01" step="0.01" required>
                                                    </div>
                                                <div class="col-md-2">
                                                    <label>Total</label>
                                                    <input type="number" class="form-control item-total" name="items[{{ $index }}][total_amount]" value="{{ $item->total_amount }}" readonly required>
                                                </div>
                                                <div class="col-md-2">
                                                        <label>Specifications</label>
                                                    <input type="text" class="form-control" name="items[{{ $index }}][specifications]" value="{{ $item->specifications }}">
                                                </div>
                                                <div class="col-md-1 d-flex align-items-end">
                                                    <button type="button" class="btn btn-danger remove-item"><i class="fas fa-trash"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            <button type="button" class="btn btn-secondary" id="add-item">
                                <i class="fas fa-plus"></i> Add Item
                            </button>
                        </div>
                    </div>

                    <div class="form-group mt-4">
                        <label for="notes">Notes</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                                  id="notes" name="notes" rows="3">{{ old('notes', $purchaseRequest->notes ?? '') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback" style="text-decoration: underline wavy red;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mt-4">
                        <label>Attachments</label>
                        <input type="file" class="form-control @error('attachments.*') is-invalid @enderror" 
                               name="attachments[]" multiple>
                        @error('attachments.*')
                            <div class="invalid-feedback" style="text-decoration: underline wavy red;">{{ $message }}</div>
                        @enderror
                    </div>

                    @if(isset($purchaseRequest) && $purchaseRequest->attachments->count() > 0)
                        <div class="mt-3">
                            <h6>Current Attachments:</h6>
                            <ul class="list-unstyled">
                                @foreach($purchaseRequest->attachments as $attachment)
                                    <li class="mb-2">
                                        <i class="fas fa-file"></i> {{ $attachment->original_name }}
                                        <button type="button" class="btn btn-sm btn-danger remove-attachment" 
                                                data-attachment-id="{{ $attachment->id }}">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="text-end mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> {{ isset($purchaseRequest) ? 'Update' : 'Create' }} Purchase Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
{{-- Extracted to resources/css/admin-purchase-requests-form.css --}}
@vite('resources/css/admin-purchase-requests-form.css')
@endpush
@push('scripts')
{{-- Extracted to resources/js/admin-purchase-requests-form.js --}}
@vite('resources/js/admin-purchase-requests-form.js')
@endpush 