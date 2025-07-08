@extends('layouts.app')

@push('styles')
<style>
.quotation-card {
    max-width: 700px;
    margin: 40px auto;
    border-radius: 18px;
    box-shadow: 0 4px 24px rgba(0,0,0,0.10);
    background: #fff;
    padding: 32px 24px 24px 24px;
    position: relative;
}
.quotation-card h2 {
    font-weight: 700;
    color: #198754;
}
.quotation-card .lead {
    font-size: 1.1rem;
    color: #444;
}
.quotation-card .badge.bg-primary {
    font-size: 1rem;
    padding: 0.5em 1em;
    border-radius: 8px;
}
.quotation-card .badge.bg-warning {
    color: #fff;
    background: #f59e42;
}
.quotation-card .table {
    margin-top: 24px;
    border-radius: 10px;
    overflow: hidden;
    background: #f8fafc;
}
.quotation-card .table th, .quotation-card .table td {
    vertical-align: middle;
    text-align: center;
    background: #f8fafc;
}
.quotation-card .table-striped > tbody > tr:nth-of-type(odd) {
    background-color: #f1f5f9;
}
.quotation-card .btn-primary {
    background: #2563eb;
    border: none;
    border-radius: 24px;
    padding: 0.75em 2em;
    font-weight: 600;
    transition: background 0.2s;
}
.quotation-card .btn-primary:hover {
    background: #1d4ed8;
}
.quotation-card .btn-secondary {
    background: #6c757d;
    border: none;
    border-radius: 24px;
    padding: 0.75em 2em;
    font-weight: 600;
    transition: background 0.2s;
}
.quotation-card .btn-secondary:hover {
    background: #495057;
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
@media (max-width: 768px) {
    .quotation-card {
        padding: 16px 4px 16px 4px;
    }
}
</style>
@endpush

@section('content')
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
                <table class="table table-bordered table-striped">
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
                                                <li><strong>{{ $scope->scope_name }}</strong> ({{ $scope->scope_category }})</li>
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
                <table class="table table-bordered table-striped">
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
            @if(isset($materialSupplierResponses) && count($materialSupplierResponses) > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-light">
                            <tr>
                                <th>Material</th>
                                <th>Supplier Offers</th>
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
                                        @if(count($offers) > 0)
                                            <ul class="list-unstyled mb-0">
                                                @foreach($offers as $offer)
                                                    <li>
                                                        <span class="fw-bold">{{ $offer['supplier_name'] }}</span>
                                                        <span class="badge bg-primary">₱{{ number_format($offer['unit_price'], 2) }}</span>
                                                        @if(!empty($offer['badges']))
                                                            @foreach($offer['badges'] as $badge)
                                                                <span class="badge bg-info">{{ $badge }}</span>
                                                            @endforeach
                                                        @endif
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <span class="text-muted">No supplier offers yet.</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(count($offers) > 0)
                                            <select name="selected_suppliers[{{ $materialId }}]" class="form-select">
                                                <option value="">Select Supplier</option>
                                                @foreach($offers as $offer)
                                                    <option value="{{ $offer['supplier_id'] }}" @if(isset($selectedSuppliers[$materialId]) && $selectedSuppliers[$materialId] == $offer['supplier_id']) selected @endif>
                                                        {{ $offer['supplier_name'] }} (₱{{ number_format($offer['unit_price'], 2) }})
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
            <div class="mt-3 text-end">
                <button type="submit" class="btn btn-success btn-lg">Submit Final Selection</button>
            </div>
        </form>
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
@endsection 

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
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
                        if (select) select.value = supplierId;
                    }
                }
                recommendModal.hide();
            });
    });
});
</script>
@endpush 