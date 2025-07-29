@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4" style="font-weight:700;color:#198754;letter-spacing:0.01em;">My Feedback</h1>

            @if(isset($error))
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    {{ $error }}
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('info'))
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    {{ session('info') }}
                </div>
            @endif

            <!-- Feedback Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-comments fa-3x text-primary mb-3"></i>
                            <h5 class="card-title">Total Feedback</h5>
                            <h2 class="text-primary">{{ $feedbacks->count() }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <h5 class="card-title">Submitted</h5>
                            <h2 class="text-success">{{ $feedbacks->where('submitted_at', '!=', null)->count() }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-clock fa-3x text-warning mb-3"></i>
                            <h5 class="card-title">Draft</h5>
                            <h2 class="text-warning">{{ $feedbacks->where('submitted_at', null)->count() }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-star fa-3x text-info mb-3"></i>
                            <h5 class="card-title">Avg Rating</h5>
                            <h2 class="text-info">{{ $feedbacks->where('submitted_at', '!=', null)->avg('overall_rating') ? number_format($feedbacks->where('submitted_at', '!=', null)->avg('overall_rating'), 1) : 'N/A' }}</h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Feedback List -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">My Feedback History</h5>
                </div>
                <div class="card-body">
                    @if($feedbacks->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Contract</th>
                                        <th>Contractor</th>
                                        <th>Overall Rating</th>
                                        <th>Status</th>
                                        <th>Submitted Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($feedbacks as $feedback)
                                        <tr>
                                            <td>
                                                <strong>{{ $feedback->contract->contract_number ?? 'Contract #' . $feedback->contract_id }}</strong><br>
                                                <small class="text-muted">{{ $feedback->contract->title ?? 'N/A' }}</small>
                                            </td>
                                            <td>
                                                {{ $feedback->contract->contractor->name ?? 'N/A' }}<br>
                                                <small class="text-muted">{{ $feedback->contract->contractor->company_name ?? 'N/A' }}</small>
                                            </td>
                                            <td>
                                                @if($feedback->overall_rating)
                                                    <div class="d-flex align-items-center">
                                                        <div class="text-warning me-2">
                                                            @for($i = 1; $i <= 5; $i++)
                                                                <i class="fas fa-star{{ $i <= $feedback->overall_rating ? '' : '-o' }}"></i>
                                                            @endfor
                                                        </div>
                                                        <span class="badge bg-primary">{{ $feedback->overall_rating }}/5</span>
                                                    </div>
                                                @else
                                                    <span class="text-muted">Not rated</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($feedback->submitted_at)
                                                    <span class="badge bg-success">Submitted</span>
                                                @else
                                                    <span class="badge bg-warning">Draft</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($feedback->submitted_at)
                                                    {{ $feedback->submitted_at->format('M d, Y H:i') }}
                                                @else
                                                    <span class="text-muted">Not submitted</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($feedback->submitted_at)
                                                    <a href="{{ route('client.feedback.show', $feedback) }}" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-eye"></i> View
                                                    </a>
                                                @else
                                                    <a href="{{ route('client.feedback.edit', $feedback) }}" class="btn btn-sm btn-warning">
                                                        <i class="fas fa-edit"></i> Continue
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-comments fa-4x text-muted mb-3"></i>
                            <h5 class="text-muted">No feedback yet</h5>
                            <p class="text-muted">You haven't provided any feedback for your completed contracts yet.</p>
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
</style>
@endsection 