{{-- Analytics hub for admin and procurement. Param: $links — [card key => route name] for the viewer's role. --}}
@extends('layouts.app')

@section('title', 'Analytics')

@php
    $cards = [
        'transactions' => ['Transactions', 'History and reports of past transactions.', 'aimage1.jpg'],
        'budget' => ['Budget Allocation and Expenditures', 'Monitor budget allocations and track expenditures.', 'aimage2.jpg'],
        'rankings' => ['Supplier Ranking and Performance', 'Evaluate and compare supplier performance metrics.', 'aimage3.jpg'],
        'prices' => ['Price Trend Analysis', 'Analyze price trends and market fluctuations.', 'aimage4.jpg'],
        'recommendation' => ['Supplier Recommendation', 'Get data-driven supplier recommendations by category and metrics.', 'aimage5.jpg'],
        'feedback' => ['Client Feedback Analytics', 'Analyze client satisfaction and identify improvement areas.', null],
    ];
@endphp

@section('content')
<div class="container-fluid">
    <x-page-header title="Analytics" icon="chart-bar" />

    <div class="row g-4">
        @foreach($cards as $key => [$title, $text, $image])
            @continue(!isset($links[$key]))
            <div class="col-md-6 col-lg-4 col-xl-3">
                <a href="{{ route($links[$key]) }}" class="card analytics-tile h-100 text-decoration-none">
                    <img src="{{ asset('images/' . ($image ?? 'placeholder.png')) }}" class="card-img-top" alt="{{ $title }}"
                         onerror="this.onerror=null;this.src='{{ asset('images/placeholder.png') }}';">
                    <div class="card-body">
                        <h5 class="card-title">{{ $title }}</h5>
                        <p class="card-text text-muted small mb-0">{{ $text }}</p>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
</div>
@endsection

@push('styles')
@vite('resources/css/pages/analytics-dashboard.css')
@endpush
