@extends('layouts.app')

@section('content')
<h1 class="h3 mb-4 text-gray-800 text-center" style="font-weight:700;color:#198754;letter-spacing:0.01em;">Create Material Request</h1>
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
                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        <!-- Items Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h4>Request Items</h4>
                                @if(isset($selectedSuppliers) && count($selectedSuppliers) > 0)
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i>
                                        <strong>Note:</strong> Suppliers have been pre-selected by the client during the quotation process. 
                                        You can modify these selections if needed, but the client's choices are recommended.
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
                                                                <span class="text-success font-weight-bold">{{ $selectedSupplier ? $selectedSupplier->company_name : 'Client Selected Supplier' }}</span>
                                                                <small class="text-muted d-block">(Client's choice)</small>
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
<div class="modal fade" id="supplierRecommendationModal" tabindex="-1" aria-labelledby="supplierRecommendationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="supplierRecommendationModalLabel">Supplier Recommendation for <span id="modalMaterialName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
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
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let itemIndex = {{ !empty($items) ? count($items) : 0 }};
    const materials = @json($materials);
    let currentSupplierRow = null;

    // Handle contract change
    var contractSelect = document.getElementById('contract_id');
    if (contractSelect) {
        contractSelect.addEventListener('change', function() {
            const contractId = this.value;
            if (contractId) {
                window.location.href = '{{ route("material-requests.create") }}?contract_id=' + contractId;
            }
        });
    }

    // Add new item row
    var addRowBtn = document.getElementById('addRow');
    if (addRowBtn) {
        addRowBtn.addEventListener('click', function() {
            const newRow = `
                <tr class="item-row">
                    <td>
                        <select name="items[${itemIndex}][material_id]" class="form-control material-select" required>
                            <option value="">Select Material</option>
                            ${materials.map(m => `<option value="${m.id}">${m.name}</option>`).join('')}
                        </select>
                    </td>
                    <td class="unit">
                        <select name="items[${itemIndex}][unit]" class="form-control unit-select" required>
                            <option value="">Select Unit</option>
                            <option value="pcs">Pcs</option>
                            <option value="kg">Kg</option>
                            <option value="m">M</option>
                            <option value="set">Set</option>
                            <option value="box">Box</option>
                        </select>
                    </td>
                    <td>
                        <span class="badge bg-secondary stock-display">0.00</span>
                    </td>
                    <td>
                        <input type="number" name="items[${itemIndex}][quantity]" class="form-control quantity" step="0.01" required>
                    </td>
                    <td>
                        <select name="items[${itemIndex}][preferred_supplier_id]" class="form-control supplier-select">
                            <option value="">Select Supplier</option>
                        </select>
                    </td>
                    <td>
                        <button type="button" class="btn btn-info btn-sm recommend-supplier-btn" data-material-id="" data-material-name="">\
                            <i class="fas fa-lightbulb"></i> Recommend Supplier
                        </button>
                        <button type="button" class="btn btn-danger btn-sm remove-row" title="Remove"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
            `;
            document.getElementById('items-container').insertAdjacentHTML('beforeend', newRow);
            itemIndex++;
        });
    }

    // Delegate recommend supplier button click
    document.getElementById('items-container').addEventListener('click', function(e) {
        const btn = e.target.closest('.recommend-supplier-btn');
        if (btn) {
            const row = btn.closest('.item-row');
            currentSupplierRow = row;
            let materialName = '';
            let materialId = '';
            // Try to get from select or from static data
            const select = row.querySelector('.material-select');
            if (select) {
                materialId = select.value;
                materialName = select.options[select.selectedIndex]?.text || '';
            } else {
                materialId = btn.getAttribute('data-material-id');
                materialName = btn.getAttribute('data-material-name');
            }
            document.getElementById('modalMaterialId').value = materialId;
            document.getElementById('modalMaterialName').textContent = materialName;
            document.getElementById('supplier-recommendation-results').innerHTML = '';
            var modal = new bootstrap.Modal(document.getElementById('supplierRecommendationModal'));
            modal.show();
        }
    });

    // Handle recommendation form submit
    document.getElementById('supplier-recommendation-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const form = e.target;
        const params = new URLSearchParams(new FormData(form)).toString();
        fetch('{{ url("material-requests/recommend-suppliers-for-material") }}?' + params, {
            method: 'GET',
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('supplier-recommendation-results').innerHTML = data.html;
        });
    });

    // Delegate select supplier button click in modal
    document.getElementById('supplier-recommendation-results').addEventListener('click', function(e) {
        const btn = e.target.closest('.select-supplier-btn');
        if (btn && currentSupplierRow) {
            const supplierName = btn.getAttribute('data-supplier-name');
            const supplierId = btn.getAttribute('data-supplier-id');
            const supplierSelect = currentSupplierRow.querySelector('.supplier-select');
            if (supplierSelect) {
                supplierSelect.value = supplierId;
            }
            // Close modal
            const modalEl = document.getElementById('supplierRecommendationModal');
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();
        }
    });

    // Handle material selection to populate supplier dropdown and update stock
    document.addEventListener('change', function(e) {
        if (e.target.matches('.material-select')) {
            const row = e.target.closest('.item-row');
            const materialId = e.target.value;
            const supplierSelect = row.querySelector('.supplier-select');
            const stockDisplay = row.querySelector('.stock-display');
            
            if (materialId) {
                // Update stock display
                if (stockDisplay) {
                    // Fetch stock information for this material
                    fetch(`/admin/materials/${materialId}/stock`)
                        .then(response => response.json())
                        .then(data => {
                            const totalStock = data.total_stock || 0;
                            stockDisplay.textContent = totalStock.toFixed(2);
                            stockDisplay.className = `badge bg-${totalStock > 0 ? 'success' : 'danger'} stock-display`;
                        })
                        .catch(error => {
                            console.error('Error fetching stock:', error);
                            stockDisplay.textContent = '0.00';
                            stockDisplay.className = 'badge bg-secondary stock-display';
                        });
                }
                
                // Update supplier dropdown
                if (supplierSelect) {
                    // Clear existing options
                    supplierSelect.innerHTML = '<option value="">Select Supplier</option>';
                    
                    // Fetch suppliers for this material
                    fetch(`/admin/materials/${materialId}/suppliers`)
                        .then(response => response.json())
                        .then(data => {
                            data.suppliers.forEach(supplier => {
                                const option = document.createElement('option');
                                option.value = supplier.id;
                                option.textContent = supplier.company_name;
                                supplierSelect.appendChild(option);
                            });
                        })
                        .catch(error => {
                            console.error('Error fetching suppliers:', error);
                        });
                }
            }
        }
    });
});
</script>
@endpush

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
<style>
body, .container-fluid, .card {
    font-family: 'Inter', Arial, sans-serif;
    background: linear-gradient(120deg, #f8fafc 0%, #e0e7ef 100%);
}
.card {
    border-radius: 18px;
    box-shadow: 0 4px 24px rgba(0,0,0,0.07);
    border: 1px solid #e5e7eb;
    margin-bottom: 0.5rem;
}
.card-header, .card-footer {
    background: #f1f5f9;
    border-radius: 18px 18px 0 0;
    border-bottom: 1px solid #e5e7eb;
}
.card-footer {
    border-radius: 0 0 18px 18px;
    border-top: 1px solid #e5e7eb;
}
.table-responsive {
    width: 100%;
    overflow-x: unset !important;
    margin-bottom: 0;
}
.table {
    width: 100%;
    table-layout: fixed;
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    margin-bottom: 0;
}
.table th, .table td {
    padding: 6px 2px;
    font-size: 0.95rem;
    word-break: break-word;
    white-space: normal;
    vertical-align: middle;
    text-align: center;
    background: #f8fafc;
    border-bottom: 1px solid #e5e7eb;
}
.table th {
    background: #e0e7ef;
    font-weight: 600;
    color: #2563eb;
    letter-spacing: 0.5px;
    font-size: 0.97rem;
}
.table-striped > tbody > tr:nth-of-type(odd) {
    background-color: #f3f6fa;
}
.form-control, .form-control-plaintext {
    border-radius: 8px;
    border: 1.5px solid #cbd5e1;
    font-size: 0.95rem;
    min-height: 32px;
    background: #f8fafc;
}
.form-control-plaintext[readonly] {
    background: #f1f5f9;
    color: #6b7280;
    border: none;
    font-style: italic;
}
input[type="number"].form-control {
    text-align: right;
}
.btn-success, .btn-primary {
    border-radius: 24px;
    padding: 0.4em 1.1em;
    font-weight: 600;
    font-size: 1.01rem;
}
#addRow {
    margin-top: 8px;
    font-size: 1.01rem;
    font-weight: 600;
    border-radius: 18px;
    background: linear-gradient(90deg, #22c55e 0%, #16a34a 100%);
    color: #fff;
    border: none;
    box-shadow: 0 2px 8px rgba(34,197,94,0.08);
}
#addRow:hover {
    background: linear-gradient(90deg, #16a34a 0%, #22c55e 100%);
}
@media (max-width: 991.98px) {
    .card {
        padding: 0 2px;
    }
    .table th, .table td {
        font-size: 0.93rem;
        padding: 4px 1px;
    }
}
</style>
@endpush
@endsection