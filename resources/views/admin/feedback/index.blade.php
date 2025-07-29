@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0" style="font-weight:700;color:#198754;letter-spacing:0.01em;">Client Feedback Management</h1>
                <div>
                    <a href="{{ route('admin.feedback.analytics') }}" class="btn btn-info me-2">
                        <i class="fas fa-chart-bar me-2"></i>Analytics
                    </a>
                    <a href="{{ route('admin.feedback.export') }}" class="btn btn-success">
                        <i class="fas fa-download me-2"></i>Export CSV
                    </a>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-comments fa-3x text-primary mb-3"></i>
                            <h5 class="card-title">Total Feedback</h5>
                            <h2 class="text-primary">{{ $stats['total_feedback'] }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-star fa-3x text-warning mb-3"></i>
                            <h5 class="card-title">Average Rating</h5>
                            <h2 class="text-warning">{{ number_format($stats['average_rating'], 1) }}/5</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-user-secret fa-3x text-info mb-3"></i>
                            <h5 class="card-title">Anonymous</h5>
                            <h2 class="text-info">{{ $stats['anonymous_count'] }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-thumbs-up fa-3x text-success mb-3"></i>
                            <h5 class="card-title">Recommendation</h5>
                            <h2 class="text-success">{{ number_format($stats['recommendation_avg'], 1) }}/10</h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rating Distribution -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Rating Distribution</h5>
                        </div>
                        <div class="card-body">
                            @foreach($ratingDistribution as $rating)
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="d-flex align-items-center">
                                        <div class="text-warning me-2">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star{{ $i <= $rating->overall_rating ? '' : '-o' }}"></i>
                                            @endfor
                                        </div>
                                        <span>{{ $rating->overall_rating }} stars</span>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <div class="progress me-2" style="width: 100px; height: 8px;">
                                            <div class="progress-bar bg-warning" role="progressbar" 
                                                 style="width: {{ ($rating->count / $stats['total_feedback']) * 100 }}%">
                                            </div>
                                        </div>
                                        <span class="badge bg-secondary">{{ $rating->count }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Feedback List -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">All Submitted Feedback</h5>
                </div>
                <div class="card-body">
                    @if($feedbacks->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Contract</th>
                                        <th>Client</th>
                                        <th>Contractor</th>
                                        <th>Overall Rating</th>
                                        <th>Recommendation</th>
                                        <th>Anonymous</th>
                                        <th>Submitted</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($feedbacks as $feedback)
                                        <tr>
                                            <td>{{ $feedback->id }}</td>
                                            <td>
                                                <strong>{{ $feedback->contract->contract_number ?? 'Contract #' . $feedback->contract_id }}</strong><br>
                                                <small class="text-muted">{{ $feedback->contract->title ?? 'N/A' }}</small>
                                            </td>
                                            <td>
                                                @if($feedback->is_anonymous)
                                                    <span class="text-muted">Anonymous</span>
                                                @else
                                                    {{ $feedback->contract->client->name ?? 'N/A' }}<br>
                                                    <small class="text-muted">{{ $feedback->contract->client->email ?? 'N/A' }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $feedback->contract->contractor->name ?? 'N/A' }}<br>
                                                <small class="text-muted">{{ $feedback->contract->contractor->company_name ?? 'N/A' }}</small>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="text-warning me-2">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            <i class="fas fa-star{{ $i <= $feedback->overall_rating ? '' : '-o' }}"></i>
                                                        @endfor
                                                    </div>
                                                    <span class="badge bg-primary">{{ $feedback->overall_rating }}/5</span>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-success">{{ $feedback->recommendation_likelihood }}/10</span><br>
                                                <small class="text-muted">{{ $feedback->recommendation_text }}</small>
                                            </td>
                                            <td>
                                                @if($feedback->is_anonymous)
                                                    <span class="badge bg-info">Yes</span>
                                                @else
                                                    <span class="badge bg-secondary">No</span>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $feedback->submitted_at->format('M d, Y H:i') }}<br>
                                                <small class="text-muted">{{ $feedback->submitted_at->diffForHumans() }}</small>
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.feedback.show', $feedback) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $feedbacks->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-comments fa-4x text-muted mb-3"></i>
                            <h5 class="text-muted">No feedback submitted yet</h5>
                            <p class="text-muted">Client feedback will appear here once submitted.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border-radius: 1rem;
    box-shadow: 0 4px 24px rgba(44,62,80,0.08), 0 1.5px 6px rgba(44,62,80,0.04);
    border: none;
    margin-bottom: 2rem;
}

.card-header {
    background: #fff;
    border-radius: 1rem 1rem 0 0;
    border-bottom: 1px solid #e9ecef;
    padding: 1.5rem 2rem 1rem 2rem;
}

.table {
    margin-bottom: 0;
    font-size: 0.9rem;
}

.table th {
    background: #f8f9fa;
    font-weight: 600;
    color: #495057;
    border-bottom: 2px solid #dee2e6;
}

.badge {
    font-size: 0.75rem;
    padding: 0.5em 0.75em;
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

.text-warning .fas {
    color: #ffc107 !important;
}

.progress {
    border-radius: 0.5rem;
}
</style>
@endsection 