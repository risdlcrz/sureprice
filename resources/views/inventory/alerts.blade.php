{{-- Low-stock / expiring inventory list. Params: $inventories, $mode ('low-stock'|'expiring'), $backRoute. --}}
@extends('layouts.app')

@php
    $expiring = $mode === 'expiring';
    $title = $expiring ? 'Expiring Items' : 'Low Stock Items';
@endphp
@section('title', $title)

@section('content')
<div class="container-fluid">
    <x-page-header :title="$title" :icon="$expiring ? 'hourglass-half' : 'exclamation-triangle'">
        <a href="{{ route($backRoute) }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i>Back to Inventory</a>
    </x-page-header>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Material</th><th>Category</th><th>Quantity</th><th>Unit</th><th>Location</th>
                        <th>{{ $expiring ? 'Expiry Date' : 'Minimum Threshold' }}</th><th>Status</th>
                        @unless($expiring)<th>Last Restock</th>@endunless
                    </tr>
                </thead>
                <tbody>
                    @forelse($inventories as $inventory)
                        <tr>
                            <td>{{ $inventory->material->name ?? '-' }}</td>
                            <td>{{ $inventory->material->category->name ?? '-' }}</td>
                            <td>{{ $inventory->quantity }}</td>
                            <td>{{ $inventory->unit }}</td>
                            <td>{{ $inventory->location ?? 'N/A' }}</td>
                            @if($expiring)
                                <td>{{ $inventory->expiry_date?->format('M d, Y') ?? 'N/A' }}</td>
                                <td><span class="badge bg-danger">Expiring Soon</span></td>
                            @else
                                <td>{{ $inventory->minimum_threshold }}</td>
                                <td><span class="badge bg-warning text-dark">Low Stock</span></td>
                                <td>{{ $inventory->last_restock_date?->format('M d, Y') ?? 'N/A' }}</td>
                            @endif
                        </tr>
                    @empty
                        <x-empty-row :cols="$expiring ? 7 : 8">No {{ strtolower($title) }} found.</x-empty-row>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $inventories->links() }}</div>
</div>
@endsection
