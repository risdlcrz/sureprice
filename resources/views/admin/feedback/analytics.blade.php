@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0" style="font-weight:700;color:#198754;letter-spacing:0.01em;">Feedback Analytics Dashboard</h1>
                <div>
                    <a href="{{ route('admin.feedback.index') }}" class="btn btn-outline-primary me-2">
                        <i class="fas fa-list me-2"></i>All Feedback
                    </a>
                    <div class="btn-group">
                        <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-download me-2"></i>Export Data
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('admin.feedback.export', ['format'=>'csv']) }}">CSV</a></li>
                            <li><a class="dropdown-item" href="{{ route('admin.feedback.export', ['format'=>'xlsx']) }}">Excel</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Overall Statistics -->
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
                            <i class="fas fa-thumbs-up fa-3x text-success mb-3"></i>
                            <h5 class="card-title">Recommendation Score</h5>
                            <h2 class="text-success">{{ number_format($stats['recommendation_avg'], 1) }}/10</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-user-secret fa-3x text-info mb-3"></i>
                            <h5 class="card-title">Anonymous Rate</h5>
                            <h2 class="text-info">{{ $stats['total_feedback'] > 0 ? round(($stats['anonymous_count'] / $stats['total_feedback']) * 100, 1) : 0 }}%</h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rating Breakdown by Category -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Rating Breakdown by Category</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Communication & Responsiveness</label>
                                        <div class="d-flex align-items-center">
                                            <div class="text-warning me-2">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="fas fa-star{{ $i <= $categoryRatings['communication'] ? '' : '-o' }}"></i>
                                                @endfor
                                            </div>
                                            <span class="badge bg-primary">{{ number_format($categoryRatings['communication'], 1) }}/5</span>
                                        </div>
                                        <div class="progress mt-1" style="height: 8px;">
                                            <div class="progress-bar bg-warning" role="progressbar" 
                                                 style="width: {{ ($categoryRatings['communication'] / 5) * 100 }}%">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Quality of Work</label>
                                        <div class="d-flex align-items-center">
                                            <div class="text-warning me-2">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="fas fa-star{{ $i <= $categoryRatings['quality'] ? '' : '-o' }}"></i>
                                                @endfor
                                            </div>
                                            <span class="badge bg-primary">{{ number_format($categoryRatings['quality'], 1) }}/5</span>
                                        </div>
                                        <div class="progress mt-1" style="height: 8px;">
                                            <div class="progress-bar bg-warning" role="progressbar" 
                                                 style="width: {{ ($categoryRatings['quality'] / 5) * 100 }}%">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Timeliness & Project Completion</label>
                                        <div class="d-flex align-items-center">
                                            <div class="text-warning me-2">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="fas fa-star{{ $i <= $categoryRatings['timeliness'] ? '' : '-o' }}"></i>
                                                @endfor
                                            </div>
                                            <span class="badge bg-primary">{{ number_format($categoryRatings['timeliness'], 1) }}/5</span>
                                        </div>
                                        <div class="progress mt-1" style="height: 8px;">
                                            <div class="progress-bar bg-warning" role="progressbar" 
                                                 style="width: {{ ($categoryRatings['timeliness'] / 5) * 100 }}%">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Professionalism & Courtesy</label>
                                        <div class="d-flex align-items-center">
                                            <div class="text-warning me-2">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="fas fa-star{{ $i <= $categoryRatings['professionalism'] ? '' : '-o' }}"></i>
                                                @endfor
                                            </div>
                                            <span class="badge bg-primary">{{ number_format($categoryRatings['professionalism'], 1) }}/5</span>
                                        </div>
                                        <div class="progress mt-1" style="height: 8px;">
                                            <div class="progress-bar bg-warning" role="progressbar" 
                                                 style="width: {{ ($categoryRatings['professionalism'] / 5) * 100 }}%">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Value for Money</label>
                                        <div class="d-flex align-items-center">
                                            <div class="text-warning me-2">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="fas fa-star{{ $i <= $categoryRatings['value'] ? '' : '-o' }}"></i>
                                                @endfor
                                            </div>
                                            <span class="badge bg-primary">{{ number_format($categoryRatings['value'], 1) }}/5</span>
                                        </div>
                                        <div class="progress mt-1" style="height: 8px;">
                                            <div class="progress-bar bg-warning" role="progressbar" 
                                                 style="width: {{ ($categoryRatings['value'] / 5) * 100 }}%">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Top Contractors by Rating</h5>
                        </div>
                        <div class="card-body">
                            @if($topContractors->count() > 0)
                                @foreach($topContractors->take(5) as $contractor)
                                    <div class="mb-3 p-2 border rounded">
                                        <div class="fw-bold">{{ $contractor->name }}</div>
                                        <div class="small text-muted">{{ $contractor->company_name }}</div>
                                        <div class="d-flex align-items-center mt-1">
                                            <div class="text-warning me-2">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="fas fa-star{{ $i <= $contractor->avg_rating ? '' : '-o' }}" style="font-size: 0.8em;"></i>
                                                @endfor
                                            </div>
                                            <span class="badge bg-success">{{ number_format($contractor->avg_rating, 1) }}/5</span>
                                            <span class="badge bg-secondary ms-1">{{ $contractor->feedback_count }} reviews</span>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <p class="text-muted">No contractor ratings available yet.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Feedback -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Recent Feedback</h5>
                        </div>
                        <div class="card-body">
                            @if($recentFeedback->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Contract</th>
                                                <th>Client</th>
                                                <th>Contractor</th>
                                                <th>Rating</th>
                                                <th>Recommendation</th>
                                                <th>Priority</th>
                                                <th>Submitted</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($recentFeedback as $feedback)
                                                <tr>
                                                    <td>
                                                        <strong>{{ $feedback->contract->contract_number ?? 'Contract #' . $feedback->contract_id }}</strong>
                                                    </td>
                                                    <td>
                                                        @if($feedback->is_anonymous)
                                                            <span class="text-muted">Anonymous</span>
                                                        @else
                                                            {{ $feedback->contract->client->name ?? 'N/A' }}
                                                        @endif
                                                    </td>
                                                    <td>{{ $feedback->contract->contractor->name ?? 'N/A' }}</td>
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
                                                        <span class="badge bg-success">{{ $feedback->recommendation_likelihood }}/10</span>
                                                    </td>
                                                    <td>
                                                        @if($feedback->overall_rating <= 2 || $feedback->recommendation_likelihood <= 3)
                                                            <span class="badge bg-danger">High</span>
                                                        @elseif($feedback->overall_rating <= 3 || $feedback->recommendation_likelihood <= 5)
                                                            <span class="badge bg-warning">Medium</span>
                                                        @else
                                                            <span class="badge bg-success">Low</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $feedback->submitted_at->format('M d, Y H:i') }}</td>
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