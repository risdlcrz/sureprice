@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="h3 mb-4 text-gray-800 text-center" style="font-weight:700;color:#198754;letter-spacing:0.01em;">Supplier Profile Updates Pending Approval</h1>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
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
                    <tr>
                        <td colspan="5" class="text-center text-muted">No pending updates.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('styles')
<style>
body {
    background: linear-gradient(135deg, #f8fafc 0%, #e9ecef 100%) !important;
}
.modern-table {
    border-radius: 1.1rem;
    overflow: hidden;
    box-shadow: 0 4px 24px rgba(44,62,80,0.08), 0 1.5px 6px rgba(44,62,80,0.04);
    background: #fff;
}
.modern-table thead {
    background: #f1f5f9;
    font-weight: 600;
    color: #198754;
    border-bottom: 2px solid #e3e3e3;
}
.modern-table th, .modern-table td {
    vertical-align: middle;
    padding: 1rem 0.75rem;
    border: none;
}
.modern-table tbody tr {
    transition: background 0.2s;
}
.modern-table tbody tr:hover {
    background: #e3f2fd44;
}
.modern-table .badge {
    font-size: 0.95em;
    font-weight: 600;
    letter-spacing: 0.01em;
    box-shadow: 0 1px 4px #ffc10722;
}
.modern-table .btn-primary {
    background: linear-gradient(90deg, #38b6ff 0%, #198754 100%);
    border: none;
    font-weight: 600;
    transition: background 0.2s, box-shadow 0.2s;
}
.modern-table .btn-primary:hover {
    filter: brightness(1.08);
    box-shadow: 0 2px 8px #38b6ff33;
}
</style>
@endpush 