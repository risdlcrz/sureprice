@extends('layouts.app')

@section('content')
<h1 class="h3 mb-4 text-gray-800 text-center" style="font-weight:700;color:#198754;letter-spacing:0.01em;">Supplier Invitations</h1>
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <a href="{{ route('supplier-invitations.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> New Invitation
                    </a>
                </div>
                <div class="card-body">
                    @if($invitations->isEmpty())
                        <div class="text-center py-4">
                            <h4>No supplier invitations found</h4>
                            <p>Start by inviting suppliers to your projects.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Code</th>
                                        <th>Project</th>
                                        <th>Company</th>
                                        <th>Contact</th>
                                        <th>Materials</th>
                                        <th>Due Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($invitations as $invitation)
                                        <tr>
                                            <td>{{ $invitation->invitation_code }}</td>
                                            <td>{{ $invitation->project->name }}</td>
                                            <td>{{ $invitation->company_name }}</td>
                                            <td>
                                                {{ $invitation->contact_name }}<br>
                                                <small class="text-muted">
                                                    {{ $invitation->email }}<br>
                                                    {{ $invitation->phone }}
                                                </small>
                                            </td>
                                            <td>
                                                @foreach($invitation->materials as $material)
                                                    <span class="badge bg-info">{{ $material->name }}</span>
                                                @endforeach
                                            </td>
                                            <td>{{ $invitation->due_date->format('M d, Y') }}</td>
                                            <td>
                                                <span class="badge bg-{{ $invitation->status_color }}">
                                                    {{ ucfirst($invitation->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('supplier-invitations.show', $invitation) }}" 
                                                       class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @if($invitation->status === 'pending')
                                                        <a href="{{ route('supplier-invitations.edit', $invitation) }}" 
                                                           class="btn btn-sm btn-warning">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <form action="{{ route('supplier-invitations.destroy', $invitation) }}" 
                                                              method="POST" 
                                                              class="d-inline"
                                                              onsubmit="return confirm('Are you sure you want to delete this invitation?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                        <form action="{{ route('supplier-invitations.resend', $invitation) }}" 
                                                              method="POST" 
                                                              class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-success">
                                                                <i class="fas fa-paper-plane"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            {{ $invitations->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

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
.table-responsive {
    border-radius: 1.1rem;
    overflow-x: auto;
    box-shadow: 0 4px 24px rgba(44,62,80,0.08), 0 1.5px 6px rgba(44,62,80,0.04);
    background: #fff;
    max-width: 100%;
}
.table {
    margin-bottom: 0;
    background: #fff;
    border-radius: 1.1rem;
    overflow: hidden;
    font-size: 0.97rem;
}
.table th, .table td {
    vertical-align: middle;
    padding: 0.7rem 0.5rem;
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
.badge.bg-info {
    background-color: #38b6ff !important;
    color: #fff;
}
.badge.bg-success {
    background-color: #28a745 !important;
    color: #fff;
}
.badge.bg-danger {
    background-color: #dc3545 !important;
    color: #fff;
}
.badge.bg-warning {
    background-color: #ffc107 !important;
    color: #212529;
}
.btn-group .btn {
    margin-right: 4px;
    border-radius: 1.5rem !important;
    font-size: 0.95rem;
    min-width: 28px;
    padding: 0.25rem 0.45rem;
    height: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.btn-group .btn:last-child {
    margin-right: 0;
}
.text-center.py-4 {
    background: #f8fafc;
    border-radius: 1.1rem;
    box-shadow: 0 2px 12px rgba(44,62,80,0.06);
    margin: 2rem 0;
    padding: 2.5rem 1rem;
}
@media (max-width: 991.98px) {
    .card-header {
        padding: 1rem 0.5rem 0.5rem 0.5rem;
    }
    .card {
        padding: 0.5rem;
    }
    .table th, .table td {
        padding: 0.4rem 0.2rem;
        font-size: 0.93rem;
    }
}
</style>
@endpush 