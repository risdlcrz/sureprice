@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Inventory Report (Web Preview)</h1>
        <a href="{{ route('warehouse.reports.inventory.pdf', request()->all()) }}" class="btn btn-primary">
            <i class="fas fa-file-pdf me-1"></i> Download PDF
        </a>
    </div>
    <div class="card">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">Inventory Levels</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Material</th>
                        <th>Category</th>
                        <th>Current Stock</th>
                        <th>Minimum Stock</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($materials as $material)
                    <tr>
                        <td>{{ $material->name }}</td>
                        <td>{{ $material->category->name ?? '-' }}</td>
                        <td>{{ $material->current_stock }}</td>
                        <td>{{ $material->minimum_stock }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection 