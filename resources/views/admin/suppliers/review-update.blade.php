@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">Review Supplier Profile Update</h1>
    <div class="card mb-4">
        <div class="card-header">Current Information</div>
        <div class="card-body">
            <ul>
                <li><strong>Company Name:</strong> {{ $supplier->company_name }}</li>
                <li><strong>Contact Person:</strong> {{ $supplier->contact_person }}</li>
                <li><strong>Email:</strong> {{ $supplier->email }}</li>
                <li><strong>Phone:</strong> {{ $supplier->phone }}</li>
                <li><strong>Address:</strong> {{ $supplier->address }}</li>
                <li><strong>Tax Number:</strong> {{ $supplier->tax_number }}</li>
                <li><strong>Registration Number:</strong> {{ $supplier->registration_number }}</li>
            </ul>
        </div>
    </div>
    <div class="card mb-4">
        <div class="card-header">Requested Changes</div>
        <div class="card-body">
            @if($pending)
            <ul>
                <li><strong>Company Name:</strong> {{ $pending['company_name'] ?? '-' }}</li>
                <li><strong>Contact Person:</strong> {{ $pending['contact_person'] ?? '-' }}</li>
                <li><strong>Email:</strong> {{ $pending['email'] ?? '-' }}</li>
                <li><strong>Phone:</strong> {{ $pending['phone'] ?? '-' }}</li>
                <li><strong>Address:</strong> {{ $pending['address'] ?? '-' }}</li>
                <li><strong>Tax Number:</strong> {{ $pending['tax_number'] ?? '-' }}</li>
                <li><strong>Registration Number:</strong> {{ $pending['registration_number'] ?? '-' }}</li>
            </ul>
            @else
            <p class="text-muted">No pending changes.</p>
            @endif
        </div>
    </div>
    <form method="POST" action="{{ route('admin.suppliers.approve-update', $supplier->id) }}" class="d-inline">
        @csrf
        <button type="submit" class="btn btn-success">Approve</button>
    </form>
    <form method="POST" action="{{ route('admin.suppliers.reject-update', $supplier->id) }}" class="d-inline ms-2">
        @csrf
        <button type="submit" class="btn btn-danger">Reject</button>
    </form>
    <a href="{{ route('admin.suppliers.pending-updates') }}" class="btn btn-secondary ms-2">Back</a>
</div>
@endsection 