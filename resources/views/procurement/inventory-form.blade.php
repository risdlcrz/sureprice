{{-- Procurement create/edit inventory item (material + category by name). Optional $inventory. --}}
@extends('layouts.app')

@php $inv = $inventory ?? null; $title = $inv ? 'Edit Inventory Item' : 'Add New Inventory Item'; @endphp
@section('title', $title)

@section('content')
<div class="container-fluid">
    <x-page-header :title="$title" icon="boxes">
        <a href="{{ route('procurement.inventory.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i>Back to Inventory</a>
    </x-page-header>

    <div class="card">
        <div class="card-body">
            <form action="{{ $inv ? route('procurement.inventory.update', $inv) : route('procurement.inventory.store') }}" method="POST">
                @csrf
                @if($inv) @method('PUT') @endif
                <div class="row">
                    <x-field name="material_name" label="Material Name" :value="$inv?->material?->name" placeholder="e.g., Plywood/MDF" col="md-6" required />
                    <x-field name="category_name" label="Category" :value="$inv?->material?->category?->name" placeholder="e.g., Cabinetry" col="md-6" required />
                    <x-field name="quantity" label="Quantity" type="number" step="0.01" :value="$inv?->quantity" placeholder="0" col="md-4" required />
                    <x-field name="unit" label="Unit" :value="$inv?->unit" placeholder="e.g., sqm, pcs, kg" col="md-4" required />
                    <x-field name="location" label="Location" :value="$inv?->location" placeholder="e.g., Warehouse A" col="md-4" />
                    <x-field name="status" label="Status" col="md-6" required>
                        <select id="status" name="status" class="form-select @error('status') is-invalid @enderror">
                            @foreach(['active', 'inactive', 'obsolete'] as $s)
                                <option value="{{ $s }}" @selected(old('status', $inv?->status ?? 'active') === $s)>{{ ucfirst($s) }}</option>
                            @endforeach
                        </select>
                    </x-field>
                    <x-field name="last_restock_date" label="Last Restock Date" type="date" :value="$inv?->last_restock_date?->format('Y-m-d')" col="md-6" />
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>{{ $inv ? 'Update Item' : 'Add Item' }}</button>
            </form>
        </div>
    </div>
</div>
@endsection
