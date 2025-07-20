@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="h3 mb-4 text-center" style="font-weight:700;color:#dc3545;letter-spacing:0.01em;">Expiring Items</h1>
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
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
        </div>
    </div>
    <div class="d-grid gap-2">
        <a href="{{ route('procurement.inventory.index') }}" class="btn btn-outline-secondary rounded-pill">Back to Inventory</a>
    </div>
</div>
@push('styles')
    @vite(['resources/css/procurement/inventory-expiring.css'])
@endpush
@endsection 