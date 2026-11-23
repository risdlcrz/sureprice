@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4 page-title">My Feedback</h1>

            @if(isset($error))
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    {{ $error }}
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
                            <h5>No feedback yet</h5>
                            <p class="text-muted mb-0">Once a contract is completed and fully paid, you can rate the work here.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@vite('resources/css/pages/client-feedback-index.css')
@endsection 