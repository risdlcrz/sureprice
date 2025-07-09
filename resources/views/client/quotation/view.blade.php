@extends('layouts.app')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
body {
    font-family: 'Inter', Arial, sans-serif;
    background: linear-gradient(120deg, #f8fafc 0%, #e0e7ef 100%);
    min-height: 100vh;
}
.quotation-card {
    max-width: 950px;
    margin: 48px auto 32px auto;
    border-radius: 22px;
    box-shadow: 0 8px 32px rgba(0,0,0,0.13);
    background: #fff;
    padding: 48px 40px 40px 40px;
    position: relative;
    border: 1px solid #e5e7eb;
}
.quotation-card h2 {
    font-weight: 700;
    color: #2563eb;
    letter-spacing: 1px;
    margin-bottom: 0.5em;
}
.quotation-card .lead {
    font-size: 1.18rem;
    color: #374151;
    margin-bottom: 1.5em;
}
.quotation-card .badge {
    font-size: 0.98rem;
    padding: 0.45em 1em;
    border-radius: 8px;
    margin-right: 0.25em;
    font-weight: 500;
    letter-spacing: 0.5px;
}
.badge-cheapest { background: #22c55e; color: #fff; }
.badge-delivery { background: #0ea5e9; color: #fff; }
.badge-defects { background: #fbbf24; color: #333; }
.badge-overall { background: #6366f1; color: #fff; }
.quotation-card .table {
    margin-top: 24px;
    border-radius: 14px;
    overflow: visible;
    background: #f8fafc;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    width: 100%;
    min-width: unset;
    table-layout: auto;
}
.quotation-card .table th, .quotation-card .table td {
    vertical-align: middle;
    text-align: center;
    background: #f8fafc;
    font-size: 1.08rem;
    border-bottom: 1px solid #e5e7eb;
    min-width: 120px;
    word-break: break-word;
}
.quotation-card .table th {
    background: #f1f5f9;
    position: sticky;
    top: 0;
    z-index: 2;
    font-weight: 600;
    color: #2563eb;
    letter-spacing: 0.5px;
}
.quotation-card .table-striped > tbody > tr:nth-of-type(odd) {
    background-color: #f3f6fa;
}
.quotation-card .form-select, .select2-container--default .select2-selection--single {
    border-radius: 12px;
    border: 1.5px solid #cbd5e1;
    font-size: 1.05rem;
    min-height: 44px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.03);
    transition: border 0.2s;
}
.quotation-card .form-select:focus, .select2-container--default .select2-selection--single:focus {
    border-color: #2563eb;
    outline: none;
}
.select2-container--default .select2-selection--single {
    height: 44px;
    padding: 8px 12px;
    font-size: 1.05rem;
    display: flex;
    align-items: center;
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 44px;
}
.select2-results__option .badge { margin-left: 0.5em; font-size: 0.9em; }
.quotation-card .btn-primary {
    background: linear-gradient(90deg, #2563eb 0%, #6366f1 100%);
    border: none;
    border-radius: 24px;
    padding: 0.85em 2.2em;
    font-weight: 700;
    font-size: 1.08rem;
    transition: background 0.2s, box-shadow 0.2s;
    box-shadow: 0 2px 8px rgba(99,102,241,0.08);
}
.quotation-card .btn-primary:hover {
    background: linear-gradient(90deg, #1d4ed8 0%, #6366f1 100%);
    box-shadow: 0 4px 16px rgba(99,102,241,0.13);
}
.quotation-card .btn-success {
    background: linear-gradient(90deg, #22c55e 0%, #16a34a 100%);
    border: none;
    border-radius: 24px;
    padding: 0.85em 2.2em;
    font-weight: 700;
    font-size: 1.08rem;
    transition: background 0.2s, box-shadow 0.2s;
    box-shadow: 0 2px 8px rgba(34,197,94,0.08);
}
.quotation-card .btn-success:hover {
    background: linear-gradient(90deg, #16a34a 0%, #22c55e 100%);
    box-shadow: 0 4px 16px rgba(34,197,94,0.13);
}
.quotation-card .btn-danger {
    background: linear-gradient(90deg, #ef4444 0%, #f87171 100%);
    border: none;
    border-radius: 24px;
    padding: 0.85em 2.2em;
    font-weight: 700;
    font-size: 1.08rem;
    transition: background 0.2s, box-shadow 0.2s;
    box-shadow: 0 2px 8px rgba(239,68,68,0.08);
}
.quotation-card .btn-danger:hover {
    background: linear-gradient(90deg, #b91c1c 0%, #ef4444 100%);
    box-shadow: 0 4px 16px rgba(239,68,68,0.13);
}
.quotation-card .alert-success {
    background: #e6f9ed;
    color: #198754;
    border: none;
    border-radius: 10px;
    font-weight: 500;
    margin-bottom: 24px;
    text-align: center;
}
.quotation-card .alert-info {
    background: #e7f3fe;
    color: #2563eb;
    border: none;
    border-radius: 10px;
    font-weight: 500;
    margin-bottom: 24px;
    text-align: center;
}
.table-responsive {
    overflow-x: unset;
    width: 100%;
    min-width: unset;
}
@media (max-width: 991.98px) {
    .quotation-card {
        padding: 10px 2px 10px 2px;
    }
    .quotation-card .table {
        font-size: 0.93rem;
        min-width: unset;
        table-layout: auto;
    }
    .quotation-card .table th, .quotation-card .table td {
        min-width: 80px;
        padding: 6px 4px;
    }
    .quotation-card h2 {
        font-size: 1.3rem;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid my-4">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card shadow-sm quotation-card">
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    <h2 class="mb-4 text-success text-center">Quotation Request Submitted!</h2>
                    <p class="lead text-center">Thank you for your request. Our team will review your details and contact you soon.</p>
                    
                    @if($quotationRequest)
                        <div class="text-center mb-4">
                            <h5>Request Number: <span class="badge bg-primary">{{ $quotationRequest->request_number }}</span></h5>
                            <p class="text-muted">Status: <span class="badge bg-{{ $quotationRequest->status_color }}">{{ $quotationRequest->status_label }}</span></p>
                        </div>
                    @endif
                    
                    <hr>
                    <h4 class="mb-3 text-center">Quotation Request Details</h4>
                    
                    @if($quotationRequest && $quotationRequest->rooms->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped w-100" style="width: 100%; min-width: unset; table-layout: auto;">
                                <thead class="table-light">
                                    <tr>
                                        <th>Room</th>
                                        <th>Dimensions</th>
                                        <th>Area</th>
                                        <th>Volume</th>
                                        <th>Scopes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($quotationRequest->rooms as $room)
                                        <tr>
                                            <td><strong>{{ $room->name }}</strong></td>
                                            <td>{{ $room->length }}m × {{ $room->width }}m × {{ $room->height }}m</td>
                                            <td>{{ $room->area }} sqm</td>
                                            <td>{{ $room->volume }} cubic m</td>
                                            <td>
                                                @if($room->scopes->count() > 0)
                                                    <ul class="list-unstyled mb-0">
                                                        @foreach($room->scopes as $scope)
                                                            <li><strong>{{ $scope->scope_name }}</strong> ({{ $scope->scope_category }})
                                                                @if(is_array($scope->selected_materials) && count($scope->selected_materials) > 0)
                                                                    <div class="table-responsive mt-2">
                                                                        <table class="table table-sm table-bordered">
                                                                            <thead>
                                                                                <tr>
                                                                                    <th>Material</th>
                                                                                    <th>Requested Quantity</th>
                                                                                    <th>Unit</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                @foreach($scope->selected_materials as $mat)
                                                                                    @php
                                                                                        $material = \App\Models\Material::find($mat['material_id']);
                                                                                    @endphp
                                                                                    <tr>
                                                                                        <td>{{ $material ? $material->name : 'Material #'.$mat['material_id'] }}</td>
                                                                                        <td>{{ $mat['quantity'] }}{{ $mat['coverage_info'] ?? '' }}</td>
                                                                                        <td>{{ $mat['unit'] ?? ($material ? $material->unit : '') }}</td>
                                                                                    </tr>
                                                                                @endforeach
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                @endif
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    <span class="text-muted">No scopes selected</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @elseif($sessionData)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped w-100" style="min-width: 700px;">
                                <thead class="table-light">
                                    <tr>
                                        <th>Room</th>
                                        <th>Dimensions</th>
                                        <th>Area</th>
                                        <th>Volume</th>
                                        <th>Scopes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($sessionData['rooms'] as $room)
                                        @php
                                            $area = $room['length'] * $room['width'];
                                            $volume = $area * $room['height'];
                                        @endphp
                                        <tr>
                                            <td><strong>{{ $room['name'] }}</strong></td>
                                            <td>{{ $room['length'] }}m × {{ $room['width'] }}m × {{ $room['height'] }}m</td>
                                            <td>{{ $area }} sqm</td>
                                            <td>{{ $volume }} cubic m</td>
                                            <td>
                                                @if(isset($room['scope']) && count($room['scope']) > 0)
                                                    <ul class="list-unstyled mb-0">
                                                        @foreach($room['scope'] as $scopeCode => $scopeData)
                                                            @if(!empty($scopeData['materials']))
                                                                <li><strong>{{ $scopeCode }}</strong></li>
                                                            @endif
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    <span class="text-muted">No scopes selected</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info text-center">
                            No quotation request data found.
                        </div>
                    @endif

                    <hr>
                    <h4 class="mb-3 text-center">Supplier Offers & Selection</h4>
                    <form method="POST" action="{{ route('client.quotation.finalize', ['id' => $quotationRequest->id]) }}" id="client-finalize-form">
                        @csrf
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold">Choose your preferred supplier for each material below:</span>
                            <button type="button" class="btn btn-primary" id="clientRecommendAllBtn">Recommend for All</button>
                        </div>
                        <div class="row mt-4">
                            <div class="col-12 col-lg-6 mb-4 mb-lg-0">
                                {{-- Materials Table (existing) --}}
                                @if(isset($materialSupplierResponses) && count($materialSupplierResponses) > 0)
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped w-100" style="width: 100%; min-width: 900px; table-layout: fixed;">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Material</th>
                                                    <th>Supplier Responses</th>
                                                    <th>Your Selection</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($materialSupplierResponses as $materialId => $offers)
                                                    <tr>
                                                        <td>
                                                            @php
                                                                $materialName = null;
                                                                if($quotationRequest) {
                                                                    foreach($quotationRequest->rooms as $room) {
                                                                        foreach($room->scopes as $scope) {
                                                                            if($scope->scopeType && $scope->scopeType->materials) {
                                                                                foreach($scope->scopeType->materials as $mat) {
                                                                                    if($mat->id == $materialId) $materialName = $mat->name;
                                                                                }
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            @endphp
                                                            <strong>{{ $materialName ?? 'Material #'.$materialId }}</strong>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-secondary">{{ count($offers) }}</span>
                                                        </td>
                                                        <td>
                                                            @if(count($offers) > 0)
                                                                <select name="selected_suppliers[{{ $materialId }}]" class="form-select supplier-select" data-material-id="{{ $materialId }}">
                                                                    <option value="">Select Supplier</option>
                                                                    @foreach($offers as $offer)
                                                                        <option value="{{ $offer['supplier_id'] }}"
                                                                            data-badges='@json($offer["badges"] ?? [])'
                                                                            data-supplier="{{ $offer['supplier_name'] ?? 'Unknown' }}"
                                                                            data-price="{{ isset($offer['unit_price']) ? number_format($offer['unit_price'], 2) : '0.00' }}"
                                                                            @if(isset($selectedSuppliers[$materialId]) && $selectedSuppliers[$materialId] == $offer['supplier_id']) selected @endif>
                                                                            {{ $offer['supplier_name'] ?? 'Unknown' }} (₱{{ isset($offer['unit_price']) ? number_format($offer['unit_price'], 2) : '0.00' }})
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            @else
                                                                <span class="text-muted">No selection</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="alert alert-info text-center">No supplier offers available yet.</div>
                                @endif
                            </div>
                        </div>
                        <div class="mt-3 text-end">
                            <button type="submit" class="btn btn-success btn-lg">Submit Final Selection</button>
                        </div>
                    </form>
                    <div class="d-flex justify-content-center gap-3 mt-4">
                        <form method="POST" action="{{ route('client.quotation.cancel', ['id' => $quotationRequest->id]) }}" onsubmit="return confirm('Are you sure you want to cancel this quotation?');">
                            @csrf
                            <button type="submit" class="btn btn-danger btn-lg">Cancel Quotation</button>
                        </form>
                        <form method="POST" action="{{ route('client.quotation.proceed', ['id' => $quotationRequest->id]) }}">
                            @csrf
                            <button type="submit" class="btn btn-success btn-lg">Proceed with Quotation</button>
                        </form>
                    </div>
                    <!-- Recommend Modal for Client -->
                    <div class="modal fade" id="clientRecommendModal" tabindex="-1" aria-labelledby="clientRecommendModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="clientRecommendModalLabel">Recommend Suppliers for All Materials</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <label for="clientRecommendCategory" class="form-label">Optimization Category</label>
                                    <select id="clientRecommendCategory" class="form-select">
                                        <option value="overall_best">Overall Best</option>
                                        <option value="cheapest">Cheapest</option>
                                        <option value="fastest_delivery">Best Delivery</option>
                                        <option value="least_defects">Least Defects</option>
                                    </select>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="button" class="btn btn-primary" id="clientApplyRecommendBtn">Apply Recommendation</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <a href="{{ route('client.quotation.create') }}" class="btn btn-primary me-2">Request Another Quotation</a>
                        <a href="{{ route('client.quotation.index') }}" class="btn btn-success me-2">View All Requests</a>
                        <a href="{{ url('/') }}" class="btn btn-secondary">Back to Home</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Only initialize Select2 once for all supplier-select elements
    if (window.__supplierSelect2Initialized !== true) {
        window.__supplierSelect2Initialized = true;
        $('.supplier-select').select2({
            width: '100%',
            templateResult: function (data) {
                if (!data.id) return data.text;
                var badges = [];
                try { badges = JSON.parse($(data.element).attr('data-badges')); } catch {}
                var html = '<span>' + $(data.element).attr('data-supplier') + ' (₱' + $(data.element).attr('data-price') + ')';
                if (badges && badges.length) {
                    badges.forEach(function(badge) {
                        if (badge === 'Cheapest') html += ' <span class="badge badge-cheapest">Cheapest</span>';
                        if (badge === 'Best Delivery') html += ' <span class="badge badge-delivery">Best Delivery</span>';
                        if (badge === 'Least Defects') html += ' <span class="badge badge-defects">Least Defects</span>';
                        if (badge === 'Overall Best') html += ' <span class="badge badge-overall">Overall Best</span>';
                    });
                }
                html += '</span>';
                return $(html);
            },
            templateSelection: function (data) {
                if (!data.id) return data.text;
                var badges = [];
                try { badges = JSON.parse($(data.element).attr('data-badges')); } catch {}
                var html = '<span>' + $(data.element).attr('data-supplier') + ' (₱' + $(data.element).attr('data-price') + ')';
                if (badges && badges.length) {
                    badges.forEach(function(badge) {
                        if (badge === 'Cheapest') html += ' <span class="badge badge-cheapest">Cheapest</span>';
                        if (badge === 'Best Delivery') html += ' <span class="badge badge-delivery">Best Delivery</span>';
                        if (badge === 'Least Defects') html += ' <span class="badge badge-defects">Least Defects</span>';
                        if (badge === 'Overall Best') html += ' <span class="badge badge-overall">Overall Best</span>';
                    });
                }
                html += '</span>';
                return $(html);
            },
            escapeMarkup: function (m) { return m; }
        });
    }
    const recommendBtn = document.getElementById('clientRecommendAllBtn');
    const recommendModal = new bootstrap.Modal(document.getElementById('clientRecommendModal'));
    const applyRecommendBtn = document.getElementById('clientApplyRecommendBtn');
    recommendBtn.addEventListener('click', function() {
        recommendModal.show();
    });
    applyRecommendBtn.addEventListener('click', function() {
        const category = document.getElementById('clientRecommendCategory').value;
        fetch(`{{ url('client/quotation/recommend-suppliers') }}?id={{ $quotationRequest->id }}&category=${category}`)
            .then(res => res.json())
            .then(data => {
                if (data && data.recommendations) {
                    for (const materialId in data.recommendations) {
                        const supplierId = data.recommendations[materialId];
                        const select = document.querySelector(`select[name='selected_suppliers[${materialId}]']`);
                        if (select) {
                            $(select).val(supplierId).trigger('change');
                        }
                    }
                }
                recommendModal.hide();
            });
    });
});
</script>
@endpush 