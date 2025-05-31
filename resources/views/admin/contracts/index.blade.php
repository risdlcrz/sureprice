@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Contracts</h1>
        <a href="{{ route('contracts.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> New Contract
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

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
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($contracts as $contract)
                            <tr>
                                <td>{{ $contract->contract_id }}</td>
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
                                <td>${{ number_format($contract->total_amount, 2) }}</td>
                                <td>
                                    <span class="badge bg-{{ $contract->status === 'draft' ? 'warning' : ($contract->status === 'approved' ? 'success' : 'secondary') }}">
                                        {{ ucfirst($contract->status) }}
                                    </span>
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
                                        <form action="{{ route('contracts.destroy', $contract->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                    onclick="return confirm('Are you sure you want to delete this contract?')"
                                                    title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
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
</style>
@endpush
@endsection 