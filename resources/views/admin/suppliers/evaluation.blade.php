@foreach($evaluationCriteria as $criterion)
<div class="form-group mb-4">
    <label class="form-label">{{ $criterion->name }}</label>
    <div class="rating">
        @for($i = 5; $i >= 0.5; $i -= 0.5)
            <input type="radio" 
                id="{{ $criterion->id }}_{{ str_replace('.', '_', $i) }}" 
                name="ratings[{{ $criterion->id }}]" 
                value="{{ $i }}"
                {{ old("ratings.{$criterion->id}", isset($evaluation) ? $evaluation->ratings[$criterion->id] ?? 0 : 0) == $i ? 'checked' : '' }}>
            <label for="{{ $criterion->id }}_{{ str_replace('.', '_', $i) }}" 
                class="{{ $i % 1 == 0 ? 'full' : 'half' }}" 
                title="{{ $i }} stars">
                <i class="{{ $i % 1 == 0 ? 'fas fa-star' : 'fas fa-star-half-alt' }}"></i>
            </label>
        @endfor
        <div class="current-rating"></div>
    </div>
</div>
@endforeach

@push('styles')
<style>
.rating {
    display: inline-block;
    position: relative;
    height: 40px;
    line-height: 40px;
    font-size: 24px;
    direction: rtl;
}

.rating label {
    position: absolute;
    top: 0;
    cursor: pointer;
    color: #ddd;
}

.rating label.half {
    width: 12px;
    z-index: 3;
}

.rating label.full {
    width: 24px;
    z-index: 2;
}

/* Position each star and half-star */
.rating label:nth-of-type(1) { right: 0px; }
.rating label:nth-of-type(2) { right: 12px; }
.rating label:nth-of-type(3) { right: 24px; }
.rating label:nth-of-type(4) { right: 36px; }
.rating label:nth-of-type(5) { right: 48px; }
.rating label:nth-of-type(6) { right: 60px; }
.rating label:nth-of-type(7) { right: 72px; }
.rating label:nth-of-type(8) { right: 84px; }
.rating label:nth-of-type(9) { right: 96px; }
.rating label:nth-of-type(10) { right: 108px; }

.rating input {
    display: none;
}

.rating label:hover,
.rating label:hover ~ label,
.rating input:checked ~ label {
    color: #ffd700;
}

.rating label i {
    font-size: 24px;
    transition: color 0.2s;
}

.rating label.half i {
    position: absolute;
    width: 12px;
    overflow: hidden;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ratings = document.querySelectorAll('.rating');
    
    ratings.forEach(rating => {
        const inputs = rating.querySelectorAll('input');
        const labels = rating.querySelectorAll('label');
        
        // Handle keyboard navigation
        inputs.forEach((input, index) => {
            input.addEventListener('keydown', (e) => {
                let nextIndex;
                
                switch(e.key) {
                    case 'ArrowRight':
                        nextIndex = Math.max(0, index - 1);
                        break;
                    case 'ArrowLeft':
                        nextIndex = Math.min(inputs.length - 1, index + 1);
                        break;
                    default:
                        return;
                }
                
                inputs[nextIndex].checked = true;
                inputs[nextIndex].focus();
                e.preventDefault();
            });
        });
    });
});
</script>
@endpush 