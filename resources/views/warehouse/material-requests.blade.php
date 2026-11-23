@extends('layouts.app')

@section('content')
<div class="container-fluid req-page">
    <x-page-header title="Material Requests" icon="dolly" subtitle="Approve requests to release the materials from stock." />

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover req-table">
                <thead>
                    <tr>
                        <th>Request</th>
                        <th>Contract</th>
                        <th>Requested by</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($materialRequests as $mr)
                        <tr>
                            <td>
                                <a href="{{ route('warehouse.material-requests.show', $mr) }}" class="req-ref">MR-{{ $mr->id }}</a>
                                <span class="req-sub">{{ $mr->created_at->format('M d, Y H:i') }}</span>
                            </td>
                            <td>
                                {{ $mr->contract->contract_number ?? '—' }}
                                @if($mr->contract?->title)<span class="req-sub">{{ $mr->contract->title }}</span>@endif
                            </td>
                            <td>{{ $mr->requestedBy->name ?? $mr->user->name ?? '—' }}</td>
                            <td><x-status-badge :status="$mr->status" /></td>
                            <td class="text-end">
                                <div class="req-actions">
                                    <a href="{{ route('warehouse.material-requests.show', $mr) }}" class="btn btn-sm btn-outline-secondary">View</a>
                                    @if($mr->status === 'pending')
                                        <form action="{{ route('warehouse.material-requests.approve', $mr) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Approve material request MR-{{ $mr->id }}?')">
                                                <i class="fas fa-check me-1"></i> Approve
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <x-empty-row cols="5">No material requests found.</x-empty-row>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($materialRequests->hasPages())
            <div class="req-footer">{{ $materialRequests->links() }}</div>
        @endif
    </div>
</div>
@endsection

@push('styles')
@vite('resources/css/pages/requests.css')
@endpush
