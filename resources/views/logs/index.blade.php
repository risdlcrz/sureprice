{{-- Activity log table shared by admin, procurement and warehouse. Params: $title, $activities, optional $userTypes/$filter. --}}
@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">
    <x-page-header :title="$title" icon="history">
        @isset($userTypes)
            <form method="GET" class="d-flex align-items-center gap-2">
                <label for="filter" class="text-muted small text-nowrap">Show logs for</label>
                <select name="filter" id="filter" class="form-select form-select-sm" onchange="this.form.submit()">
                    @foreach($userTypes as $key => $label)
                        <option value="{{ $key }}" @selected($filter === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </form>
        @endisset
    </x-page-header>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr><th>User</th><th>Action</th><th>Description</th><th>Related</th><th>Date</th></tr></thead>
                <tbody>
                    @forelse($activities as $activity)
                        <tr>
                            <td>{{ $activity->user->name ?? 'System' }}</td>
                            <td><span class="badge bg-{{ $activity->action_color }}">{{ ucfirst($activity->action) }}</span></td>
                            <td>{{ $activity->description }}</td>
                            <td>{{ $activity->model_type && $activity->model_id ? class_basename($activity->model_type) . ' #' . $activity->model_id : '-' }}</td>
                            <td class="text-nowrap">{{ $activity->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                    @empty
                        <x-empty-row cols="5">No activity found.</x-empty-row>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
