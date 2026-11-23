@extends('layouts.app')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@vite('resources/css/pages/requests.css')
@endpush

@php
    $qr = $quotationRequest;
    $status = $qr->status;
    $rooms = $qr->rooms ?? collect();
    $totalArea = $rooms->sum(fn ($r) => $r->area ?? $r->length * $r->width);

    // Progress: Submitted → Supplier quotes → Your selection → Processing
    $stage = ['pending' => 1, 'reviewed' => 2, 'proceeded' => 4][$status] ?? 1;
    $cancelled = in_array($status, ['cancelled', 'rejected'], true);
    $steps = [
        ['Submitted', 'done', $qr->created_at],
        ['Supplier quotes', $stage > 1 ? 'done' : ($cancelled ? 'failed' : 'current'), null],
        ['Your selection', $stage > 2 ? 'done' : ($stage === 2 ? ($cancelled ? 'failed' : 'current') : 'todo'), null],
        ['Processing', $stage === 4 ? 'current' : 'todo', null],
    ];
    $stepIcons = ['done' => 'fa-check', 'current' => 'fa-hourglass-half', 'failed' => 'fa-times', 'todo' => ''];

    $uniqueSuppliers = collect($selectedSuppliers)->unique()->filter();
    $showDiscountSummary = $uniqueSuppliers->count() === 1 && count($selectedSuppliers) > 0;
    $awardedSupplierId = $showDiscountSummary ? $uniqueSuppliers->first() : null;
    $awardedResponse = $awardedSupplierId ? \App\Models\QuotationResponse::forSupplier($rfqs ?? [], $awardedSupplierId) : null;
    ['type' => $discountType, 'value' => $discountValue, 'final' => $finalAmount, 'total' => $totalQuoted, 'found' => $discountFound]
        = $awardedResponse?->discountSummary() ?? ['type' => null, 'value' => null, 'final' => null, 'total' => 0, 'found' => false];
@endphp

