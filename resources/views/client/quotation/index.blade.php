@extends('layouts.app')

@php
    // What the client should know / do next for each status
    $nextStep = [
        'pending' => ['fa-hourglass-half', 'Collecting supplier quotes'],
        'reviewed' => ['fa-hand-pointer', 'Quotes ready — choose your suppliers'],
        'proceeded' => ['fa-check-circle', 'Submitted — contract in preparation'],
        'cancelled' => ['fa-times-circle', 'Cancelled'],
        'rejected' => ['fa-times-circle', 'Rejected'],
    ];
@endphp

@section('content')
<div class="container-fluid req-page">
    <x-page-header title="My Quotation Requests" icon="file-signature" subtitle="Track your requests, compare supplier offers and go ahead when you're ready.">
        <a href="{{ route('client.quotation.create') }}" class="btn btn-success"><i class="fas fa-plus me-1"></i> New quotation request</a>
    </x-page-header>

    @if($quotationRequests->count() > 0)
        <div class="req-request-grid">
            @foreach($quotationRequests as $request)
                @php
                    [$icon, $hint] = $nextStep[$request->status] ?? ['fa-info-circle', \App\Support\Status::label($request->status)];
                    $area = $request->rooms->sum(fn ($r) => $r->area ?? $r->length * $r->width);
                    $actionable = $request->status === 'reviewed';
                @endphp
                <a href="{{ route('client.quotation.view') }}?id={{ $request->id }}" class="req-request-card @if($actionable) is-actionable @endif">
                    <div class="req-request-card__top">
                        <span class="req-ref">{{ $request->request_number }}</span>
                        <x-status-badge :status="$request->status" />
                    </div>
                    <dl class="req-request-card__facts">
                        <div><dt>Submitted</dt><dd>{{ $request->created_at->format('M d, Y') }}</dd></div>
                        <div><dt>Rooms</dt><dd>{{ $request->rooms->count() }}</dd></div>
                        <div><dt>Floor area</dt><dd>{{ rtrim(rtrim(number_format($area, 2), '0'), '.') }} m²</dd></div>
                    </dl>
                    @if($request->rooms->isNotEmpty())
                        <div class="req-room__scopes mb-2">
                            @foreach($request->rooms->take(4) as $room)
                                <span class="req-tag">{{ $room->name }}</span>
                            @endforeach
                            @if($request->rooms->count() > 4)<span class="req-tag">+{{ $request->rooms->count() - 4 }} more</span>@endif
                        </div>
                    @endif
                    @if($request->admin_notes)
                        <p class="req-request-card__note"><i class="fas fa-comment-dots me-1"></i>{{ Str::limit($request->admin_notes, 120) }}</p>
                    @endif
                    <div class="req-request-card__next">
                        <i class="fas {{ $icon }}" aria-hidden="true"></i> {{ $hint }}
                        <span class="ms-auto">View <i class="fas fa-arrow-right" aria-hidden="true"></i></span>
                    </div>
                </a>
            @endforeach
        </div>
    @else
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-file-signature fa-3x text-muted mb-3" aria-hidden="true"></i>
                <h2 class="h5">No quotation requests yet</h2>
                <p class="text-muted">Describe your rooms and the work you need, and we'll gather quotes from our suppliers.</p>
                <a href="{{ route('client.quotation.create') }}" class="btn btn-success"><i class="fas fa-plus me-1"></i> Create your first request</a>
            </div>
        </div>
    @endif
</div>
@endsection

@push('styles')
@vite('resources/css/pages/requests.css')
@endpush
