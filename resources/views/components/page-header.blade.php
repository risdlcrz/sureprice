@props(['title', 'icon' => null])
<div {{ $attributes->merge(['class' => 'page-header d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4']) }}>
    <h1 class="page-title h3 mb-0">@if($icon)<i class="fas fa-{{ $icon }} me-2"></i>@endif{{ $title }}</h1>
    @if($slot->isNotEmpty())
        <div class="d-flex flex-wrap align-items-center gap-2">{{ $slot }}</div>
    @endif
</div>
