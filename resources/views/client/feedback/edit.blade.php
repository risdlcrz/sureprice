@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-edit text-warning me-2"></i>
                        Edit Feedback for Contract: {{ $feedback->contract->contract_number }}
                    </h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Editing Feedback</strong> You can modify your feedback before submitting it.
                    </div>

                    <form action="{{ route('client.feedback.update', $feedback) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <!-- Contract Information -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6 class="text-primary">Contract Details</h6>
                                <p><strong>Contract:</strong> {{ $feedback->contract->contract_number }}</p>
                                <p><strong>Contractor:</strong> {{ $feedback->contract->contractor->name ?? 'N/A' }}</p>
                                <p><strong>Total Amount:</strong> ₱{{ number_format($feedback->contract->total_amount, 2) }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-primary">Project Information</h6>
                                <p><strong>Title:</strong> {{ $feedback->contract->title ?? 'N/A' }}</p>
                                <p><strong>Start Date:</strong> {{ $feedback->contract->start_date ? $feedback->contract->start_date->format('M d, Y') : 'N/A' }}</p>
                                <p><strong>End Date:</strong> {{ $feedback->contract->end_date ? $feedback->contract->end_date->format('M d, Y') : 'N/A' }}</p>
                            </div>
                        </div>

                        <hr>

                        <!-- Rating Section -->
                        <div class="mb-4">
                            <h5 class="text-primary mb-3">Rate Your Experience (1-5 stars)</h5>
                            
                            <!-- Overall Rating -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">Overall Satisfaction *</label>
                                <div class="rating-group">
                                    @for($i = 5; $i >= 1; $i--)
                                        <input type="radio" name="overall_rating" value="{{ $i }}" id="overall_{{ $i }}" class="rating-input" required {{ $feedback->overall_rating == $i ? 'checked' : '' }}>
                                        <label for="overall_{{ $i }}" class="rating-label">
                                            <i class="fas fa-star"></i>
                                        </label>
                                    @endfor
                                </div>
                                @error('overall_rating')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Communication Rating -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">Communication & Responsiveness *</label>
                                <div class="rating-group">
                                    @for($i = 5; $i >= 1; $i--)
                                        <input type="radio" name="communication_rating" value="{{ $i }}" id="communication_{{ $i }}" class="rating-input" required {{ $feedback->communication_rating == $i ? 'checked' : '' }}>
                                        <label for="communication_{{ $i }}" class="rating-label">
                                            <i class="fas fa-star"></i>
                                        </label>
                                    @endfor
                                </div>
                                @error('communication_rating')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Quality Rating -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">Quality of Work *</label>
                                <div class="rating-group">
                                    @for($i = 5; $i >= 1; $i--)
                                        <input type="radio" name="quality_rating" value="{{ $i }}" id="quality_{{ $i }}" class="rating-input" required {{ $feedback->quality_rating == $i ? 'checked' : '' }}>
                                        <label for="quality_{{ $i }}" class="rating-label">
                                            <i class="fas fa-star"></i>
                                        </label>
                                    @endfor
                                </div>
                                @error('quality_rating')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Timeliness Rating -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">Timeliness & Project Completion *</label>
                                <div class="rating-group">
                                    @for($i = 5; $i >= 1; $i--)
                                        <input type="radio" name="timeliness_rating" value="{{ $i }}" id="timeliness_{{ $i }}" class="rating-input" required {{ $feedback->timeliness_rating == $i ? 'checked' : '' }}>
                                        <label for="timeliness_{{ $i }}" class="rating-label">
                                            <i class="fas fa-star"></i>
                                        </label>
                                    @endfor
                                </div>
                                @error('timeliness_rating')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Professionalism Rating -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">Professionalism & Courtesy *</label>
                                <div class="rating-group">
                                    @for($i = 5; $i >= 1; $i--)
                                        <input type="radio" name="professionalism_rating" value="{{ $i }}" id="professionalism_{{ $i }}" class="rating-input" required {{ $feedback->professionalism_rating == $i ? 'checked' : '' }}>
                                        <label for="professionalism_{{ $i }}" class="rating-label">
                                            <i class="fas fa-star"></i>
                                        </label>
                                    @endfor
                                </div>
                                @error('professionalism_rating')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Value Rating -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">Value for Money *</label>
                                <div class="rating-group">
                                    @for($i = 5; $i >= 1; $i--)
                                        <input type="radio" name="value_rating" value="{{ $i }}" id="value_{{ $i }}" class="rating-input" required {{ $feedback->value_rating == $i ? 'checked' : '' }}>
                                        <label for="value_{{ $i }}" class="rating-label">
                                            <i class="fas fa-star"></i>
                                        </label>
                                    @endfor
                                </div>
                                @error('value_rating')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr>

                        <!-- Recommendation Section -->
                        <div class="mb-4">
                            <h5 class="text-primary mb-3">Recommendation</h5>
                            <div class="mb-3">
                                <label class="form-label fw-bold">How likely are you to recommend GDC to others? *</label>
                                <select name="recommendation_likelihood" class="form-select" required>
                                    <option value="">Select likelihood</option>
                                    @for($i = 10; $i >= 1; $i--)
                                        <option value="{{ $i }}" {{ $feedback->recommendation_likelihood == $i ? 'selected' : '' }}>
                                            {{ $i }} - {{ $i >= 9 ? 'Definitely' : ($i >= 7 ? 'Very Likely' : ($i >= 5 ? 'Likely' : ($i >= 3 ? 'Maybe' : 'Unlikely'))) }}
                                        </option>
                                    @endfor
                                </select>
                                @error('recommendation_likelihood')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr>

                        <!-- Comments Section -->
                        <div class="mb-4">
                            <h5 class="text-primary mb-3">Additional Comments</h5>
                            <div class="mb-3">
                                <label class="form-label">Please share your experience with us (optional)</label>
                                <textarea name="comments" class="form-control" rows="4" placeholder="Tell us about your experience, what went well, and how we can improve...">{{ old('comments', $feedback->comments) }}</textarea>
                                @error('comments')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr>

                        <!-- Anonymous Option -->
                        <div class="mb-4">
                            <div class="form-check">
                                <input type="checkbox" name="is_anonymous" value="1" class="form-check-input" id="is_anonymous" {{ $feedback->is_anonymous ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_anonymous">
                                    Submit feedback anonymously
                                </label>
                            </div>
                            <small class="text-muted">If checked, your name will not be associated with this feedback in our reports.</small>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('client.feedback.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Back to Feedback
                            </a>
                            <div>
                                <button type="submit" class="btn btn-warning me-2">
                                    <i class="fas fa-save me-2"></i>Update Feedback
                                </button>
                                @if(!$feedback->submitted_at)
                                    <a href="{{ route('client.feedback.submit', $feedback) }}" class="btn btn-success" onclick="return confirm('Are you sure you want to submit this feedback? You won\'t be able to edit it after submission.')">
                                        <i class="fas fa-paper-plane me-2"></i>Submit Feedback
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
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

.rating-group {
    display: flex;
    flex-direction: row-reverse;
    gap: 0.25rem;
}

.rating-input {
    display: none;
}

.rating-label {
    cursor: pointer;
    font-size: 1.5rem;
    color: #dee2e6;
    transition: color 0.2s ease;
}

.rating-label:hover,
.rating-label:hover ~ .rating-label,
.rating-input:checked ~ .rating-label {
    color: #ffc107;
}

.form-control, .form-select {
    border-radius: 0.5rem;
    border: 1.5px solid #ced4da;
    padding: 0.75rem 1rem;
    transition: all 0.2s ease;
}

.form-control:focus, .form-select:focus {
    border-color: #198754;
    box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25);
}

.btn {
    border-radius: 0.5rem;
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    transition: all 0.2s ease;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-calculate overall rating based on other ratings
    const ratingInputs = document.querySelectorAll('input[name="communication_rating"], input[name="quality_rating"], input[name="timeliness_rating"], input[name="professionalism_rating"], input[name="value_rating"]');
    const overallRatingInputs = document.querySelectorAll('input[name="overall_rating"]');
    
    function calculateOverallRating() {
        const ratings = [];
        ratingInputs.forEach(input => {
            if (input.checked) {
                ratings.push(parseInt(input.value));
            }
        });
        
        if (ratings.length === 5) {
            const average = Math.round(ratings.reduce((a, b) => a + b, 0) / ratings.length);
            overallRatingInputs.forEach(input => {
                if (parseInt(input.value) === average) {
                    input.checked = true;
                }
            });
        }
    }
    
    ratingInputs.forEach(input => {
        input.addEventListener('change', calculateOverallRating);
    });
});
</script>
@endsection 