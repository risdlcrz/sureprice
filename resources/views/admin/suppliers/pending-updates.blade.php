@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">Supplier Profile Updates Pending Approval</h1>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Company Name</th>
                <th>Contact Person</th>
                <th>Email</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($suppliers as $supplier)
                <tr>
                    <td>{{ $supplier->company_name }}</td>
                    <td>{{ $supplier->contact_person }}</td>
                    <td>{{ $supplier->email }}</td>
                    <td><span class="badge bg-warning">Pending Update</span></td>
                    <td>
                        <a href="{{ route('admin.suppliers.review-update', $supplier->id) }}" class="btn btn-primary btn-sm">Review</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center text-muted">No pending updates.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection 