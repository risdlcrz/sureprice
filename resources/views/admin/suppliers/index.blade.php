@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Suppliers</h1>
        <a href="{{ route('suppliers.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> New Supplier
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
                            <th>Name</th>
                            <th>Company</th>
                            <th>Contact</th>
                            <th>Location</th>
                            <th>Tax ID</th>
                            <th>Status</th>
                            <th>Materials</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($suppliers as $supplier)
                            <tr>
                                <td>{{ $supplier->name }}</td>
                                <td>{{ $supplier->company_name }}</td>
                                <td>
                                    <div>{{ $supplier->email }}</div>
                                    <small class="text-muted">{{ $supplier->phone }}</small>
                                </td>
                                <td>
                                    <div>{{ $supplier->city }}, {{ $supplier->state }}</div>
                                    <small class="text-muted">{{ $supplier->postal }}</small>
                                </td>
                                <td>{{ $supplier->tax_id }}</td>
                                <td>
                                    <span class="badge bg-{{ $supplier->status === 'active' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($supplier->status) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-info">
                                        {{ $supplier->materials->count() }} material(s)
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('suppliers.edit', $supplier->id) }}" 
                                           class="btn btn-sm btn-outline-secondary"
                                           title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('suppliers.destroy', $supplier->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                    onclick="return confirm('Are you sure you want to delete this supplier?')"
                                                    title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No suppliers found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end mt-3">
                {{ $suppliers->links() }}
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