@extends('layouts.app')

@section('content')
<h1 class="h3 mb-4 text-gray-800 text-center" style="font-weight:700;color:#198754;letter-spacing:0.01em;">Requests for Inquiry</h1>
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-white py-3 d-flex justify-content-end align-items-center">
                    <a href="{{ route('inquiries.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create New Inquiry
                    </a>
                </div>
                <div class="card-body">
                    <!-- Search and Filters -->
                    <form method="GET" action="{{ route('inquiries.index') }}" class="row mb-3 align-items-end" id="filterForm">
                        <div class="col-md-3 mb-2 mb-md-0">
                            <input type="text" name="search" class="form-control filter-input" placeholder="Search by subject, project, or department..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2 mb-2 mb-md-0">
                            <select name="priority" class="form-control filter-input">
                                <option value="">All Priorities</option>
                                <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                                <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                                <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-2 mb-md-0">
                            <select name="status" class="form-control filter-input">
                                <option value="">All Statuses</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-2 mb-md-0">
                            <input type="number" name="per_page" class="form-control filter-input" placeholder="Per Page" value="{{ request('per_page', 10) }}" min="1" max="100">
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('inquiries.index') }}" class="btn btn-secondary w-100">
                                <i class="fas fa-times"></i> Clear Filters
                            </a>
                        </div>
                    </form>

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
                                    <td>{{ $inquiry->contract->contract_id ?? 'N/A' }}</td>
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
                                        <span class="badge badge-priority">
                                            {{ ucfirst($inquiry->priority) }}
                                        </span>
                                    </td>
                                    <td>{{ $inquiry->required_date->format('M d, Y') }}</td>
                                    <td>
                                        <span class="badge badge-status">
                                            {{ ucfirst($inquiry->status) }}
                                        </span>
                                    </td>
                                    <td class="text-nowrap">
                                        <a href="{{ route('inquiries.show', $inquiry->id) }}" class="btn btn-info btn-sm" title="View Inquiry" data-bs-toggle="tooltip">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('inquiries.edit', $inquiry->id) }}" class="btn btn-primary btn-sm" title="Edit Inquiry" data-bs-toggle="tooltip">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('inquiries.update', $inquiry->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Approve this inquiry?');">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="approved">
                                            <button type="submit" class="btn btn-success btn-sm" title="Approve" data-bs-toggle="tooltip">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('inquiries.update', $inquiry->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Reject this inquiry?');">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="rejected">
                                            <button type="submit" class="btn btn-warning btn-sm" title="Reject" data-bs-toggle="tooltip">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('inquiries.destroy', $inquiry->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Delete this inquiry? This cannot be undone.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Delete" data-bs-toggle="tooltip">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
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
body {
    background: linear-gradient(135deg, #f8fafc 0%, #e9ecef 100%) !important;
}
.card {
    border-radius: 1.25rem;
    box-shadow: 0 4px 24px rgba(44,62,80,0.08), 0 1.5px 6px rgba(44,62,80,0.04);
    border: none;
    margin-bottom: 2rem;
}
.card-header {
    background: #fff;
    border-radius: 1.25rem 1.25rem 0 0;
    border-bottom: none;
    padding: 1.5rem 2rem 1rem 2rem;
}
.btn-primary {
    border-radius: 2rem;
    font-weight: 600;
    font-size: 1.1rem;
    background: linear-gradient(90deg, #38b6ff 0%, #2563eb 100%);
    border: none;
    box-shadow: 0 2px 8px #38b6ff33;
    transition: background 0.2s, color 0.2s, box-shadow 0.2s;
}
.btn-primary:hover {
    filter: brightness(1.08);
    box-shadow: 0 4px 16px #38b6ff33;
}
.btn-secondary {
    border-radius: 2rem;
    font-weight: 600;
    font-size: 1.1rem;
    background: #6c757d;
    color: #fff;
    border: none;
    transition: background 0.2s, color 0.2s;
}
.btn-secondary:hover {
    background: #495057;
    color: #fff;
}
.filter-input, .form-control, .form-select {
    border-radius: 1.2rem;
    border: 1px solid #d1d5db;
    background: #f8fafc;
    font-size: 1.08rem;
    padding: 0.85rem 1.1rem;
    transition: border-color 0.2s, box-shadow 0.2s;
}
.filter-input:focus, .form-control:focus, .form-select:focus {
    border-color: #38b6ff;
    box-shadow: 0 0 0 2px #38b6ff33;
    background: #fff;
}
.table-responsive {
    border-radius: 1.1rem;
    overflow: hidden;
    box-shadow: 0 4px 24px rgba(44,62,80,0.08), 0 1.5px 6px rgba(44,62,80,0.04);
    background: #fff;
}
.table {
    margin-bottom: 0;
}
.table th, .table td {
    vertical-align: middle;
    padding: 1rem 0.75rem;
    border: none;
    background: #f8fafc;
    text-align: center;
}
.table thead th {
    background: #f1f5f9;
    font-weight: 700;
    color: #198754;
    border-bottom: 2px solid #e3e3e3;
    text-align: center;
}
.table-hover tbody tr:hover {
    background: #e3f2fd44;
}
.badge {
    font-size: 0.95em;
    font-weight: 600;
    letter-spacing: 0.01em;
    box-shadow: 0 1px 4px #ffc10722;
    border-radius: 0.7em;
    padding: 0.5em 1em;
}
.badge-priority, .badge-status {
    background: #6c757d !important;
    color: #fff !important;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.forEach(function (tooltipTriggerEl) {
        new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Auto-submit form when filters change
    const filterInputs = document.querySelectorAll('.filter-input');
    filterInputs.forEach(input => {
        input.addEventListener('change', () => {
            document.getElementById('filterForm').submit();
        });
    });

    // Add debounce to search input
    const searchInput = document.querySelector('input[name="search"]');
    let timeout = null;
    searchInput.addEventListener('input', (e) => {
        clearTimeout(timeout);
        timeout = setTimeout(() => {
            document.getElementById('filterForm').submit();
        }, 500); // Wait 500ms after user stops typing
    });
});
</script>
@endpush
@endsection 