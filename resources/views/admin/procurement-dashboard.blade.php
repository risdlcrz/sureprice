@extends('layouts.app')

@section('content')
    <div class="sidebar">
        @include('include.header_project')
    </div>

    <div class="content">
        <h1 class="text-center my-4">Procurement Dashboard</h1>

        <div class="top-controls">
            <!-- Optional top controls (if any) go here -->
        </div>

        <div class="row row-cols-1 row-cols-md-3 g-4">
            <!-- Card 1 -->
            <div class="col">
                <div class="card" onclick="window.location.href = '{{ route('contracts.index') }}';">
                    <img src="{{ asset('images/contract.jpg') }}" alt="Contract Management">
                    <div class="card-body">
                        <h5 class="card-title">Contract Management</h5>
                    </div>
                </div>
            </div>
            <!-- Card 2 -->
            <div class="col">
                <div class="card" onclick="window.location.href = '{{ route('materials.index') }}';">
                    <img src="{{ asset('images/materials.jpg') }}" alt="Materials Management">
                    <div class="card-body">
                        <h5 class="card-title">Materials Management</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection 