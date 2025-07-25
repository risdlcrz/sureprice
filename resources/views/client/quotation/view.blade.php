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
    max-width: 1600px;
    margin: 40px auto 32px auto;
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
.quotation-card .table, .quotation-card .table-responsive {
    width: 100% !important;
    min-width: 0 !important;
    max-width: 100% !important;
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
                    <!-- Top Title Row -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h1 class="fw-bold mb-2" style="font-size:2rem; color:#2563eb;">Quotation Request Details</h1>
                            <div class="d-flex align-items-center gap-3">
                                <span class="fs-6 fw-semibold">Request Number: <span class="badge bg-primary fs-6">{{ $quotationRequest->request_number }}</span></span>
                                <span class="fs-6 fw-semibold">Status: <span class="badge bg-{{ $quotationRequest->status_color }} fs-6">{{ $quotationRequest->status_label }}</span></span>
                            </div>
                        </div>
                        <div class="d-flex flex-wrap gap-2 justify-content-end">
                            <a href="{{ route('client.quotation.create') }}" class="btn btn-primary btn-sm">Request Another Quotation</a>
                            <a href="{{ route('client.quotation.index') }}" class="btn btn-success btn-sm">View All Requests</a>
                            <a href="{{ url('/') }}" class="btn btn-secondary btn-sm">Back to Home</a>
                        </div>
                    </div>
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    <hr>
                    <h3 class="mb-3 fw-bold" style="font-size:1.5rem;">Quotation Request Details</h3>
                    
                    @if($quotationRequest && $quotationRequest->rooms->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped w-100">
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
                        <div class="table-responsive" style="min-width: 1200px; width: 100%;">
                            <table class="table table-bordered table-striped w-100" style="width: 100%; min-width: 1200px; table-layout: fixed;">
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
                    <h3 class="mb-3 fw-bold" style="font-size:1.5rem;">Supplier Offers & Selection</h3>
                    @if($quotationRequest->status === 'pending' || $quotationRequest->status === 'reviewed')
                    <div class="alert alert-info alert-static mb-3" id="discount-tip-alert">
                        <strong>Tip:</strong> If you choose the <b>same supplier for all materials</b>, you may get a discount if that supplier offers one. If you choose different suppliers, <b>no discount will be applied</b>.
                    </div>
                    @endif
                    @php
                        $uniqueSuppliers = collect($selectedSuppliers)->unique()->filter();
                        $showDiscountSummary = $uniqueSuppliers->count() === 1 && count($selectedSuppliers) > 0;
                        $awardedSupplierId = $showDiscountSummary ? $uniqueSuppliers->first() : null;
                        $totalQuoted = 0;
                        $discountType = null;
                        $discountValue = null;
                        $finalAmount = null;
                        $discountFound = false;
                        if($showDiscountSummary && isset($rfqs)) {
                            foreach($rfqs as $rfq) {
                                $response = \App\Models\QuotationResponse::where('quotation_id', $rfq->id)
                                    ->where('supplier_id', $awardedSupplierId)
                                    ->first();
                                if($response) {
                                    $discountType = $response->discount_type;
                                    $discountValue = $response->discount_percentage ? $response->discount_percentage.'%' : ($response->discount_amount ? '₱'.number_format($response->discount_amount,2) : null);
                                    $finalAmount = $response->final_amount;
                                    $totalQuoted = $response->total_amount;
                                    $discountFound = $discountType && $discountType !== 'none';
                                    break;
                                }
                            }
                        }
                    @endphp
                    @if($quotationRequest->status === 'proceeded')
                        <div class="alert alert-success text-center my-5">
                            <h4>Thank you for proceeding!</h4>
                            <p>Your quotation request has already been submitted and is being processed by our admin team.</p>
                            <p>Request Number: <span class="badge bg-primary">{{ $quotationRequest->request_number }}</span></p>
                            <a href="{{ route('client.quotation.index') }}" class="btn btn-primary mt-3">Back to My Quotations</a>
                        </div>
                        @if(isset($materialSupplierResponses) && isset($selectedSuppliers) && count($selectedSuppliers) > 0)
                        <div class="card mt-4">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0">Chosen Suppliers</h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-bordered mb-0 align-middle">
                                        <thead>
                                            <tr>
                                                <th>Material</th>
                                                <th>Chosen Supplier</th>
                                                <th>Quoted Price</th>
                                                <th>Badges</th>
                                                <th>Contact</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($selectedSuppliers as $materialId => $supplierId)
                                                @php
                                                    $materialName = null;
                                                    $supplierName = null;
                                                    $price = null;
                                                    $badges = [];
                                                    $contact = null;
                                                    // Try to get from offers (supplier responses)
                                                    if(isset($materialSupplierResponses[$materialId])) {
                                                        foreach($materialSupplierResponses[$materialId] as $offer) {
                                                            if($offer['supplier_id'] == $supplierId) {
                                                                $supplierName = $offer['supplier_name'] ?? 'Unknown';
                                                                $price = isset($offer['unit_price']) ? $offer['unit_price'] : null;
                                                                $badges = $offer['badges'] ?? [];
                                                                $contact = $offer['supplier_contact'] ?? null;
                                                            }
                                                            $materialName = $offer['material_name'] ?? $materialName;
                                                        }
                                                    }
                                                    // Fallback: try to get from pivot (material_quotation)
                                                    if ($price === null && isset($rfqs)) {
                                                        foreach ($rfqs as $rfq) {
                                                            $mat = $rfq->materials->firstWhere('id', $materialId);
                                                            if ($mat && $mat->pivot && $mat->pivot->selected_supplier_id == $supplierId) {
                                                                $price = $mat->pivot->unit_price ?? null;
                                                                break;
                                                            }
                                                        }
                                                    }
                                                    $contact = $contact ?? (isset($supplierId) ? (\App\Models\Supplier::find($supplierId)->phone ?? null) : null);
                                                @endphp
                                                <tr>
                                                    <td>{{ $materialName ?? 'Material #'.$materialId }}</td>
                                                    <td>
                                                        @if($supplierName)
                                                            {{ $supplierName }}
                                                        @elseif($supplierId)
                                                            {{ \App\Models\Supplier::find($supplierId)->company_name ?? 'N/A' }}
                                                        @else
                                                            N/A
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($price && $price > 0)
                                                            ₱{{ number_format($price, 2) }}
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if(!empty($badges))
                                                            @foreach($badges as $badge)
                                                                <span class="badge @if($badge=='Cheapest') badge-cheapest @elseif($badge=='Best Delivery') badge-delivery @elseif($badge=='Least Defects') badge-defects @elseif($badge=='Overall Best') badge-overall @endif" data-bs-toggle="tooltip" title="{{ $badge }}">{{ $badge }}</span>
                                                            @endforeach
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($contact)
                                                            <span class="text-nowrap"><i class="fas fa-phone-alt me-1"></i>{{ $contact }}</span>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        @php
                            $uniqueSuppliers = collect($selectedSuppliers)->unique()->filter();
                        @endphp
                        @if($uniqueSuppliers->count() === 1)
                            @php
                                // Calculate total, discount, and final amount for the single supplier
                                $totalQuoted = 0;
                                $discountType = null;
                                $discountValue = null;
                                $finalAmount = null;
                                $awardedSupplierId = $uniqueSuppliers->first();
                                $discountFound = false;
                                // Find the QuotationResponse for this supplier
                                if(isset($rfqs)) {
                                    foreach($rfqs as $rfq) {
                                        $response = \App\Models\QuotationResponse::where('quotation_id', $rfq->id)
                                            ->where('supplier_id', $awardedSupplierId)
                                            ->first();
                                        if($response) {
                                            $discountType = $response->discount_type;
                                            $discountValue = $response->discount_percentage ? $response->discount_percentage.'%' : ($response->discount_amount ? '₱'.number_format($response->discount_amount,2) : null);
                                            $finalAmount = $response->final_amount;
                                            $totalQuoted = $response->total_amount;
                                            $discountFound = $discountType && $discountType !== 'none';
                                            break;
                                        }
                                    }
                                }
                            @endphp
                            <div class="alert alert-info mt-3">
                                <strong>Summary for Supplier:</strong> {{ \App\Models\Supplier::find($awardedSupplierId)->company_name ?? 'N/A' }}<br>
                                <strong>Total Quoted Price:</strong> ₱{{ number_format($totalQuoted, 2) }}<br>
                                @if($discountFound)
                                    <strong>Discount:</strong> {{ ucfirst($discountType) }} ({{ $discountValue }})<br>
                                    <strong>Final Amount After Discount:</strong> ₱{{ number_format($finalAmount, 2) }}
                                @else
                                    <span class="text-danger">No discount was offered by this supplier.</span>
                                @endif
                            </div>
                        @else
                            <div class="alert alert-warning mt-3">
                                <strong>No discount will be applied if you select different suppliers for your materials.</strong>
                            </div>
                        @endif
                        @endif
                    @elseif($quotationRequest->status === 'reviewed')
                        {{-- Supplier selection and proceed UI --}}
                        <form method="POST" action="{{ route('client.quotation.finalize', ['id' => $quotationRequest->id]) }}" id="client-finalize-form-table">
                            @csrf
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-bold fs-5">Choose your preferred supplier for each material below:</span>
                                <button type="button" class="btn btn-primary" id="clientRecommendAllBtn">Recommend for All</button>
                            </div>
                            @php
                                $hasAnySupplierResponses = false;
                                if(isset($materialSupplierResponses)) {
                                    foreach($materialSupplierResponses as $materialId => $offers) {
                                        if(count($offers) > 0) {
                                            $hasAnySupplierResponses = true;
                                            break;
                                        }
                                    }
                                }
                            @endphp
                            @if(!$hasAnySupplierResponses)
                                <div class="alert alert-warning text-center mb-3">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>No supplier responses yet!</strong> Please wait for suppliers to submit their offers before making recommendations or proceeding.
                                </div>
                            @endif
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
                                                                        @php
                                                                            // Ensure only one supplier per badge per material
                                                                            $badgeWinners = [];
                                                                            foreach ($offers as $offer) {
                                                                                if (!empty($offer['badges'])) {
                                                                                    foreach ($offer['badges'] as $badge) {
                                                                                        if (!isset($badgeWinners[$badge])) {
                                                                                            $badgeWinners[$badge] = $offer['supplier_id'];
                                                                                        }
                                                                                    }
                                                                                }
                                                                            }
                                                                        @endphp
                                                                        @foreach($offers as $offer)
                                                                            @php
                                                                                $uniqueBadges = [];
                                                                                if (!empty($offer['badges'])) {
                                                                                    foreach ($offer['badges'] as $badge) {
                                                                                        if (isset($badgeWinners[$badge]) && $badgeWinners[$badge] == $offer['supplier_id']) {
                                                                                            $uniqueBadges[] = $badge;
                                                                                        }
                                                                                    }
                                                                                }
                                                                            @endphp
                                                                            <option value="{{ $offer['supplier_id'] }}"
                                                                                data-badges='@json($uniqueBadges)'
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
                                        <div id="live-supplier-summary" class="alert alert-info alert-static mt-3" style="display:none;"></div>
                                    @else
                                        <div class="alert alert-info text-center">No supplier offers available yet.</div>
                                    @endif
                                </div>
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
                        @php
                            $allSuppliersSelected = true;
                            $hasAnyResponses = false;
                            if(isset($materialSupplierResponses)) {
                                foreach($materialSupplierResponses as $materialId => $offers) {
                                    if(count($offers) > 0) {
                                        $hasAnyResponses = true;
                                    }
                                    if(empty($selectedSuppliers[$materialId])) {
                                        $allSuppliersSelected = false;
                                    }
                                }
                            }
                        @endphp
                        <!-- Always render Proceed Button and Modal -->
                        <div class="d-flex flex-wrap justify-content-center gap-3 mt-4">
                            <button type="button" class="btn btn-success btn-lg" id="proceedQuotationBtn" data-bs-toggle="modal" data-bs-target="#proceedQuotationModal" disabled title="Please select suppliers for all materials before proceeding.">Proceed with Quotation</button>
                            <!-- Modal for Proceed Confirmation -->
                            <div class="modal fade" id="proceedQuotationModal" tabindex="-1" aria-labelledby="proceedQuotationModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form method="POST" action="{{ route('client.quotation.proceed', ['id' => $quotationRequest->id]) }}" id="proceedQuotationForm">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="proceedQuotationModalLabel">Proceed with Quotation</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label for="property_address" class="form-label">Property Address</label>
                                                    <input type="text" class="form-control" id="property_address" name="property_address" required>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <label for="project_start_date" class="form-label">Start Date</label>
                                                        <input type="date" class="form-control" id="project_start_date" name="project_start_date" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="project_end_date" class="form-label">End Date</label>
                                                        <input type="date" class="form-control" id="project_end_date" name="project_end_date" required readonly>
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <label for="payment_method" class="form-label">Payment Method</label>
                                                        <select name="payment_method" id="payment_method" class="form-select" required>
                                                            <option value="">Select Method</option>
                                                            <option value="bank_transfer">Bank Transfer</option>
                                                            <option value="check">Check</option>
                                                            <option value="cash">Cash</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="payment_plan" class="form-label">Payment Plan</label>
                                                        <select name="payment_plan" id="payment_plan" class="form-select" required>
                                                            <option value="">Select Plan</option>
                                                            <option value="30% down, 40% halfway, 30% on completion">30% down, 40% halfway, 30% on completion</option>
                                                            <option value="50/50">50% down, 50% on completion</option>
                                                            <option value="Full upon completion">Full upon completion</option>
                                                            <option value="custom">Other (specify below)</option>
                                                        </select>
                                                        <input type="text" name="payment_plan_custom" id="payment_plan_custom" class="form-control mt-2" placeholder="Enter custom payment plan..." style="display:none;">
                                                    </div>
                                                </div>
                                                <p>Are you sure you want to proceed with this quotation? This action cannot be undone.</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-success">Yes, Proceed</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <form method="POST" action="{{ route('client.quotation.cancel', ['id' => $quotationRequest->id]) }}" onsubmit="return confirm('Are you sure you want to cancel this quotation?');" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-danger btn-lg">Cancel Quotation</button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
// Move hasAnyResponses to global scope
let hasAnyResponses = false;

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
    
    // Check if there are any supplier responses
    hasAnyResponses = false;
    $('.supplier-select').each(function() {
        if ($(this).find('option').length > 1) { // More than just "Select Supplier"
            hasAnyResponses = true;
        }
    });
    
    // Disable recommend button if no responses
    if (!hasAnyResponses) {
        recommendBtn.disabled = true;
        recommendBtn.title = 'No supplier responses available yet';
        recommendBtn.classList.add('btn-secondary');
        recommendBtn.classList.remove('btn-primary');
    }
    
    recommendBtn.addEventListener('click', function() {
        if (!hasAnyResponses) {
            alert('No supplier responses available yet. Please wait for suppliers to submit their offers.');
            return;
        }
        recommendModal.show();
    });
    
    applyRecommendBtn.addEventListener('click', function() {
        if (!hasAnyResponses) {
            alert('No supplier responses available yet. Please wait for suppliers to submit their offers.');
            return;
        }
        
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
                // --- NEW: Check if all suppliers are selected and show the button ---
                setTimeout(function() {
                    let allSelected = true;
                    $('.supplier-select').each(function() {
                        if (!$(this).val()) {
                            allSelected = false;
                        }
                    });
                    if (allSelected && hasAnyResponses) {
                        // The proceed button is now directly in the HTML, no need to inject here
                    }
                }, 500);
                // --- END NEW ---
            });
    });
    // Disable the Proceed button after click to prevent double submission
    $(document).on('submit', '#proceedQuotationForm', function(e) {
        // Remove any existing hidden inputs for selected_suppliers
        $(this).find('input[name^="selected_suppliers"]').remove();
        // For each supplier-select, add a hidden input with the current value
        $('.supplier-select').each(function() {
            var materialId = $(this).data('material-id');
            var supplierId = $(this).val();
            if (supplierId) {
                $('<input>').attr({
                    type: 'hidden',
                    name: 'selected_suppliers[' + materialId + ']',
                    value: supplierId
                }).appendTo('#proceedQuotationForm');
            }
        });
        var btn = $(this).find('button[type="submit"]');
        btn.prop('disabled', true);
        btn.text('Processing...');
        // Hide the tip alert only when the client clicks 'Proceed with Quotation'
        $('#discount-tip-alert').hide();
        $('#live-supplier-summary').hide();
    });
});
function updateLiveSupplierSummary() {
    // Gather all selected supplier IDs
    var selected = [];
    var allSelected = true;
    var supplierId = null;
    $('.supplier-select').each(function() {
        var val = $(this).val();
        if (!val) allSelected = false;
        selected.push(val);
    });
    // Check if all are the same and not empty
    var unique = [...new Set(selected.filter(Boolean))];
    var summaryDiv = $('#live-supplier-summary');
    if (allSelected && unique.length === 1) {
        supplierId = unique[0];
        // Calculate total and check for discount
        var total = 0;
        var discountType = null;
        var discountValue = null;
        var finalAmount = null;
        var discountFound = false;
        // Find all offers for this supplier
        var offers = [];
        $('.supplier-select').each(function() {
            var matId = $(this).data('material-id');
            var price = 0;
            var qty = 1;
            var offer = null;
            // Find the offer in the materialSupplierResponses (rendered as JS object)
            if (window.materialSupplierResponses && window.materialSupplierResponses[matId]) {
                window.materialSupplierResponses[matId].forEach(function(o) {
                    if (o.supplier_id == supplierId) offer = o;
                });
            }
            if (offer && offer.unit_price) price = parseFloat(offer.unit_price);
            if (offer && offer.quantity) qty = parseFloat(offer.quantity);
            total += price * qty;
        });
        // Find discount info from window.supplierDiscounts (rendered as JS object)
        if (window.supplierDiscounts && window.supplierDiscounts[supplierId]) {
            var d = window.supplierDiscounts[supplierId];
            discountType = d.discount_type;
            discountValue = d.discount_percentage ? d.discount_percentage+'%' : (d.discount_amount ? '₱'+parseFloat(d.discount_amount).toLocaleString(undefined, {minimumFractionDigits:2}) : null);
            finalAmount = d.final_amount;
            discountFound = discountType && discountType !== 'none';
        }
        var html = '<strong>Summary for Supplier:</strong> ' + ($('.supplier-select option[value="'+supplierId+'"]:first').text() || supplierId) + '<br>';
        html += '<strong>Total Quoted Price:</strong> ₱' + total.toLocaleString(undefined, {minimumFractionDigits:2}) + '<br>';
        if (discountFound) {
            html += '<strong>Discount:</strong> ' + discountType.charAt(0).toUpperCase() + discountType.slice(1) + ' (' + discountValue + ')<br>';
            html += '<strong>Final Amount After Discount:</strong> ₱' + parseFloat(finalAmount).toLocaleString(undefined, {minimumFractionDigits:2});
        } else {
            html += '<strong>Final Amount:</strong> ₱' + total.toLocaleString(undefined, {minimumFractionDigits:2}) + '<br>';
            html += '<span class="text-danger">This supplier does not include a discount for your selected materials.</span>';
        }
        summaryDiv.html(html).show();
    } else {
        summaryDiv.hide();
    }
}
// Prepare materialSupplierResponses and supplierDiscounts as JS objects
window.materialSupplierResponses = @json($materialSupplierResponses ?? []);
window.supplierDiscounts = {};
@if(isset($rfqs))
    @foreach($rfqs as $rfq)
        @foreach($rfq->responses as $response)
            window.supplierDiscounts['{{ $response->supplier_id }}'] = {
                discount_type: '{{ $response->discount_type }}',
                discount_percentage: '{{ $response->discount_percentage }}',
                discount_amount: '{{ $response->discount_amount }}',
                final_amount: '{{ $response->final_amount }}'
            };
        @endforeach
    @endforeach
