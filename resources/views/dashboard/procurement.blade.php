@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 mb-4">
            <h2>Procurement Dashboard</h2>
        </div>
    </div>

    <div class="row">
        <!-- Materials Request Card -->
        <div class="col-md-6 col-lg-4 mb-4">
            @include('dashboard.partials.materials-request-card')
        </div>

        <!-- Other dashboard cards can go here -->
    </div>
</div>
@endsection 