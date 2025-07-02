@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Supplier Recommendations for Project: {{ $project->name }}</h2>

    <form id="recommendation-form" class="row g-3 mb-4">
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
            <input type="number" class="form-control" id="on_time_delivery_rate" name="on_time_delivery_rate" value="{{ $projectFeatures['on_time_delivery_rate'] }}" min="0" max="100">
        </div>
        <div class="col-md-2">
            <label for="average_defect_rate" class="form-label">Avg. Defect Rate (%)</label>
            <input type="number" class="form-control" id="average_defect_rate" name="average_defect_rate" value="{{ $projectFeatures['average_defect_rate'] }}" min="0" max="100" step="0.01">
        </div>
        <div class="col-md-2">
            <label for="average_cost_variance" class="form-label">Avg. Cost Variance</label>
            <input type="number" class="form-control" id="average_cost_variance" name="average_cost_variance" value="{{ $projectFeatures['average_cost_variance'] }}" step="0.01">
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">Update Recommendations</button>
        </div>
    </form>

    <div id="recommendation-tables">
        @include('admin.suppliers.partials.recommendation-tables', ['recommended' => $recommended, 'optimal' => $optimal])
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('recommendation-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const form = e.target;
        const data = new FormData(form);
        const url = window.location.href;
        fetch(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: data
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('recommendation-tables').innerHTML = data.html;
        });
    });
</script>
@endpush 