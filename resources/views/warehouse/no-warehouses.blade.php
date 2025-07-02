@extends('layouts.app')

@section('content')
<div class="container py-5 text-center">
    <div class="alert alert-warning">
        <h2 class="mb-3"><i class="fas fa-warehouse fa-2x"></i></h2>
        <h3>No Warehouses Found</h3>
        <p class="lead">There are currently no warehouses in the system.</p>
        <p>Please ask an administrator to create at least one warehouse to use the inventory features.</p>
        <a href="{{ url()->previous() }}" class="btn btn-secondary mt-3">Go Back</a>
    </div>
</div>
@endsection 