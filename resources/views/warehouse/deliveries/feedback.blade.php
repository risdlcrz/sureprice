@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2>Feedback for Delivery: #{{ $delivery->delivery_number }}</h2>
    <form method="POST" action="{{ route('warehouse.deliveries.submitFeedback', $delivery) }}">
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
            <label for="comments" class="form-label">Comments</label>
            <textarea name="comments" id="comments" class="form-control" rows="4">{{ old('comments', $existing->comments ?? '') }}</textarea>
        </div>
        <button type="submit" class="btn btn-primary">Submit Feedback</button>
        <a href="{{ route('warehouse.deliveries.show', $delivery) }}" class="btn btn-secondary ms-2">Back to Delivery</a>
    </form>
</div>
@endsection 