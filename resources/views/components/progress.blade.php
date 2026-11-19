{{-- Labelled progress bar. <x-progress :value="$task->progress" /> — green at 100%. --}}
@props(['value' => 0, 'height' => 20])
@php $pct = max(0, min(100, (int) $value)); @endphp
<div {{ $attributes->merge(['class' => 'progress']) }} style="height: {{ $height }}px;">
    <div class="progress-bar {{ $pct >= 100 ? 'bg-success' : 'bg-primary' }}" role="progressbar" style="width: {{ $pct }}%"
         aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100">{{ $pct }}%</div>
</div>
