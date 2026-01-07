@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Supplier Recommendation (Analytics)</h2>
    <form id="general-recommendation-form" class="row g-3 mb-4">
        <div class="col-md-4">
            <label for="material_id" class="form-label">Select Material</label>
            <select class="form-select" id="material_id" name="material_id">
                @foreach($materials as $material)
                    <option value="{{ $material->id }}" @if($selectedMaterialId == $material->id) selected @endif>{{ $material->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-12 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">Update Recommendations</button>
        </div>
    </form>
    <div id="general-recommendation-tables">
        @include('procurement.suppliers.partials.recommendation-tables', ['recommended' => $recommended])
    </div>
</div>
@endsection

@push('styles')
    @vite(['resources/css/procurement/suppliers/general-recommendation.css'])
@endpush

@push('scripts')
    @vite(['resources/js/procurement/suppliers/general-recommendation.js'])
@endpush 