@endif
// Listen for changes
$(document).on('change', '.supplier-select', function() {
    updateLiveSupplierSummary();
    // Recompute hasAnyResponses in case options change
    hasAnyResponses = false;
    $('.supplier-select').each(function() {
        if ($(this).find('option').length > 1) {
            hasAnyResponses = true;
        }
    });
    // Show/hide Proceed button
    let allSelected = true;
    $('.supplier-select').each(function() {
        if (!$(this).val()) {
            allSelected = false;
        }
    });
    if (allSelected && hasAnyResponses) {
        // The proceed button is now directly in the HTML, no need to inject here
    } else {
        // The proceed button is now directly in the HTML, no need to remove here
    }
});
$(document).ready(updateLiveSupplierSummary);
$(function () {
    $('[data-bs-toggle="tooltip"]').tooltip();
});

function updateProceedButtonState() {
    let allSelected = true;
    $('.supplier-select').each(function() {
        if (!$(this).val()) {
            allSelected = false;
        }
    });
    const btn = $('#proceedQuotationBtn');
    if (allSelected && hasAnyResponses) {
        btn.prop('disabled', false);
        btn.attr('title', '');
    } else {
        btn.prop('disabled', true);
        btn.attr('title', 'Please select suppliers for all materials before proceeding.');
    }
}
// Call on page load and whenever supplier selection changes
$(document).ready(function() {
    updateProceedButtonState();
});
$(document).on('change', '.supplier-select', function() {
    updateProceedButtonState();
});
</script>
<script>
    var totalEstimatedDays = {{ $totalEstimatedDays ?? 1 }};
    function updateEndDate() {
        var start = $('#project_start_date').val();
        if (!start) {
            $('#project_end_date').val('');
            return;
        }
        var [yyyy, mm, dd] = start.split('-');
        var startDate = new Date(Number(yyyy), Number(mm) - 1, Number(dd));
        if (isNaN(startDate)) {
            $('#project_end_date').val('');
            return;
        }
        var days = totalEstimatedDays;
        var endDate = new Date(startDate);
        endDate.setDate(startDate.getDate() + days - 1);
        var yyyyEnd = endDate.getFullYear();
        var mmEnd = String(endDate.getMonth() + 1).padStart(2, '0');
        var ddEnd = String(endDate.getDate()).padStart(2, '0');
        var endDateStr = `${yyyyEnd}-${mmEnd}-${ddEnd}`;
        $('#project_end_date').val(endDateStr);
        $('#project_end_date').attr('min', start);
        console.log('Start:', start, 'Days:', days, 'Calculated End:', endDateStr);
    }
    // Always recalculate on start date change or modal show
    $('#project_start_date').on('change', updateEndDate);
    $('#proceedQuotationModal').on('show.bs.modal', updateEndDate);
    // On page load, initialize
    $(document).ready(function() {
        $('#project_end_date').attr('readonly', true);
        updateEndDate();
    });
</script>
@endpush 