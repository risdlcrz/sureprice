@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">My Materials</h1>
        <a href="{{ route('supplier.materials.create') }}" class="btn btn-primary">Add New Material</a>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('supplier.materials.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search" placeholder="Material Name or Code" value="{{ request('search') }}">
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                    <a href="{{ route('supplier.materials.index') }}" class="btn btn-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Materials Table -->
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Code</th>
                        <th>Category</th>
                        <th>Unit</th>
                        <th>Your Price (₱)</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($materials as $material)
                    <tr>
                        <td>{{ $material->name }}</td>
                        <td>{{ $material->code }}</td>
                        <td>{{ $material->category->name ?? '-' }}</td>
                        <td>{{ $material->unit }}</td>
                        <td>₱{{ number_format($material->pivot->price, 2) }}</td>
                        <td>
                            <div class="btn-group">
                                <a href="{{ route('supplier.materials.edit', $material) }}" class="btn btn-sm btn-secondary">Edit</a>
                                <form action="{{ route('supplier.materials.destroy', $material) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to remove this material from your listings?')">Remove</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">No materials found for your company.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($materials->hasPages())
        <div class="card-footer">
            {{ $materials->links() }}
        </div>
        @endif
    </div>
</div>
@endsection 