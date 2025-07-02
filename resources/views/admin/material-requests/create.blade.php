@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Create Material Request</h3>
                    <div class="card-tools">
                        <a href="{{ route('material-requests.index') }}" class="btn btn-default">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <form action="{{ route('material-requests.store') }}" method="POST" id="materialRequestForm">
                    @csrf
                    <div class="card-body">
                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="contract_id">Contract</label>
                                    <select name="contract_id" id="contract_id" class="form-control" required>
                                        <option value="">Select a contract</option>
                                        @foreach($contracts as $contract)
                                            <option value="{{ $contract->id }}" {{ $selectedContract && $selectedContract->id == $contract->id ? 'selected' : '' }}>
                                                {{ $contract->contract_number }} - {{ $contract->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Items Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h4>Request Items</h4>
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="itemsTable">
                                        <thead>
                                            <tr>
                                                <th>Material</th>
                                                <th>Unit</th>
                                                <th>Available Stock</th>
                                                <th>Requested Quantity</th>
                                                <th>Notes</th>
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
                                                        <td>{{ $item['unit'] }}</td>
                                                        <td class="available-stock">{{ $item['available'] }}</td>
                                                        <td>
                                                            <input type="number" name="items[{{ $index }}][quantity]" class="form-control quantity" step="0.01" value="{{ $item['quantity'] }}" required>
                                                        </td>
                                                        <td>
                                                            <input type="text" name="items[{{ $index }}][notes]" class="form-control">
                                                        </td>
                                                        <td>
                                                            <button type="button" class="btn btn-info btn-sm recommend-supplier-btn" data-material-id="{{ $item['material_id'] }}" data-material-name="{{ $item['name'] }}">
                                                                <i class="fas fa-lightbulb"></i> Recommend Supplier
                                                            </button>
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
                                        <input type="checkbox" class="custom-control-input" id="create_purchase_request" name="create_purchase_request" value="1">
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
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="supplierRecommendationModalLabel">Supplier Recommendation for <span id="modalMaterialName"></span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="supplier-recommendation-form" class="row g-3 mb-3">
          <input type="hidden" id="modalMaterialId" name="material_id">
          <div class="col-md-3">
            <label for="rec_budget" class="form-label">Budget</label>
            <input type="number" class="form-control" id="rec_budget" name="budget" value="100000" min="0" step="0.01">
          </div>
          <div class="col-md-3">
            <label for="rec_on_time_delivery_rate" class="form-label">On-Time Delivery Rate (%)</label>
            <input type="number" class="form-control" id="rec_on_time_delivery_rate" name="on_time_delivery_rate" value="90" min="0" max="100">
          </div>
          <div class="col-md-3">
            <label for="rec_average_defect_rate" class="form-label">Avg. Defect Rate (%)</label>
            <input type="number" class="form-control" id="rec_average_defect_rate" name="average_defect_rate" value="2" min="0" max="100" step="0.01">
          </div>
          <div class="col-md-3">
            <label for="rec_average_cost_variance" class="form-label">Avg. Cost Variance</label>
            <input type="number" class="form-control" id="rec_average_cost_variance" name="average_cost_variance" value="0" step="0.01">
          </div>
          <div class="col-md-12">
            <label for="rec_mode" class="form-label">Recommendation Mode</label>
            <select class="form-select" id="rec_mode" name="mode">
              <option value="best_score">Best Overall Score</option>
              <option value="lowest_cost">Lowest Cost</option>
              <option value="best_delivery">Best On-Time Delivery</option>
            </select>
          </div>
          <div class="col-md-12 d-flex align-items-end">
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

    // Handle contract change
    document.getElementById('contract_id').addEventListener('change', function() {
        const contractId = this.value;
        if (contractId) {
            window.location.href = '{{ route("material-requests.create") }}?contract_id=' + contractId;
        }
    });

    // Add new item row
    document.getElementById('addRow').addEventListener('click', function() {
        const newRow = `
            <tr class="item-row">
                <td>
                    <select name="items[${itemIndex}][material_id]" class="form-control material-select" required>
                        <option value="">Select Material</option>
                        ${materials.map(m => `<option value="${m.id}">${m.name}</option>`).join('')}
                    </select>
                </td>
                <td class="unit"></td>
                <td class="available-stock"></td>
                <td>
                    <input type="number" name="items[${itemIndex}][quantity]" class="form-control quantity" step="0.01" required>
                </td>
                <td>
                    <input type="text" name="items[${itemIndex}][notes]" class="form-control">
                </td>
                <td>
                    <button type="button" class="btn btn-info btn-sm recommend-supplier-btn" data-material-id="${itemIndex}" data-material-name="${materials[itemIndex].name}">
                        <i class="fas fa-lightbulb"></i> Recommend Supplier
                    </button>
                    <button type="button" class="btn btn-danger btn-sm remove-row" title="Remove"><i class="fas fa-trash"></i></button>
                </td>
            </tr>
        `;
        document.getElementById('items-container').insertAdjacentHTML('beforeend', newRow);
        itemIndex++;
    });

    // Handle material selection in a row
    document.getElementById('items-container').addEventListener('change', function(e) {
        if (e.target.classList.contains('material-select')) {
            const selectedMaterialId = e.target.value;
            const row = e.target.closest('.item-row');
            if (selectedMaterialId) {
                // Fetch material details (including inventory)
                fetch(`/api/materials/${selectedMaterialId}`)
                    .then(response => response.json())
                    .then(data => {
                        row.querySelector('.unit').textContent = data.unit;
                        row.querySelector('.available-stock').textContent = data.inventory ? data.inventory.quantity : 0;
                    });
            } else {
                row.querySelector('.unit').textContent = '';
                row.querySelector('.available-stock').textContent = '';
            }
        }
    });

    // Remove row
    document.getElementById('items-container').addEventListener('click', function(e) {
        if (e.target.closest('.remove-row')) {
            e.target.closest('.item-row').remove();
        }
    });

    // Recommend Supplier button click
    document.querySelectorAll('.recommend-supplier-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const materialId = this.getAttribute('data-material-id');
            const materialName = this.getAttribute('data-material-name');
            document.getElementById('modalMaterialId').value = materialId;
            document.getElementById('modalMaterialName').textContent = materialName;
            document.getElementById('supplier-recommendation-results').innerHTML = '';
            var modal = new bootstrap.Modal(document.getElementById('supplierRecommendationModal'));
            modal.show();
        });
    });

    // Handle recommendation form submit
    document.getElementById('supplier-recommendation-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const form = e.target;
        const params = new URLSearchParams(new FormData(form)).toString();
        fetch('/material-requests/recommend-suppliers-for-material?' + params, {
            method: 'GET',
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('supplier-recommendation-results').innerHTML = data.html;
        });
    });
});
</script>
@endpush
@endsection