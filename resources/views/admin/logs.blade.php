@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h1 class="h3 mb-4 text-gray-800 text-center" style="font-weight:700;color:#198754;letter-spacing:0.01em;">Administrator Logs</h1>
    @if(auth()->user() && auth()->user()->user_type === 'admin')
    <form method="GET" class="mb-3" id="userTypeFilterForm">
        <div class="row g-2 align-items-center">
            <div class="col-auto">
                <label for="filter" class="col-form-label">Show logs for:</label>
            </div>
            <div class="col-auto">
                <select name="filter" id="filter" class="form-select" onchange="document.getElementById('userTypeFilterForm').submit()">
                    @foreach($userTypes as $key => $label)
                        <option value="{{ $key }}" @if($filter === $key) selected @endif>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </form>
    @endif
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Action</th>
                            <th>Description</th>
                            <th>Related</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activities as $activity)
                        <tr>
                            <td>{{ $activity->user ? $activity->user->name : 'System' }}</td>
                            <td><span class="badge bg-{{ $activity->action_color }}">{{ ucfirst($activity->action) }}</span></td>
                            <td>{{ $activity->description }}</td>
                            <td>
                                @if($activity->model_type && $activity->model_id)
                                    {{ class_basename($activity->model_type) }} #{{ $activity->model_id }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ $activity->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No administrator activities found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

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
.card {
    border: none;
    border-radius: 1.25rem;
    box-shadow: 0 8px 32px 0 rgba(44,62,80,0.10), 0 1.5px 6px rgba(44,62,80,0.04);
    margin-bottom: 1.5rem;
    background: rgba(255,255,255,0.92);
    backdrop-filter: blur(8px);
    transition: box-shadow 0.2s, background 0.2s;
}
.card:hover {
    box-shadow: 0 16px 48px 0 rgba(44,62,80,0.16), 0 2px 8px rgba(44,62,80,0.08);
    background: rgba(255,255,255,0.97);
}
.form-select {
    padding: 0.7rem 1.2rem;
    border-radius: 1.1rem;
    border: 1.5px solid #ced4da;
    font-size: 1.08em;
    background: #f8fafc;
    transition: border 0.2s, box-shadow 0.2s;
}
.form-select:focus {
    border-color: #198754;
    box-shadow: 0 0 0 0.2rem #19875422;
}
.table th {
    font-weight: 600;
    color: #495057;
    background: #f8fafc;
    border-top: none;
}
.table-striped > tbody > tr:nth-of-type(odd) {
    background-color: #f8fafc;
}
.table-hover tbody tr:hover {
    background: #f4faff;
    transition: background 0.2s;
}
.table td, .table th {
    vertical-align: middle;
}
.badge {
    font-size: 0.95em;
    padding: 0.5em 1em;
    border-radius: 0.7em;
    font-weight: 600;
    letter-spacing: 0.01em;
    box-shadow: 0 1px 4px #38b6ff22;
}
</style>
@endpush 