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
                                                        <select class="form-select material-select" name="items[{{ $index }}][material_id]" required>
                                                            <option value="">Select Material</option>
                                                            @foreach($materials ?? [] as $material)
                                                                <option value="{{ $material->id }}"
                                                                    data-description="{{ $material->description }}"
                                                                    data-unit="{{ $material->unit }}"
                                                                    data-price="{{ $material->base_price }}"
                                                                    data-specifications="{{ $material->specifications }}"
                                                                    data-suppliers='@json($material->suppliers->map(fn($s) => ["id"=>$s->id,"name"=>$s->company_name,"is_preferred"=>$s->pivot->is_preferred??false]))'
                                                                    {{ (isset($item) && $item->material_id == $material->id) ? 'selected' : '' }}>
                                                                    {{ $material->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <input type="hidden" name="items[{{ $index }}][supplier_id]" class="supplier-id" />
                                                        <div class="supplier-name text-info small mt-1"></div>
                                                    </div>
                                                <div class="col-md-2">
                                                    <label>Supplier</label>
                                                    <div class="input-group">
                                                        <select class="form-select supplier-select select2" name="items[{{ $index }}][supplier_id]">
                                                            <option value="">Select Supplier</option>
                                                            @php
                                                                $matId = $item->material_id ?? null;
                                                                $best = $matId && isset($bestSuppliers[$matId]) ? $bestSuppliers[$matId] : null;
                                                            @endphp
                                                            @if(isset($item) && $item->supplier)
                                                                <option value="{{ $item->supplier->id }}" selected>{{ $item->supplier->company_name }}</option>
                                                            @elseif($best)
                                                                <option value="{{ $best['id'] }}" selected>{{ $suppliers->firstWhere('id', $best['id'])->company_name ?? 'Best Supplier' }}</option>
                                                            @endif
                                                        </select>
                                                        <button type="button" class="btn btn-info supplier-view-btn" disabled>
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                    </div>
                                                    @if($best)
                                                        <div class="text-success small mt-1">{{ $best['reason'] }}</div>
                                                    @endif
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

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    let itemIndex = {{ isset($purchaseRequest) ? $purchaseRequest->items->count() : 0 }};

    function createItemRow() {
        const template = `
            <div class="item-row mb-4">
                <div class="row">
                    <div class="col-md-2">
                            <label>Material</label>
                        <select class="form-select material-select" name="items[${itemIndex}][material_id]" required>
                                <option value="">Select Material</option>
                                @foreach($materials ?? [] as $material)
                                <option value="{{ $material->id }}"
                                    data-description="{{ $material->description }}"
                                    data-unit="{{ $material->unit }}"
                                    data-price="{{ $material->base_price }}"
                                    data-specifications="{{ $material->specifications }}"
                                    data-suppliers='@json($material->suppliers->map(fn($s) => ["id"=>$s->id,"name"=>$s->company_name,"is_preferred"=>$s->pivot->is_preferred??false]))'
                                >
                                    {{ $material->name }}
                                </option>
                                @endforeach
                            </select>
                    </div>
                    <div class="col-md-2">
                        <label>Supplier</label>
                        <div class="input-group">
                            <select class="form-select supplier-select select2" name="items[${itemIndex}][supplier_id]">
                                <option value="">Select Supplier</option>
                            </select>
                            <button type="button" class="btn btn-info supplier-view-btn" disabled>
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="best-supplier-reason text-success small mt-1"></div>
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
        
        if (e.target.classList.contains('supplier-view-btn') || e.target.closest('.supplier-view-btn')) {
            const row = e.target.closest('.item-row');
            const supplierId = row.querySelector('select[name$="[supplier_id]"]').value;
            if (supplierId) {
                window.open(`/admin/suppliers/${supplierId}`, '_blank');
            }
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
                
                // Auto-select preferred supplier if available
                const supplierSelect = row.querySelector('select[name$="[supplier_id]"]');
                const preferredSupplier = Array.from(supplierSelect.options).find(opt => 
                    opt.dataset.isPreferred === 'true'
                );
                if (preferredSupplier) {
                    supplierSelect.value = preferredSupplier.value;
                    const viewBtn = row.querySelector('.supplier-view-btn');
                    viewBtn.disabled = false;
                }
            }
        }
        
        if (e.target.matches('select[name^="items"][name$="[supplier_id]"]')) {
            const row = e.target.closest('.item-row');
            const viewBtn = row.querySelector('.supplier-view-btn');
            viewBtn.disabled = !e.target.value;
            
            // Update the hidden supplier_id field
            const supplierIdInput = row.querySelector('input[name$="[supplier_id]"]');
            if (supplierIdInput) {
                supplierIdInput.value = e.target.value;
            }
        }
    });

    $(document).ready(function() {
        // Initialize Select2 for existing supplier selects
        $('.supplier-select').select2({
            placeholder: 'Search for suppliers...',
            allowClear: true,
            ajax: {
                url: '{{ route("admin.suppliers.search") }}',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        search: params.term,
                        page: params.page || 1
                    };
                },
                processResults: function(data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.data.map(function(item) {
                            return {
                                id: item.id,
                                text: item.company_name
                            };
                        }),
                        pagination: {
                            more: data.current_page < data.last_page
                        }
                    };
                },
                cache: true
            },
            minimumInputLength: 2,
            templateResult: formatSupplier,
            templateSelection: formatSupplierSelection
        });

        function formatSupplier(supplier) {
            if (supplier.loading) {
                return supplier.text;
            }
            return $('<span>' + supplier.text + '</span>');
        }

        function formatSupplierSelection(supplier) {
            return supplier.text;
        }

        // Update the add-item template to include Select2
        const itemTemplate = `
            <div class="item-row mb-4">
                <div class="row">
                    <div class="col-md-2">
                        <label>Material</label>
                        <select class="form-select material-select" name="items[__INDEX__][material_id]" required>
                            <option value="">Select Material</option>
                            @foreach($materials ?? [] as $material)
                                <option value="{{ $material->id }}"
                                    data-description="{{ $material->description }}"
                                    data-unit="{{ $material->unit }}"
                                    data-price="{{ $material->base_price }}"
                                    data-specifications="{{ $material->specifications }}"
                                    data-suppliers='@json($material->suppliers->map(fn($s) => ["id"=>$s->id,"name"=>$s->company_name,"is_preferred"=>$s->pivot->is_preferred??false]))'>
                                    {{ $material->name }}
                                </option>
                            @endforeach
                        </select>
                        <input type="hidden" name="items[__INDEX__][supplier_id]" class="supplier-id" />
                        <div class="supplier-name text-info small mt-1"></div>
                    </div>
                    <div class="col-md-2">
                        <label>Supplier</label>
                        <div class="input-group">
                            <select class="form-select supplier-select select2" name="items[__INDEX__][supplier_id]">
                                <option value="">Select Supplier</option>
                            </select>
                            <button type="button" class="btn btn-info supplier-view-btn" disabled>
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="best-supplier-reason text-success small mt-1"></div>
                    </div>
                    <div class="col-md-2">
                        <label>Description</label>
                        <input type="text" class="form-control" name="items[__INDEX__][description]" required>
                    </div>
                    <div class="col-md-1">
                        <label>Qty</label>
                        <input type="number" class="form-control item-quantity" name="items[__INDEX__][quantity]" min="0.01" step="0.01" required>
                    </div>
                    <div class="col-md-1">
                        <label>Unit</label>
                        <input type="text" class="form-control" name="items[__INDEX__][unit]" required>
                    </div>
                    <div class="col-md-2">
                        <label>Unit Price</label>
                        <input type="number" class="form-control item-unit-price" name="items[__INDEX__][estimated_unit_price]" min="0.01" step="0.01" required>
                    </div>
                    <div class="col-md-2">
                        <label>Total</label>
                        <input type="number" class="form-control item-total" name="items[__INDEX__][total_amount]" readonly required>
                    </div>
                    <div class="col-md-2">
                        <label>Specifications</label>
                        <input type="text" class="form-control" name="items[__INDEX__][specifications]">
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="button" class="btn btn-danger remove-item"><i class="fas fa-trash"></i></button>
                    </div>
                </div>
            </div>
        `;

        // Update the add-item click handler to initialize Select2 for new items
        $('#add-item').click(function() {
            const itemIndex = $('.item-row').length;
            const newItem = itemTemplate.replace(/__INDEX__/g, itemIndex);
            $('#items-container').append(newItem);
            
            // Initialize Select2 for the new supplier select
            $(`select[name="items[${itemIndex}][supplier_id]"]`).select2({
                placeholder: 'Search for suppliers...',
                allowClear: true,
                ajax: {
                    url: '{{ route("admin.suppliers.search") }}',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            search: params.term,
                            page: params.page || 1
                        };
                    },
                    processResults: function(data, params) {
                        params.page = params.page || 1;
                        return {
                            results: data.data.map(function(item) {
                                return {
                                    id: item.id,
                                    text: item.company_name
                                };
                            }),
                            pagination: {
                                more: data.current_page < data.last_page
                            }
                        };
                    },
                    cache: true
                },
                minimumInputLength: 2,
                templateResult: formatSupplier,
                templateSelection: formatSupplierSelection
            });
        });
    });

    // In the JS, when a material is selected, set the best supplier and show the reason
    $(document).on('change', '.material-select', function() {
        var $row = $(this).closest('.item-row');
        var matId = $(this).val();
        var best = window.materialBestSuppliers && window.materialBestSuppliers[matId];
        var $supplierSelect = $row.find('.supplier-select');
        var $reasonDiv = $row.find('.best-supplier-reason');
        if (best) {
            // Set the supplier select
            var exists = $supplierSelect.find('option[value="'+best.id+'"]').length > 0;
            if (!exists) {
                $supplierSelect.append('<option value="'+best.id+'">Best Supplier</option>');
            }
            $supplierSelect.val(best.id).trigger('change');
            $reasonDiv.text(best.reason);
        } else {
            $supplierSelect.val('').trigger('change');
            $reasonDiv.text('');
        }
    });
</script>
@endpush 