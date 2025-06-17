@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">My Performance & Ranking</h1>
    </div>

    <!-- Overall Performance Card -->
    <div class="card shadow mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">Overall Performance</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <div class="text-center">
                        <h6 class="text-muted mb-2">Overall Score</h6>
                        <h2 class="display-4 text-primary mb-0">{{ $ranking->score ?? 'N/A' }}</h2>
                        <small class="text-muted">out of 100</small>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="text-center">
                        <h6 class="text-muted mb-2">Rank</h6>
                        <h2 class="display-4 text-success mb-0">#{{ $ranking->rank ?? 'N/A' }}</h2>
                        <small class="text-muted">among all suppliers</small>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="text-center">
                        <h6 class="text-muted mb-2">Completed Orders</h6>
                        <h2 class="display-4 text-info mb-0">{{ $completedOrders ?? 0 }}</h2>
                        <small class="text-muted">total orders</small>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="text-center">
                        <h6 class="text-muted mb-2">Average Rating</h6>
                        <h2 class="display-4 text-warning mb-0">{{ $averageRating ?? 'N/A' }}</h2>
                        <small class="text-muted">out of 5.0</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Metrics -->
    <div class="row">
        <!-- Delivery Performance -->
        <div class="col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Delivery Performance</h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h6 class="text-muted mb-2">On-Time Delivery Rate</h6>
                        <div class="progress" style="height: 25px;">
                            <div class="progress-bar bg-success" role="progressbar" 
                                 style="width: {{ $onTimeRate ?? 0 }}%" 
                                 aria-valuenow="{{ $onTimeRate ?? 0 }}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                                {{ $onTimeRate ?? 0 }}%
                            </div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <h6 class="text-muted mb-2">Average Delivery Time</h6>
                        <p class="h4 mb-0">{{ $averageDeliveryTime ?? 'N/A' }} days</p>
                    </div>
                    <div>
                        <h6 class="text-muted mb-2">Late Deliveries</h6>
                        <p class="h4 mb-0">{{ $lateDeliveries ?? 0 }} orders</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quality Metrics -->
        <div class="col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Quality Metrics</h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h6 class="text-muted mb-2">Material Quality Rating</h6>
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <div class="progress" style="height: 25px;">
                                    <div class="progress-bar bg-primary" role="progressbar" 
                                         style="width: {{ ($qualityRating ?? 0) * 20 }}%" 
                                         aria-valuenow="{{ $qualityRating ?? 0 }}" 
                                         aria-valuemin="0" 
                                         aria-valuemax="5">
                                        {{ $qualityRating ?? 'N/A' }}/5
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <h6 class="text-muted mb-2">Return Rate</h6>
                        <div class="progress" style="height: 25px;">
                            <div class="progress-bar bg-danger" role="progressbar" 
                                 style="width: {{ $returnRate ?? 0 }}%" 
                                 aria-valuenow="{{ $returnRate ?? 0 }}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                                {{ $returnRate ?? 0 }}%
                            </div>
                        </div>
                    </div>
                    <div>
                        <h6 class="text-muted mb-2">Quality Complaints</h6>
                        <p class="h4 mb-0">{{ $qualityComplaints ?? 0 }} complaints</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Evaluations -->
    <div class="card shadow">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">Recent Evaluations</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Order #</th>
                        <th>Rating</th>
                        <th>Comments</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentEvaluations ?? [] as $evaluation)
                    <tr>
                        <td>{{ $evaluation->created_at->format('M d, Y') }}</td>
                        <td>{{ $evaluation->order_number }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="text-warning me-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star{{ $i <= $evaluation->rating ? '' : '-o' }}"></i>
                                    @endfor
                                </span>
                                <span class="text-muted">({{ $evaluation->rating }}/5)</span>
                            </div>
                        </td>
                        <td>{{ Str::limit($evaluation->comments, 50) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted">No recent evaluations found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection 