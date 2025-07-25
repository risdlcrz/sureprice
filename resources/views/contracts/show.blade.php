@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <p style="color:red;font-weight:bold;">Contract ID: {{ $contract->id ?? 'NOT SET' }}</p>
    <!-- Header Actions -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Contract Details</h2>
        <div class="d-flex gap-2">
            @if($contract->status === 'draft')
                <span class="badge bg-warning">Draft</span>
            @endif
            
            @if($contract->status === 'approved')
                <a href="{{ route('material-requests.create', ['contract_id' => $contract->id]) }}" 
                   class="btn btn-info">
                    <i class="fas fa-file-alt"></i> Create Material Request
                </a>
            @else
                <button class="btn btn-info" disabled title="Contract must be approved by admin before requesting materials.">
                    <i class="fas fa-file-alt"></i> Create Material Request
                </button>
            @endif

            @if(Auth::user()->hasRole('admin'))
            <button class="btn btn-success" onclick="approveContract()">
                <i class="fas fa-check"></i> Approve
            </button>
            <button class="btn btn-danger" onclick="rejectContract()">
                <i class="fas fa-times"></i> Reject
            </button>
            @elseif(Auth::user()->hasRole('manager') && $contract->status === 'draft')
                <form method="POST" action="{{ route('contracts.requestApproval', $contract) }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i> Request for Admin Approval
                    </button>
                </form>
            @endif
            <a href="{{ route('contracts.edit', $contract) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Edit Contract
            </a>
            <a href="{{ route('purchase-requests.edit', $contract->purchaseRequest) }}" 
               class="btn btn-success">
                <i class="fas fa-edit"></i> Edit Purchase Request
            </a>
            <form action="{{ route('contracts.destroy', $contract) }}" 
                  method="POST" 
                  class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        class="btn btn-danger" 
                        onclick="return confirm('Are you sure you want to delete this contract?')">
                    <i class="fas fa-trash"></i> Delete Contract
                </button>
            </form>
            <a href="{{ route('contracts.pdf', $contract) }}" 
               class="btn btn-success">
                <i class="fas fa-download"></i> Download/Print
            </a>
            <a href="{{ route('contracts.index') }}" 
               class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <!-- Contract Information -->
    <div class="card mb-4">
        <div class="card-header">
            <h5>Contract Information</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table">
                        <tr>
                            <th width="30%">Contract ID:</th>
                            <td>{{ $contract->contract_number }}</td>
                        </tr>
                        <tr>
                            <th>Start Date:</th>
                            <td>{{ $contract->start_date->format('F d, Y') }}</td>
                        </tr>
                        <tr>
                            <th>End Date:</th>
                            <td>{{ $contract->end_date->format('F d, Y') }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table">
                        <tr>
                            <th width="30%">Status:</th>
                            <td>
                                <span class="badge bg-{{ $contract->status_color }}">
                                    {{ ucwords(str_replace('_', ' ', $contract->status)) }}
                                </span>
                                @if($contract->payments && $contract->payments->count())
                                    <div class="mt-2">
                                        @php
                                            $total = $contract->total_amount;
                                            $paid = $contract->total_paid;
                                            $percent = $total > 0 ? round(($paid / $total) * 100) : 0;
                                        @endphp
                                        <div class="progress" style="height: 18px;">
                                            <div class="progress-bar {{ $percent >= 100 ? 'bg-success' : 'bg-info' }}" role="progressbar" style="width: {{ $percent }}%" aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100">
                                                {{ $percent }}% Paid
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Total Amount:</th>
                            <td>₱{{ number_format($contract->total_amount, 2) }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Property Information -->
    <div class="card mb-4">
        <div class="card-header">
            <h5>Property Information</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <table class="table">
                        <tr>
                            <th width="20%">Property Type:</th>
                            <td>{{ $contract->property->type }}</td>
                        </tr>
                        <tr>
                            <th>Address:</th>
                            <td>
                                {{ $contract->property->address }}<br>
                                {{ $contract->property->city }}<br>
                                {{ $contract->property->postal_code }}
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Contractor and Client Information -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5>Contractor Information</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <tr>
                            <th width="30%">Name:</th>
                            <td>{{ $contract->contractor->name }}</td>
                        </tr>
                        <tr>
                            <th>Company:</th>
                            <td>{{ $contract->contractor->company_name }}</td>
                        </tr>
                        <tr>
                            <th>Address:</th>
                            <td>{{ $contract->contractor->address }}</td>
                        </tr>
                        <tr>
                            <th>Email:</th>
                            <td>{{ $contract->contractor->email }}</td>
                        </tr>
                        <tr>
                            <th>Phone:</th>
                            <td>{{ $contract->contractor->phone }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5>Client Information</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <tr>
                            <th width="30%">Name:</th>
                            <td>{{ $contract->client->name }}</td>
                        </tr>
                        <tr>
                            <th>Company:</th>
                            <td>{{ $contract->client->company_name }}</td>
                        </tr>
                        <tr>
                            <th>Address:</th>
                            <td>{{ $contract->client->address }}</td>
                        </tr>
                        <tr>
                            <th>Email:</th>
                            <td>{{ $contract->client->email }}</td>
                        </tr>
                        <tr>
                            <th>Phone:</th>
                            <td>{{ $contract->client->phone }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Details -->
    @if($contract->status === 'approved')
    <div class="card mb-4">
        <div class="card-header">
            <h5>Payment Details</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table">
                        <tr>
                            <th width="30%">Payment Method:</th>
                            <td>{{ $contract->payment_method }}</td>
                        </tr>
                        <tr>
                            <th>Payment Plan:</th>
                            <td>{{ $contract->payment_terms }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif

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

    <!-- Scope, Materials, and Chosen Suppliers Table -->
    @if($contract->project && $contract->project->quotationRequest)
        <div class="card mb-4">
            <div class="card-header">
                <h5>Scope of Work, Materials, and Chosen Suppliers (from Client Quotation Request)</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                
                                <th>Room</th>
                                <th>Scope</th>
                                <th>Material</th>
                                <th>Quantity</th>
                                <th>Unit</th>
                                <th>Chosen Supplier</th>
                                <th>Unit Price</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($contract->project->quotationRequest->rooms as $room)
                                @foreach($room->scopes as $scope)
                                    @if(is_array($scope->selected_materials) && count($scope->selected_materials) > 0)
                                        @foreach($scope->selected_materials as $mat)
                                            @php
                                                $material = \App\Models\Material::find($mat['material_id']);
                                                $supplier = isset($mat['chosen_supplier_id']) ? \App\Models\Supplier::find($mat['chosen_supplier_id']) : null;
                                                $unitPrice = null;
                                                if ($material && $supplier) {
                                                    // Find the unit price from the material_quotation pivot
                                                    $rfqs = \App\Models\Quotation::where('notes', 'like', '%client quotation request #' . $contract->project->quotationRequest->request_number . '%')->with(['materials'])->get();
                                                    foreach ($rfqs as $rfq) {
                                                        $pivot = \DB::table('material_quotation')
                                                            ->where('quotation_id', $rfq->id)
                                                            ->where('material_id', $material->id)
                                                            ->where('selected_supplier_id', $supplier->id)
                                                            ->first();
                                                        if ($pivot && $pivot->unit_price) {
                                                            $unitPrice = $pivot->unit_price;
                                                            break;
                                                        }
                                                    }
                                                }
                                                if (!$unitPrice && $material) {
                                                    $unitPrice = $material->base_price;
                                                }
                                                $qty = $mat['quantity'] ?? 1;
                                                $total = $unitPrice * $qty;
                                            @endphp
                                            <tr>
                                                <td>{{ $room->name }}</td>
                                                <td>{{ $scope->scope_name }}</td>
                                                <td>{{ $material ? $material->name : 'Material #'.$mat['material_id'] }}</td>
                                                <td>{{ $qty }}</td>
                                                <td>{{ $mat['unit'] ?? ($material ? $material->unit : '-') }}</td>
                                                <td>{{ $supplier ? $supplier->company_name : '-' }}</td>
                                                <td>₱{{ number_format($unitPrice, 2) }}</td>
                                                <td>₱{{ number_format($total, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    @endif
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- Workflow Status -->
    @include('contracts.partials.workflow-status')
</div>

<!-- Signature Modal -->
<div class="modal fade" id="signatureModal" tabindex="-1" aria-labelledby="signatureModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="signatureModalLabel">Add Signature</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6 id="signatureModalSubtitle">Add your signature below</h6>
                <div class="signature-pad-container mb-3">
                    <canvas id="signatureCanvas" class="signature-pad" style="border: 1px solid #dee2e6; border-radius: 4px; width: 100%; height: 200px;"></canvas>
                </div>
                <button type="button" class="btn btn-secondary btn-sm mb-2" onclick="clearSignature()">Clear</button>
                <button type="button" class="btn btn-primary btn-sm mb-2" onclick="saveSignature()">Save Signature</button>
            </div>
        </div>
    </div>
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