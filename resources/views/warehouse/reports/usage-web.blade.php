@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Material Usage Report (Web Preview)</h1>
        <a href="{{ route('warehouse.reports.usage.pdf', request()->all()) }}" class="btn btn-primary">
            <i class="fas fa-file-pdf me-1"></i> Download PDF
        </a>
    </div>
    <div class="card">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">Material Usage</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Material</th>
                        <th>Total Out</th>
                        <th>Total In</th>
                        <th>Net Change</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($usageStats as $stat)
                    <tr>
                        <td>{{ $stat['material']->name ?? '-' }}</td>
                        <td>{{ $stat['total_out'] }}</td>
                        <td>{{ $stat['total_in'] }}</td>
                        <td>{{ $stat['net_change'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection 