@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Supplier Details: {{ $supplier->name }}</h1>
        <div class="btn-group">
            <a href="{{ route('procurement.suppliers.index') }}" class="btn btn-secondary">Back to Suppliers</a>
            <a href="{{ route('procurement.suppliers.edit', $supplier) }}" class="btn btn-primary">Edit Supplier</a>
            <form action="{{ route('procurement.suppliers.destroy', $supplier) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this supplier and associated user account?')">Delete</button>
            </form>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">Supplier Information</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Company Name:</strong> {{ $supplier->name }}</p>
                    <p><strong>Email:</strong> {{ $supplier->email }}</p>
                    <p><strong>Contact Person:</strong> {{ $supplier->contact_person ?? 'N/A' }}</p>
                    <p><strong>Phone Number:</strong> {{ $supplier->phone ?? 'N/A' }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Status:</strong> 
                        <span class="badge bg-{{ $supplier->status === 'active' ? 'success' : 'danger' }}">
                            {{ ucfirst($supplier->status) }}
                        </span>
                    </p>
                    <p><strong>Address:</strong> {{ $supplier->address ?? 'N/A' }}</p>
                    <p><strong>Account Created:</strong> {{ $supplier->created_at->format('M d, Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Associated User Account -->
    <div class="card shadow mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">Associated User Account</h5>
        </div>
        <div class="card-body">
            <p><strong>User Name:</strong> {{ $supplier->user->name ?? 'N/A' }}</p>
            <p><strong>User Email:</strong> {{ $supplier->user->email ?? 'N/A' }}</p>
            <p><strong>User Role:</strong> {{ $supplier->user->role ?? 'N/A' }}</p>
        </div>
    </div>

    <!-- Supplier Performance (Optional, if you have this data) -->
    {{-- <div class="card shadow mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">Supplier Performance Metrics</h5>
        </div>
        <div class="card-body">
            <p><strong>Overall Score:</strong> N/A</p>
            <p><strong>Completed Orders:</strong> N/A</p>
            <p><strong>On-Time Delivery Rate:</strong> N/A</p>
            <p><strong>Average Rating:</strong> N/A</p>
        </div>
    </div> --}}
</div>
@endsection 