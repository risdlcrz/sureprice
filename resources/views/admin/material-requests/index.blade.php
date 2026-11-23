@extends('layouts.app')

@section('content')
<div class="container-fluid req-page">
    <x-page-header title="Material Requests" icon="dolly" subtitle="Materials requested for approved contracts — filled from stock first, the rest goes to purchasing.">
        @if(auth()->user()->hasRole('manager'))
            {{-- Material requests are raised from an approved contract ("Create Material Request" on the contract page). --}}
            <a href="{{ route('contracts.index', ['status' => 'approved']) }}" class="btn btn-success" title="Choose an approved contract to request materials for">
                <i class="fas fa-plus me-1"></i> New Material Request
            </a>
        @endif
    </x-page-header>

    <x-requests.kpis :filters="App\Models\MaterialRequest::STATUS_FILTERS" :counts="$counts" route="material-requests.index" />

    <div class="card">
        <x-requests.toolbar route="material-requests.index" placeholder="Search by MR #, contract or requester" />

        <div class="table-responsive">
            <table class="table table-hover req-table">
                <thead>
                    <tr>
                        <th>Request</th>
                        <th>Contract</th>
                        <th>Requested by</th>
                        <th class="req-num">Items</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($materialRequests as $mr)
                        <tr>
                            <td>
                                <a href="{{ route('material-requests.show', $mr) }}" class="req-ref">MR-{{ $mr->id }}</a>
                                <span class="req-sub">{{ $mr->created_at->format('M d, Y') }}</span>
                            </td>
                            <td class="req-wrap">
                                @if($mr->contract)
                                    <a href="{{ route('contracts.show', $mr->contract) }}">{{ $mr->contract->contract_number ?? 'Contract' }}</a>
                                    <span class="req-sub">{{ $mr->contract->title }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>{{ $mr->user->name ?? '—' }}</td>
                            <td class="req-num">{{ $mr->items_count }}</td>
                            <td><x-status-badge :status="$mr->status" /></td>
                            <td class="text-end">
                                <a href="{{ route('material-requests.show', $mr) }}" class="btn btn-sm btn-outline-secondary">View</a>
                            </td>
                        </tr>
                    @empty
                        <x-empty-row cols="6">No material requests match these filters.</x-empty-row>
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
