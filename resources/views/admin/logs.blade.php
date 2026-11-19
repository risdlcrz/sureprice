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
                        <x-empty-row cols="5">No administrator activities found.</x-empty-row>
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
.table-striped > tbody > tr:nth-of-type(odd) {
    background-color: #f8fafc;
}
</style>
@endpush 