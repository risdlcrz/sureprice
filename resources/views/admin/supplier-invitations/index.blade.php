@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Supplier Invitations</h4>
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