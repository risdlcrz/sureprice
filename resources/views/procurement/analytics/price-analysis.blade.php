@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="h3 mb-4">Price Trend Analysis</h1>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Filter Options</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('procurement.analytics.price-analysis') }}" method="GET">
                <div class="mb-3">
                    <label for="material_ids" class="form-label">Select Materials to Display</label>
                    <select name="material_ids[]" id="material_ids" class="form-control" multiple="multiple">
                        @foreach ($materials as $material)
                            <option value="{{ $material->id }}" {{ in_array($material->id, $selectedMaterialIds ?? []) ? 'selected' : '' }}>
                                {{ $material->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> Apply Filter
                </button>
                <a href="{{ route('procurement.analytics.price-analysis') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Clear Filter
                </a>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Material Price Trends Over Time</h5>
        </div>
        <div class="card-body">
            @if(count($priceData) > 0)
                <canvas id="priceTrendChart"></canvas>
            @else
                <div class="text-center p-5">
                    <p class="text-muted">
                        @if(empty($selectedMaterialIds))
                            Please select one or more materials from the filter above to display the price trend chart.
                        @else
                            No historical price data found for the selected material(s).
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
{{-- Extracted to resources/css/price-analysis.css --}}
@vite('resources/css/price-analysis.css')
@endpush

@push('scripts')
{{-- Extracted to resources/js/price-analysis.js --}}
@vite('resources/js/price-analysis.js')
@endpush