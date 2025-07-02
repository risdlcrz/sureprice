@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2>Expiring Items</h2>
    @if($inventories->isEmpty())
        <div class="alert alert-info">No expiring items found.</div>
    @else
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Material</th>
                        <th>Category</th>
                        <th>Quantity</th>
                        <th>Unit</th>
                        <th>Location</th>
                        <th>Expiry Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($inventories as $inventory)
                        <tr>
                            <td>{{ $inventory->material->name }}</td>
                            <td>{{ $inventory->material->category->name ?? '-' }}</td>
                            <td>{{ $inventory->quantity }}</td>
                            <td>{{ $inventory->unit }}</td>
                            <td>{{ $inventory->location ?? 'N/A' }}</td>
                            <td>{{ $inventory->expiry_date ? $inventory->expiry_date->format('M d, Y') : 'N/A' }}</td>
                            <td>
                                <span class="badge bg-danger">Expiring Soon</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $inventories->links() }}
        </div>
    @endif
    <a href="{{ route('procurement.inventory.index') }}" class="btn btn-secondary mt-3">Back to Inventory</a>
</div>
@endsection 