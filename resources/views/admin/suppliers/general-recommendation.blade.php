@extends('layouts.app')

@section('content')
<div class="container">
<h1 class="h3 mb-4 text-gray-800 text-center" style="font-weight:700;color:#198754;letter-spacing:0.01em;">Supplier Recommendations</h1>
    <form id="general-recommendation-form" class="row g-3 mb-4">
        <div class="col-md-4">
            <label for="material_id" class="form-label">Select Material</label>
            <select class="form-select" id="material_id" name="material_id">
                @foreach($materials as $material)
                    <option value="{{ $material->id }}" @if($selectedMaterialId == $material->id) selected @endif>{{ $material->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label for="on_time_delivery_rate" class="form-label">On-Time Delivery Rate (%)</label>
            <input type="number" class="form-control" id="on_time_delivery_rate" name="on_time_delivery_rate" value="90" min="0" max="100">
        </div>
        <div class="col-md-2">
            <label for="average_defect_rate" class="form-label">Avg. Defect Rate (%)</label>
            <input type="number" class="form-control" id="average_defect_rate" name="average_defect_rate" value="2" min="0" max="100" step="0.01">
        </div>
        <div class="col-md-2">
            <label for="average_cost_variance" class="form-label">Avg. Cost Variance</label>
            <input type="number" class="form-control" id="average_cost_variance" name="average_cost_variance" value="0" step="0.01">
        </div>
        <div class="col-md-2">
            <label for="budget" class="form-label">Budget</label>
            <input type="number" class="form-control" id="budget" name="budget" value="{{ $budget }}" min="0" step="0.01">
        </div>
        <div class="col-md-12 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">Update Recommendations</button>
        </div>
    </form>
    <div id="general-recommendation-tables">
        @include('admin.suppliers.partials.recommendation-tables', ['recommended' => $recommended, 'optimal' => $optimal])
    </div>
</div>
@endsection

@push('styles')
    @vite(['resources/css/admin/suppliers/general-recommendation.css'])
@endpush

@push('scripts')
    @vite(['resources/js/admin/suppliers/general-recommendation.js'])
@endpush 