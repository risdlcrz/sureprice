@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">My Materials</h1>
        <a href="{{ route('supplier.materials.create') }}" class="btn btn-primary">Add New Material</a>
    </div>
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Stock</th>
                        <th>Price</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($materials as $material)
                    <tr>
                        <td>{{ $material->name }}</td>
                        <td>{{ $material->category->name ?? '-' }}</td>
                        <td>{{ $material->stock }}</td>
                        <td>₱{{ number_format($material->price, 2) }}</td>
                        <td>
                            <a href="{{ route('supplier.materials.edit', $material) }}" class="btn btn-sm btn-secondary">Edit</a>
                            <form action="{{ route('supplier.materials.destroy', $material) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this material?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">No materials found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection 