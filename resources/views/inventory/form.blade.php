{{-- Create/edit inventory item. Params: $materials, optional $inventory. --}}
@extends('layouts.app')

@php $inv = $inventory ?? null; $title = $inv ? 'Edit Inventory Item' : 'Add New Inventory Item'; @endphp
@section('title', $title)

@section('content')
<div class="container-fluid">
    <x-page-header :title="$title" icon="boxes">
        <a href="{{ route('inventory.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i>Back to Inventory</a>
    </x-page-header>

    <div class="card">
        <div class="card-body">
            <form action="{{ $inv ? route('inventory.update', $inv) : route('inventory.store') }}" method="POST">
                @csrf
                @if($inv) @method('PUT') @endif
                <div class="row">
                    <x-field name="material_id" label="Material" col="md-6" required>
                        @if($inv)
                            <input type="hidden" name="material_id" value="{{ $inv->material_id }}">
                            <input class="form-control" value="{{ $inv->material->name }} ({{ $inv->material->category->name ?? '-' }})" disabled>
                        @else
                            <select name="material_id" id="material_id" class="form-select @error('material_id') is-invalid @enderror" required>
                                <option value="">Select Material</option>
                                @foreach($materials as $material)
                                    <option value="{{ $material->id }}" data-unit="{{ $material->unit }}" @selected(old('material_id') == $material->id)>
                                        {{ $material->name }} ({{ $material->category->name ?? '-' }})
                                    </option>
                                @endforeach
                            </select>
                        @endif
                    </x-field>
                    <x-field name="quantity" label="Quantity" type="number" step="0.01" :value="$inv?->quantity" col="md-6" required />
                    <x-field name="unit" label="Unit" :value="$inv?->unit" col="md-6" required />
                    <x-field name="location" label="Location" :value="$inv?->location" col="md-6" />
                    <x-field name="batch_number" label="Batch Number" :value="$inv?->batch_number" col="md-6" />
                    <x-field name="expiry_date" label="Expiry Date" type="date" :value="$inv?->expiry_date?->format('Y-m-d')" col="md-6" />
                    <x-field name="minimum_threshold" label="Minimum Threshold" type="number" step="0.01" :value="$inv?->minimum_threshold" col="md-6" required />
                    <x-field name="status" label="Status" col="md-6" required>
                        <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                            @foreach(['active', 'inactive', 'discontinued'] as $s)
                                <option value="{{ $s }}" @selected(old('status', $inv?->status) === $s)>{{ ucfirst($s) }}</option>
                            @endforeach
                        </select>
                    </x-field>
                </div>
                <x-field name="notes" label="Notes" type="textarea" :value="$inv?->notes" />
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>{{ $inv ? 'Update' : 'Save' }} Inventory Item</button>
            </form>
        </div>
    </div>
</div>
@endsection

@unless($inv)
@push('scripts')
<script>
document.getElementById('material_id')?.addEventListener('change', function () {
    const unit = this.selectedOptions[0]?.dataset.unit;
    if (unit) document.getElementById('unit').value = unit;
});
</script>
@endpush
@endunless
