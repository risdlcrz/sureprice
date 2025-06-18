@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h1 class="text-center mb-5">Client Dashboard</h1>

    <!-- Statistics Cards -->
    <div class="row row-cols-1 row-cols-md-3 g-4 mb-5">
        <div class="col">
            <div class="card h-100 shadow-sm bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Payments</h5>
                    <p class="card-text display-4">{{ $totalPayments }}</p>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card h-100 shadow-sm bg-warning text-dark">
                <div class="card-body">
                    <h5 class="card-title">Pending Payments</h5>
                    <p class="card-text display-4">{{ $pendingPayments }}</p>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card h-100 shadow-sm bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Paid Payments</h5>
                    <p class="card-text display-4">{{ $paidPayments }}</p>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card h-100 shadow-sm bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Amount</h5>
                    <p class="card-text display-4">₱{{ number_format($totalAmount, 2) }}</p>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card h-100 shadow-sm bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Paid Amount</h5>
                    <p class="card-text display-4">₱{{ number_format($paidAmount, 2) }}</p>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card h-100 shadow-sm bg-warning text-dark">
                <div class="card-body">
                    <h5 class="card-title">Pending Amount</h5>
                    <p class="card-text display-4">₱{{ number_format($pendingAmount, 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-md-2 g-4 justify-content-center">
        <!-- Project & Procurement Card -->
        <div class="col">
            <div class="card h-100 shadow-sm">
                <img src="{{ asset('images/project_procurement.svg') }}" class="card-img-top" alt="Project & Procurement" style="object-fit:cover; height:180px;">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title">Project & Procurement</h5>
                    <p class="card-text">View and manage your contracts, project timelines, and procurement details.</p>
                    <a href="{{ route('client.project.procurement') }}" class="btn btn-primary mt-auto">Go to Project & Procurement</a>
                </div>
            </div>
        </div>
        <!-- Payments Card -->
        <div class="col">
            <div class="card h-100 shadow-sm">
                <img src="{{ asset('images/payments.svg') }}" class="card-img-top" alt="Payments" style="object-fit:cover; height:180px;">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title">Payments</h5>
                    <p class="card-text">Track your payment schedules, upload payment proof, and view your payment history.</p>
                    <a href="{{ route('client.payments') }}" class="btn btn-success mt-auto">Go to Payments</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 