@extends('layouts.app')

@section('content')
@php $mr = $materialRequest; @endphp
<div class="container-fluid req-page">
    <x-page-header title="Material Request" icon="dolly">
        <a href="{{ route('warehouse.material-requests.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Back</a>
    </x-page-header>

    <div class="card mb-4">
        <div class="card-body">
            <div class="req-hero mb-4">
                <div>
                    <div class="req-hero__ref">Material request</div>
                    <h2 class="req-hero__title">MR-{{ $mr->id }} <x-status-badge :status="$mr->status" /></h2>
                </div>
            </div>
            <dl class="req-meta">
                <div><dt>Contract</dt><dd>{{ $mr->contract->contract_number ?? '—' }}@if($mr->contract?->title)<span class="req-sub">{{ $mr->contract->title }}</span>@endif</dd></div>
                <div><dt>Requested by</dt><dd>{{ $mr->user->name ?? '—' }}</dd></div>
                <div><dt>Requested</dt><dd>{{ $mr->created_at->format('M d, Y H:i') }}</dd></div>
            </dl>
            @if($mr->notes)
                <div class="mt-4">
                    <div class="form-label">Notes</div>
                    <div class="req-note">{{ $mr->notes }}</div>
                </div>
            @endif
        </div>
    </div>

    @if($mr->status === 'pending')
        <div class="req-callout req-callout--warning mb-4">
            <div class="req-callout__text">
                <i class="fas fa-hourglass-half" aria-hidden="true"></i>
                <div><strong>Waiting for warehouse approval.</strong><br>Approving releases the requested quantities from stock.</div>
            </div>
            <form action="{{ route('warehouse.material-requests.approve', $mr) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-success" onclick="return confirm('Approve this material request?')"><i class="fas fa-check me-1"></i> Approve request</button>
            </form>
        </div>
    @elseif($mr->status === 'approved')
        <div class="req-callout req-callout--success mb-4">
            <div class="req-callout__text">
                <i class="fas fa-check-circle" aria-hidden="true"></i>
                <div><strong>Approved.</strong> The materials have been released.</div>
            </div>
        </div>
    @endif

    <div class="card">
        <div class="card-header"><h3 class="req-section-title"><i class="fas fa-boxes"></i> Items</h3></div>
        <div class="table-responsive">
            <table class="table req-table">
                <thead>
                    <tr>
                        <th>Material</th>
                        <th class="req-num">Requested</th>
                        <th class="req-num">Fulfilled</th>
                        <th>Unit</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mr->items as $item)
                        <tr>
                            <td class="fw-semibold">{{ $item->material->name ?? '—' }}</td>
                            <td class="req-num">{{ number_format($item->quantity, 2) }}</td>
                            <td class="req-num">{{ number_format($item->fulfilled_quantity ?? 0, 2) }}</td>
                            <td>{{ $item->unit }}</td>
                        </tr>
                    @empty
                        <x-empty-row cols="4">No items on this request.</x-empty-row>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('styles')
@vite('resources/css/pages/requests.css')
@endpush
