@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Requests for Quotation</h4>
                    <a href="{{ route('quotations.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create New RFQ
                    </a>
                </div>
                <div class="card-body">
                    <!-- Search and Filters -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="search">Search</label>
                                <input type="text" class="form-control" id="search" 
                                    placeholder="Search by project, supplier, or material...">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select class="form-control" id="status">
                                    <option value="">All Statuses</option>
                                    <option value="draft">Draft</option>
                                    <option value="sent">Sent</option>
                                    <option value="responded">Responded</option>
                                    <option value="approved">Approved</option>
                                    <option value="rejected">Rejected</option>
                                    <option value="expired">Expired</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="purchase_request">Purchase Request</label>
                                <select class="form-control" id="purchase_request">
                                    <option value="">All Purchase Requests</option>
                                    @foreach($purchaseRequests as $pr)
                                        <option value="{{ $pr->id }}">PR-{{ $pr->id }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="perPage">Per Page</label>
                                <select class="form-control" id="perPage">
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Quotations Table -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>RFQ No.</th>
                                    <th>Project</th>
                                    <th>Suppliers</th>
                                    <th>Materials</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                    <th>Awarded Supplier</th>
                                    <th>Awarded Amount</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="quotationsTableBody">
                                @foreach($quotations as $quotation)
                                <tr>
                                    <td>{{ $quotation->rfq_number }}</td>
                                    <td>
                                        <div>
                                            <strong>PR-{{ $quotation->purchaseRequest->id }}</strong><br>
                                            <small class="text-muted">{{ $quotation->purchaseRequest->department }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            {{ $quotation->suppliers->count() }} suppliers<br>
                                            <small class="text-muted">
                                                <span
                                                    @if($quotation->responses->count() > 0)
                                                        data-toggle="tooltip"
                                                        title="{{ $quotation->responses->map(function($r) { return $r->supplier->company_name; })->implode(', ') }}"
                                                    @endif
                                                >
                                                    {{ $quotation->responses->count() }} responded
                                                </span>
                                            </small>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            {{ $quotation->purchaseRequest->items->count() }} materials<br>
                                            <small class="text-muted">
                                                Top categories: {{
                                                    $quotation->purchaseRequest->items
                                                        ->pluck('material')
                                                        ->filter()
                                                        ->pluck('category')
                                                        ->filter()
                                                        ->unique()
                                                        ->take(2)
                                                        ->map(function($cat) {
                                                            return is_object($cat) && isset($cat->name) ? $cat->name : (is_string($cat) ? $cat : '');
                                                        })
                                                        ->implode(', ')
                                                }}
                                            </small>
                                        </div>
                                    </td>
                                    <td>
                                        {{ $quotation->due_date->format('M d, Y') }}<br>
                                        <small class="text-muted">
                                            {{ $quotation->due_date->isPast() ? 'Overdue' : $quotation->due_date->diffForHumans() }}
                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $quotation->status_color }}">
                                            {{ ucfirst($quotation->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($quotation->awarded_supplier_id)
                                            {{ optional($quotation->suppliers->find($quotation->awarded_supplier_id))->company_name ?? 'N/A' }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td>
                                        @if($quotation->awarded_amount)
                                            ₱{{ number_format($quotation->awarded_amount, 2) }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('quotations.show', $quotation->id) }}" 
                                                class="btn btn-sm btn-info" 
                                                title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if(in_array($quotation->status, ['draft', 'sent']))
                                            <a href="{{ route('quotations.edit', $quotation->id) }}" 
                                                class="btn btn-sm btn-primary" 
                                                title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endif
                                            @if($quotation->status == 'draft')
                                            <button type="button" 
                                                class="btn btn-sm btn-success send-quotation" 
                                                data-id="{{ $quotation->id }}"
                                                title="Send">
                                                <i class="fas fa-paper-plane"></i>
                                            </button>
                                            @endif
                                            @if($quotation->status == 'responded')
                                            <button type="button" 
                                                class="btn btn-sm btn-success approve-quotation" 
                                                data-id="{{ $quotation->id }}"
                                                title="Approve">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button type="button" 
                                                class="btn btn-sm btn-warning reject-quotation" 
                                                data-id="{{ $quotation->id }}"
                                                title="Reject">
                                                <i class="fas fa-times"></i>
                                            </button>
                                            @endif
                                            <button type="button" 
                                                class="btn btn-sm btn-danger delete-quotation" 
                                                data-id="{{ $quotation->id }}"
                                                title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div>
                            Showing {{ $quotations->firstItem() }} to {{ $quotations->lastItem() }} of {{ $quotations->total() }} quotations
                        </div>
                        {{ $quotations->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .table td {
        vertical-align: middle;
    }
    .badge {
        font-size: 0.9em;
    }
    .badge-draft {
        background-color: #6c757d;
        color: white;
    }
    .badge-sent {
        background-color: #17a2b8;
        color: white;
    }
    .badge-responded {
        background-color: #ffc107;
        color: black;
    }
    .badge-approved {
        background-color: #28a745;
        color: white;
    }
    .badge-rejected {
        background-color: #dc3545;
        color: white;
    }
    .badge-expired {
        background-color: #6c757d;
        color: white;
    }
    .btn-group .btn {
        margin-right: 2px;
    }
</style>
@endpush

<!-- Send Confirmation Modal -->
<div class="modal fade" id="sendModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Send Request for Quotation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to send this RFQ to all selected suppliers?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmSend">Send</button>
            </div>
        </div>
    </div>
</div>

<!-- Status Change Modal -->
<div class="modal fade" id="statusModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Change RFQ Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="statusForm">
                    <div class="form-group">
                        <label for="statusNote">Note</label>
                        <textarea class="form-control" id="statusNote" rows="3" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmStatus">Confirm</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete RFQ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this RFQ? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline-block;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<!-- jQuery (required for Bootstrap 4 modals) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap JS (if not already included) -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Search and filter functionality
    let searchTimeout;
    const search = document.getElementById('search');
    const status = document.getElementById('status');
    const purchaseRequest = document.getElementById('purchase_request');
    const perPage = document.getElementById('perPage');

    function updateQuotations() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            const params = new URLSearchParams({
                search: search.value,
                status: status.value,
                purchase_request: purchaseRequest.value,
                per_page: perPage.value
            });

            window.location.href = `${window.location.pathname}?${params.toString()}`;
        }, 500);
    }

    search.addEventListener('input', updateQuotations);
    status.addEventListener('change', updateQuotations);
    purchaseRequest.addEventListener('change', updateQuotations);
    perPage.addEventListener('change', updateQuotations);

    // Set initial values from URL params
    const urlParams = new URLSearchParams(window.location.search);
    search.value = urlParams.get('search') || '';
    status.value = urlParams.get('status') || '';
    purchaseRequest.value = urlParams.get('purchase_request') || '';
    perPage.value = urlParams.get('per_page') || '10';

    // Send quotation functionality
    const sendModal = document.getElementById('sendModal');
    const confirmSend = document.getElementById('confirmSend');
    let currentQuotationId = '';

    document.querySelectorAll('.send-quotation').forEach(btn => {
        btn.addEventListener('click', function() {
            currentQuotationId = this.dataset.id;
            $(sendModal).modal('show');
        });
    });

    confirmSend.addEventListener('click', function() {
        fetch(`api/quotations/${currentQuotationId}/send`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert('Failed to send RFQ');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error sending RFQ');
        });
    });

    // Status change functionality
    const statusModal = document.getElementById('statusModal');
    const statusForm = document.getElementById('statusForm');
    const statusNote = document.getElementById('statusNote');
    const confirmStatus = document.getElementById('confirmStatus');
    let currentAction = '';

    function showStatusModal(quotationId, action) {
        currentQuotationId = quotationId;
        currentAction = action;
        statusNote.value = '';
        $(statusModal).modal('show');
    }

    document.querySelectorAll('.approve-quotation').forEach(btn => {
        btn.addEventListener('click', function() {
            showStatusModal(this.dataset.id, 'approve');
        });
    });

    document.querySelectorAll('.reject-quotation').forEach(btn => {
        btn.addEventListener('click', function() {
            showStatusModal(this.dataset.id, 'reject');
        });
    });

    confirmStatus.addEventListener('click', function() {
        if (!statusForm.checkValidity()) {
            statusForm.reportValidity();
            return;
        }

        fetch(`/api/quotations/${currentQuotationId}/${currentAction}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                note: statusNote.value
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert('Failed to update status');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error updating status');
        });
    });

    // Delete quotation functionality
    const deleteModal = document.getElementById('deleteModal');
    const deleteForm = document.getElementById('deleteForm');
    const deleteBtns = document.querySelectorAll('.delete-quotation');

    deleteBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const quotationId = this.dataset.id;
            deleteForm.action = `/quotations/${quotationId}`;
            $(deleteModal).modal('show');
        });
    });
});
</script>
@endpush
@endsection 