@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Requests for Inquiry</h4>
                    <a href="{{ route('inquiries.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create New Inquiry
                    </a>
                </div>
                <div class="card-body">
                    <!-- Search and Filters -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="search">Search</label>
                                <input type="text" class="form-control" id="search" 
                                    placeholder="Search by subject, project, or department...">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="priority">Priority</label>
                                <select class="form-control" id="priority">
                                    <option value="">All Priorities</option>
                                    <option value="low">Low</option>
                                    <option value="medium">Medium</option>
                                    <option value="high">High</option>
                                    <option value="urgent">Urgent</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select class="form-control" id="status">
                                    <option value="">All Statuses</option>
                                    <option value="pending">Pending</option>
                                    <option value="approved">Approved</option>
                                    <option value="rejected">Rejected</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="completed">Completed</option>
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

                    <!-- Inquiries Table -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Project</th>
                                    <th>Subject</th>
                                    <th>Department</th>
                                    <th>Priority</th>
                                    <th>Required Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="inquiriesTableBody">
                                @foreach($inquiries as $inquiry)
                                <tr>
                                    <td>{{ $inquiry->id }}</td>
                                    <td>{{ $inquiry->project->name }}</td>
                                    <td>
                                        <div>
                                            <strong>{{ $inquiry->subject }}</strong>
                                            @if($inquiry->description)
                                                <br>
                                                <small class="text-muted">{{ Str::limit($inquiry->description, 50) }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>{{ $inquiry->department }}</td>
                                    <td>
                                        <span class="badge badge-{{ $inquiry->priority_color }}">
                                            {{ ucfirst($inquiry->priority) }}
                                        </span>
                                    </td>
                                    <td>{{ $inquiry->required_date->format('M d, Y') }}</td>
                                    <td>
                                        <span class="badge badge-{{ $inquiry->status_color }}">
                                            {{ ucfirst($inquiry->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('inquiries.show', $inquiry->id) }}" 
                                                class="btn btn-sm btn-info" 
                                                title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($inquiry->status == 'pending')
                                            <a href="{{ route('inquiries.edit', $inquiry->id) }}" 
                                                class="btn btn-sm btn-primary" 
                                                title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" 
                                                class="btn btn-sm btn-success approve-inquiry" 
                                                data-id="{{ $inquiry->id }}"
                                                title="Approve">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button type="button" 
                                                class="btn btn-sm btn-warning reject-inquiry" 
                                                data-id="{{ $inquiry->id }}"
                                                title="Reject">
                                                <i class="fas fa-times"></i>
                                            </button>
                                            @endif
                                            <button type="button" 
                                                class="btn btn-sm btn-danger delete-inquiry" 
                                                data-id="{{ $inquiry->id }}"
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
                            Showing {{ $inquiries->firstItem() }} to {{ $inquiries->lastItem() }} of {{ $inquiries->total() }} inquiries
                        </div>
                        {{ $inquiries->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Status Change Modal -->
<div class="modal fade" id="statusModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Change Inquiry Status</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
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
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
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
                <h5 class="modal-title">Delete Inquiry</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this inquiry? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline-block;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
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
    .badge-low {
        background-color: #28a745;
        color: white;
    }
    .badge-medium {
        background-color: #ffc107;
        color: black;
    }
    .badge-high {
        background-color: #fd7e14;
        color: white;
    }
    .badge-urgent {
        background-color: #dc3545;
        color: white;
    }
    .badge-pending {
        background-color: #6c757d;
        color: white;
    }
    .badge-approved {
        background-color: #28a745;
        color: white;
    }
    .badge-rejected {
        background-color: #dc3545;
        color: white;
    }
    .badge-in_progress {
        background-color: #17a2b8;
        color: white;
    }
    .badge-completed {
        background-color: #007bff;
        color: white;
    }
    .btn-group .btn {
        margin-right: 2px;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Search and filter functionality
    let searchTimeout;
    const search = document.getElementById('search');
    const priority = document.getElementById('priority');
    const status = document.getElementById('status');
    const perPage = document.getElementById('perPage');

    function updateInquiries() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            const params = new URLSearchParams({
                search: search.value,
                priority: priority.value,
                status: status.value,
                per_page: perPage.value
            });

            window.location.href = `${window.location.pathname}?${params.toString()}`;
        }, 500);
    }

    search.addEventListener('input', updateInquiries);
    priority.addEventListener('change', updateInquiries);
    status.addEventListener('change', updateInquiries);
    perPage.addEventListener('change', updateInquiries);

    // Set initial values from URL params
    const urlParams = new URLSearchParams(window.location.search);
    search.value = urlParams.get('search') || '';
    priority.value = urlParams.get('priority') || '';
    status.value = urlParams.get('status') || '';
    perPage.value = urlParams.get('per_page') || '10';

    // Status change functionality
    const statusModal = document.getElementById('statusModal');
    const statusForm = document.getElementById('statusForm');
    const statusNote = document.getElementById('statusNote');
    const confirmStatus = document.getElementById('confirmStatus');
    let currentAction = '';
    let currentInquiryId = '';

    function showStatusModal(inquiryId, action) {
        currentInquiryId = inquiryId;
        currentAction = action;
        statusNote.value = '';
        $(statusModal).modal('show');
    }

    document.querySelectorAll('.approve-inquiry').forEach(btn => {
        btn.addEventListener('click', function() {
            showStatusModal(this.dataset.id, 'approve');
        });
    });

    document.querySelectorAll('.reject-inquiry').forEach(btn => {
        btn.addEventListener('click', function() {
            showStatusModal(this.dataset.id, 'reject');
        });
    });

    confirmStatus.addEventListener('click', function() {
        if (!statusForm.checkValidity()) {
            statusForm.reportValidity();
            return;
        }

        fetch(`/api/inquiries/${currentInquiryId}/${currentAction}`, {
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

    // Delete inquiry functionality
    const deleteModal = document.getElementById('deleteModal');
    const deleteForm = document.getElementById('deleteForm');
    const deleteBtns = document.querySelectorAll('.delete-inquiry');

    deleteBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const inquiryId = this.dataset.id;
            deleteForm.action = `/inquiries/${inquiryId}`;
            $(deleteModal).modal('show');
        });
    });
});
</script>
@endpush
@endsection 