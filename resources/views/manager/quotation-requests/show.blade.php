@extends('layouts.app')

@php
    $qr = $quotationRequest;
    $alreadySent = in_array($qr->status, ['sent_to_suppliers', 'reviewed', 'proceeded'], true);
@endphp

@section('content')
<div class="container-fluid req-page">
    <x-page-header title="Quotation Request" icon="file-signature">
        <a href="{{ route('manager.quotations', ['tab' => 'client']) }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Back</a>
        @if($qr->status === 'proceeded' && !$qr->materialRequest)
            <a href="{{ route('material-requests.create', ['quotation_id' => $qr->id]) }}" class="btn btn-success"><i class="fas fa-dolly me-1"></i> Create material request</a>
        @endif
    </x-page-header>

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
        <div class="card-body">
            <div class="req-hero mb-4">
                <div>
                    <div class="req-hero__ref">Client quotation request</div>
                    <h2 class="req-hero__title">{{ $qr->request_number }} <x-status-badge :status="$qr->status" /></h2>
                </div>
                @if($qr->status === 'proceeded' && $finalAmount)
                    <div class="req-hero__amount">
                        <strong>₱{{ number_format($finalAmount, 2) }}</strong>
                        <span>agreed amount</span>
                    </div>
                @endif
            </div>
            <dl class="req-meta">
                <div><dt>Client</dt><dd>{{ $qr->user->name ?? 'User #' . $qr->user_id }}</dd></div>
                <div><dt>Submitted</dt><dd>{{ $qr->created_at->format('M d, Y H:i') }}</dd></div>
                <div><dt>Rooms</dt><dd>{{ $qr->rooms->count() }}</dd></div>
            </dl>
        </div>
    </div>

    @if(!$alreadySent)
        <div class="req-callout req-callout--warning mb-4">
            <div class="req-callout__text">
                <i class="fas fa-paper-plane" aria-hidden="true"></i>
                <div><strong>Not sent to suppliers yet.</strong><br>Send it to every supplier of these materials to collect their prices.</div>
            </div>
            <form method="POST" action="{{ route('manager.quotation-requests.send-to-suppliers', $qr->id) }}">
                @csrf
                <button type="submit" class="btn btn-success"><i class="fas fa-paper-plane me-1"></i> Send to all suppliers</button>
            </form>
        </div>
    @elseif($qr->status !== 'proceeded')
        <div class="req-callout req-callout--info mb-4">
            <div class="req-callout__text">
                <i class="fas fa-check" aria-hidden="true"></i>
                <div><strong>Sent to suppliers.</strong> Waiting for their quotes and the client's decision.</div>
            </div>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-header"><h3 class="req-section-title"><i class="fas fa-ruler-combined"></i> Rooms &amp; scope of work</h3></div>
        <div class="card-body">
            <x-requests.rooms :rooms="$qr->rooms" />
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header"><h3 class="req-section-title"><i class="fas fa-boxes"></i> Materials</h3></div>
        <div class="table-responsive">
            <table class="table req-table">
                <thead>
                    <tr>
                        <th>Room</th>
                        <th>Scope</th>
                        <th>Material</th>
                        <th>Unit</th>
                        <th class="req-num">Base price</th>
                    </tr>
                </thead>
                <tbody>
                    @php $hasMaterials = false; @endphp
                    @foreach($qr->rooms as $room)
                        @foreach($room->scopes as $scope)
                            @foreach($scope->scopeType?->materials ?? [] as $material)
                                @php $hasMaterials = true; @endphp
                                <tr>
                                    <td>{{ $room->name }}</td>
                                    <td>{{ $scope->scopeType->name ?? '' }}</td>
                                    <td class="fw-semibold">{{ $material->name }}</td>
                                    <td>{{ $material->unit }}</td>
                                    <td class="req-num">₱{{ number_format($material->base_price, 2) }}</td>
                                </tr>
                            @endforeach
                        @endforeach
                    @endforeach
                    @unless($hasMaterials)
                        <x-empty-row cols="5">No materials linked to these scopes.</x-empty-row>
                    @endunless
                </tbody>
            </table>
        </div>
    </div>

    @if($qr->status === 'proceeded')
        <div class="card mb-4">
            <div class="card-header"><h3 class="req-section-title"><i class="fas fa-file-contract"></i> Client's submission</h3></div>
            <div class="card-body">
                <dl class="req-meta mb-4">
                    <div><dt>Property address</dt><dd>{{ $address ?: '—' }}</dd></div>
                    <div><dt>Start date</dt><dd>{{ $start ?: '—' }}</dd></div>
                    <div><dt>End date</dt><dd>{{ $end ?: '—' }}</dd></div>
                    <div><dt>Payment method</dt><dd>{{ $method ? ucfirst(str_replace('_', ' ', $method)) : '—' }}</dd></div>
                    <div><dt>Payment plan</dt><dd>{{ $plan ?: '—' }}</dd></div>
                </dl>
                <div class="form-label">Payment breakdown</div>
                <div class="table-responsive">
                    <table class="table req-table">
                        <thead><tr><th>Stage</th><th class="req-num">Share</th><th class="req-num">Amount</th></tr></thead>
                        <tbody>
                            @foreach($breakdownRows as [$label, $percent])
                                <tr>
                                    <td>{{ $label }}</td>
                                    <td class="req-num">{{ rtrim(rtrim(number_format($percent, 2), '0'), '.') }}%</td>
                                    <td class="req-num">₱{{ number_format(round($finalAmount * $percent / 100, 2), 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot><tr><th>Total</th><th class="req-num">100%</th><th class="req-num">₱{{ number_format($finalAmount, 2) }}</th></tr></tfoot>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <div class="card">
        <div class="card-header"><h3 class="req-section-title"><i class="fas fa-handshake"></i> Chosen suppliers</h3></div>
        <div class="table-responsive">
            <table class="table req-table">
                <thead>
                    <tr>
                        <th>Material</th>
                        <th>Supplier</th>
                        <th class="req-num">Quoted price</th>
                        <th>Contact</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($selectedSuppliers as $materialId => $supplierId)
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
                            <td class="fw-semibold">{{ $materialName ?? 'Material #' . $materialId }}</td>
                            <td>{{ $supplierName ?? '—' }}</td>
                            <td class="req-num">{{ $price && $price > 0 ? '₱' . number_format($price, 2) : '—' }}</td>
                            <td>
                                @if($contact)
                                    <span class="text-nowrap"><i class="fas fa-phone-alt me-1 text-muted"></i>{{ $contact }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <x-empty-row cols="4">The client hasn't chosen suppliers yet.</x-empty-row>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('styles')
@vite('resources/css/pages/requests.css')
@endpush
