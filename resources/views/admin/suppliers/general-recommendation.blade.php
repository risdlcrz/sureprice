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

@push('scripts')
<script>
    document.getElementById('general-recommendation-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const form = e.target;
        const params = new URLSearchParams(new FormData(form)).toString();
        const url = window.location.pathname + '?' + params;
        fetch(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            }
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('general-recommendation-tables').innerHTML = data.html;
        });
    });
</script>
@endpush

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
<style>
body, .container {
    background: linear-gradient(135deg, #f8fafc 0%, #e9ecef 100%) !important;
    font-family: 'Inter', 'Segoe UI', Arial, sans-serif;
}
h1, .h3 {
    font-family: 'Inter', Arial, sans-serif;
    font-weight: 700;
    letter-spacing: 0.01em;
    color: #198754;
    font-size: 2.2rem;
    margin-bottom: 2rem;
}
.card, .main-box, .recommendation-box {
    border: none;
    border-radius: 1.25rem;
    box-shadow: 0 8px 32px 0 rgba(44,62,80,0.10), 0 1.5px 6px rgba(44,62,80,0.04);
    margin-bottom: 1.5rem;
    background: rgba(255,255,255,0.92);
    backdrop-filter: blur(8px);
    transition: box-shadow 0.2s, background 0.2s;
    padding: 2rem 2.2rem;
}
.card:hover, .main-box:hover, .recommendation-box:hover {
    box-shadow: 0 16px 48px 0 rgba(44,62,80,0.16), 0 2px 8px rgba(44,62,80,0.08);
    background: rgba(255,255,255,0.97);
}
.form-label {
    font-weight: 600;
    color: #198754;
}
.form-select, .form-control {
    padding: 0.7rem 1.2rem;
    border-radius: 1.1rem;
    border: 1.5px solid #ced4da;
    font-size: 1.08em;
    background: #f8fafc;
    transition: border 0.2s, box-shadow 0.2s;
}
.form-select:focus, .form-control:focus {
    border-color: #198754;
    box-shadow: 0 0 0 0.2rem #19875422;
}
.btn-primary, .btn-gradient-blue {
    background: linear-gradient(90deg, #2196f3 0%, #21cbf3 100%) !important;
    color: #fff !important;
    border: none;
    font-weight: 600;
    border-radius: 2rem;
    padding: 0.7em 1.5em;
    font-size: 1.08em;
    letter-spacing: 0.01em;
    box-shadow: 0 2px 8px #2196f322;
    transition: background 0.2s, color 0.2s, box-shadow 0.2s;
}
.btn-primary:hover, .btn-gradient-blue:hover {
    background: linear-gradient(90deg, #21cbf3 0%, #2196f3 100%) !important;
    color: #fff;
    box-shadow: 0 4px 16px #2196f344;
}
.table th {
    font-weight: 600;
    color: #495057;
    background: #f8fafc;
    border-top: none;
}
.table-hover tbody tr:hover {
    background: #f4faff;
    transition: background 0.2s;
}
.table td, .table th {
    vertical-align: middle;
}
</style>
@endpush 