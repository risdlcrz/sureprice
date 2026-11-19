@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="container-fluid">
    <x-page-header title="Notifications" icon="bell">
        <form method="POST" action="{{ route('notifications.mark-all-read') }}">
            @csrf
            <button class="btn btn-outline-primary btn-sm"><i class="fas fa-check-double me-1"></i>Mark all as read</button>
        </form>
        <form method="POST" action="{{ route('notifications.clear-read') }}" onsubmit="return confirm('Delete all read notifications?')">
            @csrf
            <button class="btn btn-outline-danger btn-sm"><i class="fas fa-trash me-1"></i>Clear read</button>
        </form>
    </x-page-header>

    <div class="card">
        <div class="list-group list-group-flush">
            @forelse($notifications as $n)
                @php $d = $n->payload; @endphp
                <div class="list-group-item d-flex gap-3 py-3 {{ $n->read_at ? '' : 'bg-light' }}">
                    <i class="fas fa-circle mt-2 text-{{ $n->read_at ? 'secondary opacity-25' : $n->tone }}" style="font-size:.55rem"></i>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between gap-2">
                            <span class="{{ $n->read_at ? '' : 'fw-semibold' }}">{{ $n->title }}</span>
                            <small class="text-muted text-nowrap" title="{{ $n->created_at }}">{{ $n->created_at->diffForHumans() }}</small>
                        </div>
                        @if($n->message)<div class="text-muted small mt-1">{!! nl2br(e($n->message)) !!}</div>@endif

                        @php
                            $meta = array_filter([
                                'Supplier' => $d['supplier_name'] ?? null,
                                'Client' => $d['client_name'] ?? null,
                                'Contract' => $d['contract_number'] ?? null,
                                'Amount' => isset($d['amount']) ? '₱' . number_format($d['amount'], 2) : null,
                                'Rating' => isset($d['overall_rating']) ? $d['overall_rating'] . '/5' : null,
                            ]);
                        @endphp
                        @if($meta || ($d['priority'] ?? null) === 'high')
                            <div class="small mt-1 d-flex flex-wrap gap-3">
                                @foreach($meta as $label => $value)<span><span class="text-muted">{{ $label }}:</span> {{ $value }}</span>@endforeach
                                @if(($d['priority'] ?? null) === 'high')<x-status-badge status="high" kind="priority">High priority</x-status-badge>@endif
                            </div>
                        @endif

                        <div class="mt-2 d-flex gap-2">
                            @if($n->link)
                                <a href="{{ route('notifications.open', $n->id) }}" class="btn btn-sm btn-primary">View details</a>
                            @endif
                            @unless($n->read_at)
                                <form method="POST" action="{{ route('notifications.read', $n->id) }}">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-secondary">Mark as read</button>
                                </form>
                            @endunless
                        </div>
                    </div>
                </div>
            @empty
                <div class="list-group-item text-center text-muted py-5">
                    <i class="fas fa-bell-slash fa-2x mb-2 d-block opacity-50"></i>You have no notifications.
                </div>
            @endforelse
        </div>
    </div>

    <div class="mt-3">{{ $notifications->links() }}</div>
</div>
@endsection
