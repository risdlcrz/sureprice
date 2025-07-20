@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="h3 mb-4">Budget Allocation & Expenditures</h1>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Budget vs. Expenditure per Contract</h5>
        </div>
        <div class="card-body">
            <canvas id="budgetChart" data-chart-data='@json($chartData)'></canvas>
        </div>
    </div>
</div>
@endsection

@push('styles')
    @vite(['resources/css/procurement/analytics/budget-allocation.css'])
@endpush

@push('scripts')
    @vite(['resources/js/procurement/analytics/budget-allocation.js'])
@endpush 