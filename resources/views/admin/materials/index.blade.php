@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Materials</h1>
        <a href="{{ route('materials.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> New Material
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
                            <th>Description</th>
                            <th>Unit</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Suppliers</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($materials as $material)
                            <tr>
                                <td>{{ $material->name }}</td>
                                <td>{{ Str::limit($material->description, 50) }}</td>
                                <td>{{ $material->unit }}</td>
                                <td>${{ number_format($material->price, 2) }}</td>
                                <td>{{ number_format($material->stock, 2) }} {{ $material->unit }}</td>
                                <td>
                                    <span class="badge bg-info">
                                        {{ $material->suppliers->count() }} supplier(s)
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('materials.edit', $material->id) }}" 
                                           class="btn btn-sm btn-outline-secondary"
                                           title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('materials.destroy', $material->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                    onclick="return confirm('Are you sure you want to delete this material?')"
                                                    title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No materials found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end mt-3">
                {{ $materials->links() }}
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