@extends('layouts.app')

@section('content')
{{-- @formatter:off --}}
{{-- stylelint-disable --}}
<div class="container mt-4">
    @if(auth()->user() && auth()->user()->party && auth()->user()->party->banned)
        <div class="alert alert-danger mb-4">
            <strong>You have been banned from the system.</strong>
            @if(auth()->user()->party->ban_reason)
                <br>Reason: {{ auth()->user()->party->ban_reason }}
            @endif
            <br>Please contact support for more information.
        </div>
    @endif
    <h1 class="h3 mb-4 text-gray-800 text-center" style="font-weight:700;color:#198754;letter-spacing:0.01em;">Contracts</h1>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div></div>
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
            @if(auth()->user() && auth()->user()->party && auth()->user()->party->banned)
                <button class="btn btn-primary" disabled title="You are banned and cannot create new contracts."><i class="bi bi-plus-lg"></i> New Contract</button>
            @else
                <a href="{{ route('contracts.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg"></i> New Contract
                </a>
            @endif
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
                    <div class="btn-group w-100 flex-wrap">
                        <button type="button" class="btn btn-outline-secondary status-btn" data-status="draft">Draft</button>
                        <button type="button" class="btn btn-outline-primary status-btn" data-status="active">Active</button>
                        <button type="button" class="btn btn-outline-info status-btn" data-status="partially_paid">Partially Paid</button>
                        <button type="button" class="btn btn-outline-success status-btn" data-status="fully_paid">Fully Paid</button>
                        <button type="button" class="btn btn-outline-danger status-btn" data-status="overdue">Overdue</button>
                        <button type="button" class="btn btn-outline-warning status-btn" data-status="suspended">Suspended</button>
                        <button type="button" class="btn btn-outline-dark status-btn" data-status="terminated">Terminated</button>
                        <button type="button" class="btn btn-outline-secondary status-btn" data-status="expired">Expired</button>
                        <button type="button" class="btn btn-outline-success status-btn" data-status="renewed">Renewed</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card contracts-table-container">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table contracts-table table-hover">
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
                            <th>Signatures</th>
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
                                    <span class="badge bg-{{ $contract->status_color }}">
                                        {{ ucwords(str_replace('_', ' ', $contract->status)) }}
                                    </span>
                                    @if($contract->payments && $contract->payments->count())
                                        @php
                                            $total = $contract->total_amount;
                                            $paid = $contract->total_paid;
                                            $percent = $total > 0 ? round(($paid / $total) * 100) : 0;
                                            $progressClass = $percent >= 100 ? 'bg-success' : 'bg-info';
                                            $progressStyle = "width: {$percent}%";
                                        @endphp
                                        <div class="progress mt-1">
                                            <div class="progress-bar {{ $progressClass }}" role="progressbar" style="{{ $progressStyle }}" aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100">
                                                {{ $percent }}%
                                            </div>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        @if($contract->contractor_signature)
                                            <span class="badge bg-success mb-1">
                                                <i class="fas fa-check"></i> Contractor
                                            </span>
                                        @else
                                            <span class="badge bg-warning mb-1">
                                                <i class="fas fa-clock"></i> Contractor
                                            </span>
                                        @endif
                                        @if($contract->client_signature)
                                            <span class="badge bg-success">
                                                <i class="fas fa-check"></i> Client
                                            </span>
                                        @else
                                            <span class="badge bg-warning">
                                                <i class="fas fa-clock"></i> Client
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('contracts.show', $contract->id) }}" 
                                           class="btn btn-outline-primary" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @if($contract->canBeEdited())
                                            <a href="{{ route('contracts.edit', $contract->id) }}" 
                                               class="btn btn-outline-secondary" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-outline-danger" 
                                                    title="Delete"
                                                    data-contract-id="{{ $contract->id }}"
                                                    onclick="confirmDelete(this.dataset.contractId)">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        @else
                                            <span class="badge bg-success" title="Completed contracts cannot be edited or deleted">
                                                <i class="bi bi-lock"></i> Locked
                                            </span>
                                        @endif
                                    </div>
                                    @if($contract->canBeDeleted())
                                        <form id="delete-form-{{ $contract->id }}" 
                                              action="{{ route('contracts.destroy', $contract->id) }}" 
                                              method="POST" 
                                              style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center">No contracts found.</td>
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
    .contracts-table-container {
        border-radius: 20px;
        overflow: hidden;
        border: 1px solid #e3e8ee;
        background: #fff;
        box-shadow: 0 4px 24px rgba(56,189,248,0.07), 0 1.5px 6px rgba(0,0,0,0.03);
        transition: box-shadow 0.25s;
        padding: 8px 0;
        margin-bottom: 32px;
    }
    .contracts-table {
        border-radius: 20px;
        overflow: hidden;
        background: #fff;
    }
    .contracts-table th, .contracts-table td {
        vertical-align: middle;
        padding: 18px 24px;
        font-size: 1.08rem;
        border: none;
        transition: background 0.22s, color 0.18s;
    }
    .contracts-table th {
        background: #f7fafc;
        font-weight: 700;
        color: #1a7f4e;
        border-bottom: 2px solid #e3e8ee;
        letter-spacing: 0.01em;
    }
    .contracts-table tbody tr:nth-child(even) {
        background: #f4f7fa;
    }
    .contracts-table tbody tr:hover {
        background: #e0f2fe;
        box-shadow: 0 2px 8px rgba(56,189,248,0.10);
        z-index: 1;
        position: relative;
    }
    .contracts-table .btn-group .btn {
        padding: 0.25rem 0.6rem;
        font-size: 1rem;
        border-radius: 8px;
        margin-right: 4px;
        transition: background 0.18s, color 0.18s;
    }
    .contracts-table .btn-group .btn:last-child {
        margin-right: 0;
    }
    .contracts-table .btn-outline-primary {
        border: 1px solid #38bdf8;
        color: #38bdf8;
        background: #e0f2fe;
    }
    .contracts-table .btn-outline-primary:hover {
        background: #38bdf8;
        color: #fff;
    }
    .contracts-table .btn-outline-secondary {
        border: 1px solid #a0aec0;
        color: #4a5568;
        background: #f7fafc;
    }
    .contracts-table .btn-outline-secondary:hover {
        background: #a0aec0;
        color: #fff;
    }
    .contracts-table .btn-outline-danger {
        border: 1px solid #ef4444;
        color: #ef4444;
        background: #fee2e2;
    }
    .contracts-table .btn-outline-danger:hover {
        background: #ef4444;
        color: #fff;
    }
    .contracts-table .badge {
        font-size: 0.95rem;
        border-radius: 8px;
        padding: 0.4em 0.8em;
    }
    .contracts-table .progress {
        border-radius: 8px;
        height: 14px;
    }
    .contracts-table .progress-bar {
        font-size: 0.85rem;
        font-weight: 600;
    }
    .contracts-table .text-center {
        color: #a0aec0;
        font-size: 1.1rem;
        padding: 32px 0;
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