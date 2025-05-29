@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-2 bg-success text-white min-vh-100">
            <div class="p-3">
                <div class="text-center mb-4">
                    <img src="{{ asset('images/logo.png') }}" alt="GEOCON" class="img-fluid mb-2" style="max-width: 120px;">
                    <h4>GEOCON</h4>
                </div>

                <div class="text-center mb-4">
                    <p class="mb-1">Logged in as:</p>
                    <div class="d-flex align-items-center justify-content-center">
                        <div class="rounded-circle bg-white text-success d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="bi bi-person-fill"></i>
                        </div>
                        <span class="ms-2">{{ Auth::user()->name }}</span>
                    </div>
                </div>

                <div class="nav flex-column">
                    <a href="{{ route('admin.project') }}" class="nav-link text-white">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                    <a href="{{ route('contracts.index') }}" class="nav-link text-white active">
                        <i class="bi bi-file-text"></i> Project Approval
                    </a>
                    <a href="#" class="nav-link text-white">
                        <i class="bi bi-cart"></i> Procurement Request
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-10">
            <div class="container mt-4">
                <h2 class="mb-4">Project Approval Management</h2>

                <div class="mb-4">
                    <div class="btn-group">
                        <a href="{{ route('contracts.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-lg"></i> Create New Contract
                        </a>
                        <a href="{{ route('contracts.index') }}" class="btn btn-secondary">
                            <i class="bi bi-list"></i> View All Contracts
                        </a>
                        <button class="btn btn-info">
                            <i class="bi bi-download"></i> Download All PDFs
                        </button>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="btn-group">
                        <button class="btn btn-outline-secondary active">All</button>
                        <button class="btn btn-outline-secondary">Pending</button>
                        <button class="btn btn-outline-secondary">Approved</button>
                        <button class="btn btn-outline-secondary">Rejected</button>
                        <button class="btn btn-outline-secondary">Draft</button>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-dark text-white">
                                    <tr>
                                        <th>Contract ID</th>
                                        <th>Client</th>
                                        <th>Period</th>
                                        <th>Amount</th>
                                        <th>Actions</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($contracts as $contract)
                                    <tr>
                                        <td>{{ $contract->contract_id }}</td>
                                        <td>{{ $contract->client->name }}</td>
                                        <td>{{ $contract->start_date->format('m/d/Y') }} - {{ $contract->end_date->format('m/d/Y') }}</td>
                                        <td>${{ number_format($contract->total_amount, 2) }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('contracts.show', $contract->id) }}" 
                                                   class="btn btn-sm btn-info"
                                                   title="View">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('contracts.edit', $contract->id) }}" 
                                                   class="btn btn-sm btn-warning"
                                                   title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <a href="#" class="btn btn-sm btn-success" title="Download PDF">
                                                    <i class="bi bi-download"></i>
                                                </a>
                                                <form action="{{ route('contracts.destroy', $contract->id) }}" 
                                                      method="POST" 
                                                      class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-sm btn-danger"
                                                            onclick="return confirm('Are you sure you want to delete this contract?')"
                                                            title="Delete">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ ucfirst($contract->status) }}</span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No contracts found.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-3">
                    {{ $contracts->links() }}
                </div>
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
    .nav-link.active {
        background-color: rgba(255, 255, 255, 0.1);
    }
    .nav-link {
        padding: 0.5rem 1rem;
        margin-bottom: 0.25rem;
        border-radius: 0.25rem;
    }
    .nav-link:hover {
        background-color: rgba(255, 255, 255, 0.1);
    }
</style>
@endpush
@endsection 