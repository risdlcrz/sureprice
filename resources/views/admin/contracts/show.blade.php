@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Contract Details</h1>
        <div>
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
            <a href="{{ route('contracts.download', $contract->id) }}" class="btn btn-success">
                <i class="bi bi-download"></i> Download PDF
            </a>
            <button type="button" class="btn btn-danger" onclick="showDeleteModal()">
                <i class="bi bi-trash"></i> Delete Contract
            </button>
            <a href="{{ route('contracts.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <!-- Status Update Success Message -->
    <div id="statusAlert" class="alert alert-success" style="display: none;" role="alert">
        Contract status updated successfully
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
                    <p><strong>Contract ID:</strong> {{ $contract->contract_id }}</p>
                    <p><strong>Start Date:</strong> {{ $contract->start_date->format('F d, Y') }}</p>
                    <p><strong>End Date:</strong> {{ $contract->end_date->format('F d, Y') }}</p>
                        </div>
                <div class="col-md-6">
                    <p><strong>Status:</strong> <span class="badge bg-{{ $contract->status === 'draft' ? 'warning' : 'success' }}">{{ ucfirst($contract->status) }}</span></p>
                    <p><strong>Total Amount:</strong> ₱{{ number_format($contract->total_amount, 2) }}</p>
                    <p><strong>Budget Allocation:</strong> ₱{{ number_format($contract->budget_allocation, 2) }}</p>
                </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Property Information</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Address:</strong><br>
                        {{ $contract->property->street }}<br>
                        @if($contract->property->unit)
                            Unit {{ $contract->property->unit }},<br>
                        @endif
                        Barangay {{ $contract->property->barangay }},<br>
                        {{ $contract->property->city }},<br>
                        {{ $contract->property->state }} {{ $contract->property->postal }}
                    </p>
                </div>
                <div class="col-md-6">
                    <p><strong>Property Type:</strong> {{ ucfirst($contract->property->property_type) }}</p>
                    @if($contract->property->property_size)
                        <p><strong>Property Size:</strong> {{ number_format($contract->property->property_size) }} sq ft</p>
                    @endif
                </div>
            </div>
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
                    <p><strong>Payment Method:</strong> {{ ucfirst($contract->payment_method) }}</p>
                    <p><strong>Payment Terms:</strong><br>{{ $contract->payment_terms }}</p>
                </div>
                <div class="col-md-6">
                    @if($contract->payment_method === 'bank_transfer')
                        <p><strong>Bank Name:</strong> {{ $contract->bank_name }}</p>
                        <p><strong>Account Name:</strong> {{ $contract->bank_account_name }}</p>
                        <p><strong>Account Number:</strong> {{ $contract->bank_account_number }}</p>
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
                    <p><strong>Start Date:</strong> {{ $contract->start_date->format('F d, Y') }}</p>
                    <p><strong>End Date:</strong> {{ $contract->end_date->format('F d, Y') }}</p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <h6>Work Categories</h6>
                    <p>{{ $contract->scope_of_work }}</p>
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
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Purchase Order Linked</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($contract->items as $item)
                                    <tr>
                                        <td>
                                            <div class="mb-2">
                                                @if($contract->purchaseOrder)
                                                    <div><strong>PO ID:</strong> 
                                                        <a href="{{ route('purchase-orders.show', $contract->purchaseOrder->id) }}" target="_blank">
                                                            {{ $contract->purchaseOrder->po_number }} (ID: {{ $contract->purchaseOrder->id }})
                                                        </a>
                                                    </div>
                                                    <div><strong>Supplier:</strong> 
                                                        {{ $contract->purchaseOrder->supplier->company_name ?? $contract->purchaseOrder->supplier->name ?? 'N/A' }}
                                                    </div>
                                                @else
                                                    <div><strong>PO ID:</strong> N/A</div>
                                                    <div><strong>Supplier:</strong> 
                                                        {{ $item->supplier->company_name ?? $item->supplier->name ?? $item->supplier_name ?? 'N/A' }}
                                                    </div>
                                                @endif
                                                <div><strong>Quantity:</strong> {{ number_format($item->quantity, 2) }} {{ $item->material_unit }}</div>
                                                <div><strong>Unit Price:</strong> {{ number_format($item->amount, 2) }}</div>
                                                <div><strong>Total:</strong> {{ number_format($item->total, 2) }}</div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="1" class="text-center">No items found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
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
                        <img src="{{ Storage::url($contract->contractor_signature) }}" 
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
                        <img src="{{ Storage::url($contract->client_signature) }}" 
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
</div>
@endsection

@push('scripts')
<script>
function updateStatus(status) {
    // Get the CSRF token from the meta tag
    const token = document.querySelector('meta[name="csrf-token"]');
    if (!token) {
        console.error('CSRF token meta tag not found');
        alert('Error: CSRF token not found. Please refresh the page and try again.');
        return;
    }

    const csrfToken = token.getAttribute('content');
    if (!csrfToken) {
        console.error('CSRF token is empty');
        alert('Error: CSRF token is empty. Please refresh the page and try again.');
        return;
    }

    console.log('Updating contract status:', {
        status: status,
        url: "{{ url('contracts/' . $contract->id . '/status') }}",
        token: csrfToken.substring(0, 8) + '...' // Log only first 8 chars for security
    });
    
    fetch("{{ url('contracts/' . $contract->id . '/status') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ 
            status: status, 
            _method: 'PATCH' 
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
            // Show success message
            const alert = document.getElementById('statusAlert');
            alert.style.display = 'block';
            setTimeout(() => {
                alert.style.display = 'none';
                // Reload the page to reflect the new status
                window.location.reload();
            }, 1000);
        } else {
            console.error('Error updating status:', data);
            alert('Error updating status: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating status: ' + error.message);
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