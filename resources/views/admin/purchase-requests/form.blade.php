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
                                <label for="contract_id">Contract</label>
                                <select class="form-select @error('contract_id') is-invalid @enderror" 
                                        id="contract_id" name="contract_id" required>
                                    <option value="">Select Contract</option>
                                    @foreach($contracts as $contract)
                                        <option value="{{ $contract->id }}" 
                                            {{ (old('contract_id', $purchaseRequest->contract_id ?? '') == $contract->id) ? 'selected' : '' }}>
                                            {{ $contract->contract_id }} - Client: {{ $contract->client->company_name ?? $contract->client->name ?? '' }} | Contractor: {{ $contract->contractor->company_name ?? $contract->contractor->name ?? '' }} | Status: {{ ucfirst($contract->status) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('contract_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
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
                                    <div class="invalid-feedback">{{ $message }}</div>
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
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="purpose">Purpose</label>
                                <textarea class="form-control @error('purpose') is-invalid @enderror" 
                                          id="purpose" name="purpose" rows="3" required>{{ old('purpose', $purchaseRequest->purpose ?? '') }}</textarea>
                                @error('purpose')
                                    <div class="invalid-feedback">{{ $message }}</div>
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
                                                        <select class="form-select" name="items[{{ $index }}][material_id]" required>
                                                            <option value="">Select Material</option>
                                                            @foreach($materials ?? [] as $material)
                                                                <option value="{{ $material->id }}" 
                                                                data-description="{{ $material->description }}"
                                                                data-unit="{{ $material->unit }}"
                                                                data-price="{{ $material->base_price }}"
                                                                data-specifications="{{ $material->specifications }}">
                                                                    {{ $material->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
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
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mt-4">
                        <label>Attachments</label>
                        <input type="file" class="form-control @error('attachments.*') is-invalid @enderror" 
                               name="attachments[]" multiple>
                        @error('attachments.*')
                            <div class="invalid-feedback">{{ $message }}</div>
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

@push('scripts')
<script>
    let itemIndex = {{ isset($purchaseRequest) ? $purchaseRequest->items->count() : 0 }};

    function createItemRow() {
        const template = `
            <div class="item-row mb-4">
                <div class="row">
                    <div class="col-md-2">
                            <label>Material</label>
                        <select class="form-select" name="items[${itemIndex}][material_id]" required>
                                <option value="">Select Material</option>
                                @foreach($materials ?? [] as $material)
                                <option value="{{ $material->id }}"
                                    data-description="{{ $material->description }}"
                                    data-unit="{{ $material->unit }}"
                                    data-price="{{ $material->base_price }}"
                                    data-specifications="{{ $material->specifications }}">
                                    {{ $material->name }}
                                </option>
                                @endforeach
                            </select>
                    </div>
                    <div class="col-md-2">
                        <label>Description</label>
                        <input type="text" class="form-control" name="items[${itemIndex}][description]" required>
                    </div>
                    <div class="col-md-1">
                        <label>Qty</label>
                        <input type="number" class="form-control item-quantity" name="items[${itemIndex}][quantity]" min="0.01" step="0.01" required>
                    </div>
                    <div class="col-md-1">
                        <label>Unit</label>
                        <input type="text" class="form-control" name="items[${itemIndex}][unit]" required>
                    </div>
                    <div class="col-md-2">
                        <label>Unit Price</label>
                        <input type="number" class="form-control item-unit-price" name="items[${itemIndex}][estimated_unit_price]" min="0.01" step="0.01" required>
                    </div>
                    <div class="col-md-2">
                        <label>Total</label>
                        <input type="number" class="form-control item-total" name="items[${itemIndex}][total_amount]" readonly required>
                    </div>
                    <div class="col-md-2">
                        <label>Specifications</label>
                        <input type="text" class="form-control" name="items[${itemIndex}][specifications]">
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="button" class="btn btn-danger remove-item"><i class="fas fa-trash"></i></button>
                    </div>
                </div>
            </div>
        `;
        itemIndex++;
        return template;
    }

    document.getElementById('add-item').addEventListener('click', function() {
        const container = document.getElementById('items-container');
        container.insertAdjacentHTML('beforeend', createItemRow());
    });

    document.getElementById('items-container').addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-item') || e.target.closest('.remove-item')) {
            const row = e.target.closest('.item-row');
            row.remove();
        }
    });

    // Add at least one item row if there are none
    if (document.querySelectorAll('.item-row').length === 0) {
        document.getElementById('add-item').click();
    }

    // Handle attachment removal
    document.querySelectorAll('.remove-attachment').forEach(button => {
        button.addEventListener('click', function() {
            const attachmentId = this.dataset.attachmentId;
            if (confirm('Are you sure you want to remove this attachment?')) {
                fetch(`/api/purchase-requests/${attachmentId}/remove-attachment`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.closest('li').remove();
                    } else {
                        alert('Error removing attachment');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error removing attachment');
                });
            }
        });
    });

    document.getElementById('items-container').addEventListener('input', function(e) {
        if (e.target.classList.contains('item-quantity') || e.target.classList.contains('item-unit-price')) {
            const row = e.target.closest('.item-row');
            const quantity = parseFloat(row.querySelector('.item-quantity').value) || 0;
            const unitPrice = parseFloat(row.querySelector('.item-unit-price').value) || 0;
            row.querySelector('.item-total').value = (quantity * unitPrice).toFixed(2);
        }
    });

    document.getElementById('items-container').addEventListener('change', function(e) {
        if (e.target.matches('select[name^="items"][name$="[material_id]"]')) {
            const row = e.target.closest('.item-row');
            const option = e.target.selectedOptions[0];
            if (option) {
                row.querySelector('input[name$="[description]"]').value = option.dataset.description || '';
                row.querySelector('input[name$="[unit]"]').value = option.dataset.unit || '';
                row.querySelector('input[name$="[estimated_unit_price]"]').value = option.dataset.price || '';
                if (row.querySelector('input[name$="[specifications]"]')) {
                    row.querySelector('input[name$="[specifications]"]').value = option.dataset.specifications || '';
                }
            }
        }
    });
</script>
@endpush 