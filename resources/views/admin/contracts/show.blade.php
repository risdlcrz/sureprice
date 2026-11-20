@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="container mt-4">
    <!-- Status Alert -->
    <div id="statusAlert" class="alert alert-success" style="display: none;" role="alert">
        Contract status updated successfully!
    </div>

    <!-- Success Messages -->


    <!-- Contract Approval Banner -->
    @if($contract->status === 'approved')
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <h5 class="alert-heading">
                <i class="fas fa-check-circle"></i> Contract Approved!
            </h5>
            <p class="mb-0">
                This contract has been approved. Material requests and purchase requests are now enabled for manager, procurement, and warehouse users.
                Payment schedule has been created and is ready for finance processing.
            </p>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @php
        $isClient = auth()->check() && auth()->user()->user_type === 'company' && auth()->user()->company && auth()->user()->company->designation === 'client';
        // Only the project & procurement team manages contracts; clients and finance view them read-only.
        $canManage = auth()->check() && in_array(auth()->user()->role, \App\Models\Contract::STAFF_ROLES, true);
    @endphp
    <x-page-header title="Contract Details">
        @if($canManage)
                @if(Auth::user()->hasRole('admin'))
                    @if($contract->canBeEdited())
                        <div class="btn-group me-2">
                            <button type="button" 
                                    class="btn {{ $contract->status === 'draft' ? 'btn-warning' : 'btn-outline-warning' }}"
                                    onclick="updateStatus('draft')">
                                Draft
                            </button>
                            <button type="button" 
                                    class="btn {{ $contract->status === 'approved' ? 'btn-success' : 'btn-outline-success' }}"
                                    onclick="updateStatus('approved')"
                                    @if(!$contract->canBeApproved()) disabled title="Both contractor and client signatures are required before approval." @endif>
                                Approve
                            </button>
                            <button type="button" 
                                    class="btn {{ $contract->status === 'rejected' ? 'btn-danger' : 'btn-outline-danger' }}"
                                    onclick="updateStatus('rejected')">
                                Reject
                            </button>
                        </div>
                    @endif
                @elseif(Auth::user()->hasRole('manager') && $contract->status === 'draft')
                    <form method="POST" action="{{ route('contracts.requestApproval', $contract) }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-primary" @if(!$contract->canBeApproved()) disabled title="Both contractor and client signatures are required before requesting approval." @endif>
                            <i class="fas fa-paper-plane"></i> Request for Approval
                        </button>
                    </form>
                @endif
                @if($contract->canBeEdited())
                    <a href="{{ route('contracts.edit', $contract->id) }}" class="btn btn-primary">
                        <i class="bi bi-pencil"></i> Edit Contract
                    </a>
                @else
                    <span class="badge bg-success fs-6" title="Completed contracts cannot be edited or deleted">
                        <i class="bi bi-lock"></i> Contract Locked
                    </span>
                @endif
                @if(Auth::user()->hasRole('manager') && $contract->status === 'approved')
                    <a href="{{ route('material-requests.create', ['contract_id' => $contract->id]) }}" class="btn btn-info" id="createMaterialRequest">
                        <i class="fas fa-boxes"></i> Create Material Request
                    </a>
                @elseif(Auth::user()->hasRole('procurement') && $contract->status === 'approved')
                    <a href="{{ route('material-requests.create', ['contract_id' => $contract->id]) }}" class="btn btn-info" id="createMaterialRequest">
                        <i class="fas fa-boxes"></i> Create Material Request
                    </a>
                @elseif(Auth::user()->hasRole('warehousing') && $contract->status === 'approved')
                    <a href="{{ route('material-requests.create', ['contract_id' => $contract->id]) }}" class="btn btn-info" id="createMaterialRequest">
                        <i class="fas fa-boxes"></i> Create Material Request
                    </a>
                @elseif(in_array(Auth::user()->role, ['manager', 'procurement', 'contractor', 'warehousing']))
                    <button class="btn btn-info" disabled title="Contract must be approved by admin before requesting materials.">
                        <i class="fas fa-boxes"></i> Create Material Request
                    </button>
                @endif
                
                @if($contract->status === 'approved' && $contract->payments()->count() == 0)
                    <button type="button" class="btn btn-warning" onclick="generatePayments()" id="generatePaymentsBtn">
                        <i class="fas fa-money-bill"></i> Generate Payments
                    </button>
                @endif
            @endif
            <a href="{{ route('contracts.download', $contract->id) }}" class="btn btn-success">
                <i class="bi bi-download"></i> Download PDF
            </a>
            <a href="{{ route('contracts.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to List
            </a>
    </x-page-header>

    <!-- Delete Confirmation Modal -->
    <x-modal-dialog id="deleteModal">
        <x-slot:title>Delete Contract</x-slot:title>
                <div class="modal-body">
                    Are you sure you want to delete this contract? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" onclick="submitDelete()">Delete</button>
                </div>
    </x-modal-dialog>

    <!-- Signature Modal -->
    <x-modal-dialog id="signatureModal" dialog-class="modal-lg">
        <x-slot:title>Add Signature</x-slot:title>
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <h6 id="signatureModalSubtitle">Add your signature below</h6>
                    </div>
                    <div class="signature-pad-container">
                        <canvas id="signatureCanvas" class="signature-pad" style="border: 1px solid #dee2e6; border-radius: 4px;"></canvas>
                    </div>
                    <div class="text-center mt-3">
                        <button type="button" class="btn btn-secondary btn-sm" onclick="clearSignature()">Clear</button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveSignature()">Save Signature</button>
                </div>
    </x-modal-dialog>

    @if($contract->canBeDeleted())
        <form id="delete-form" action="{{ route('contracts.destroy', $contract->id) }}" method="POST" style="display: none;">
            @csrf
            @method('DELETE')
        </form>
    @endif

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
                    <p><strong>Status:</strong> <x-status-badge :status="$contract->status" /></p>
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

    <div class="row g-4 mb-4">
        <div class="col-md-6">@include('admin.contracts.partials.party-card', ['title' => 'Contractor Information', 'party' => $contract->contractor])</div>
        <div class="col-md-6">@include('admin.contracts.partials.party-card', ['title' => 'Client Information', 'party' => $contract->client])</div>
    </div>

    @include('admin.contracts.partials.payment-details')

    @include('admin.contracts.partials.scope-of-work')

    @include('admin.contracts.partials.materials')

    @include('admin.contracts.partials.items')

    @include('admin.contracts.partials.signatures')



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

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>window.__page = Object.assign(window.__page || {}, { show0_0: @json((string) (url('/contracts/' . ($contract->id ?? 'MISSING_ID') . '/signatures'))), show0_1: {{ Js::from((string) (route('contracts.updateStatus', $contract->id))) }}, show0_2: @json($contract->id), show0_3: @json((string) (url('contracts'))) });</script>
@vite('resources/js/pages/admin-contracts-show.js')
@endpush 