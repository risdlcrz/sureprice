@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2>Low Stock Items</h2>
    @if($inventories->isEmpty())
        <div class="alert alert-info">No low stock items found.</div>
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
                        <th>Minimum Threshold</th>
                        <th>Status</th>
                        <th>Last Restock</th>
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
                            <td>{{ $inventory->minimum_threshold }}</td>
                            <td>
                                <span class="badge bg-warning text-dark">Low Stock</span>
                            </td>
                            <td>{{ $inventory->last_restock_date ? $inventory->last_restock_date->format('M d, Y') : 'N/A' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $inventories->links() }}
        </div>
    @endif
    <a href="{{ route('inventory.index') }}" class="btn btn-secondary mt-3">Back to Inventory</a>
</div>
@endsection 