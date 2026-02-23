@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0" style="font-weight:700;color:#198754;letter-spacing:0.01em;">Feedback Details</h1>
                <div>
                    <a href="{{ route('admin.feedback.index') }}" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-arrow-left me-2"></i>Back to Feedback
                    </a>
                    <a href="{{ route('admin.feedback.analytics') }}" class="btn btn-info">
                        <i class="fas fa-chart-bar me-2"></i>Analytics
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-star text-warning me-2"></i>
                        Feedback for Contract: {{ $feedback->contract->contract_number }}
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Contract Information -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-primary">Contract Details</h6>
                            <p><strong>Contract:</strong> {{ $feedback->contract->contract_number }}</p>
                            <p><strong>Contractor:</strong> {{ $feedback->contract->contractor->name ?? 'N/A' }}</p>
                            <p><strong>Total Amount:</strong> ₱{{ number_format($feedback->contract->total_amount, 2) }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-primary">Feedback Information</h6>
                            <p><strong>Client:</strong> 
                                @if($feedback->is_anonymous)
                                    <span class="text-muted">Anonymous</span>
                                    <span class="badge bg-info ms-1">Anonymous</span>
                                @else
                                    {{ $feedback->contract->client->name ?? 'N/A' }}
                                @endif
                            </p>
                            <p><strong>Submitted:</strong> {{ $feedback->submitted_at->format('M d, Y H:i') }}</p>
                            <p><strong>Status:</strong> 
                                @if($feedback->submitted_at)
                                    <span class="badge bg-success">Submitted</span>
                                @else
                                    <span class="badge bg-warning">Draft</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <hr>

                    <!-- Ratings -->
                    <div class="mb-4">
                        <h5 class="text-primary mb-3">Client Ratings</h5>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Overall Satisfaction</label>
                                    <div class="d-flex align-items-center">
                                        <div class="text-warning me-2">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star{{ $i <= $feedback->overall_rating ? '' : '-o' }}"></i>
                                            @endfor
                                        </div>
                                        <span class="badge bg-primary">{{ $feedback->overall_rating }}/5</span>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Communication & Responsiveness</label>
                                    <div class="d-flex align-items-center">
                                        <div class="text-warning me-2">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star{{ $i <= $feedback->communication_rating ? '' : '-o' }}"></i>
                                            @endfor
                                        </div>
                                        <span class="badge bg-primary">{{ $feedback->communication_rating }}/5</span>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Quality of Work</label>
                                    <div class="d-flex align-items-center">
                                        <div class="text-warning me-2">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star{{ $i <= $feedback->quality_rating ? '' : '-o' }}"></i>
                                            @endfor
                                        </div>
                                        <span class="badge bg-primary">{{ $feedback->quality_rating }}/5</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Timeliness & Project Completion</label>
                                    <div class="d-flex align-items-center">
                                        <div class="text-warning me-2">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star{{ $i <= $feedback->timeliness_rating ? '' : '-o' }}"></i>
                                            @endfor
                                        </div>
                                        <span class="badge bg-primary">{{ $feedback->timeliness_rating }}/5</span>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Professionalism & Courtesy</label>
                                    <div class="d-flex align-items-center">
                                        <div class="text-warning me-2">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star{{ $i <= $feedback->professionalism_rating ? '' : '-o' }}"></i>
                                            @endfor
                                        </div>
                                        <span class="badge bg-primary">{{ $feedback->professionalism_rating }}/5</span>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Value for Money</label>
                                    <div class="d-flex align-items-center">
                                        <div class="text-warning me-2">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star{{ $i <= $feedback->value_rating ? '' : '-o' }}"></i>
                                            @endfor
                                        </div>
                                        <span class="badge bg-primary">{{ $feedback->value_rating }}/5</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Average Rating -->
                        <div class="mt-3 p-3 bg-light rounded">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h6 class="mb-1">Average Rating</h6>
                                    <div class="d-flex align-items-center">
                                        <div class="text-warning me-2">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star{{ $i <= $feedback->average_rating ? '' : '-o' }}"></i>
                                            @endfor
                                        </div>
                                        <span class="badge bg-success fs-6">{{ number_format($feedback->average_rating, 1) }}/5</span>
                                    </div>
                                </div>
                                <div class="col-md-4 text-end">
                                    <span class="badge bg-info fs-6">{{ $feedback->rating_text }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <!-- Recommendation -->
                    <div class="mb-4">
                        <h5 class="text-primary mb-3">Recommendation</h5>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Likelihood to Recommend GDC</label>
                            <div class="d-flex align-items-center">
                                <div class="progress flex-grow-1 me-3" style="height: 25px;">
                                    <div class="progress-bar bg-success" role="progressbar" 
                                         style="width: {{ ($feedback->recommendation_likelihood / 10) * 100 }}%">
                                        {{ $feedback->recommendation_likelihood }}/10
                                    </div>
                                </div>
                                <span class="badge bg-primary">{{ $feedback->recommendation_text }}</span>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <!-- Comments -->
                    @if($feedback->comments)
                        <div class="mb-4">
                            <h5 class="text-primary mb-3">Additional Comments</h5>
                            <div class="p-3 bg-light rounded">
                                <p class="mb-0">{{ $feedback->comments }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- Priority Level -->
                    <div class="mb-4">
                        <h5 class="text-primary mb-3">Priority Assessment</h5>
                        <div class="p-3 bg-light rounded">
                            @if($feedback->overall_rating <= 2 || $feedback->recommendation_likelihood <= 3)
                                <div class="alert alert-danger">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>High Priority:</strong> This feedback indicates significant areas for improvement. 
                                    Consider immediate action to address client concerns.
                                </div>
                            @elseif($feedback->overall_rating <= 3 || $feedback->recommendation_likelihood <= 5)
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-circle me-2"></i>
                                    <strong>Medium Priority:</strong> This feedback suggests room for improvement. 
                                    Review processes and consider enhancements.
                                </div>
                            @else
                                <div class="alert alert-success">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <strong>Low Priority:</strong> This feedback indicates good performance. 
                                    Continue maintaining current standards.
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.feedback.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Feedback
                        </a>
                        <div>
                            <a href="{{ route('admin.feedback.analytics') }}" class="btn btn-info me-2">
                                <i class="fas fa-chart-bar me-2"></i>View Analytics
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
}

.card-header {
    background: #fff;
    border-radius: 1rem 1rem 0 0;
    border-bottom: 1px solid #e9ecef;
    padding: 1.5rem 2rem 1rem 2rem;
}

.text-warning .fas {
    color: #ffc107 !important;
}

.progress {
    border-radius: 0.5rem;
}

.badge {
    font-size: 0.875rem;
    padding: 0.5em 0.75em;
}

.fs-6 {
    font-size: 1rem !important;
}

.alert {
    border-radius: 0.5rem;
    border: none;
}
</style>
@endsection 