{{-- Create or edit client feedback. Params: $contract (create) or $feedback (edit). --}}
@extends('layouts.app')

@php
    $fb = $feedback ?? null;
    $contract = $fb?->contract ?? $contract;
    $ratings = [
        'overall_rating' => 'Overall Satisfaction',
        'communication_rating' => 'Communication & Responsiveness',
        'quality_rating' => 'Quality of Work',
        'timeliness_rating' => 'Timeliness & Project Completion',
        'professionalism_rating' => 'Professionalism & Courtesy',
        'value_rating' => 'Value for Money',
    ];
    $likelihood = [10 => 'Definitely', 9 => 'Very Likely', 8 => 'Likely', 7 => 'Probably', 6 => 'Maybe',
        5 => 'Neutral', 4 => 'Unlikely', 3 => 'Very Unlikely', 2 => 'Definitely Not', 1 => 'Never'];
@endphp
@section('title', $fb ? 'Edit Feedback' : 'Provide Feedback')

@section('content')
<div class="container-fluid">
    <x-page-header :title="($fb ? 'Edit Feedback' : 'Provide Feedback') . ' — ' . $contract->contract_number" :icon="$fb ? 'edit' : 'star'">
        <a href="{{ route('client.feedback.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i>Back to Feedback</a>
    </x-page-header>

    <div class="card">
        <div class="card-body">
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                @if($fb)
                    <strong>Editing feedback.</strong> You can change it until you submit it.
                @else
                    <strong>Thank you for choosing GDC!</strong> Please take a moment to rate your experience with us.
                @endif
            </div>

            <form action="{{ $fb ? route('client.feedback.update', $fb) : route('client.feedback.store', $contract) }}" method="POST">
                @csrf
                @if($fb) @method('PUT') @endif

                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6 class="text-primary">Contract Details</h6>
                        <p class="mb-1"><strong>Contract:</strong> {{ $contract->contract_number }}</p>
                        <p class="mb-1"><strong>Contractor:</strong> {{ $contract->contractor->name ?? 'N/A' }}</p>
                        <p class="mb-1"><strong>Total Amount:</strong> ₱{{ number_format($contract->total_amount, 2) }}</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-primary">Project Information</h6>
                        <p class="mb-1"><strong>Title:</strong> {{ $contract->title ?? 'N/A' }}</p>
                        <p class="mb-1"><strong>Start Date:</strong> {{ $contract->start_date?->format('M d, Y') ?? 'N/A' }}</p>
                        <p class="mb-1"><strong>End Date:</strong> {{ $contract->end_date?->format('M d, Y') ?? 'N/A' }}</p>
                    </div>
                </div>
                <hr>

                <h5 class="text-primary mb-3">Rate Your Experience (1-5 stars)</h5>
                @foreach($ratings as $name => $label)
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ $label }} *</label>
                        <div class="rating-group">
                            @for($i = 5; $i >= 1; $i--)
                                <input type="radio" name="{{ $name }}" value="{{ $i }}" id="{{ $name }}_{{ $i }}" class="rating-input" required @checked(old($name, $fb?->$name) == $i)>
                                <label for="{{ $name }}_{{ $i }}" class="rating-label"><i class="fas fa-star"></i></label>
                            @endfor
                        </div>
                        @error($name)<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                @endforeach
                <hr>

                <x-field name="recommendation_likelihood" label="How likely are you to recommend GDC to others?" required>
                    <select name="recommendation_likelihood" id="recommendation_likelihood" class="form-select" required>
                        <option value="">Select likelihood</option>
                        @foreach($likelihood as $value => $text)
                            <option value="{{ $value }}" @selected(old('recommendation_likelihood', $fb?->recommendation_likelihood) == $value)>{{ $value }} - {{ $text }}</option>
                        @endforeach
                    </select>
                </x-field>
                <x-field name="comments" label="Additional comments (optional)" type="textarea" rows="4" :value="$fb?->comments"
                    placeholder="Tell us about your experience, what went well, and how we can improve..." />

                <div class="form-check mb-1">
                    <input type="checkbox" name="is_anonymous" value="1" class="form-check-input" id="is_anonymous" @checked(old('is_anonymous', $fb?->is_anonymous))>
                    <label class="form-check-label" for="is_anonymous">Submit feedback anonymously</label>
                </div>
                <small class="text-muted d-block mb-4">If checked, your name will not be associated with this feedback in our reports.</small>

                <div class="d-flex justify-content-end gap-2">
                    @if($fb)
                        <button type="submit" class="btn btn-warning"><i class="fas fa-save me-1"></i>Update Feedback</button>
                        @unless($fb->submitted_at)
                            <button type="submit" form="submit-feedback-form" class="btn btn-success"
                                onclick="return confirm('Submit this feedback? You won\'t be able to edit it afterwards.')">
                                <i class="fas fa-paper-plane me-1"></i>Submit Feedback
                            </button>
                        @endunless
                    @else
                        <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane me-1"></i>Submit Feedback</button>
                    @endif
                </div>
            </form>
            @if($fb && !$fb->submitted_at)
                <form id="submit-feedback-form" method="POST" action="{{ route('client.feedback.submit', $fb) }}">@csrf</form>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.rating-group { display: flex; flex-direction: row-reverse; justify-content: flex-end; gap: .25rem; }
.rating-input { display: none; }
.rating-label { cursor: pointer; font-size: 1.5rem; color: #dee2e6; transition: color .2s; }
.rating-label:hover, .rating-label:hover ~ .rating-label, .rating-input:checked ~ .rating-label { color: #ffc107; }
</style>
@endpush

@push('scripts')
<script>
// Overall rating defaults to the rounded average of the five category ratings.
(() => {
    const parts = ['communication_rating', 'quality_rating', 'timeliness_rating', 'professionalism_rating', 'value_rating'];
    const update = () => {
        const vals = parts.map(n => document.querySelector(`input[name="${n}"]:checked`)?.value).filter(Boolean).map(Number);
        if (vals.length !== parts.length) return;
        const avg = Math.round(vals.reduce((a, b) => a + b, 0) / vals.length);
        const target = document.getElementById(`overall_rating_${avg}`);
        if (target) target.checked = true;
    };
    parts.forEach(n => document.querySelectorAll(`input[name="${n}"]`).forEach(i => i.addEventListener('change', update)));
})();
</script>
@endpush
