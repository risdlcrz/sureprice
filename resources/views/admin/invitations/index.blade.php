@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Supplier Invitations</h4>
                    <a href="{{ route('invitations.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Send New Invitation
                    </a>
                </div>
                <div class="card-body">
                    <!-- Search and Filters -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="search">Search</label>
                                <input type="text" class="form-control" id="search" 
                                    placeholder="Search by company, contact person, or email...">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select class="form-control" id="status">
                                    <option value="">All Statuses</option>
                                    <option value="pending">Pending</option>
                                    <option value="accepted">Accepted</option>
                                    <option value="rejected">Rejected</option>
                                    <option value="expired">Expired</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="business_type">Business Type</label>
                                <select class="form-control" id="business_type">
                                    <option value="">All Types</option>
                                    <option value="corporation">Corporation</option>
                                    <option value="partnership">Partnership</option>
                                    <option value="sole_proprietorship">Sole Proprietorship</option>
                                    <option value="other">Other</option>
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

                    <!-- Invitations Table -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Company</th>
                                    <th>Contact Person</th>
                                    <th>Contact Info</th>
                                    <th>Categories</th>
                                    <th>Status</th>
                                    <th>Sent Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="invitationsTableBody">
                                @foreach($invitations as $invitation)
                                <tr>
                                    <td>
                                        <div>
                                            <strong>{{ $invitation->company_name }}</strong><br>
                                            <small class="text-muted">{{ ucfirst($invitation->business_type) }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            {{ $invitation->contact_person }}<br>
                                            <small class="text-muted">{{ $invitation->position }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <i class="fas fa-envelope"></i> {{ $invitation->email }}<br>
                                            <i class="fas fa-phone"></i> {{ $invitation->phone }}
                                        </div>
                                    </td>
                                    <td>
                                        @foreach($invitation->categories as $category)
                                            <span class="badge badge-info">{{ $category->name }}</span>
                                        @endforeach
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $invitation->status_color }}">
                                            {{ ucfirst($invitation->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        {{ $invitation->created_at->format('M d, Y') }}<br>
                                        <small class="text-muted">{{ $invitation->created_at->diffForHumans() }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('invitations.show', $invitation->id) }}" 
                                                class="btn btn-sm btn-info" 
                                                title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($invitation->status == 'pending')
                                            <a href="{{ route('invitations.edit', $invitation->id) }}" 
                                                class="btn btn-sm btn-primary" 
                                                title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" 
                                                class="btn btn-sm btn-warning resend-invitation" 
                                                data-id="{{ $invitation->id }}"
                                                title="Resend">
                                                <i class="fas fa-paper-plane"></i>
                                            </button>
                                            @endif
                                            <button type="button" 
                                                class="btn btn-sm btn-danger delete-invitation" 
                                                data-id="{{ $invitation->id }}"
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
                            Showing {{ $invitations->firstItem() }} to {{ $invitations->lastItem() }} of {{ $invitations->total() }} invitations
                        </div>
                        {{ $invitations->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Resend Confirmation Modal -->
<div class="modal fade" id="resendModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Resend Invitation</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to resend this invitation?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmResend">Resend</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Invitation</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this invitation? This action cannot be undone.</p>
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
        margin-right: 0.25rem;
        margin-bottom: 0.25rem;
    }
    .badge-pending {
        background-color: #6c757d;
        color: white;
    }
    .badge-accepted {
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
    .fas {
        width: 16px;
        text-align: center;
        margin-right: 5px;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Search and filter functionality
    let searchTimeout;
    const search = document.getElementById('search');
    const status = document.getElementById('status');
    const businessType = document.getElementById('business_type');
    const perPage = document.getElementById('perPage');

    function updateInvitations() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            const params = new URLSearchParams({
                search: search.value,
                status: status.value,
                business_type: businessType.value,
                per_page: perPage.value
            });

            window.location.href = `${window.location.pathname}?${params.toString()}`;
        }, 500);
    }

    search.addEventListener('input', updateInvitations);
    status.addEventListener('change', updateInvitations);
    businessType.addEventListener('change', updateInvitations);
    perPage.addEventListener('change', updateInvitations);

    // Set initial values from URL params
    const urlParams = new URLSearchParams(window.location.search);
    search.value = urlParams.get('search') || '';
    status.value = urlParams.get('status') || '';
    businessType.value = urlParams.get('business_type') || '';
    perPage.value = urlParams.get('per_page') || '10';

    // Resend invitation functionality
    const resendModal = document.getElementById('resendModal');
    const confirmResend = document.getElementById('confirmResend');
    let currentInvitationId = '';

    document.querySelectorAll('.resend-invitation').forEach(btn => {
        btn.addEventListener('click', function() {
            currentInvitationId = this.dataset.id;
            $(resendModal).modal('show');
        });
    });

    confirmResend.addEventListener('click', function() {
        fetch(`/api/invitations/${currentInvitationId}/resend`, {
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
                alert('Failed to resend invitation');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error resending invitation');
        });
    });

    // Delete invitation functionality
    const deleteModal = document.getElementById('deleteModal');
    const deleteForm = document.getElementById('deleteForm');
    const deleteBtns = document.querySelectorAll('.delete-invitation');

    deleteBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const invitationId = this.dataset.id;
            deleteForm.action = `/invitations/${invitationId}`;
            $(deleteModal).modal('show');
        });
    });
});
</script>
@endpush
@endsection 