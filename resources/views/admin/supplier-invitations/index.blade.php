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
                                        <th>Contract</th>
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
                                            <td>{{ $invitation->contract->contract_number ?? '—' }}</td>
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
                                                <x-status-badge :status="$invitation->status" />
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