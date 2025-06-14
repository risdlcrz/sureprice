@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h1 class="text-center mb-5">Client Dashboard</h1>
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