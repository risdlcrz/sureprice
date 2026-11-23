@extends('layouts.app')

@push('styles')
@vite('resources/css/pages/inventory-index.css')
@endpush

@section('content')
@php
    // "Warehouse A" -> "WH A" for the column header; the full name stays in the tooltip and the mobile label.
    $shortWarehouse = fn ($name) => preg_replace('/^warehouse\s+/i', 'WH ', $name);
    $qty = fn ($v) => rtrim(rtrim(number_format((float) $v, 2), '0'), '.');
    $colCount = 6 + $warehouses->count();
@endphp
<div class="container-fluid inventory-page">
    <x-page-header title="Inventory Management" icon="boxes-stacked" />

    {{-- KPI tiles --}}
    <div class="inv-kpis">
        <div class="inv-kpi inv-kpi--total">
            <div>
                <div class="inv-kpi-label">Total Items</div>
                <div class="inv-kpi-value">{{ number_format($totalItems) }}</div>
            </div>
            <span class="inv-kpi-icon"><i class="fas fa-boxes"></i></span>
        </div>
        <div class="inv-kpi inv-kpi--low">
            <div>
                <div class="inv-kpi-label">Low Stock Items</div>
                <div class="inv-kpi-value">{{ number_format($lowStockItems) }}</div>
            </div>
            <span class="inv-kpi-icon"><i class="fas fa-exclamation-triangle"></i></span>
        </div>
        <div class="inv-kpi inv-kpi--expiring">
            <div>
                <div class="inv-kpi-label">Expiring Items</div>
                <div class="inv-kpi-value">{{ number_format($expiringItems) }}</div>
            </div>
            <span class="inv-kpi-icon"><i class="fas fa-clock"></i></span>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            {{-- Toolbar: search on the left, actions on the right --}}
            <div class="inv-toolbar">
                <form class="inv-search" role="search" id="inventorySearch">
                    <label for="searchInput" class="visually-hidden">Search inventory</label>
                    <input type="search" id="searchInput" class="form-control" placeholder="Search inventory..." autocomplete="off">
                    <button type="submit" class="btn btn-outline-secondary" title="Search"><i class="fas fa-search"></i></button>
                </form>
                <div class="inv-toolbar-actions">
                    <a href="{{ route('inventory.create') }}" class="btn btn-success"><i class="fas fa-plus me-1"></i> Add New Item</a>
                    <a href="{{ route('inventory.low-stock') }}" class="btn btn-outline-warning"><i class="fas fa-exclamation-triangle me-1"></i> Low Stock</a>
                    <a href="{{ route('inventory.expiring') }}" class="btn btn-outline-danger"><i class="fas fa-clock me-1"></i> Expiring Soon</a>
                </div>
            </div>

            <div class="table-responsive inv-table-wrap">
                <table class="table inv-table mb-0" id="inventoryTable">
                    <thead>
                        <tr>
                            <th>Material</th>
                            <th>Category</th>
                            @foreach($warehouses as $warehouse)
                                <th class="text-end" title="{{ $warehouse->name }} stock">{{ $shortWarehouse($warehouse->name) }}</th>
                            @endforeach
                            <th class="text-end">Total</th>
                            <th>Unit</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($materials as $material)
                            @php
                                $threshold = $material->minimum_stock ?? 0;
                                [$statusClass, $statusLabel] = match (true) {
                                    $material->total_stock <= 0 => ['out', 'Out of Stock'],
                                    $material->total_stock < $threshold => ['low', 'Low Stock'],
                                    default => ['active', 'Active'],
                                };
                            @endphp
                            <tr>
                                <td data-label="Material" class="inv-material">{{ $material->name }}</td>
                                <td data-label="Category" class="text-muted">{{ $material->category->name ?? '—' }}</td>
                                @foreach($warehouses as $warehouse)
                                    <td data-label="{{ $warehouse->name }}" class="inv-num">{{ $qty($material->warehouse_stocks[$warehouse->id] ?? 0) }}</td>
                                @endforeach
                                <td data-label="Total" class="inv-num fw-semibold">{{ $qty($material->total_stock) }}</td>
                                <td data-label="Unit">{{ $material->unit }}</td>
                                <td data-label="Status"><span class="inv-pill inv-pill--{{ $statusClass }}">{{ $statusLabel }}</span></td>
                                <td data-label="Actions">
                                    <div class="inv-actions">
                                        <a href="{{ route('purchase-requests.create', ['material_id' => $material->id]) }}" class="inv-icon-btn inv-icon-btn--restock" title="Add / restock" aria-label="Restock {{ $material->name }}">
                                            <i class="fas fa-plus"></i>
                                        </a>
                                        @if($material->primary_inventory)
                                            <a href="{{ route('inventory.edit', $material->primary_inventory) }}" class="inv-icon-btn inv-icon-btn--edit" title="Edit" aria-label="Edit {{ $material->name }}">
                                                <i class="fas fa-pen"></i>
                                            </a>
                                            <button type="button" class="inv-icon-btn inv-icon-btn--delete" title="Delete" aria-label="Delete {{ $material->name }}"
                                                    data-bs-toggle="modal" data-bs-target="#deleteInventory{{ $material->primary_inventory->id }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @else
                                            {{-- No inventory record yet: offer to create one --}}
                                            <a href="{{ route('inventory.create') }}" class="inv-icon-btn inv-icon-btn--edit" title="Create inventory record" aria-label="Create inventory record for {{ $material->name }}">
                                                <i class="fas fa-pen"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <x-empty-row :cols="$colCount">No inventory items yet.</x-empty-row>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($materials->hasPages())
                <div class="mt-3">{{ $materials->links() }}</div>
            @endif
        </div>
    </div>

    @foreach($materials as $material)
        @if($material->primary_inventory)
            <x-confirm-modal :id="'deleteInventory' . $material->primary_inventory->id" title="Delete inventory item"
                             :action="route('inventory.destroy', $material->primary_inventory)" method="DELETE" confirm="Delete" tone="danger">
                Delete <strong>{{ $material->name }}</strong> from inventory? This cannot be undone.
            </x-confirm-modal>
        @endif
    @endforeach
</div>

@push('scripts')
@vite('resources/js/procurement/inventory-dashboard.js')
@endpush
@endsection
