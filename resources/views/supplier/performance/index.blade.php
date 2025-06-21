@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card">
        <div class="card-header">
            <h4>My Performance</h4>
        </div>
        <div class="card-body">
            <h5>Performance Metrics</h5>
            @if($metrics)
                <div class="row">
                    <div class="col-md-3">
                        <p><strong>Ontime Deliveries:</strong> {{ $metrics->ontime_deliveries }} / {{ $metrics->total_deliveries }}</p>
                    </div>
                    <div class="col-md-3">
                        <p><strong>Defective Units:</strong> {{ $metrics->defective_units }} / {{ $metrics->total_units }}</p>
                    </div>
                    <div class="col-md-3">
                        <p><strong>Cost Variance:</strong> {{ $metrics->actual_cost }} / {{ $metrics->estimated_cost }}</p>
                    </div>
                </div>
            @else
                <p>No metrics available yet.</p>
            @endif

            <h5 class="mt-4">Evaluations</h5>
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Average Rating</th>
                        <th>Comments</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($evaluations as $evaluation)
                        <tr>
                            <td>{{ $evaluation->evaluation_date->format('Y-m-d') }}</td>
                            <td>{{ number_format($evaluation->average_rating, 2) }}</td>
                            <td>{{ $evaluation->comments }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">No evaluations yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $evaluations->links() }}
        </div>
    </div>
</div>
@endsection 