@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Contract Details</h1>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Contract Information</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Contract ID:</strong> {{ $contract->contract_number }}</p>
                    <p><strong>Start Date:</strong> {{ $contract->start_date->format('F d, Y') }}</p>
                    <p><strong>End Date:</strong> {{ $contract->end_date->format('F d, Y') }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Status:</strong> <span class="badge bg-{{ $contract->status === 'draft' ? 'warning' : 'success' }}">{{ ucfirst($contract->status) }}</span></p>
                    <p><strong>Total Amount:</strong> 
                        ₱{{ number_format($contract->materials_cost + $contract->labor_cost, 2) }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Property Information</h5>
        </div>
        <div class="card-body">
                <p><strong>Property Address:</strong><br>
                {{ $contract->property_address ?? 'N/A' }}
                </p>
            @if($contract->property && $contract->property->property_size)
                    <p><strong>Property Size:</strong> {{ $contract->property->property_size }}㎡</p>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Contractor Information</h5>
                </div>
                <div class="card-body">
                    <p><strong>Name:</strong> {{ $contract->contractor->name }}</p>
                    @if($contract->contractor->company_name)
                    <p><strong>Company:</strong> {{ $contract->contractor->company_name }}</p>
                    @endif
                    <p><strong>Address:</strong><br>
                        @if($contract->contractor->street) {{ $contract->contractor->street }}<br> @endif
                        @if($contract->contractor->unit) Unit {{ $contract->contractor->unit }},<br> @endif
                        @if($contract->contractor->barangay) Barangay {{ $contract->contractor->barangay }},<br> @endif
                        @if($contract->contractor->city) {{ $contract->contractor->city }},<br> @endif
                        @if($contract->contractor->state || $contract->contractor->postal)
                            {{ $contract->contractor->state }} {{ $contract->contractor->postal }}
            @endif
                    </p>
                    <p><strong>Email:</strong> {{ $contract->contractor->email }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Client Information</h5>
                </div>
                <div class="card-body">
                    <p><strong>Name:</strong> {{ $contract->client->name }}</p>
                    @if($contract->client->company_name)
                        <p><strong>Company:</strong> {{ $contract->client->company_name }}</p>
                    @endif
                    <p><strong>Address:</strong><br>
                        @if($contract->client->street) {{ $contract->client->street }}<br> @endif
                        @if($contract->client->unit) Unit {{ $contract->client->unit }},<br> @endif
                        @if($contract->client->barangay) Barangay {{ $contract->client->barangay }},<br> @endif
                        @if($contract->client->city) {{ $contract->client->city }},<br> @endif
                        @if($contract->client->state || $contract->client->postal)
                            {{ $contract->client->state }} {{ $contract->client->postal }}
            @endif
                    </p>
                    <p><strong>Email:</strong> {{ $contract->client->email }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Payment Details</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Payment Method:</strong> {{ ucfirst(str_replace('_', ' ', $contract->payment_method)) }}</p>
                    <p><strong>Payment Plan:</strong> {{ $contract->payment_plan }}</p>
                    <p><strong>Payment Terms:</strong><br>{{ $contract->payment_terms }}</p>

                    {{-- Payment Breakdown Table --}}
                    @if($contract->payment_plan && $contract->total_amount)
                        @php
                            $plan = $contract->payment_plan;
                            $total = $contract->total_amount;
                            $rows = [];
                            if ($plan === '30% down, 40% halfway, 30% on completion') {
                                $rows = [['Downpayment', 30], ['Halfway Payment', 40], ['Completion Payment', 30]];
                            } elseif ($plan === '50/50') {
                                $rows = [['Downpayment', 50], ['Completion Payment', 50]];
                            } elseif ($plan === 'Full upon completion') {
                                $rows = [['Completion Payment', 100]];
                            } elseif ($plan === 'milestone') {
                                $rows = [['Downpayment', 20], ['After Foundation', 20], ['After Structure', 30], ['Completion Payment', 30]];
                            } elseif ($plan === 'monthly3') {
                                for ($i = 1; $i <= 3; $i++) $rows[] = ["Month $i Payment", 100/3];
                            } elseif ($plan === 'monthly6') {
                                for ($i = 1; $i <= 6; $i++) $rows[] = ["Month $i Payment", 100/6];
                            } elseif ($plan === 'monthly12') {
                                for ($i = 1; $i <= 12; $i++) $rows[] = ["Month $i Payment", 100/12];
                            }
                        @endphp
                        <div class="table-responsive mt-4">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Stage</th>
                                        <th>Percent</th>
                                        <th>Amount (₱)</th>
                        </tr>
                                </thead>
                                <tbody>
                                    @php $sum = 0; @endphp
                                    @foreach($rows as [$label, $percent])
                                        @php $amt = round($total * $percent / 100, 2); $sum += $amt; @endphp
                        <tr>
                                            <td>{{ $label }}</td>
                                            <td>{{ rtrim(rtrim(number_format($percent,2), '0'), '.') }}%</td>
                                            <td>₱{{ number_format($amt,2) }}</td>
                        </tr>
                                    @endforeach
                                    <tr class="fw-bold">
                                        <td>Total</td>
                                        <td>100%</td>
                                        <td>₱{{ number_format($total,2) }}</td>
                        </tr>
                                </tbody>
                    </table>
                        </div>
                    @else
                        <div class="alert alert-warning mt-4">No payment breakdown available. Please check contract payment plan and amount.</div>
                    @endif
                </div>
                <div class="col-md-6">
                    @if($contract->payment_method === 'bank_transfer')
                        <p><strong>Bank Name:</strong> {{ $contract->bank_name }}</p>
                        <p><strong>Account Name:</strong> {{ $contract->bank_account_name }}</p>
                        <p><strong>Account Number:</strong> {{ $contract->bank_account_number }}</p>
                    @elseif($contract->payment_method === 'check')
                        <p><strong>Check Number:</strong> {{ $contract->check_number }}</p>
                        <p><strong>Check Date:</strong> 
                            {{ $contract->check_date ? \Carbon\Carbon::parse($contract->check_date)->format('F d, Y') : '' }}
                        </p>
                    @else
                        <p><strong>Cash Payment</strong></p>
                                @endif
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Scope of Work</h5>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-12">
                    <h6>Project Timeline</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="review-item">
                                <span class="review-label">Project Timeline:</span>
                                <span class="review-value">
                                    {{ $contract->start_date ? \Carbon\Carbon::parse($contract->start_date)->format('M d, Y') : '-' }} to 
                                    {{ $contract->end_date ? \Carbon\Carbon::parse($contract->end_date)->format('M d, Y') : '-' }}
                                    @if($contract->estimated_days)
                                        ({{ $contract->estimated_days }} days)
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <h6>Rooms & Work Categories</h6>
                    @if($contract->rooms && $contract->rooms->count())
                        @foreach($contract->rooms as $room)
                            <div class="room-section mb-4">
                                <h6>{{ $room->name }}</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Dimensions:</strong> {{ $room->length }}m x {{ $room->width }}m (Area: {{ $room->area }}㎡)</p>
                                    </div>
                                </div>
                                @if($room->scopeTypes && $room->scopeTypes->count())
                                    <div class="scope-types mt-2">
                                        <strong>Work Categories:</strong>
                                        <ul class="list-unstyled">
                                            @foreach($room->scopeTypes as $scope)
                                                <li>
                                                    <i class="fas fa-check-circle text-success"></i>
                                                    {{ $scope->name }} ({{ $scope->category }})
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    @elseif($contract->quotationRequest && $contract->quotationRequest->rooms && $contract->quotationRequest->rooms->count())
                        @foreach($contract->quotationRequest->rooms as $room)
                            <div class="room-section mb-4">
                                <h6>{{ $room->name }}</h6>
                                <!-- Add more details if needed -->
                            </div>
                        @endforeach
                    @else
                        <p class="text-center">No rooms defined for this contract.</p>
                    @endif
                    <h6>Description</h6>
                    <p>{{ $contract->scope_description }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
                <div class="card-header">
            <h5 class="mb-0">Scope, Materials, and Suppliers</h5>
                </div>
                <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Room</th>
                            <th>Scope of Work</th>
                            <th>Material</th>
                            <th>Supplier</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($contract->items && $contract->items->count())
                            @foreach($contract->items as $item)
                                <tr>
                                    <td>{{ $item->room->name ?? '-' }}</td>
                                    <td>{{ $item->scope->scope_name ?? '-' }}</td>
                                    <td>{{ $item->material_name }}</td>
                                    <td>{{ $item->supplier_name ?? 'N/A' }}</td>
                        </tr>
                            @endforeach
                        @elseif($contract->quotationRequest && $contract->quotationRequest->rooms && $contract->quotationRequest->rooms->count())
                            @foreach($contract->quotationRequest->rooms as $room)
                                @foreach($room->scopes as $scope)
                                    @if(is_array($scope->selected_materials))
                                        @foreach($scope->selected_materials as $material)
                                            <tr>
                                                <td>{{ $room->name }}</td>
                                                <td>{{ $scope->scope_name }}</td>
                                                <td>{{ $material['name'] ?? $material }}</td>
                                                <td>{{ $material['supplier'] ?? 'N/A' }}</td>
                        </tr>
                                        @endforeach
                                    @endif
                                @endforeach
                            @endforeach
                        @else
                            <tr><td colspan="4">No rooms or materials found for this contract.</td></tr>
                        @endif
                    </tbody>
                    </table>
                </div>
            </div>
        </div>

    <div class="card mb-4">
                <div class="card-header">
            <h5 class="mb-0">Contract Items</h5>
                </div>
                <div class="card-body">
            <div class="table-responsive">
                <table id="contractItemsTable" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Unit</th>
                            <th>Unit Cost</th>
                            <th>Quantity</th>
                            <th>Total Cost</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($contract->items as $item)
                            <tr>
                                <td data-item-name>{{ $item->material_name }}</td>
                                <td data-item-unit>{{ $item->unit }}</td>
                                <td data-item-unit-cost>₱{{ number_format($item->amount, 2) }}</td>
                                <td data-item-quantity>{{ number_format($item->quantity, 2) }}</td>
                                <td data-item-total-cost>₱{{ number_format($item->total, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No items found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" class="text-end"><strong>Total Materials Cost:</strong></td>
                            <td>
                                @if($contract->discount_amount && $contract->final_amount)
                                    <span class="text-decoration-line-through text-muted">₱{{ number_format($contract->materials_cost, 2) }}</span><br>
                                    <span>₱{{ number_format($contract->final_amount, 2) }}</span>
                                @else
                                    ₱{{ number_format($contract->materials_cost, 2) }}
                                @endif
                            </td>
                        </tr>
                        @if($contract->discount_amount && $contract->final_amount)
                        <tr>
                            <td colspan="4" class="text-end"><strong>Supplier Discount:</strong></td>
                            <td>{{ ucfirst($contract->discount_type) }}
                                @if($contract->discount_percentage)
                                    ({{ rtrim(rtrim(number_format($contract->discount_percentage,2), '0'), '.') }}%)
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" class="text-end"><strong>Final Amount After Discount:</strong></td>
                            <td>₱{{ number_format($contract->final_amount, 2) }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td colspan="4" class="text-end"><strong>Total Labor Cost:</strong></td>
                            <td>₱{{ number_format($contract->labor_cost, 2) }}</td>
                        </tr>
                        <tr>
                            <td colspan="4" class="text-end"><strong>Grand Total:</strong></td>
                            <td>₱{{ number_format(($contract->final_amount ?? $contract->materials_cost) + $contract->labor_cost, 2) }}</td>
                        </tr>
                    </tfoot>
                    </table>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Signatures</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 text-center">
                    <h6>Contractor's Signature</h6>
                    @if($contract->contractor_signature)
                        <img src="{{ asset('storage/' . $contract->contractor_signature) }}" 
                             alt="Contractor's Signature" 
                             class="img-fluid mb-2" 
                             style="max-height: 100px;">
                        <p class="mb-0">{{ $contract->contractor->name }}</p>
                        <small class="text-muted">Contractor</small>
                        @if($contract->contractor_date_signed)
                            <br><small class="text-muted">Signed: {{ $contract->contractor_date_signed instanceof \Carbon\Carbon ? $contract->contractor_date_signed->format('M d, Y') : \Carbon\Carbon::parse($contract->contractor_date_signed)->format('M d, Y') }}</small>
                        @endif
                    @else
                        <p class="text-muted">No signature provided</p>
                    @endif
                </div>
                <div class="col-md-6 text-center">
                    <h6>Client's Signature</h6>
                    @if($contract->client_signature)
                        <img src="{{ asset('storage/' . $contract->client_signature) }}" 
                             alt="Client's Signature" 
                             class="img-fluid mb-2" 
                             style="max-height: 100px;">
                        <p class="mb-0">{{ $contract->client->name }}</p>
                        <small class="text-muted">Client</small>
                        @if($contract->client_date_signed)
                            <br><small class="text-muted">Signed: {{ $contract->client_date_signed instanceof \Carbon\Carbon ? $contract->client_date_signed->format('M d, Y') : \Carbon\Carbon::parse($contract->client_date_signed)->format('M d, Y') }}</small>
                        @endif
                    @else
                        <p class="text-muted">No signature provided</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Project Progress Bar -->
    @if($contract->start_date && $contract->end_date)
    @php
        $now = \Carbon\Carbon::now();
        $start = \Carbon\Carbon::parse($contract->start_date);
        $end = \Carbon\Carbon::parse($contract->end_date);
        $totalDays = $start->diffInDays($end) ?: 1;
        $elapsedDays = $start->diffInDays(min($now, $end));
        $progressPercent = min(100, round(($elapsedDays / $totalDays) * 100));
    @endphp
    <div class="mb-4">
        <label class="fw-bold">Project Progress (by Days)</label>
        <div class="progress" style="height: 24px;">
            <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $progressPercent }}%" aria-valuenow="{{ $progressPercent }}" aria-valuemin="0" aria-valuemax="100">
                {{ $progressPercent }}% ({{ $elapsedDays }} of {{ $totalDays }} days)
            </div>
        </div>
    </div>
    @endif

    <!-- Payment Schedule Table -->
    @if($contract->payment_schedule)
    @php $schedule = is_string($contract->payment_schedule) ? json_decode($contract->payment_schedule, true) : $contract->payment_schedule; @endphp
    <div class="card mb-4">
        <div class="card-header">
            <h5>Payment Schedule</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Stage</th>
                            <th>Amount</th>
                            <th>Due Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($schedule as $segment)
                        @php
                            $status = '-';
                            if(isset($contract->payments)) {
                                $p = $contract->payments->firstWhere('stage', $segment['stage']);
                                $status = $p ? ucfirst($p->status) : 'Pending';
                            }
                        @endphp
                        <tr>
                            <td>{{ $segment['stage'] }}</td>
                            <td>₱{{ number_format($segment['amount'], 2) }}</td>
                            <td>{{ $segment['due_date'] ?? '-' }}</td>
                            <td>{{ $status }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('styles')
    @vite(['resources/css/contracts-show.css'])
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        window.contractSignatureUrl = '{{ url('/contracts/' . ($contract->id ?? 'MISSING_ID') . '/signatures') }}';
        console.log('Signature URL:', window.contractSignatureUrl);
    </script>
    @vite(['resources/js/contracts-show.js'])
@endpush 