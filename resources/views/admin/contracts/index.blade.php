@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Contracts</h1>
        <div>
            <div class="btn-group me-2">
                <a href="{{ route('contracts.index') }}" class="btn btn-outline-secondary {{ !request('status') ? 'active' : '' }}">
                    All
                </a>
                <a href="{{ route('contracts.index', ['status' => 'draft']) }}" class="btn btn-outline-secondary {{ request('status') === 'draft' ? 'active' : '' }}">
                    Draft
                </a>
                <a href="{{ route('contracts.index', ['status' => 'approved']) }}" class="btn btn-outline-secondary {{ request('status') === 'approved' ? 'active' : '' }}">
                    Approved
                </a>
                <a href="{{ route('contracts.index', ['status' => 'rejected']) }}" class="btn btn-outline-secondary {{ request('status') === 'rejected' ? 'active' : '' }}">
                    Rejected
                </a>
            </div>
        <a href="{{ route('contracts.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> New Contract
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
                    <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Update Modal -->
    <div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="statusModalLabel">Update Contract Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to change the status of this contract?</p>
                    <div class="btn-group w-100">
                        <button type="button" class="btn btn-outline-secondary status-btn" data-status="draft">Draft</button>
                        <button type="button" class="btn btn-outline-success status-btn" data-status="approved">Approve</button>
                        <button type="button" class="btn btn-outline-danger status-btn" data-status="rejected">Reject</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Contract ID</th>
                            <th>Client</th>
                            <th>Contractor</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Materials Cost</th>
                            <th>Labor Cost</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($contracts as $contract)
                            <tr>
                                <td>{{ $contract->contract_number }}</td>
                                <td>
                                    {{ $contract->client->name }}
                                    @if($contract->client->company_name)
                                        <br>
                                        <small class="text-muted">{{ $contract->client->company_name }}</small>
                                    @endif
                                </td>
                                <td>
                                    {{ $contract->contractor->name }}
                                    @if($contract->contractor->company_name)
                                        <br>
                                        <small class="text-muted">{{ $contract->contractor->company_name }}</small>
                                    @endif
                                </td>
                                <td>{{ $contract->start_date->format('M d, Y') }}</td>
                                <td>{{ $contract->end_date->format('M d, Y') }}</td>
                                <td>₱{{ number_format($contract->total_amount - $contract->labor_cost, 2) }}</td>
                                <td>₱{{ number_format($contract->labor_cost, 2) }}</td>
                                <td>₱{{ number_format($contract->total_amount, 2) }}</td>
                                <td>
                                    <button type="button" 
                                            class="btn btn-sm status-badge {{ $contract->status === 'draft' ? 'btn-warning' : ($contract->status === 'approved' ? 'btn-success' : 'btn-secondary') }}"
                                            onclick="showStatusModal({{ $contract->id }})">
                                        {{ ucfirst($contract->status) }}
                                    </button>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('contracts.show', $contract->id) }}" 
                                           class="btn btn-sm btn-outline-primary"
                                           title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('contracts.edit', $contract->id) }}" 
                                           class="btn btn-sm btn-outline-secondary"
                                           title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger" 
                                                title="Delete"
                                                onclick="confirmDelete({{ $contract->id }})">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                    </div>
                                    <form id="delete-form-{{ $contract->id }}" 
                                          action="{{ route('contracts.destroy', $contract->id) }}" 
                                          method="POST" 
                                          style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No contracts found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end mt-3">
                {{ $contracts->links() }}
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .btn-group .btn {
        padding: 0.25rem 0.5rem;
    }
    .badge {
        font-size: 0.875rem;
    }
    .status-badge {
        cursor: pointer;
    }
    .status-badge:hover {
        opacity: 0.8;
    }
</style>
@endpush

@push('scripts')
<script>
let currentContractId = null;
const statusModal = new bootstrap.Modal(document.getElementById('statusModal'));

function showStatusModal(contractId) {
    currentContractId = contractId;
    statusModal.show();
}

document.querySelectorAll('.status-btn').forEach(button => {
    button.addEventListener('click', function() {
        const status = this.dataset.status;
        updateStatus(currentContractId, status);
    });
});

function updateStatus(contractId, status) {
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    fetch(`/sureprice/public/contracts/${contractId}/status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ status: status, _method: 'PATCH' })
    })
    .then(async response => {
        let data;
        try {
            data = await response.clone().json();
        } catch (e) {
            const text = await response.text();
            throw new Error(text);
        }
        if (!response.ok) {
            throw new Error(data.message || 'Failed to update status');
        }
        return data;
    })
    .then(data => {
        if (data.success) {
            // Update the status button text and class
            const statusButton = document.querySelector(`button[onclick="showStatusModal(${contractId})"]`);
            statusButton.textContent = data.status;
            statusButton.className = `btn btn-sm status-badge ${
                data.status.toLowerCase() === 'draft' ? 'btn-warning' : 
                data.status.toLowerCase() === 'approved' ? 'btn-success' : 
                'btn-secondary'
            }`;
            
            // Close the modal
            const statusModal = bootstrap.Modal.getInstance(document.getElementById('statusModal'));
            statusModal.hide();
            
            // Show success message
            alert('Contract status updated successfully');
        } else {
            throw new Error(data.message || 'Failed to update status');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert(error.message || 'Error updating status. Please try again.');
    });
}

let deleteForm = null;

function confirmDelete(contractId) {
    deleteForm = document.getElementById('delete-form-' + contractId);
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}

document.getElementById('confirmDelete').addEventListener('click', function() {
    if (deleteForm) {
        deleteForm.submit();
    }
});
</script>
@endpush
@endsection 