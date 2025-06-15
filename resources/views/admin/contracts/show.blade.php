@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="container mt-4">
    <!-- Status Alert -->
    <div id="statusAlert" class="alert alert-success" style="display: none;" role="alert">
        Contract status updated successfully!
    </div>

    @php
        $isClient = auth()->check() && auth()->user()->user_type === 'company' && auth()->user()->company && auth()->user()->company->designation === 'client';
    @endphp
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Contract Details</h1>
        <div>
            @if(!$isClient)
                <div class="btn-group me-2">
                    <button type="button" 
                            class="btn {{ $contract->status === 'draft' ? 'btn-warning' : 'btn-outline-warning' }}"
                            onclick="updateStatus('draft')">
                        Draft
                    </button>
                    <button type="button" 
                            class="btn {{ $contract->status === 'approved' ? 'btn-success' : 'btn-outline-success' }}"
                            onclick="updateStatus('approved')">
                        Approve
                    </button>
                    <button type="button" 
                            class="btn {{ $contract->status === 'rejected' ? 'btn-danger' : 'btn-outline-danger' }}"
                            onclick="updateStatus('rejected')">
                        Reject
                    </button>
                </div>
                <a href="{{ route('contracts.edit', $contract->id) }}" class="btn btn-primary">
                    <i class="bi bi-pencil"></i> Edit Contract
                </a>
                @if($contract->status === 'approved')
                    <a href="{{ route('purchase-requests.create', ['contract_id' => $contract->id]) }}" class="btn btn-success" id="generatePurchaseRequest">
                        <i class="fas fa-file-invoice"></i> Edit Purchase Request
                    </a>
                @endif
                <button type="button" class="btn btn-danger" onclick="showDeleteModal()">
                    <i class="bi bi-trash"></i> Delete Contract
                </button>
            @endif
            <a href="{{ route('contracts.download', $contract->id) }}" class="btn btn-success">
                <i class="bi bi-download"></i> Download PDF
            </a>
            <a href="{{ route('contracts.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Delete Contract</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this contract? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" onclick="submitDelete()">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <form id="delete-form" action="{{ route('contracts.destroy', $contract->id) }}" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

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
                    <p><strong>Total Amount:</strong> ₱{{ number_format($contract->total_amount, 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Property Information</h5>
        </div>
        <div class="card-body">
            <p><strong>Property Type:</strong> {{ ucfirst($contract->property->property_type) }}</p>
            <p><strong>Address:</strong><br>
                {{ $contract->property->street }}
                @if($contract->property->unit_number)
                    Unit {{ $contract->property->unit_number }},<br>
                @endif
                Barangay {{ $contract->property->barangay }},<br>
                {{ $contract->property->city }},<br>
                {{ $contract->property->state }} {{ $contract->property->postal }}
            </p>
            @if($contract->property->property_size)
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
                        {{ $contract->contractor->street }}
                        @if($contract->contractor->unit)
                            Unit {{ $contract->contractor->unit }},<br>
                        @endif
                        Barangay {{ $contract->contractor->barangay }},<br>
                        {{ $contract->contractor->city }},<br>
                        {{ $contract->contractor->state }} {{ $contract->contractor->postal }}
                    </p>
                    <p><strong>Email:</strong> {{ $contract->contractor->email }}</p>
                    <p><strong>Phone:</strong> {{ $contract->contractor->phone }}</p>
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
                        {{ $contract->client->street }}
                        @if($contract->client->unit)
                            Unit {{ $contract->client->unit }},<br>
                        @endif
                        Barangay {{ $contract->client->barangay }},<br>
                        {{ $contract->client->city }},<br>
                        {{ $contract->client->state }} {{ $contract->client->postal }}
                    </p>
                    <p><strong>Email:</strong> {{ $contract->client->email }}</p>
                    <p><strong>Phone:</strong> {{ $contract->client->phone }}</p>
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
                    <p><strong>Payment Terms:</strong><br>{{ $contract->payment_terms }}</p>
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
                                    {{ \Carbon\Carbon::parse($contract->start_date)->format('M d, Y') }} to 
                                    {{ \Carbon\Carbon::parse($contract->end_date)->format('M d, Y') }}
                                    ({{ $contract->estimated_days }} days)
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <h6>Rooms & Work Categories</h6>
                    @forelse($contract->rooms as $room)
                        <div class="room-section mb-4">
                            <h6>{{ $room->name }}</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Dimensions:</strong> {{ $room->length }}m x {{ $room->width }}m (Area: {{ $room->area }}㎡)</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Total Cost:</strong> ₱{{ number_format($room->materials_cost + $room->labor_cost, 2) }}</p>
                                </div>
                            </div>
                            
                            @if($room->scopeTypes->count() > 0)
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
                    @empty
                        <p class="text-center">No rooms defined for this contract.</p>
                    @endforelse
                    <h6>Description</h6>
                    <p>{{ $contract->scope_description }}</p>
                </div>
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
                            <td>₱{{ number_format($contract->materials_cost, 2) }}</td>
                        </tr>
                        <tr>
                            <td colspan="4" class="text-end"><strong>Total Labor Cost:</strong></td>
                            <td>₱{{ number_format($contract->labor_cost, 2) }}</td>
                        </tr>
                        <tr>
                            <td colspan="4" class="text-end"><strong>Grand Total:</strong></td>
                            <td>₱{{ number_format($contract->total_amount, 2) }}</td>
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
                    @else
                        <p class="text-muted">No signature provided</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Cost Breakdown</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <p><strong>Materials Cost:</strong></p>
                    <h5>₱{{ number_format($contract->total_amount - $contract->labor_cost, 2) }}</h5>
                </div>
                <div class="col-md-4">
                    <p><strong>Labor Cost:</strong></p>
                    <h5>₱{{ number_format($contract->labor_cost, 2) }}</h5>
                </div>
                <div class="col-md-4">
                    <p><strong>Total Amount:</strong></p>
                    <h5>₱{{ number_format($contract->total_amount, 2) }}</h5>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function updateStatus(status) {
    // Get the CSRF token from the meta tag
    const token = document.querySelector('meta[name="csrf-token"]');
    if (!token) {
        console.error('CSRF token meta tag not found');
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'CSRF token not found. Please refresh the page and try again.'
        });
        return;
    }

    const csrfToken = token.getAttribute('content');
    if (!csrfToken) {
        console.error('CSRF token is empty');
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'CSRF token is empty. Please refresh the page and try again.'
        });
        return;
    }

    // Show loading state
    Swal.fire({
        title: 'Updating Status',
        text: 'Please wait...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    fetch("{{ url('contracts/' . $contract->id . '/status') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ 
            status: status 
        }),
        credentials: 'same-origin'
    })
    .then(response => {
        if (!response.ok) {
            if (response.status === 419) {
                throw new Error('CSRF token mismatch. Please refresh the page and try again.');
            }
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: 'Contract status updated successfully!',
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                window.location.reload();
            });
        } else {
            throw new Error(data.message || 'Unknown error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error updating status: ' + error.message
        });
    });
}

function showDeleteModal() {
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}

function submitDelete() {
    document.getElementById('delete-form').submit();
}
</script>
@endpush 