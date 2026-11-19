@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="h3 mb-4 text-gray-800 text-center" style="font-weight:700;color:#198754;letter-spacing:0.01em;">Supplier Profile Updates Pending Approval</h1>
    <div class="table-responsive">
        <table class="table table-bordered modern-table align-middle">
            <thead class="table-light">
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
                        <td><span class="badge bg-warning text-dark rounded-pill px-3 py-2" style="font-size:0.95em;">Pending Update</span></td>
                        <td>
                            <a href="{{ route('admin.suppliers.review-update', $supplier->id) }}" class="btn btn-primary btn-sm rounded-pill px-3">Review</a>
                        </td>
                    </tr>
                @empty
                    <x-empty-row cols="5">No pending updates.</x-empty-row>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('styles')
@vite('resources/css/pages/admin-suppliers-pending-updates.css')
@endpush 