@section('content')
<div class="container-fluid req-page">
    <x-page-header title="Quotation Request" icon="file-signature">
        <a href="{{ route('client.quotation.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> My requests</a>
        <a href="{{ route('client.quotation.create') }}" class="btn btn-success"><i class="fas fa-plus me-1"></i> Request another quotation</a>
    </x-page-header>

    {{-- Summary --}}
    <div class="card mb-4">
        <div class="card-body">
            <div class="req-hero mb-4">
                <div>
                    <div class="req-hero__ref">Quotation request</div>
                    <h2 class="req-hero__title">{{ $qr->request_number }} <x-status-badge :status="$status" /></h2>
                </div>
                @if($status === 'proceeded' && $finalAmount)
                    <div class="req-hero__amount">
                        <strong>₱{{ number_format($finalAmount, 2) }}</strong>
                        <span>{{ $discountFound ? 'after discount' : 'quoted total' }}</span>
                    </div>
                @endif
            </div>

            @unless($cancelled)
                <ol class="req-steps mb-4" aria-label="Request progress">
                    @foreach($steps as [$label, $state, $date])
                        <li class="req-steps__item is-{{ $state }}" @if($state === 'current') aria-current="step" @endif>
                            <span class="req-steps__dot">@if($stepIcons[$state])<i class="fas {{ $stepIcons[$state] }}" aria-hidden="true"></i>@else{{ $loop->iteration }}@endif</span>
                            <span class="req-steps__label">{{ $label }}</span>
                            <span class="req-steps__date">{{ $date ? $date->format('M d, Y') : ($state === 'current' ? 'In progress' : '') }}</span>
                        </li>
                    @endforeach
                </ol>
            @endunless

            <dl class="req-meta">
                <div><dt>Submitted</dt><dd>{{ $qr->created_at->format('M d, Y H:i') }}</dd></div>
                <div><dt>Rooms</dt><dd>{{ $rooms->count() }}</dd></div>
                <div><dt>Total floor area</dt><dd>{{ rtrim(rtrim(number_format($totalArea, 2), '0'), '.') }} m²</dd></div>
                @if(!empty($qr->contract_data['property_address']))
                    <div><dt>Property</dt><dd>{{ $qr->contract_data['property_address'] }}</dd></div>
                @endif
            </dl>

            @if($qr->notes)
                <div class="mt-4">
                    <div class="form-label">Your notes</div>
                    <div class="req-note">{{ $qr->notes }}</div>
                </div>
            @endif
            @if($qr->admin_notes)
                <div class="mt-3">
                    <div class="form-label">Response from our team</div>
                    <div class="req-note">{{ $qr->admin_notes }}</div>
                </div>
            @endif
        </div>
    </div>

    {{-- What happens next --}}
    @if($status === 'pending')
        <div class="req-callout req-callout--info mb-4">
            <div class="req-callout__text">
                <i class="fas fa-hourglass-half" aria-hidden="true"></i>
                <div><strong>We're collecting quotes from suppliers.</strong><br>You'll be able to compare their offers and choose a supplier for each material here once they reply.</div>
            </div>
        </div>
    @elseif($status === 'reviewed')
        <div class="req-callout req-callout--warning mb-4">
            <div class="req-callout__text">
                <i class="fas fa-hand-pointer" aria-hidden="true"></i>
                <div><strong>Supplier quotes are in.</strong><br>Choose a supplier for each material below, then proceed with the quotation.</div>
            </div>
        </div>
    @elseif($status === 'proceeded')
        <div class="req-callout req-callout--success mb-4">
            <div class="req-callout__text">
                <i class="fas fa-check-circle" aria-hidden="true"></i>
                <div><strong>Thank you for proceeding!</strong><br>Your quotation has been submitted and our team is preparing your contract.</div>
            </div>
        </div>
    @elseif($cancelled)
        <div class="req-callout req-callout--danger mb-4">
            <div class="req-callout__text">
                <i class="fas fa-times-circle" aria-hidden="true"></i>
                <div><strong>This quotation request was {{ $status }}.</strong> You can submit a new one at any time.</div>
            </div>
            <a href="{{ route('client.quotation.create') }}" class="btn btn-outline-secondary">New request</a>
        </div>
    @endif

    {{-- Rooms & scopes --}}
    <div class="card mb-4">
        <div class="card-header"><h3 class="req-section-title"><i class="fas fa-ruler-combined"></i> Rooms &amp; scope of work</h3></div>
        <div class="card-body">
            @if($rooms->count() > 0)
                <x-requests.rooms :rooms="$rooms" />
            @elseif($sessionData)
                <div class="req-rooms">
                    @foreach($sessionData['rooms'] as $room)
                        @php $area = $room['length'] * $room['width']; @endphp
                        <section class="req-room">
                            <header class="req-room__head">
                                <span class="req-room__index">{{ $loop->iteration }}</span>
                                <div class="req-room__title">
                                    <div class="req-room__name">{{ $room['name'] }}</div>
                                    <div class="req-room__dims">{{ $room['length'] }} m × {{ $room['width'] }} m × {{ $room['height'] }} m</div>
                                </div>
                                <dl class="req-room__stats">
                                    <div><dt>Area</dt><dd>{{ $area }} m²</dd></div>
                                    <div><dt>Volume</dt><dd>{{ $area * $room['height'] }} m³</dd></div>
                                </dl>
                            </header>
                            <div class="req-scope">
                                <div class="req-scope__name"><i class="fas fa-tools" aria-hidden="true"></i><span>Scopes</span></div>
                                <div class="req-room__scopes">
                                    @forelse(collect($room['scope'] ?? [])->filter(fn ($s) => !empty($s['materials'])) as $scopeCode => $scopeData)
                                        <span class="req-tag">{{ $scopeCode }}</span>
                                    @empty
                                        <span class="text-muted small">No scopes selected.</span>
                                    @endforelse
                                </div>
                            </div>
                        </section>
                    @endforeach
                </div>
            @else
                <p class="text-muted mb-0">No room details found for this request.</p>
            @endif
        </div>
    </div>

    @if($status === 'proceeded')
        @php
            $contract = $qr->contract_data ?? [];
            $plan = $contract['payment_plan'] ?? null;
            $address = $contract['property_address'] ?? null;
            $method = $contract['payment_method'] ?? null;
            // discount values come from the lookup above ($awardedResponse)
            if (!$finalAmount) {
                // fallback: sum up all selected supplier prices
                $finalAmount = 0;
                foreach ($selectedSuppliers ?? [] as $materialId => $supplierId) {
                    foreach ($materialSupplierResponses[$materialId] ?? [] as $offer) {
                        if ($offer['supplier_id'] == $supplierId && isset($offer['unit_price'])) {
                            $finalAmount += $offer['unit_price'] * ($offer['quantity'] ?? 1);
                        }
                    }
                }
            }
            $breakdownRows = match (true) {
                $plan === '30% down, 40% halfway, 30% on completion' => [['Downpayment', 30], ['Halfway payment', 40], ['Completion payment', 30]],
                $plan === '50/50' => [['Downpayment', 50], ['Completion payment', 50]],
                $plan === 'Full upon completion' => [['Completion payment', 100]],
                $plan === 'milestone' => [['Downpayment', 20], ['After foundation', 20], ['After structure', 30], ['Completion payment', 30]],
                in_array($plan, ['monthly3', 'monthly6', 'monthly12'], true) => collect(range(1, (int) substr($plan, 7)))->map(fn ($i) => ["Month $i payment", 100 / (int) substr($plan, 7)])->all(),
                default => [],
            };
            $planLabels = ['50/50' => '50% down, 50% on completion', 'milestone' => 'Milestone-based', 'monthly3' => 'Monthly for 3 months', 'monthly6' => 'Monthly for 6 months', 'monthly12' => 'Monthly for 12 months'];
        @endphp

        <div class="card mb-4">
            <div class="card-header"><h3 class="req-section-title"><i class="fas fa-file-contract"></i> What you submitted</h3></div>
            <div class="card-body">
                <dl class="req-meta mb-4">
                    <div><dt>Property address</dt><dd>{{ $address ?: '—' }}</dd></div>
                    <div><dt>Start date</dt><dd>{{ !empty($contract['project_start_date']) ? \Carbon\Carbon::parse($contract['project_start_date'])->format('M d, Y') : '—' }}</dd></div>
                    <div><dt>End date</dt><dd>{{ !empty($contract['project_end_date']) ? \Carbon\Carbon::parse($contract['project_end_date'])->format('M d, Y') : '—' }}</dd></div>
                    <div><dt>Payment method</dt><dd>{{ $method ? ucfirst(str_replace('_', ' ', $method)) : '—' }}</dd></div>
                    <div><dt>Payment plan</dt><dd>{{ $planLabels[$plan] ?? $plan ?? '—' }}</dd></div>
                </dl>

                @if($breakdownRows)
                    <div class="form-label">Payment schedule</div>
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
                @endif
            </div>
        </div>

        @if(isset($materialSupplierResponses) && count($selectedSuppliers ?? []) > 0)
            <div class="card mb-4">
                <div class="card-header"><h3 class="req-section-title"><i class="fas fa-handshake"></i> Your chosen suppliers</h3></div>
                <div class="table-responsive">
                    <table class="table req-table">
                        <thead>
                            <tr>
                                <th>Material</th>
                                <th>Supplier</th>
                                <th class="req-num">Quoted price</th>
                                <th>Highlights</th>
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
                                    foreach ($materialSupplierResponses[$materialId] ?? [] as $offer) {
                                        if ($offer['supplier_id'] == $supplierId) {
                                            $supplierName = $offer['supplier_name'] ?? 'Unknown';
                                            $price = $offer['unit_price'] ?? null;
                                            $badges = $offer['badges'] ?? [];
                                            $contact = $offer['supplier_contact'] ?? null;
                                        }
                                        $materialName = $offer['material_name'] ?? $materialName;
                                    }
                                    // Fallback: the price on the RFQ pivot (material_quotation)
                                    if ($price === null && isset($rfqs)) {
                                        foreach ($rfqs as $rfq) {
                                            $mat = $rfq->materials->firstWhere('id', $materialId);
                                            if ($mat && $mat->pivot && $mat->pivot->selected_supplier_id == $supplierId) {
                                                $price = $mat->pivot->unit_price ?? null;
                                                break;
                                            }
                                        }
                                    }
                                    $supplierModel = $supplierId && (!$supplierName || !$contact) ? \App\Models\Supplier::find($supplierId) : null;
                                    $supplierName ??= $supplierModel->company_name ?? null;
                                    $contact ??= $supplierModel->phone ?? null;
                                @endphp
                                <tr>
                                    <td class="fw-semibold">{{ $materialName ?? 'Material #' . $materialId }}</td>
                                    <td>{{ $supplierName ?? '—' }}</td>
                                    <td class="req-num">{{ $price && $price > 0 ? '₱' . number_format($price, 2) : '—' }}</td>
                                    <td>
                                        @forelse($badges as $badge)
                                            <span class="badge {{ ['Cheapest' => 'badge-cheapest', 'Best Delivery' => 'badge-delivery', 'Least Defects' => 'badge-defects', 'Overall Best' => 'badge-overall'][$badge] ?? 'bg-light' }}">{{ $badge }}</span>
                                        @empty
                                            <span class="text-muted">—</span>
                                        @endforelse
                                    </td>
                                    <td>
                                        @if($contact)
                                            <span class="text-nowrap"><i class="fas fa-phone-alt me-1 text-muted"></i>{{ $contact }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            @if($uniqueSuppliers->count() === 1)
                <dl class="req-summary mb-4">
                    <div><dt>Supplier</dt><dd>{{ \App\Models\Supplier::find($awardedSupplierId)->company_name ?? '—' }}</dd></div>
                    <div><dt>Total quoted</dt><dd>₱{{ number_format($totalQuoted, 2) }}</dd></div>
                    @if($discountFound)
                        <div><dt>Discount</dt><dd>{{ ucfirst($discountType) }} ({{ $discountValue }})</dd></div>
                        <div><dt>Final amount</dt><dd class="is-total">₱{{ number_format($finalAmount, 2) }}</dd></div>
                    @else
                        <div><dt>Discount</dt><dd class="text-muted fw-normal fs-6">None offered by this supplier</dd></div>
                    @endif
                </dl>
            @else
                <div class="req-callout req-callout--warning mb-4">
                    <div class="req-callout__text">
                        <i class="fas fa-info-circle" aria-hidden="true"></i>
                        <div>You chose different suppliers for your materials, so no supplier discount applies.</div>
                    </div>
                </div>
            @endif
        @endif

    @elseif($status === 'reviewed')
        @php
            $hasAnySupplierResponses = collect($materialSupplierResponses ?? [])->contains(fn ($offers) => count($offers) > 0);
            // Material names from the request's scopes
            $materialNames = [];
            foreach ($rooms as $room) {
                foreach ($room->scopes as $scope) {
                    foreach ($scope->scopeType?->materials ?? [] as $mat) {
                        $materialNames[$mat->id] = $mat->name;
                    }
                }
            }
        @endphp

        <form method="POST" action="{{ route('client.quotation.finalize', ['id' => $qr->id]) }}" id="client-finalize-form-table">
            @csrf
            <div class="card mb-4">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <h3 class="req-section-title"><i class="fas fa-store"></i> Choose your suppliers</h3>
                    <button type="button" class="btn btn-sm btn-outline-success" id="clientRecommendAllBtn"><i class="fas fa-magic me-1"></i> Recommend for all</button>
                </div>

                <div class="card-body pb-0">
                    <div class="alert alert-info mb-3" id="discount-tip-alert">
                        <i class="fas fa-lightbulb me-1"></i>
                        <strong>Tip:</strong> choose the <strong>same supplier for every material</strong> to get that supplier's discount, if they offer one. Mixing suppliers means no discount.
                    </div>
                    @unless($hasAnySupplierResponses)
                        <div class="alert alert-warning mb-3">
                            <i class="fas fa-exclamation-triangle me-1"></i>
                            <strong>No supplier responses yet.</strong> Please wait for suppliers to send their offers before choosing.
                        </div>
                    @endunless
                </div>

                @if(isset($materialSupplierResponses) && count($materialSupplierResponses) > 0)
                    <div class="table-responsive">
                        <table class="table req-table req-pick-table">
                            <thead>
                                <tr>
                                    <th>Material</th>
                                    <th>Your supplier</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($materialSupplierResponses as $materialId => $offers)
                                    <tr>
                                        <td>
                                            <div class="fw-semibold">{{ $materialNames[$materialId] ?? 'Material #' . $materialId }}</div>
                                            <span class="req-pick-count">{{ count($offers) }} {{ Str::plural('offer', count($offers)) }}</span>
                                        </td>
                                        <td class="req-wrap">
                                            @if(count($offers) > 0)
                                                @php
                                                    // Each badge goes to one supplier per material (the first that earned it)
                                                    $badgeWinners = [];
                                                    foreach ($offers as $offer) {
                                                        foreach ($offer['badges'] ?? [] as $badge) {
                                                            $badgeWinners[$badge] ??= $offer['supplier_id'];
                                                        }
                                                    }
                                                @endphp
                                                <select name="selected_suppliers[{{ $materialId }}]" class="form-select supplier-select" data-material-id="{{ $materialId }}" aria-label="Supplier for {{ $materialNames[$materialId] ?? 'material ' . $materialId }}">
                                                    <option value="">Select supplier</option>
                                                    @foreach($offers as $offer)
                                                        @php $uniqueBadges = collect($offer['badges'] ?? [])->filter(fn ($b) => ($badgeWinners[$b] ?? null) == $offer['supplier_id'])->values()->all(); @endphp
                                                        <option value="{{ $offer['supplier_id'] }}"
                                                            data-badges='@json($uniqueBadges)'
                                                            data-supplier="{{ $offer['supplier_name'] ?? 'Unknown' }}"
                                                            data-price="{{ isset($offer['unit_price']) ? number_format($offer['unit_price'], 2) : '0.00' }}"
                                                            @selected(isset($selectedSuppliers[$materialId]) && $selectedSuppliers[$materialId] == $offer['supplier_id'])>
                                                            {{ $offer['supplier_name'] ?? 'Unknown' }} (₱{{ isset($offer['unit_price']) ? number_format($offer['unit_price'], 2) : '0.00' }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            @else
                                                <span class="text-muted">No offers yet</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="card-body pt-0"><p class="text-muted mb-0">No supplier offers available yet.</p></div>
                @endif
            </div>

            <div id="live-supplier-summary" class="alert alert-info mb-4" style="display:none;"></div>

            <div class="card mb-4" id="materials-cost-breakdown" style="display:none;">
                <div class="card-header"><h3 class="req-section-title"><i class="fas fa-calculator"></i> Materials cost</h3></div>
                <div class="card-body" id="materials-cost-table"></div>
            </div>
        </form>

        <x-modal-dialog id="clientRecommendModal">
            <x-slot:title>Recommend suppliers for all materials</x-slot:title>
            <div class="modal-body">
                <label for="clientRecommendCategory" class="form-label">Optimise for</label>
                <select id="clientRecommendCategory" class="form-select">
                    <option value="overall_best">Overall best</option>
                    <option value="cheapest">Cheapest</option>
                    <option value="fastest_delivery">Best delivery</option>
                    <option value="least_defects">Least defects</option>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="clientApplyRecommendBtn">Apply recommendation</button>
            </div>
        </x-modal-dialog>

        <div class="req-callout mb-4">
            <div class="req-callout__text">
                <i class="fas fa-flag-checkered text-success" aria-hidden="true"></i>
                <div><strong>Ready to go ahead?</strong><br>Pick a supplier for every material, then proceed to enter your project dates and payment plan.</div>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <form method="POST" action="{{ route('client.quotation.cancel', ['id' => $qr->id]) }}" onsubmit="return confirm('Cancel this quotation request? This cannot be undone.');">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger">Cancel request</button>
                </form>
                <button type="button" class="btn btn-success" id="proceedQuotationBtn" data-bs-toggle="modal" data-bs-target="#proceedQuotationModal" disabled
                    title="Select a supplier for every material first.">Proceed with quotation <i class="fas fa-arrow-right ms-1"></i></button>
            </div>
        </div>

        <x-modal-dialog id="proceedQuotationModal" dialog-class="modal-lg">
            <x-slot:title>Proceed with quotation</x-slot:title>
            <form method="POST" action="{{ route('client.quotation.proceed', ['id' => $qr->id]) }}" id="proceedQuotationForm">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <x-field name="property_address" label="Property address" col="md-12" required placeholder="House no., street, barangay, city" />
                        <x-field name="project_start_date" type="date" label="Start date" col="md-6" required />
                        <x-field name="project_end_date" type="date" label="End date" col="md-6" required readonly />
                        <x-field name="payment_method" label="Payment method" col="md-6" required>
                            <select name="payment_method" id="payment_method" class="form-select" required>
                                <option value="">Select method</option>
                                <option value="bank_transfer">Bank transfer</option>
                                <option value="check">Check</option>
                                <option value="cash">Cash</option>
                            </select>
                        </x-field>
                        <x-field name="payment_plan" label="Payment plan" col="md-6" required>
                            <select name="payment_plan" id="payment_plan" class="form-select" required>
                                <option value="">Select plan</option>
                                <option value="30% down, 40% halfway, 30% on completion">30% down, 40% halfway, 30% on completion</option>
                                <option value="50/50">50% down, 50% on completion</option>
                                <option value="Full upon completion">Full upon completion</option>
                                <option value="milestone">Milestone-based (20% down, 20% after foundation, 30% after structure, 30% on completion)</option>
                                <option value="monthly3" data-months="3">Monthly for 3 months (equal payments)</option>
                                <option value="monthly6" data-months="6">Monthly for 6 months (equal payments)</option>
                                <option value="monthly12" data-months="12">Monthly for 12 months (equal payments)</option>
                            </select>
                        </x-field>
                    </div>
                    <div id="payment-breakdown" class="mt-2"></div>
                    <p class="text-muted small mb-0 mt-3">Once you proceed, your supplier choices are final and our team will prepare the contract.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Back</button>
                    <button type="submit" class="btn btn-success">Yes, proceed</button>
                </div>
            </form>
        </x-modal-dialog>
    @endif
</div>
<div id="quotation-confirmation-summary" style="display:none;"></div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>window.__page = Object.assign(window.__page || {}, { view0_0: {{ Js::from((string) (url('client/quotation/recommend-suppliers'))) }}, view0_1: {{ Js::from((string) ($quotationRequest->id)) }}, view0_2: {{ Js::from($materialSupplierResponses ?? []) }}, view0_3: {{ Js::from(collect($rfqs ?? [])->flatMap(fn ($rfq) => $rfq->responses)->mapWithKeys(fn ($r) => [$r->supplier_id => [
    'discount_type' => (string) $r->discount_type, 'discount_percentage' => (string) $r->discount_percentage,
    'discount_amount' => (string) $r->discount_amount, 'final_amount' => (string) $r->final_amount,
]])->all() ?: new \stdClass) }} });</script>
<script>window.__page = Object.assign(window.__page || {}, { view1_0: {{ Js::from($totalEstimatedDays ?? 1) }} });</script>
@vite('resources/js/pages/client-quotation-view.js')
@endpush
