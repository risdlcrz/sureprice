@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1 class="h3 mb-4 text-gray-800">Project and Procurement Dashboard</h1>

    <div class="row row-cols-1 row-cols-md-3 g-4">
        <!-- Card 1: Past Transactions -->
        <div class="col">
            <div class="card h-100 shadow-sm d-flex flex-column" onclick="window.location.href = '{{ route('admin.transactions') }}';" style="cursor:pointer;">
                <img src="{{ asset('images/historydash1.jpg') }}" class="card-img-top flex-grow-1" alt="Past Transactions" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.onerror=null;this.src='{{ asset('images/placeholder.png') }}';">
                <div class="card-body">
                    <h5 class="card-title">Past Transactions</h5>
                </div>
            </div>
        </div>

        <!-- Card 2: Supplier Performance Records -->
        <div class="col">
            <div class="card h-100 shadow-sm d-flex flex-column" onclick="window.location.href = '{{ route('suppliers.rankings') }}';" style="cursor:pointer;">
                <img src="{{ asset('images/historydash2.jpeg') }}" class="card-img-top flex-grow-1" alt="Supplier Performance Records" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.onerror=null;this.src='{{ asset('images/placeholder.png') }}';">
                <div class="card-body">
                    <h5 class="card-title">Supplier Performance Records</h5>
                </div>
            </div>
        </div>

        <!-- Card 3: Administrator Logs -->
        <div class="col">
            <div class="card h-100 shadow-sm d-flex flex-column" onclick="window.location.href = '{{ route('admin.logs') }}';" style="cursor:pointer;">
                <img src="{{ asset('images/historydash3.jpg') }}" class="card-img-top flex-grow-1" alt="Administrator Logs" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.onerror=null;this.src='{{ asset('images/placeholder.png') }}';">
                <div class="card-body">
                    <h5 class="card-title">Administrator Logs</h5>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
