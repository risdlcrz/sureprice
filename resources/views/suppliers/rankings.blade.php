{{-- Supplier rankings (admin and procurement). Params: $rankings, $topSuppliers (overall top 3: rank, name, score), $search, $canEvaluate (admin), $backRoute. --}}
@extends('layouts.app')

@section('title', 'Supplier Rankings')

@php
    // [heading, evaluation attribute] for the latest evaluation's score columns
    $scores = [['Delivery', 'delivery_speed_score'], ['Quality', 'quality_score'], ['Cost', 'cost_variance_score'],
        ['Performance', 'performance_score'], ['Engagement', 'engagement_score'], ['Sustainability', 'sustainability_score']];
@endphp

@section('content')
<div class="container-fluid rankings-page">
    <x-page-header title="Supplier Rankings" icon="trophy">
        <a href="{{ route($backRoute) }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i>Analytics</a>
    </x-page-header>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Top Performing Suppliers</span>
            <span class="rk-scale-note">Overall score, 0&ndash;5</span>
        </div>
        <div class="card-body">
            @if($topSuppliers->isEmpty())
                <p class="text-muted text-center my-4">No supplier has been evaluated yet.</p>
            @else
                <div class="row g-4 align-items-stretch">
                    <div class="col-lg-8">
                        {{-- Height includes the x-axis labels so the card never scrolls --}}
                        <div class="rk-chart"><canvas id="topSuppliersChart" role="img"
                            aria-label="Bar chart of the top {{ $topSuppliers->count() }} suppliers by overall score out of 5: {{ $topSuppliers->map(fn ($s) => $s['name'] . ' ' . number_format($s['score'], 2))->implode(', ') }}"></canvas></div>
                    </div>
                    <div class="col-lg-4">
                        <div class="rk-metrics">
                            <h6 class="rk-metrics-title">Performance Metrics</h6>
                            @php $leader = $topSuppliers->first()['score']; @endphp
                            {{-- A plain list (no <ol>): the rank badge is the only rank number --}}
                            <div class="rk-list" role="list">
                                @foreach($topSuppliers as $i => $top)
                                    <div class="rk-item" role="listitem" data-chart-index="{{ $i }}" tabindex="0">
                                        <div class="rk-left">
                                            <span class="rk-badge rk-badge--{{ $top['rank'] }}" aria-label="Rank {{ $top['rank'] }}">{{ $top['rank'] }}</span>
                                            <span class="rk-name">{{ $top['name'] }}</span>
                                        </div>
                                        <div class="rk-right">
                                            <span class="rk-score-value">{{ number_format($top['score'], 2) }}</span>
                                            @if($loop->first)
                                                <span class="rk-delta rk-delta--lead">Leader</span>
                                            @else
                                                <span class="rk-delta">&minus;{{ number_format($leader - $top['score'], 2) }} vs #1</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <span>All Suppliers</span>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('suppliers.template.download') }}" class="btn btn-outline-primary btn-sm"><i class="fas fa-download me-1"></i>Download Template</a>
                <a href="{{ route('suppliers.materials.template.download') }}" class="btn btn-outline-primary btn-sm"><i class="fas fa-download me-1"></i>Materials Template</a>
            </div>
        </div>
        <div class="card-body">
            <form id="searchForm" method="GET" class="d-flex justify-content-end gap-2 mb-3 position-relative">
                <input type="text" name="search" id="searchInput" value="{{ $search }}" class="form-control form-control-sm w-auto" placeholder="Search suppliers...">
                <button type="submit" class="btn btn-secondary btn-sm">Search</button>
                @if($search)<a href="{{ url()->current() }}" class="btn btn-link btn-sm">Clear</a>@endif
                <div id="searchError" class="position-absolute text-danger small" style="top:100%; right:0; display:none;">Please enter a search term</div>
            </form>

            <div class="table-responsive">
                <table class="table rk-table mb-0">
                    <thead>
                        <tr>
                            <th class="rk-rank-col">Rank</th><th>Company Name</th><th class="text-end">Score</th>
                            @foreach($scores as [$heading])<th class="text-end">{{ $heading }}</th>@endforeach
                            @if($canEvaluate)<th class="text-end">Actions</th>@endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rankings as $ranking)
                            @php $latest = $ranking['supplier']->evaluations->last(); @endphp
                            <tr>
                                <td class="rk-rank-col">
                                    @if(in_array($ranking['rank'], [1, 2, 3], true))
                                        <span class="rk-badge rk-badge--{{ $ranking['rank'] }}">{{ $ranking['rank'] }}</span>
                                    @else
                                        <span class="rk-rank-plain">{{ $ranking['rank'] ?? '—' }}</span>
                                    @endif
                                </td>
                                <td class="rk-company">{{ $ranking['supplier']->company_name }}</td>
                                <td class="rk-num fw-semibold">{{ number_format($ranking['score'], 2) }}</td>
                                @foreach($scores as [, $attr])
                                    <td class="rk-num">{{ $latest ? number_format($latest->$attr, 2) : '—' }}</td>
                                @endforeach
                                @if($canEvaluate)
                                    <td class="text-end">
                                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#evaluationModal"
                                                data-supplier-id="{{ $ranking['supplier']->id }}">
                                            <i class="fas fa-star"></i> Evaluate
                                        </button>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <x-empty-row :cols="count($scores) + ($canEvaluate ? 4 : 3)">
                                {{ $search ? "No suppliers match '{$search}'." : 'No suppliers to rank yet.' }}
                            </x-empty-row>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@if($canEvaluate)
    @include('suppliers.partials.evaluation-modal')
@endif
@endsection

@push('styles')
    @vite(['resources/css/admin/suppliers/rankings.css'])
@endpush

@push('scripts')
    <script>
        window.__page = Object.assign(window.__page || {}, { topSuppliers: {{ Js::from($topSuppliers) }} });
        window.appBaseUrl = {{ Js::from(url('')) }};
    </script>
    @vite(['resources/js/admin/suppliers/rankings.js'])
@endpush
