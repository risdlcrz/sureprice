@extends('layouts.app')

@section('content')
<h1 class="text-center my-4">Analytics Dashboard</h1>

<div class="row row-cols-1 row-cols-md-3 g-4">
    <!-- Card 1 -->
    <div class="col">
        <div class="card" onclick="window.location.href='{{ route('admin.purchase-order') }}';" style="cursor:pointer;">
            <img src="{{ Vite::asset('resources/images/analyticsdash1.jpeg') }}" alt="Active Purchase Order" class="card-img-top">
            <div class="card-body">
                <h5 class="card-title">Active Purchase Order</h5>
            </div>
        </div>
    </div>
    <!-- Card 2 -->
    <div class="col">
        <div class="card" onclick="window.location.href='{{ route('admin.budget-allocation') }}';" style="cursor:pointer;">
            <img src="{{ Vite::asset('resources/images/analyticsdash2.jpg') }}" alt="Budget Allocation" class="card-img-top">
            <div class="card-body">
                <h5 class="card-title">Budget Allocation and Expenditures</h5>
            </div>
        </div>
    </div>
    <!-- Card 3 -->
    <div class="col">
        <div class="card" onclick="window.location.href='{{ route('admin.supplier-rankings') }}';" style="cursor:pointer;">
            <img src="{{ Vite::asset('resources/images/analyticsdash3.jpg') }}" alt="Supplier Rankings" class="card-img-top">
            <div class="card-body">
                <h5 class="card-title">Supplier Ranking and Performance</h5>
            </div>
        </div>
    </div>
    <!-- Card 4 -->
    <div class="col">
        <div class="card" onclick="window.location.href='{{ route('admin.price-analysis') }}';" style="cursor:pointer;">
            <img src="{{ Vite::asset('resources/images/analyticsdash4.jpg') }}" alt="Price Analysis" class="card-img-top">
            <div class="card-body">
                <h5 class="card-title">Price Trend Analysis</h5>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
    @vite(['resources/css/dbadmin.css'])
@endpush
