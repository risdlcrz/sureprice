@extends('layouts.app')

@section('content')
<div class="container py-4">
    <!-- Error Messages -->
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Success Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show">
            {{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show">
            {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('import_message'))
        <div class="alert alert-{{ session('import_message.type') }} alert-dismissible fade show mb-4">
            {{ session('import_message.text') }}
            @if(!empty(session('import_message.errors')))
                <ul class="mt-2 mb-0">
                    @foreach(session('import_message.errors') as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <h1 class="text-center mb-4">Supplier Directory</h1>
    
    <!-- Category Selection Dropdown -->
    <div class="row mb-4">
        <div class="col-md-6 offset-md-3">
            <div class="card">
                <div class="card-body">
                    <form method="GET" class="d-flex align-items-center gap-3" id="categoryForm">
                        <label class="form-label mb-0"><strong>Rank By:</strong></label>
                        <select name="category" class="form-select" id="categorySelect">
                            @foreach($validCategories as $value => $label)
                                <option value="{{ $value }}" {{ $category === $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        <select name="order" class="form-select" id="orderSelect">
                            <option value="desc" {{ $order === 'desc' ? 'selected' : '' }}>Highest First</option>
                            <option value="asc" {{ $order === 'asc' ? 'selected' : '' }}>Lowest First</option>
                        </select>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Rankings Chart -->
    <div class="card mb-4">
        <div class="card-body">
            <h4 class="card-title mb-4">Supplier {{ $validCategories[$category] }} Rankings</h4>
            <div class="chart-container">
                <canvas id="rankingsChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Add Supplier Button -->
    <div class="text-end mb-4">
        <button class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#importModal">
            <i class="fas fa-file-import"></i> Import Suppliers
        </button>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
            <i class="fas fa-plus"></i> Add Supplier
        </button>
    </div>

    <!-- Supplier List -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th style="width: 70px;">Rank</th>
                            <th>Company Name</th>
                            <th>Contact Person</th>
                            <th>Email</th>
                            <th>Mobile Number</th>
                            <th>Type</th>
                            <th style="width: 100px;">Rating</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sortedSuppliers as $index => $supplier)
                            @php
                                $supplierId = $supplier->id;
                                $rating = $supplier->{$category} ?? 0;
                                $rank = $index + 1;
                            @endphp
                            <tr>
                                <td>
                                    <span class="badge bg-success rounded-pill">#{{ $rank }}</span>
                                </td>
                                <td>
                                    <a href="#" class="supplier-link text-primary text-decoration-none" 
                                       data-bs-toggle="modal" 
                                       data-bs-target="#supplierDetailsModal"
                                       data-supplier-id="{{ $supplierId }}"
                                       data-supplier='@json($supplier)'>
                                        {{ $supplier->company }}
                                    </a>
                                </td>
                                <td>{{ $supplier->contact_person ?? 'N/A' }}</td>
                                <td>{{ $supplier->email ?? 'N/A' }}</td>
                                <td>{{ $supplier->mobile_number ?? 'N/A' }}</td>
                                <td>{{ $supplier->supplier_type ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-{{ 
                                        $rating >= 4.5 ? 'success' : 
                                        ($rating >= 4.0 ? 'info' : 
                                         ($rating >= 3.0 ? 'warning' : 'danger'))
                                    }}">
                                        {{ number_format($rating, 1) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Include the modals -->
    @include('admin.suppliers.partials.add-modal')
    @include('admin.suppliers.partials.details-modal')
    @include('admin.suppliers.partials.import-modal')
</div>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
    /* Add your existing styles here */
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Add your existing JavaScript here
</script>
@endpush
@endsection