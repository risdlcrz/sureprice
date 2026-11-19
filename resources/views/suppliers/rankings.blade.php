{{-- Supplier rankings (admin and procurement). Params: $rankings, $search, $canEvaluate (admin), $backRoute. --}}
@extends('layouts.app')

@section('title', 'Supplier Rankings')

@php
    // [heading, evaluation attribute] for the latest evaluation's score columns
    $scores = [['Delivery', 'delivery_speed_score'], ['Quality', 'quality_score'], ['Cost', 'cost_variance_score'],
        ['Performance', 'performance_score'], ['Engagement', 'engagement_score'], ['Sustainability', 'sustainability_score']];
@endphp

@section('content')
<div class="container-fluid">
    <x-page-header title="Supplier Rankings" icon="trophy">
        <a href="{{ route($backRoute) }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i>Analytics</a>
    </x-page-header>

    <div class="card mb-4">
        <div class="card-header">Top Performing Suppliers</div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8"><canvas id="topSuppliersChart" height="200"></canvas></div>
                <div class="col-md-4">
                    <h6 class="text-muted mb-3">Performance Metrics</h6>
                    <div id="topSuppliersLegend"></div>
                </div>
            </div>
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
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Rank</th><th>Company Name</th><th>Score</th>
                            @foreach($scores as [$heading])<th>{{ $heading }}</th>@endforeach
                            @if($canEvaluate)<th>Actions</th>@endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rankings as $ranking)
                            @php $latest = $ranking['supplier']->evaluations->last(); @endphp
                            <tr>
                                <td>{{ $ranking['rank'] ?? 'N/A' }}</td>
                                <td>{{ $ranking['supplier']->company_name }}</td>
                                <td>{{ number_format($ranking['score'], 2) }}</td>
                                @foreach($scores as [, $attr])
                                    <td>{{ $latest ? number_format($latest->$attr, 2) : 'N/A' }}</td>
                                @endforeach
                                @if($canEvaluate)
                                    <td>
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
        window.rankingsData = {{ Js::from($rankings) }};
        window.appBaseUrl = {{ Js::from(url('')) }};
    </script>
    @vite(['resources/js/admin/suppliers/rankings.js'])
@endpush
