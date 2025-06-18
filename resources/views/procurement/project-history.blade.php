@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1 class="text-center mb-4">Project and Procurement Dashboard</h1>

    <div class="row row-cols-1 row-cols-md-3 g-4">
        <!-- Card 1: Past Transactions -->
        <div class="col">
            <div class="card h-100 shadow-sm" onclick="window.location.href = '{{ route('admin.project') }}';" style="cursor:pointer;">
                <img src="{{ asset('images/historydash1.svg') }}" class="card-img-top" alt="Past Transactions" style="height: 150px; object-fit: contain;">
                <div class="card-body">
                    <h5 class="card-title">Past Transactions</h5>
                </div>
            </div>
        </div>

        <!-- Card 2: Supplier Performance Records -->
        <div class="col">
            <div class="card h-100 shadow-sm" onclick="window.location.href = '{{ route('procurement.suppliers.rankings') }}';" style="cursor:pointer;">
                <img src="{{ asset('images/historydash2.svg') }}" class="card-img-top" alt="Supplier Performance Records" style="height: 150px; object-fit: contain;">
                <div class="card-body">
                    <h5 class="card-title">Supplier Performance Records</h5>
                </div>
            </div>
        </div>

        <!-- Card 3: Procurement Logs -->
        <div class="col">
            <div class="card h-100 shadow-sm" onclick="window.location.href = '{{ route('procurement.dashboard') }}';" style="cursor:pointer;">
                <img src="{{ asset('images/historydash3.svg') }}" class="card-img-top" alt="Procurement Logs" style="height: 150px; object-fit: contain;">
                <div class="card-body">
                    <h5 class="card-title">Procurement Logs</h5>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 