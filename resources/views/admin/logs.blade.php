@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h1 class="mb-4">Administrator Logs</h1>
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