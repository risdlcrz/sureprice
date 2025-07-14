@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2>Feedback for Project: {{ $project->name }}</h2>
    <form method="POST" action="{{ route('projects.submitFeedback', $project) }}">
        @csrf
        <div class="mb-3">
            <label for="rating" class="form-label">Rating (1-5)</label>
            <select name="rating" id="rating" class="form-select" required>
                <option value="">Select Rating</option>
                @for($i = 1; $i <= 5; $i++)
                    <option value="{{ $i }}" {{ (old('rating', $existing->rating ?? null) == $i) ? 'selected' : '' }}>{{ $i }}</option>
                @endfor
            </select>
        </div>
        <div class="mb-3">
            <label for="comments" class="form-label">Comments (optional)</label>
            <textarea name="comments" id="comments" class="form-control" rows="4">{{ old('comments', $existing->comments ?? '') }}</textarea>
        </div>
        <button type="submit" class="btn btn-success">Submit Feedback</button>
        <a href="{{ route('projects.show', $project) }}" class="btn btn-secondary ms-2">Back to Project</a>
    </form>
</div>
@endsection 