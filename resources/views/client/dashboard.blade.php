@extends('layouts.app')

@php
    $banned = $company->party && $company->party->banned;
    $awaitingChoice = $quotationRequests->where('status', 'reviewed');
    $activeContracts = $contracts->whereIn('status', ['approved', 'in_progress']);
    $nextPayment = $openPayments->first();
    $overdue = $openPayments->filter(fn ($p) => $p->due_date && $p->due_date->isPast() && !$p->due_date->isToday());
    $firstName = trim(explode(' ', (string) ($company->contact_person ?: $user->name))[0]) ?: 'there';
@endphp

@section('content')
<div class="container-fluid req-page">
    <x-page-header :title="'Welcome, ' . $firstName" icon="home" :subtitle="$company->company_name ?: 'Your projects, quotations and payments at a glance.'">
        <a href="{{ route('client.quotation.create') }}" class="btn btn-success"><i class="fas fa-plus me-1"></i> Request a quotation</a>
    </x-page-header>

    @if($banned)
        <div class="req-callout req-callout--danger mb-4">
            <div class="req-callout__text">
                <i class="fas fa-ban" aria-hidden="true"></i>
                <div>
                    <strong>Your account has been suspended.</strong>
                    @if($company->party->ban_reason) Reason: {{ $company->party->ban_reason }}.@endif
                    Please contact support for more information.
                </div>
            </div>
        </div>
    @endif

    {{-- What needs your attention --}}
    @if($overdue->isNotEmpty())
        <div class="req-callout req-callout--danger mb-4">
            <div class="req-callout__text">
                <i class="fas fa-exclamation-circle" aria-hidden="true"></i>
                <div><strong>{{ $overdue->count() }} {{ Str::plural('payment', $overdue->count()) }} overdue.</strong> Please settle {{ $overdue->count() === 1 ? 'it' : 'them' }} or upload your proof of payment.</div>
            </div>
            <a href="{{ route('client.payments') }}" class="btn btn-outline-secondary">View payments</a>
        </div>
    @endif
    @foreach($awaitingChoice->take(2) as $qr)
        <div class="req-callout req-callout--warning mb-4">
            <div class="req-callout__text">
                <i class="fas fa-hand-pointer" aria-hidden="true"></i>
                <div><strong>Supplier quotes are ready for {{ $qr->request_number }}.</strong> Choose your suppliers to continue.</div>
            </div>
            <a href="{{ route('client.quotation.view') }}?id={{ $qr->id }}" class="btn btn-success">Choose suppliers</a>
        </div>
    @endforeach
    @if($quotationRequests->isEmpty() && $contracts->isEmpty())
        <div class="req-callout req-callout--info mb-4">
            <div class="req-callout__text">
                <i class="fas fa-lightbulb" aria-hidden="true"></i>
                <div><strong>Get started.</strong> Tell us about your rooms and the work you need — we'll collect quotes from our suppliers so you can compare them.</div>
            </div>
            <a href="{{ route('client.quotation.create') }}" class="btn btn-success">Request a quotation</a>
        </div>
    @endif

    {{-- At a glance --}}
    <div class="req-kpis">
        <a href="{{ route('client.quotation.index') }}" class="req-kpi req-kpi--warning">
            <span class="req-kpi__icon"><i class="fas fa-file-signature" aria-hidden="true"></i></span>
            <span><span class="req-kpi__value">{{ $quotationRequests->whereNotIn('status', ['proceeded', 'cancelled', 'rejected'])->count() }}</span><span class="req-kpi__label">Open quotation requests</span></span>
        </a>
        <a href="{{ $banned ? '#' : route('contracts.index') }}" class="req-kpi req-kpi--success">
            <span class="req-kpi__icon"><i class="fas fa-hard-hat" aria-hidden="true"></i></span>
            <span><span class="req-kpi__value">{{ $activeContracts->count() }}</span><span class="req-kpi__label">Active projects</span></span>
        </a>
        <a href="{{ route('client.payments') }}" class="req-kpi req-kpi--info">
            <span class="req-kpi__icon"><i class="fas fa-wallet" aria-hidden="true"></i></span>
            <span>
                <span class="req-kpi__value">₱{{ number_format($openPayments->sum('amount'), 2) }}</span>
                <span class="req-kpi__label">Balance due{{ $nextPayment?->due_date ? ' · next ' . $nextPayment->due_date->format('M d') : '' }}</span>
            </span>
        </a>
        <a href="{{ route('messages.index') }}" class="req-kpi req-kpi--neutral">
            <span class="req-kpi__icon"><i class="fas fa-comments" aria-hidden="true"></i></span>
            <span><span class="req-kpi__value">{{ $unreadMessages }}</span><span class="req-kpi__label">Unread {{ Str::plural('message', $unreadMessages) }}</span></span>
        </a>
    </div>

    <div class="row g-4">
        {{-- Quotation requests --}}
        <div class="col-xl-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2 class="req-section-title"><i class="fas fa-file-signature"></i> Quotation requests</h2>
                    <a href="{{ route('client.quotation.index') }}" class="btn btn-sm btn-link">View all</a>
                </div>
                <div class="table-responsive">
                    <table class="table req-table">
                        <tbody>
                            @forelse($quotationRequests->take(5) as $qr)
                                <tr>
                                    <td>
                                        <a href="{{ route('client.quotation.view') }}?id={{ $qr->id }}" class="req-ref">{{ $qr->request_number }}</a>
                                        <span class="req-sub">{{ $qr->rooms_count }} {{ Str::plural('room', $qr->rooms_count) }} · {{ $qr->created_at->format('M d, Y') }}</span>
                                    </td>
                                    <td class="text-end"><x-status-badge :status="$qr->status" /></td>
                                </tr>
                            @empty
                                <x-empty-row cols="2">No quotation requests yet.</x-empty-row>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Upcoming payments --}}
        <div class="col-xl-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2 class="req-section-title"><i class="fas fa-wallet"></i> Upcoming payments</h2>
                    <a href="{{ route('client.payments') }}" class="btn btn-sm btn-link">View all</a>
                </div>
                <div class="table-responsive">
                    <table class="table req-table">
                        <tbody>
                            @forelse($openPayments->take(5) as $payment)
                                @php $late = $payment->due_date && $payment->due_date->isPast() && !$payment->due_date->isToday(); @endphp
                                <tr>
                                    <td>
                                        <a href="{{ route('client.payments.show', $payment) }}" class="req-ref">₱{{ number_format($payment->amount, 2) }}</a>
                                        <span class="req-sub">{{ $payment->contract->contract_number ?? 'Contract' }}</span>
                                    </td>
                                    <td>
                                        {{ $payment->due_date?->format('M d, Y') ?? '—' }}
                                        @if($late)<span class="req-sub text-danger">Overdue</span>@endif
                                    </td>
                                    <td class="text-end"><x-status-badge :status="$payment->status" /></td>
                                </tr>
                            @empty
                                <x-empty-row cols="3">No payments due. Payments appear once you have an approved contract.</x-empty-row>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Contracts --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2 class="req-section-title"><i class="fas fa-file-contract"></i> My contracts</h2>
                    @unless($banned)
                        <div class="d-flex gap-2">
                            <a href="{{ route('project-timeline.index') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-stream me-1"></i> Project timeline</a>
                            <a href="{{ route('contracts.index') }}" class="btn btn-sm btn-link">View all</a>
                        </div>
                    @endunless
                </div>
                <div class="table-responsive">
                    <table class="table table-hover req-table">
                        <thead>
                            <tr>
                                <th>Contract</th>
                                <th>Schedule</th>
                                <th class="req-num">Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($contracts->take(5) as $contract)
                                <tr>
                                    <td class="req-wrap">
                                        @if($banned)
                                            <span class="req-ref">{{ $contract->contract_number }}</span>
                                        @else
                                            <a href="{{ route('contracts.show', $contract) }}" class="req-ref">{{ $contract->contract_number }}</a>
                                        @endif
                                        <span class="req-sub">{{ $contract->title }}</span>
                                    </td>
                                    <td>
                                        {{ $contract->start_date?->format('M d, Y') ?? '—' }}
                                        <span class="req-sub">to {{ $contract->end_date?->format('M d, Y') ?? '—' }}</span>
                                    </td>
                                    <td class="req-num">₱{{ number_format($contract->total_amount, 2) }}</td>
                                    <td><x-status-badge :status="$contract->status" /></td>
                                </tr>
                            @empty
                                <x-empty-row cols="4">No contracts yet. When you proceed with a quotation, our team prepares your contract.</x-empty-row>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
@vite('resources/css/pages/requests.css')
@endpush
