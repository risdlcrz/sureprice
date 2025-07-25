@extends('layouts.app')
@section('content')
<div class="container mt-4">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Client Quotation Request #{{ $quotationRequest->request_number }}</h5>
            <span class="badge bg-{{ $quotationRequest->status === 'sent_to_suppliers' ? 'info' : 'warning' }}">
                {{ $quotationRequest->status === 'sent_to_suppliers' ? 'Sent to Suppliers' : ucfirst($quotationRequest->status) }}
            </span>
            <a href="{{ route('manager.quotations', ['tab' => 'client']) }}" class="btn btn-secondary">Back to List</a>
        </div>
        <div class="card-body">
            <h6>Client Information</h6>
            <ul>
                <li><strong>User ID:</strong> {{ $quotationRequest->user_id }}</li>
                <li><strong>Submitted At:</strong> {{ $quotationRequest->created_at->format('Y-m-d H:i') }}</li>
            </ul>
            @foreach($quotationRequest->rooms as $room)
                <div class="mb-3">
                    <strong>Room:</strong> {{ $room->name }}<br>
                    <strong>Dimensions:</strong> {{ $room->length }} × {{ $room->width }} × {{ $room->height }}<br>
                    <strong>Scopes:</strong>
                    <ul>
                        @foreach($room->scopes as $scope)
                            <li>{{ $scope->scopeType->name ?? '' }} ({{ $scope->scopeType->category ?? '' }})</li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
            @if(!in_array($quotationRequest->status, ['sent_to_suppliers', 'proceeded']))
                <form method="POST" action="{{ route('manager.quotation-requests.send-to-suppliers', $quotationRequest->id) }}" class="mb-3">
                    @csrf
                    <button type="submit" class="btn btn-success float-end">Send Quotation Request to All Suppliers</button>
                </form>
            @elseif($quotationRequest->status === 'sent_to_suppliers')
                <div class="alert alert-info text-center">This request has already been sent to all suppliers.</div>
            @endif
            <h6>Materials in this Quotation</h6>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Room</th>
                        <th>Scope</th>
                        <th>Material</th>
                        <th>Unit</th>
                        <th>Base Price</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($quotationRequest->rooms as $room)
                        @foreach($room->scopes as $scope)
                            @if($scope->scopeType && $scope->scopeType->materials)
                                @foreach($scope->scopeType->materials as $material)
                                    <tr>
                                        <td>{{ $room->name }}</td>
                                        <td>{{ $scope->scopeType->name ?? '' }}</td>
                                        <td>{{ $material->name }}</td>
                                        <td>{{ $material->unit }}</td>
                                        <td>₱{{ number_format($material->base_price, 2) }}</td>
                                    </tr>
                                @endforeach
                            @endif
                        @endforeach
                    @endforeach
                </tbody>
            </table>
            @if($quotationRequest->status === 'proceeded' && !$quotationRequest->materialRequest)
                <a href="{{ route('material-requests.create', ['quotation_id' => $quotationRequest->id]) }}" class="btn btn-primary mt-3">Create Material Request</a>
            @endif
            {{-- Manager Quotation Summary Card --}}
            @php
                $contract = $quotationRequest->contract_data ?? [];
                $plan = $contract['payment_plan'] ?? null;
                $address = $contract['property_address'] ?? null;
                $start = $contract['project_start_date'] ?? null;
                $end = $contract['project_end_date'] ?? null;
                $method = $contract['payment_method'] ?? null;
                $finalAmount = null;
                $totalQuoted = null;
                $discountType = null;
                $discountValue = null;
                $discountFound = false;
                $uniqueSuppliers = collect($selectedSuppliers)->unique()->filter();
                if($uniqueSuppliers->count() === 1 && isset($rfqs)) {
                    $awardedSupplierId = $uniqueSuppliers->first();
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
                if(!$finalAmount) {
                    // fallback: sum up all selected supplier prices
                    $finalAmount = 0;
                    if(isset($rfqs) && isset($selectedSuppliers)) {
                        foreach($selectedSuppliers as $materialId => $supplierId) {
                            foreach($rfqs as $rfq) {
                                foreach($rfq->responses as $response) {
                                    if($response->supplier_id == $supplierId) {
                                        foreach($response->items as $item) {
                                            if($item->material_id == $materialId && isset($item->unit_price)) {
                                                $finalAmount += $item->unit_price * ($item->quantity ?? 1);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                // Payment breakdown logic
                $breakdownRows = [];
                if($plan === '30% down, 40% halfway, 30% on completion') {
                    $breakdownRows = [
                        ['Downpayment', 30],
                        ['Halfway Payment', 40],
                        ['Completion Payment', 30],
                    ];
                } elseif($plan === '50/50') {
                    $breakdownRows = [
                        ['Downpayment', 50],
                        ['Completion Payment', 50],
                    ];
                } elseif($plan === 'Full upon completion') {
                    $breakdownRows = [
                        ['Completion Payment', 100],
                    ];
                } elseif($plan === 'milestone') {
                    $breakdownRows = [
                        ['Downpayment', 20],
                        ['After Foundation', 20],
                        ['After Structure', 30],
                        ['Completion Payment', 30],
                    ];
                } elseif($plan === 'monthly3') {
                    for($i=1;$i<=3;$i++) $breakdownRows[] = ["Month $i Payment", 100/3];
                } elseif($plan === 'monthly6') {
                    for($i=1;$i<=6;$i++) $breakdownRows[] = ["Month $i Payment", 100/6];
                } elseif($plan === 'monthly12') {
                    for($i=1;$i<=12;$i++) $breakdownRows[] = ["Month $i Payment", 100/12];
                }
            @endphp
            <div class="card mb-4">
                <div class="card-header bg-info text-white"><h5 class="mb-0">Client Quotation Submission Details</h5></div>
                <div class="card-body">
                    <p><strong>Property Address:</strong> {{ $address }}</p>
                    <p><strong>Start Date:</strong> {{ $start }}</p>
                    <p><strong>End Date:</strong> {{ $end }}</p>
                    <p><strong>Payment Method:</strong> {{ ucfirst(str_replace('_',' ',$method ?? '')) }}</p>
                    <p><strong>Payment Plan:</strong> {{ $plan }}</p>
                    <div class="mb-3">
                        <strong>Payment Breakdown:</strong>
                        <table class="table table-bordered mt-2">
                            <thead><tr><th>Stage</th><th>Percent</th><th>Amount (₱)</th></tr></thead>
                            <tbody>
                                @php $sum = 0; @endphp
                                @foreach($breakdownRows as [$label, $percent])
                                    @php $amt = round($finalAmount * $percent / 100, 2); $sum += $amt; @endphp
                                    <tr><td>{{ $label }}</td><td>{{ rtrim(rtrim(number_format($percent,2), '0'), '.') }}%</td><td>₱{{ number_format($amt,2) }}</td></tr>
                                @endforeach
                                <tr class="fw-bold"><td>Total</td><td>100%</td><td>₱{{ number_format($finalAmount,2) }}</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
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
                                    <th>Contact</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($selectedSuppliers as $materialId => $supplierId)
                                    @php
                                        $materialName = null;
                                        $supplierName = null;
                                        $price = null;
                                        $contact = null;
                                        // Try to get from supplier response (rfqs.responses.items)
                                        foreach ($rfqs as $rfq) {
                                            foreach ($rfq->responses as $response) {
                                                if ($response->supplier_id == $supplierId) {
                                                    foreach ($response->items as $item) {
                                                        if ($item->material_id == $materialId) {
                                                            $supplierName = $response->supplier->company_name ?? 'N/A';
                                                            $price = $item->unit_price ?? null;
                                                            $contact = $response->supplier->phone ?? null;
                                                            $mat = $rfq->materials->firstWhere('id', $materialId);
                                                            if ($mat) {
                                                                $materialName = $mat->name ?? $materialName;
                                                            }
                                                            break 3;
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                        // Fallback: try to get from pivot (material_quotation)
                                        if ($supplierName === null || $price === null) {
                                            foreach ($rfqs as $rfq) {
                                                $mat = $rfq->materials->firstWhere('id', $materialId);
                                                if ($mat && $mat->pivot && $mat->pivot->selected_supplier_id == $supplierId) {
                                                    $supplierName = $supplierName ?? ($rfq->suppliers->firstWhere('id', $supplierId)->company_name ?? 'N/A');
                                                    $price = $price ?? $mat->pivot->unit_price ?? null;
                                                    $contact = $contact ?? ($rfq->suppliers->firstWhere('id', $supplierId)->phone ?? null);
                                                    $materialName = $mat->name ?? $materialName;
                                                    break;
                                                }
                                            }
                                        }
                                    @endphp
                                    <tr>
                                        <td>{{ $materialName ?? 'Material #'.$materialId }}</td>
                                        <td>{{ $supplierName ?? 'N/A' }}</td>
                                        <td>
                                            @if($price && $price > 0)
                                                ₱{{ number_format($price, 2) }}
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
        </div>
    </div>
</div>
@endsection